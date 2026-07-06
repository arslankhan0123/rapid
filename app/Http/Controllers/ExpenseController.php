<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateExpenseRequest;
use App\Http\Requests\UpdateExpenseRequest;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Repositories\ExpenseRepository;
use Exception;
use File;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Laracasts\Flash\Flash;
use Barryvdh\DomPDF\Facade\Pdf; // Import the PDF facade
use Money\Currency;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Queries\ExpenseDataTable;
use App\Models\DocumentNextNumber;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings; // Update with the correct model if necessary
use DB;
use Carbon\Carbon;
use App\Models\Setting;
use App\Models\Account;


class ExpenseController extends AppBaseController
{
    /** @var ExpenseRepository */
    private $expenseRepository;

    public function __construct(ExpenseRepository $expenseRepo)
    {
        $this->expenseRepository = $expenseRepo;
    }

    /**
     * Display a listing of the Expense.
     *
     * @return Application|Factory|View
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return DataTables::of((new ExpenseDataTable())->get($request->all()))->make(true);
        }
        $expenseCategory = ExpenseCategory::orderBy('name', 'asc')->pluck('name', 'id')->toArray();
        $usersBranches = $this->getUsersBranches();
        $expenseSubCategory = $this->expenseRepository->getSubCategories();
        $accounts = $this->expenseRepository->getAccounts();
        return view('expenses.index', compact('expenseCategory', 'usersBranches', 'expenseSubCategory', 'accounts'));
    }

    /**
     * Show the form for creating a new Expense.
     *
     * @param  null  $customerId
     * @return Application|Factory|View
     */
    public function create($customerId = null)
    {
        $data = $this->expenseRepository->getSyncList();
        // dd($data);
        $subCategories = $this->expenseRepository->getSubCategories();

        $currencies = $this->expenseRepository->getCurrencies();
        $usersBranches = $this->getUsersBranches();
        $nextNumber = DocumentNextNumber::getNextNumber('expense');
        $employees = $this->expenseRepository->getEmployees();
        $accounts = $this->expenseRepository->getAccounts();
        $suppliers = $this->expenseRepository->getSuppliers();
        return view('expenses.create', compact('data', 'suppliers', 'customerId', 'currencies', 'usersBranches', 'nextNumber', 'employees', 'subCategories', 'accounts'));
    }

    /**
     * Store a newly created Expense in storage.
     *
     * @param  CreateExpenseRequest  $request
     * @return RedirectResponse|Redirector
     */
    public function store(CreateExpenseRequest $request)
    {
        $input = $request->all();

        if (isset($input['isTaxable'])) {
            $input['isTaxable'] = 1;
        }

        $input['amount'] = removeCommaFromNumbers($input['amount']);
        $this->expenseRepository->create($input);
        DocumentNextNumber::updateNumber('expense');
        Flash::success(__('messages.expense.expense_saved_successfully'));

        return redirect(route('expenses.index'));
    }

    /**
     * Display the specified Expense.
     *
     * @param  Expense  $expense
     * @return Application|Factory|View
     */
    public function show(Expense $expense)
    {
        $expense->load(['paymentMode', 'currencyNew', 'expenseSubCategory']);

        $data = $this->expenseRepository->getReminderData($expense->id, Expense::class);
        $comments = $this->expenseRepository->getCommentData($expense);
        $notes = $this->expenseRepository->getNotesData($expense);
        $groupName = (request('group') == null) ? 'expense_details' : (request('group'));
        return view(
            "expenses.views.$groupName",
            compact('expense', 'data', 'comments', 'notes', 'groupName')
        );
    }

    public function export(Request $request)
    {
        // Fetch data using DataTables
        $expenses = DataTables::of((new ExpenseDataTable())->get($request->all()))->make(true);

        // Extract the data for processing
        $expenses = $expenses->original['data'] ?? [];


        // Prepare data for export
        $exportData = [];
        $totalAmount = 0;

        foreach ($expenses as $expense) {
            // Get nested data safely
            $formattedDate = isset($expense['expense_date']) ? Carbon::parse($expense['expense_date'])->format('d-m-Y') : 'N/A';
            $categoryName = $expense['expense_category']['name'] ?? 'N/A';
            $paymentModeName = isset($expense['payment_mode']) ? $expense['payment_mode']['account_name'] : 'N/A';
            $branchName = $expense['branch']['name'] ?? 'N/A';
            $customerName = isset($expense['customer']['name']) ? $expense['customer']['name'] : 'N/A';
            $subCategoryName = isset($expense['expense_sub_category']['name']) ? $expense['expense_sub_category']['name'] : 'N/A';

            $exportData[] = [
                'Expense Number' => $expense['expense_number'] ?? 'N/A',
                'Name' => $expense['name'] ?? 'N/A',
                'Branch Name' => $branchName,
                'Category' => $categoryName,
                'Sub Category' => $subCategoryName,
                'Payment Mode' => $paymentModeName,
                'Expense Date' => $formattedDate,
                'Customer' => $customerName,
                'Supplier' => ($expense['supplier']['company_name'] ?? ''),
                'Vat Number' => ($expense['supp_vat_number'] ?? ''),
                'Amount' => number_format($expense['amount'], 2) ?? 0.0,
            ];

            // Add to total amount
            $totalAmount += $expense['amount'] ?? 0.0;
        }

        // Add a row for total amount
        $exportData[] = [
            'Expense Number' => '',
            'Name' => '',
            'Branch Name' => '',
            'Category' => '',
            'Sub Category' => '',
            'Payment Mode' => '',
            'Expense Date' => '',
            '',
            '',
            'Customer' => 'Total',
            'Amount' => number_format($totalAmount, 2),
        ];

        // Check export type
        $type = $request->get('type');

        if ($type === 'pdf') {
            $settings = Setting::pluck('value', 'key')->toArray();
            $pdf = Pdf::loadView('expenses.pdf_list', compact('exportData', 'totalAmount', 'settings'));
            return $pdf->download('expenses.pdf');
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
                    return ['Expense Number', 'Name', 'Branch Name', 'Category', 'Sub Category', 'Payment Mode', 'Expense Date', 'Customer', 'Supplier', 'Vat Number', 'Amount'];
                }
            }, 'expenses.xlsx');
        } else {
            return response()->json(['message' => 'Invalid export type'], 400);
        }
    }



    public function downloadPDF(Expense $expense)
    {

        // Retrieve data using the repository
        $expense->load(['paymentMode', 'currencyNew', 'expenseSubCategory', 'supplier']);
        $data = $this->expenseRepository->getReminderData($expense->id, Expense::class);
        $comments = $this->expenseRepository->getCommentData($expense);
        $notes = $this->expenseRepository->getNotesData($expense);
        $groupName = (request('group') == null) ? 'expense_details' : request('group');

        // Load the view with the data
        $pdf = Pdf::loadView('expenses.views.pdf', compact('expense', 'data', 'comments', 'notes', 'groupName'));
        // Return the PDF download response
        return $pdf->download('payment_voucher_' . $expense->expense_number . '.pdf');
    }

    /**
     * Show the form for editing the specified Expense.
     *
     * @param  Expense  $expense
     * @return Application|Factory|View
     */
    public function edit(Expense $expense)
    {
        $expense->load('branch');
        $data = $this->expenseRepository->getSyncList();
        $currencies = $this->expenseRepository->getCurrencies();
        $usersBranches = $this->getUsersBranches();
        $employees = $this->expenseRepository->getEmployees();
        $subCategories = $this->expenseRepository->getSubCategories();
        $accounts = $this->expenseRepository->getAccounts();
        $suppliers = $this->expenseRepository->getSuppliers();
        return view('expenses.edit', compact('expense', 'suppliers', 'data', 'currencies', 'usersBranches', 'employees', 'subCategories', 'accounts'));
    }

    /**
     * Update the specified Expense in storage.
     *
     * @param  Expense  $expense
     * @param  UpdateExpenseRequest  $request
     * @return RedirectResponse|Redirector
     */
    public function update(Expense $expense, UpdateExpenseRequest $request)
    {
        $input = $request->all();
        if (isset($input['isTaxable'])) {
            $input['isTaxable'] = 1;
        } else {
            $input['isTaxable'] = 0;
        }

        $input['amount'] = removeCommaFromNumbers($input['amount']);
        $this->expenseRepository->update($input, $expense);

        Flash::success(__('messages.expense.expense_updated_successfully'));

        return redirect(route('expenses.index'));
    }

    /**
     * Remove the specified Expense from storage.
     *
     * @param  Expense  $expense
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function destroy(Expense $expense)
    {
        activity()->performedOn($expense)->causedBy(getLoggedInUser())
            ->useLog('Expense deleted.')->log($expense->name . ' Expense deleted.');


        $totalAmount = $expense->amount;
        $branchId = $expense->branch_id;

        $account = Account::where('id', $expense->payment_mode_id)
            ->where('branch_id', $branchId)
            ->first();
        if ($account) {
            $account->opening_balance += $totalAmount;
            $account->save();
        }


        $documents = DB::table('expense_docs')
            ->where('expense_id', $expense->id)
            ->get();

        // Delete related documents and files from public storage
        foreach ($documents as $document) {
            // Construct the full file path
            $filePath = public_path("uploads/public/expense_docs/" . $document->file);
            // Delete file from public folder if it exists
            if (File::exists($filePath)) {
                File::delete($filePath);
            }
            // Delete document record from the database
            DB::table('expense_docs')
                ->where('id', $document->id)
                ->delete();
        }

        $expense->delete();

        return $this->sendSuccess('Expense deleted successfully.');
    }

    /**
     * @param  Expense  $expense
     * @return Application|ResponseFactory|Response
     *
     * @throws FileNotFoundException
     */
    public function downloadMedia(Expense $expense)
    {
        $attachmentMedia = $expense->media[0];
        $attachmentPath = $attachmentMedia->getPath();

        if (config('app.media_disc') == 'public') {
            $attachmentPath = (Str::after($attachmentMedia->getUrl(), '/uploads'));
        }

        $file = Storage::disk(config('app.media_disc'))->get($attachmentPath);

        $headers = [
            'Content-Type' => $expense->media[0]->mime_type,
            'Content-Description' => 'File Transfer',
            'Content-Disposition' => "attachment; filename={$expense->media[0]->file_name}",
            'filename' => $expense->media[0]->file_name,
        ];

        return response($file, 200, $headers);
    }

    /**
     * @param  Expense  $expense
     * @return mixed
     */
    public function getCommentsCount(Expense $expense)
    {
        return $this->sendResponse($expense->comments()->count(), 'Comments count retrieved successfully.');
    }

    /**
     * @param  Expense  $expense
     * @return mixed
     */
    public function getNotesCount(Expense $expense)
    {
        return $this->sendResponse($expense->notes()->count(), 'Comments count retrieved successfully.');
    }

    /**
     * @param  Media  $mediaItem
     * @return Media
     */
    public function download(Media $mediaItem)
    {
        return $mediaItem;
    }

    /**
     * @return Application|Factory
     */
    public function expenseCategoryByChart()
    {
        $expenseCategories = ExpenseCategory::withCount('expenses')->get();

        return view('expenses.expense_category_by_chart', compact('expenseCategories'));
    }


    function file_delete($id)
    {

        $employee = $this->expenseRepository->delete_file($id);
        activity()->performedOn($employee)->causedBy(getLoggedInUser())
            ->useLog('Expense File Deleted')->log(' Expense Invoice  File deleted.');
        return $this->sendSuccess(__('messages.employees.delete_file'));
    }
}
