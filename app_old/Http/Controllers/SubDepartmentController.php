<?php

namespace App\Http\Controllers;

use App\Queries\SubDepartmentDataTable;
use Illuminate\Http\Request;
use App\Repositories\SubDepartmentRepository;
use Yajra\DataTables\DataTables;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Exception;
use App\Http\Requests\SubDepartmentRequest;
use App\Models\SubDepartment;
use App\Http\Requests\UpdateSubDepartmentRequest;
use Illuminate\Database\QueryException;
use App\Models\Department;
use Laracasts\Flash\Flash;

class SubDepartmentController extends AppBaseController
{
    /**
     * @var SubDepartmentRepository
     */
    private $subDepartmentRepository;
    public function __construct(SubDepartmentRepository $departmentNewRepo)
    {
        $this->subDepartmentRepository = $departmentNewRepo;
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
            return DataTables::of((new SubDepartmentDataTable())->get($request->only(['group'])))->make(true);

        }
        return view('sub_departments.index');
    }

    public function create()
    {
        $departments =$this->subDepartmentRepository->getDepartments();// Retrieves departments as key-value pairs
        return view('sub_departments.create',compact('departments'));
    }

    public function store(SubDepartmentRequest $request)
    {

        $input = $request->all();
        try {
            $designation = $this->subDepartmentRepository->create($input);
            activity()->causedBy(getLoggedInUser())
                ->performedOn($designation)
                ->useLog('Sub Department created.')
                ->log($designation->title . ' Designtion.');
            Flash::success(__('messages.department.Sub_department_updated_successfully'));
            return $this->sendResponse($designation, __('messages.department.Sub_department_updated_successfully'));
        } catch (Throwable $e) {
            throw $e;
        }
    }
    public function destroy(SubDepartment $subDepartment)
    {

        try {
            $subDepartment->delete();
            activity()->performedOn($subDepartment)->causedBy(getLoggedInUser())
                ->useLog('Sub Department deleted.')->log($subDepartment->name . 'Sub Department deleted.');
            return $this->sendSuccess(__('messages.department.sub_departments'));
        } catch (QueryException $e) {
            return $this->sendError('Failed To delete!! Already in use.');
        }
    }
    public function view(SubDepartment $subDepartment)
    {
        $subDepartment->load('department');
        return view('sub_departments.view', compact(['subDepartment']));
    }

    public function edit(SubDepartment $subDepartment)
    {
        $departments = $this->subDepartmentRepository->getDepartments();
        return view('sub_departments.edit', compact(['subDepartment','departments']));
    }
    public function update(SubDepartment $subDepartment, UpdateSubDepartmentRequest $updateSubDepartmentRequest)
    {
        $input = $updateSubDepartmentRequest->all();
        $subDepartment = $this->subDepartmentRepository->update($input, $updateSubDepartmentRequest->id);
        activity()->performedOn($subDepartment)->causedBy(getLoggedInUser())
            ->useLog('Sub Department Updated')->log($subDepartment->name . 'Sub Department updated.');
        Flash::success(__('messages.department.Sub_department_updated_successfully'));
        return $this->sendSuccess(__('messages.department.Sub_department_updated_successfully'));
    }
}
