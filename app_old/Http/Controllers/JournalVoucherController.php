<?php

namespace App\Http\Controllers;

use App\Queries\JournalVoucherDataTable;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Exception;
use App\Http\Requests\JournalVoucherRequest;
use App\Models\Leave;
use App\Http\Requests\UpdateJournalVoucherRequest;
use App\Repositories\JournalVoucherRepository;
use App\Repositories\OverTimeRepository;
use Illuminate\Database\QueryException;
use Laracasts\Flash\Flash;
use App\Models\JournalVoucher;
use Throwable;
use App\Models\DocumentNextNumber;
use App\Models\CashTransfer;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings; // Update with the correct model if necessary
use DB;
use Carbon\Carbon;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf; // Import the PDF facade

class JournalVoucherController extends AppBaseController
{
    /**
     * @var JournalVoucherRepository;
     */
    private $journalVoucherRepository;
    public function __construct(JournalVoucherRepository $journalVoucherRepo)
    {
        $this->journalVoucherRepository = $journalVoucherRepo;
    }
    /**
     * @param  Request  $request
     * @return Application|Factory|View
     *
     * @throws Exception
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return DataTables::of((new JournalVoucherDataTable())->get($request->all()))->make(true);
        }
        $usersBranches = $this->getUsersBranches();
        return view('journal-vouchers.index', compact('usersBranches'));
    }

    public function create()
    {
        $accounts = $this->journalVoucherRepository->getAccounts();
        $usersBranches = $this->getUsersBranches();
        return view('journal-vouchers.create', compact('accounts', 'usersBranches'));
    }

    public function store(JournalVoucherRequest $request)
    {
        $input = $request->all();
        try {
            $designation = $this->journalVoucherRepository->create($input);

            activity()->causedBy(getLoggedInUser())
                ->performedOn($designation)
                ->useLog('Journal Voucher created.')
                ->log("Journal Voucher Created");
            Flash::success(__('messages.journal-vouchers.saved'));
            return $this->sendResponse($designation, __('messages.journal-vouchers.saved'));
        } catch (Throwable $e) {
            throw $e;
        }
    }

    public function destroy(JournalVoucher $voucher)
    {

        try {
            $voucher->delete();
            activity()->performedOn($voucher)->causedBy(getLoggedInUser())
                ->useLog('Account deleted.')->log('Journal deleted.');
            return $this->sendSuccess(__('messages.journal-vouchers.delete'));
        } catch (QueryException $e) {
            return $this->sendError('Failed To delete!! Already in use.');
        }
    }

    public function edit(JournalVoucher $voucher)
    {

        $accounts = $this->journalVoucherRepository->getAccounts();
        $usersBranches = $this->getUsersBranches();
        return view('journal-vouchers.edit', compact(['voucher', 'accounts', 'usersBranches']));
    }
    public function update(JournalVoucher $voucher, UpdateJournalVoucherRequest $updateJournalVoucherRequest)
    {
        $input = $updateJournalVoucherRequest->all();
        $designation = $this->journalVoucherRepository->updateVoucher($input, $updateJournalVoucherRequest->id);
        activity()->performedOn($designation)->causedBy(getLoggedInUser())
            ->useLog('Journal Voucher Updated')->log('Journal Voucher updated.');
        Flash::success(__('messages.journal-vouchers.saved'));
        return $this->sendSuccess(__('messages.journal-vouchers.saved'));
    }

    public function view(JournalVoucher $voucher)
    {
        $voucher->load('account', 'branch', 'fromAccount');
        return view('journal-vouchers.view', compact(['voucher']));
    }

    public function export(Request $request)
    {
        // Fetch data using DataTables
        $journalVouchers = DataTables::of((new JournalVoucherDataTable())->get($request->all()))->make(true);

        // Extract the data for processing
        $journalVouchers = $journalVouchers->original['data'] ?? [];

        // Prepare data for export
        $exportData = [];
        $totalAmount = 0;

        foreach ($journalVouchers as $voucher) {
            // Get nested data safely
            $accountName = $voucher['account']['account_name'] ?? 'N/A';  // Accessing account_name from the related account
            $branchName = $voucher['branch']['name'] ?? 'N/A';  // Accessing branch name from the related branch

            $fromAccount = $voucher['from_account']['account_name'] ?? 'N/A';



            $exportData[] = [
                'Branch Name' => $branchName,  // First column as Branch Name
                'From Account' => $fromAccount,
                'To Account' => $accountName,      // Account Name column is labeled as "Pay To"
                'Description'  => html_entity_decode($voucher['description']),  // Escape HTML in the description
                'Amount' => number_format($voucher['amount'], 2) ?? 0.0,
            ];

            // Add to total amount
            $totalAmount += $voucher['amount'] ?? 0.0;
        }


        // Add a row for total amount
        $exportData[] = [
            'Branch Name' => '',
            'From Account' => '',
            'To Account' => '',
            'Description' => 'Total',
            'Amount' => number_format($totalAmount, 2),
        ];

        $type = $request->get('type');

        if ($type === 'pdf') {

            $settings = Setting::pluck('value', 'key')->toArray();
            $pdf = Pdf::loadView('journal-vouchers.pdf_list', compact('exportData', 'totalAmount', 'settings'));
            return $pdf->download('journal_vouchers.pdf');
        } elseif ($type === 'xls') {
            // Generate Excel using Laravel Excel
            return Excel::download(new class($exportData) implements FromCollection, WithHeadings {
                protected $data;

                public function __construct($data)
                {
                    $this->data = $data;
                }

                public function collection()
                {
                    return collect($this->data);
                }

                public function headings(): array
                {
                    return ['Branch Name', 'From Account', 'To Account', 'Description', 'Amount'];  // Adjusted headings
                }
            }, 'journal_vouchers.xlsx');
        } else {
            return response()->json(['message' => 'Invalid export type'], 400);
        }
    }
}
