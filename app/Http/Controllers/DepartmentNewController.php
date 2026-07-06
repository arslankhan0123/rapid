<?php

namespace App\Http\Controllers;

use App\Queries\DepartmentNewDataTable;
use Illuminate\Http\Request;
use App\Repositories\DepartmentNewRepository;
use Yajra\DataTables\DataTables;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Exception;
use App\Http\Requests\DepartmentNewReqeust;
use App\Models\Department;
use App\Http\Requests\UpdateDepartmentNewRequest;
use Illuminate\Database\QueryException;
use Laracasts\Flash\Flash;

class DepartmentNewController extends AppBaseController
{
    /**
     * @var DepartmentNewRepository
     */
    private $departmentRepository;
    public function __construct(DepartmentNewRepository $departmentNewRepo)
    {
        $this->departmentRepository = $departmentNewRepo;
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
            return DataTables::of((new DepartmentNewDataTable())->get($request->only(['group'])))->make(true);
        }
        return view('departments_new.index');
    }

    public function create()
    {
        return view('departments_new.create');
    }

    public function store(DepartmentNewReqeust $request)
    {

        $input = $request->all();
        try {
            $designation = $this->departmentRepository->create($input);
            activity()->causedBy(getLoggedInUser())
                ->performedOn($designation)
                ->useLog('Department created.')
                ->log($designation->name . ' Department Created');
            Flash::success(__('messages.department.department_saved_successfully'));
            return $this->sendResponse($designation, __('messages.department.department_saved_successfully'));
        } catch (Throwable $e) {
            throw $e;
        }
    }
    public function destroy(Department $department)
    {
        try {
            $department->delete();
            activity()->performedOn($department)->causedBy(getLoggedInUser())
                ->useLog('Department deleted.')->log($department->name . ' Department deleted.');
            return $this->sendSuccess(__('messages.department.department_deleted'));
        } catch (QueryException $e) {
            return $this->sendError('Failed To delete!! Already in use.');
        }
    }

    public function edit(Department $department)
    {
        return view('departments_new.edit', compact(['department']));
    }
    public function view(Department $department)
    {
        return view('departments_new.view', compact(['department']));
    }
    public function update(Department $department, UpdateDepartmentNewRequest $updateDepartmentNewRequest)
    {
        $input = $updateDepartmentNewRequest->all();
        $designation = $this->departmentRepository->update($input, $updateDepartmentNewRequest->id);
        activity()->performedOn($designation)->causedBy(getLoggedInUser())
            ->useLog('Department Updated')->log($designation->name . ' Department updated.');
        Flash::success(__('messages.department.department_saved_successfully'));
        return $this->sendSuccess(__('messages.department.department_updated_successfully'));
    }
}
