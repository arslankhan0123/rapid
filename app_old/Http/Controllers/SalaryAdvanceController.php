<?php

namespace App\Http\Controllers;

use App\Queries\SalaryAdvanceDataTable;
use Illuminate\Http\Request;
use App\Repositories\SalaryAdvanceRepository;
use Yajra\DataTables\DataTables;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Exception;
use App\Http\Requests\SalaryAdvanceRequest;
use App\Models\SalaryAdvance;
use App\Http\Requests\UpdateSalaryAdvanceRequest;
use Illuminate\Database\QueryException;
use Laracasts\Flash\Flash;
use Throwable;
use App\Models\Bank;
use App\Models\Account;
use Illuminate\Support\Facades\DB;

class SalaryAdvanceController extends AppBaseController
{
    /**
     * @var SalaryAdvanceRepository
     */
    private $salaryAdvanceRepository;
    public function __construct(SalaryAdvanceRepository $salaryAdvanceRepo)
    {
        $this->salaryAdvanceRepository = $salaryAdvanceRepo;
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
            return DataTables::of((new SalaryAdvanceDataTable())->get($request->all()))->make(true);
        }
        $usersBranches = $this->getUsersBranches();
        $accounts = $this->salaryAdvanceRepository->getAccounts();
        return view('salary_advances.index', compact('usersBranches', 'accounts'));
    }

    public function create()
    {
        $employees = $this->salaryAdvanceRepository->getEmployee(); // Retrieves departments as key-value pairs
        $usersBranches = $this->getUsersBranches();
        $accounts = $this->salaryAdvanceRepository->getAccounts();
        return view('salary_advances.create', compact('employees', 'usersBranches', 'accounts'));
    }

    public function store(SalaryAdvanceRequest $request)
    {

        $input = $request->all();
        // dd($input);
        try {
            $salaryAdvance = $this->salaryAdvanceRepository->create($input);

            $salaryAdvance->load('employee');

            activity()->causedBy(getLoggedInUser())
                ->performedOn($salaryAdvance)
                ->useLog('Salary Advance created.')
                ->log($salaryAdvance->employee->name . ' Salary Advance Created.');
            Flash::success(__('messages.salary_advances.saved'));
            return $this->sendResponse($salaryAdvance->employee->name, __('messages.salary_advances.saved'));
        } catch (Throwable $e) {
            throw $e;
        }
    }
    public function destroy(SalaryAdvance $salaryAdvance)
    {

        try {
            $salaryAdvance->delete();
            $salaryAdvance->load('employee');



            $totalAmount = $salaryAdvance->amount;
            $branchId = $salaryAdvance->branch_id;

            $account = Account::where('id', $salaryAdvance->account_id)
                ->where('branch_id', $branchId)
                ->first();
            if ($account) {
                $account->opening_balance += $totalAmount;
                $account->save();
            }


            activity()->performedOn($salaryAdvance)->causedBy(getLoggedInUser())
                ->useLog('Salary deleted.')->log($salaryAdvance->employee->name . '  Salary Advance deleted.');
            return $this->sendSuccess(__('messages.salary_advances.delete'));
        } catch (QueryException $e) {
            return $this->sendError('Failed To delete!! Already in use.');
        }
    }

    public function edit(SalaryAdvance $salaryAdvance)
    {
        $employees = $this->salaryAdvanceRepository->getEmployee();
        $usersBranches = $this->getUsersBranches();
        $accounts = $this->salaryAdvanceRepository->getAccounts();
        return view('salary_advances.edit', compact(['employees', 'salaryAdvance', 'usersBranches', 'accounts']));
    }
    public function view(SalaryAdvance $salaryAdvance)
    {
        $salaryAdvance->load('employee', 'employee.designation');
        return view('salary_advances.view', compact(['salaryAdvance']));
    }
    // public function update(SalaryAdvance $salaryAdvance, UpdateSalaryAdvanceRequest $updateSalaryAdvanceRequest)
    // {

    //     $salaryAdvance->load('employee');

    //     $input = $updateSalaryAdvanceRequest->all();
    //     $input['status'] = $input['status'] ?? 0;



    //     $oldAmount = $salaryAdvance->amount;
    //     $newAmount = formatNumber($input['amount']);
    //     $branchId = $input['branch_id'];
    //     $account = Account::where('id', $input['payment_mode_id'])
    //         ->where('branch_id', $input['account_id'])
    //         ->first();

    //     if ($account) {
    //         $account->opening_balance = ($account->opening_balance + $oldAmount) - $newAmount;
    //         $account->save();
    //     }


    //     $updateSalary = $this->salaryAdvanceRepository->update($input, $updateSalaryAdvanceRequest->id);
    //     activity()->performedOn($updateSalary)->causedBy(getLoggedInUser())
    //         ->useLog('Salary Advance Updated')->log($updateSalary->employee->name . ' Salary Advance updated.');
    //     Flash::success(__('messages.salary_advances.saved'));
    //     return $this->sendSuccess(__('messages.salary_advances.saved'));
    // }


    public function update(SalaryAdvance $salaryAdvance, UpdateSalaryAdvanceRequest $updateSalaryAdvanceRequest)
    {
        DB::beginTransaction();

        try {
            $salaryAdvance->load('employee');

            $input = $updateSalaryAdvanceRequest->all();
            $input['status'] = 1;

            $oldAmount = $salaryAdvance->amount;
            $newAmount = formatNumber($input['amount']);
            $branchId = $input['branch_id'];

            $account = Account::where('id', $input['account_id'])
                ->where('branch_id', $branchId)
                ->first();

            if ($account) {
                $account->opening_balance = ($account->opening_balance + $oldAmount) - $newAmount;
                $account->save();
            }
            $updateSalary = $this->salaryAdvanceRepository->update($input, $updateSalaryAdvanceRequest->id);
            activity()
                ->performedOn($updateSalary)
                ->causedBy(getLoggedInUser())
                ->useLog('Salary Advance Updated')
                ->log($updateSalary->employee->name . ' Salary Advance updated.');
            DB::commit();
            Flash::success(__('messages.salary_advances.saved'));
            return $this->sendSuccess(__('messages.salary_advances.saved'));
        } catch (Exception $e) {
            DB::rollBack();
            // Optional: log error or handle it appropriately
            throw $e;
        }
    }
}
