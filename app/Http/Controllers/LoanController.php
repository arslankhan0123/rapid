<?php

namespace App\Http\Controllers;

use App\Repositories\LoanRepository;

use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Http\Requests\LoanRequest;
use App\Http\Controllers\AppBaseController;
use App\Models\Loan;
use App\Http\Requests\UpdateLoanRequest;
use App\Queries\EmployeeDataTable;
use App\Queries\LoanDataTable;
use Laracasts\Flash\Flash;

class LoanController extends AppBaseController
{

    private $loanRepository;

    public function __construct(LoanRepository $loanRepo)
    {
        $this->loanRepository = $loanRepo;
    }
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return DataTables::of((new LoanDataTable())->get($request->only(['group'])))->make(true);
        }
        return view('loans.index');
    }
    public function create()
    {
        $employees = $this->loanRepository->getEmployee();
        $usersBranches = $this->getUsersBranches();

        return view('loans.create', compact(['employees', 'usersBranches']));
    }
    public function store(LoanRequest $request)
    {

        $input = $request->all();
        try {
            $loan = $this->loanRepository->create($input);
            activity()->causedBy(getLoggedInUser())
                ->performedOn($loan)
                ->useLog('Loan created.')
                ->log($loan->title . ' Loan created.');
            Flash::success(__('messages.loans.saved'));
            return $this->sendResponse($loan, __('messages.loans.saved_loan'));
        } catch (Throwable $e) {
            throw $e;
        }
    }
    public function destroy(Loan $loan)
    {
        $loan->delete();
        activity()->performedOn($loan)->causedBy(getLoggedInUser())
            ->useLog('Loan  deleted.')->log($loan->title . ' loan deleted.');
        return $this->sendSuccess(__('messages.loans.delete_loan'));
    }

    public function edit(Loan $loan)
    {
        $employees = $this->loanRepository->getEmployee();
        $usersBranches = $this->getUsersBranches();
        return view('loans.edit', compact(['loan', 'employees', 'usersBranches']));
    }
    public function view(Loan $loan)
    {
        $loan->load(['employee', 'employee.designation', 'permittedBy','employee.branch']);
        return view('loans.view', compact(['loan']));
    }
    public function update(Loan $loan, UpdateLoanRequest $updateLoanRequest)
    {
        $input = $updateLoanRequest->all();
        $result = $this->loanRepository->update($input, $updateLoanRequest->id);
        activity()->performedOn($result)->causedBy(getLoggedInUser())
            ->useLog('Loan  updated.')->log($result->name . ' Loan updated.');
        Flash::success(__('messages.loans.saved_loan'));
        return $this->sendSuccess(__('messages.loans.saved_loan'));
    }
}
