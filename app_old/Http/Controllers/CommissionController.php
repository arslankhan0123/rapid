<?php

namespace App\Http\Controllers;

use App\Queries\CommissionDataTable;
use Illuminate\Http\Request;
use App\Repositories\CommissionRepository;
use Yajra\DataTables\DataTables;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Exception;
use App\Http\Requests\CommissionRequest;
use App\Models\Commission;
use App\Http\Requests\UpdateCommissionRequest;
use Illuminate\Database\QueryException;
use App\Models\Employee;

class CommissionController extends AppBaseController
{
    /**
     * @var CommissionRepository
     */
    private $commissionRepository;
    public function __construct(CommissionRepository $commissionRepo)
    {
        $this->commissionRepository = $commissionRepo;
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
            return DataTables::of((new CommissionDataTable())->get($request->only(['group'])))->make(true);
        }
        return view('commissions.index');
    }

    public function create()
    {
        $employees = $this->commissionRepository->getEmplyee(); // Retrieves departments as key-value pairs
        return view('commissions.create', compact('employees'));
    }

    public function get_info(Employee $employee)
    {
        $employee = $employee->load('department', 'subDepartment', 'designation');
        return response()->json($employee);
    }

    public function store(CommissionRequest $request)
    {

        $input = $request->all();
        try {
            $commission = $this->commissionRepository->create($input);
            activity()->causedBy(getLoggedInUser())
                ->performedOn($commission)
                ->useLog('Commision created.')
                ->log($commission->commision . ' Commission Created.');
            return $this->sendResponse($commission, __('messages.commissions.saved'));
        } catch (Throwable $e) {
            throw $e;
        }
    }
    public function destroy(Commission $commission)
    {

        try {
            $commission->delete();
            activity()->performedOn($commission)->causedBy(getLoggedInUser())
                ->useLog('Commission deleted.')->log($commission->commission . '  Commission deleted.');
            return $this->sendSuccess(__('messages.commissions.delete'));
        } catch (QueryException $e) {
            return $this->sendError('Failed To delete!! Already in use.');
        }
    }

    public function edit(Commission $commission)
    {
        $employeeData = Employee::with(['department', 'subDepartment', 'designation'])->findOrFail($commission->employee_id);
        $employees = $this->commissionRepository->getEmplyee();
        return view('commissions.edit', compact(['employees', 'commission', 'employeeData']));
    }
    public function update(Commission $commission, UpdateCommissionRequest $updateCommissionRequest)
    {

        $input = $updateCommissionRequest->all();
        $updateCommission = $this->commissionRepository->update($input, $updateCommissionRequest->id);
        activity()->performedOn($updateCommission)->causedBy(getLoggedInUser())
            ->useLog('Commission Updated')->log($updateCommission->commission . ' Commission updated.');
        return $this->sendSuccess(__('messages.commissions.saved'));
    }

    public function View(Commission $commission)
    {
        $commission->load('employee.department', 'employee.subDepartment', 'employee.designation');
        return view('commissions.view', compact(['commission']));
    }
}
