<?php

namespace App\Http\Controllers;

use App\Queries\CashTransferDataTable;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Exception;
use App\Http\Requests\CashTransferRequest;
use App\Models\Leave;
use App\Http\Requests\UpdateCashTransferRequest;
use App\Repositories\CashTransferRepository;
use App\Repositories\OverTimeRepository;
use Illuminate\Database\QueryException;
use Laracasts\Flash\Flash;
use App\Models\Account;
use Throwable;
use App\Models\DocumentNextNumber;
use App\Models\CashTransfer;

class CashTransferController extends AppBaseController
{
    /**
     * @var CashTransferRepository;
     */
    private $cashTransferRepository;
    public function __construct(CashTransferRepository $cashTransferRepo)
    {
        $this->cashTransferRepository = $cashTransferRepo;
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
            return DataTables::of((new CashTransferDataTable())->get($request->all()))->make(true);
        }
        $usersBranches = $this->getUsersBranches();
        return view('cash-transfers.index', compact('usersBranches'));
    }

    public function create()
    {
        $accounts = $this->cashTransferRepository->getAccounts();

        $nextNumber = DocumentNextNumber::getNextNumber('account_transfer');
        $usersBranches = $this->getUsersBranches();
        return view('cash-transfers.create', compact('accounts', 'nextNumber', 'usersBranches'));
    }

    public function store(CashTransferRequest $request)
    {
        $input = $request->all();
        try {
            $designation = $this->cashTransferRepository->create($input);
            DocumentNextNumber::updateNumber('account_transfer');
            activity()->causedBy(getLoggedInUser())
                ->performedOn($designation)
                ->useLog('Cash Transfer created.')
                ->log($designation->transfer_id);
            Flash::success(__('messages.cash-transfers.saved'));
            return $this->sendResponse($designation, __('messages.cash-transfers.saved'));
        } catch (Throwable $e) {
            throw $e;
        }
    }

    public function destroy(CashTransfer $transfer)
    {

        try {
            $transfer->delete();
            activity()->performedOn($transfer)->causedBy(getLoggedInUser())
                ->useLog('Account deleted.')->log($transfer->transfer_id . ' deleted.');
            return $this->sendSuccess(__('messages.cash-transfers.delete'));
        } catch (QueryException $e) {
            return $this->sendError('Failed To delete!! Already in use.');
        }
    }

    public function edit(CashTransfer $transfer)
    {

        $accounts = $this->cashTransferRepository->getAccounts();
        $usersBranches = $this->getUsersBranches();
        return view('cash-transfers.edit', compact(['transfer', 'accounts', 'usersBranches']));
    }
    public function update(CashTransfer $transfer, UpdateCashTransferRequest $updateCashTransferRequest)
    {
        $input = $updateCashTransferRequest->all();
        $designation = $this->cashTransferRepository->updateCash($input, $updateCashTransferRequest->id);
        activity()->performedOn($designation)->causedBy(getLoggedInUser())
            ->useLog('Cash Transfer Updated')->log($designation->transfer_id . ' updated.');
        Flash::success(__('messages.cash-transfers.saved'));
        return $this->sendSuccess(__('messages.cash-transfers.saved'));
    }

    public function view(CashTransfer $transfer)
    {
        $transfer->load('fromAccount', 'toAccount','branch');
        return view('cash-transfers.view', compact(['transfer']));
    }
}
