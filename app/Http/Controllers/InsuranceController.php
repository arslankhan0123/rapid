<?php

namespace App\Http\Controllers;

use App\Queries\InsuranceDataTable;
use Illuminate\Http\Request;
use App\Repositories\InsuranceRepository;
use Yajra\DataTables\DataTables;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Exception;
use App\Http\Requests\InsuranceRequest;
use App\Models\Insurance;
use App\Http\Requests\UpdateInsuranceRequest;
use Illuminate\Database\QueryException;
use App\Models\Employee;
use Laracasts\Flash\Flash;
use Throwable;

class InsuranceController extends AppBaseController
{
    /**
     * @var InsuranceRepository
     */
    private $insuranceRepository;
    public function __construct(InsuranceRepository $insuranceRepo)
    {
        $this->insuranceRepository = $insuranceRepo;
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
            return DataTables::of((new InsuranceDataTable())->get($request->only(['group'])))->make(true);
        }
        return view('insurances.index');
    }

    public function create()
    {
        $employees = $this->insuranceRepository->getAllEmployees(); // Retrieves departments as key-value pairs
        $usersBranches = $this->getUsersBranches();
        return view('insurances.create', compact('employees', 'usersBranches'));
    }

    public function get_info(Employee $employee)
    {
        $employee = $employee->load('department', 'subDepartment', 'designation');
        return response()->json($employee);
    }

    public function store(InsuranceRequest $request)
    {

        $input = $request->all();
        try {
            $insurance = $this->insuranceRepository->create($input);
            activity()->causedBy(getLoggedInUser())
                ->performedOn($insurance)
                ->useLog('Commision created.')
                ->log($insurance->insurance . ' Commission Created.');
            Flash::success(__('messages.insurances.saved'));
            return $this->sendResponse($insurance, __('messages.insurances.saved'));
        } catch (Throwable $e) {
            throw $e;
        }
    }
    public function destroy(Insurance $insurance)
    {

        try {
            $insurance->delete();
            activity()->performedOn($insurance)->causedBy(getLoggedInUser())
                ->useLog('Insurance deleted.')->log($insurance->insurance . ' Insurance deleted.');
            return $this->sendSuccess(__('messages.insurances.delete'));
        } catch (QueryException $e) {
            return $this->sendError('Failed To delete!! Already in use.');
        }
    }

    public function edit(Insurance $insurance)
    {
        $employeeData = Employee::with(['department', 'subDepartment', 'designation'])->findOrFail($insurance->employee_id);
        $employees = $this->insuranceRepository->getAllEmployees(); // Retrieves departments as key-value pairs
        $usersBranches = $this->getUsersBranches();
        return view('insurances.edit', compact(['employees', 'insurance', 'employeeData', 'usersBranches']));
    }
    public function update(Insurance $insurance, UpdateInsuranceRequest $updateInsuranceRequest)
    {

        $input = $updateInsuranceRequest->all();
        $updateInsurance = $this->insuranceRepository->update($input, $updateInsuranceRequest->id);
        activity()->performedOn($updateInsurance)->causedBy(getLoggedInUser())
            ->useLog('Insurance Updated')->log($updateInsurance->insurance . ' Insurance updated.');
        Flash::success(__('messages.insurances.saved'));
        return $this->sendSuccess(__('messages.insurances.saved'));
    }

    public function view(Insurance $insurance)
    {
        $insurance->load('employee.department', 'employee.subDepartment', 'employee.designation', 'employee.branch');
        return view('insurances.view', compact(['insurance']));
    }
}
