<?php

namespace App\Http\Controllers;

use App\Queries\CompanyLoanDataTable;
use Illuminate\Http\Request;
use App\Repositories\CompanyLoanRepository;
use Yajra\DataTables\DataTables;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Exception;
use App\Http\Requests\CompanyLoanRequest;
use App\Models\CompanyLoan;
use App\Http\Requests\UpdateCompanyLoanRequest;
use App\Models\Customer;
use App\Models\Supplier;
use Illuminate\Database\QueryException;
use Laracasts\Flash\Flash;
use Throwable;

class CompanyLoanController extends AppBaseController
{
    private $companyLoanRepository;

    public function __construct(CompanyLoanRepository $companyLoanRepo)
    {
        $this->companyLoanRepository = $companyLoanRepo;
    }

    public function index(Request $request)
    {
        $customers = Customer::pluck('company_name', 'id')->toArray();
        $suppliers = Supplier::pluck('company_name', 'id')->toArray();

        if ($request->ajax()) {
            return DataTables::of((new CompanyLoanDataTable())->get($request->only(['type', 'customer_id', 'supplier_id', 'month', 'year'])))->make(true);
        }

        return view('company_loans.index', compact('customers', 'suppliers'));
    }

    // public function index(Request $request)
    // {
    //     if ($request->ajax()) {
    //         return DataTables::of((new CompanyLoanDataTable())->get($request->only(['type', 'month', 'year'])))->make(true);
    //     }
    //     return view('company_loans.index');
    // }
    public function create()
    {
        $customers = $this->companyLoanRepository->getCustomers();
        $suppliers = $this->companyLoanRepository->getSuppliers();

        return view('company_loans.create', compact(['customers', 'suppliers']));
    }


    public function store(CompanyLoanRequest $request)
    {
        $input = $request->all();
        try {
            $companyLoan = $this->companyLoanRepository->create($input);
            activity()->causedBy(getLoggedInUser())
                ->performedOn($companyLoan)
                ->useLog('Company Loan created.')
                ->log($companyLoan->name . ' Company Loan Created');

            Flash::success(__('messages.company_loans.saved'));
            return redirect(route('company-loans.index'));
        } catch (Throwable $e) {
            Flash::error(__('messages.company_loans.error_saving'));
            return redirect()->back()->withInput();
        }
    }

    public function destroy(CompanyLoan $companyLoan)
    {
        try {
            $companyLoan->delete();
            activity()->performedOn($companyLoan)->causedBy(getLoggedInUser())
                ->useLog('Company Loan deleted.')->log($companyLoan->name . ' Company Loan deleted.');
            return $this->sendSuccess(__('messages.company_loans.delete'));
        } catch (QueryException $e) {
            return $this->sendError('Failed To delete!! Already in use.');
        }
    }

    public function edit(CompanyLoan $companyLoan)
    {
        $customers = $this->companyLoanRepository->getCustomers();
        $suppliers = $this->companyLoanRepository->getSuppliers();

        return view('company_loans.edit', compact(['companyLoan', 'customers', 'suppliers']));
    }

    public function show(CompanyLoan $companyLoan)
    {
        $companyLoan->load('customer', 'supplier');
        return view('company_loans.view', compact(['companyLoan']));
    }

    public function update(CompanyLoan $companyLoan, UpdateCompanyLoanRequest $updateCompanyLoanRequest)
    {
        $input = $updateCompanyLoanRequest->all();
        try {
            $companyLoan = $this->companyLoanRepository->update($input, $companyLoan->id);
            activity()->performedOn($companyLoan)->causedBy(getLoggedInUser())
                ->useLog('Company Loan Updated')->log($companyLoan->name . ' Company Loan updated.');

            Flash::success(__('messages.company_loans.saved'));
            return redirect(route('company-loans.index'));
        } catch (Throwable $e) {
            Flash::error(__('messages.company_loans.error_updating'));
            return redirect()->back()->withInput();
        }
    }

    public function getCustomersByType(Request $request)
    {
        $type = $request->get('type');

        if ($type === 'customer') {
            $data = $this->companyLoanRepository->getCustomers();
        } elseif ($type === 'supplier') {
            $data = $this->companyLoanRepository->getSuppliers();
        } else {
            $data = [];
        }

        return response()->json($data);
    }
}
