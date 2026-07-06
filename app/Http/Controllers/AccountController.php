<?php

namespace App\Http\Controllers;

use App\Queries\AccountDataTable;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Exception;
use App\Http\Requests\AccountRequest;
use App\Models\Leave;
use App\Http\Requests\UpdateAccountRequest;
use App\Repositories\AccountRepository;
use App\Repositories\OverTimeRepository;
use Illuminate\Database\QueryException;
use Laracasts\Flash\Flash;
use App\Models\Account;
use App\Models\CashTransfer;
use App\Models\Expense;
use App\Models\DocumentNextNumber;

class AccountController extends AppBaseController
{
    /**
     * @var AccountRepository;
     */
    private $accountRepository;
    public function __construct(AccountRepository $accountRepoy)
    {
        $this->accountRepository = $accountRepoy;
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
            return DataTables::of((new AccountDataTable())->get($request->all()))->make(true);
        }
        $usersBranches = $this->getUsersBranches();

        $accounts = $this->accountRepository->getAccounts();
        $nextNumber = DocumentNextNumber::getNextNumber('account_transfer');

        $usersBranches = $this->getUsersBranches();
        return view('accounts.index', compact('usersBranches', 'accounts', 'nextNumber'));
    }

    public function create()
    {
        $usersBranches = $this->getUsersBranches();
        $employees = $this->accountRepository->getEmployees();
        $accountNumbers = $this->accountRepository->getAccountNumbers();


        return view('accounts.create', compact('usersBranches', 'employees', 'accountNumbers'));
    }

    public function getCardsInfo($id = null)
    {
        return true;
        // skipping it now
        // Initialize CashTransfer query
        $cashTransferQuery = CashTransfer::query()->with('fromAccount', 'toAccount');

        // Check if branch_id is provided and filter based on the related 'fromAccount' and 'toAccount' relationships
        $cashTransferQuery->whereHas('fromAccount', function ($query) use ($id) {
            if ($id) {
                $query->where('branch_id', $id);  // Filter by branch_id for 'fromAccount'
            }
        })
            ->orWhereHas('toAccount', function ($query) use ($id) {
                if ($id) {
                    $query->where('branch_id', $id);  // Filter by branch_id for 'toAccount'
                }
            });
        // Fetch CashTransfer records
        $cashTransfers = $cashTransferQuery->get();
        // Debugging the fetched CashTransfers

    }






    public function store(AccountRequest $request)
    {
        $input = $request->all();
        if (isset($input['opening_balance'])) {
            $input['current_balance'] = $input['opening_balance'];
        }
        try {
            $designation = $this->accountRepository->create($input);
            activity()->causedBy(getLoggedInUser())
                ->performedOn($designation)
                ->useLog('Leave created.')
                ->log($designation->account_name);
            Flash::success(__('messages.accounts.saved'));
            return $this->sendResponse($designation, __('messages.accounts.saved'));
        } catch (Throwable $e) {
            throw $e;
        }
    }



    public function destroy(Account $account)
    {

        try {
            $account->delete();
            activity()->performedOn($account)->causedBy(getLoggedInUser())
                ->useLog('Account deleted.')->log($account->account_name . ' deleted.');
            return $this->sendSuccess(__('messages.accounts.delete'));
        } catch (QueryException $e) {
            return $this->sendError('Failed To delete!! Already in use.');
        }
    }

    public function edit(Account $account)
    {

        $usersBranches = $this->getUsersBranches();
        $employees = $this->accountRepository->getEmployees();
        $accountNumbers = $this->accountRepository->getAccountNumbers();
        return view('accounts.edit', compact(['account', 'usersBranches', 'employees', 'accountNumbers']));
    }
    public function update(Account $account, UpdateAccountRequest $updateAccountRequest)
    {
        $input = $updateAccountRequest->all();
        if (isset($input['opening_balance'])) {
            $input['current_balance'] = $input['opening_balance'];
        }
        $designation = $this->accountRepository->update($input, $updateAccountRequest->id);
        activity()->performedOn($designation)->causedBy(getLoggedInUser())
            ->useLog('Account Updated')->log($designation->account_name . ' updated.');
        Flash::success(__('messages.accounts.saved'));
        return $this->sendSuccess(__('messages.accounts.saved'));
    }

    public function view(Account $account)
    {
        $account->load('branch');
        return view('accounts.view', compact(['account']));
    }

    public function transferCash(Request $request)
    {

        return  $this->accountRepository->transferCash($request->all());
    }
    public function payCash(Request $request)
    {


     return  $this->accountRepository->payCash($request->all());
    }
    public function updateCash(Request $request)
    {

        return  $this->accountRepository->updateCash($request->all());
    }
}
