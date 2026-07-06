<?php

namespace App\Http\Controllers;

use App\Queries\DesignationDataTable;
use Illuminate\Http\Request;
use App\Repositories\DesignationRepository;
use Yajra\DataTables\DataTables;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Exception;
use App\Http\Requests\DesignationRequest;
use App\Models\Designation;
use App\Http\Requests\UpdateDesignationRequest;
use Illuminate\Database\QueryException;
use Laracasts\Flash\Flash;

class DesignationController extends AppBaseController
{
    /**
     * @var DesignationRepository
     */
    private $designationRepository;
    public function __construct(DesignationRepository $designationRepo)
    {
        $this->designationRepository = $designationRepo;
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
            return DataTables::of((new DesignationDataTable())->get($request->only(['group'])))->make(true);
        }
        return view('designations.index');
    }

    public function create()
    {

        $departments = $this->designationRepository->getDepartment();
        $subDepartments = $this->designationRepository->getSubDepartment();
        return view('designations.create', compact(['departments', 'subDepartments']));
    }

    public function store(DesignationRequest $request)
    {

        $input = $request->all();
        try {
            $designation = $this->designationRepository->create($input);
            activity()->causedBy(getLoggedInUser())
                ->performedOn($designation)
                ->useLog('Designation created.')
                ->log($designation->name . ' Designtion created');
            Flash::success(__('messages.designations.saved_designation'));
            return $this->sendResponse($designation, __('messages.designations.saved_designation'));
        } catch (Throwable $e) {
            throw $e;
        }
    }
    public function destroy(Designation $designation)
    {
        try {
            $designation->delete();
            activity()->performedOn($designation)->causedBy(getLoggedInUser())
                ->useLog('Designation deleted.')->log($designation->name . ' Designation deleted.');
            return $this->sendSuccess('Designation deleted successfully.');
        } catch (QueryException $e) {
            return $this->sendError('Failed To delete!! Already in use.');
        }
    }

    public function edit(Designation $designation)
    {
        $departments = $this->designationRepository->getDepartment();
        $subDepartments = $this->designationRepository->getSubDepartment();
        return view('designations.edit', compact(['designation', 'departments', 'subDepartments']));
    }
    public function view(Designation $designation)
    {
        $designation->load(['department', 'subDepartment']);
        return view('designations.view', compact(['designation']));
    }
    public function update(Designation $designation, UpdateDesignationRequest $updateDesignationRequest)
    {
        $input = $updateDesignationRequest->all();
        $designation = $this->designationRepository->update($input, $updateDesignationRequest->id);
        activity()->performedOn($designation)->causedBy(getLoggedInUser())
            ->useLog('Designation Updated')->log($designation->name . ' Designation updated.');
        Flash::success(__('messages.designations.saved_designation'));
        return $this->sendSuccess(__('messages.designations.saved_designation'));
    }
}
