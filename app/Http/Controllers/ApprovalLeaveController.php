<?php

namespace App\Http\Controllers;

use App\Queries\ApprovalLeavesDataTable;
use App\Repositories\LeaveApplicationRepository;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Http\Requests\LeaveApplicationRequest;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\UpdateLeaveApplicationRequest;
use App\Models\LeaveApplication;
use Laracasts\Flash\Flash;
use Throwable;


class ApprovalLeaveController extends AppBaseController
{
    /**
     * @var LeaveApplicationRepository
     *
     */
    private $leaveApplicationRepository;
    public function __construct(LeaveApplicationRepository $leaveApplicationRepo)
    {
        $this->leaveApplicationRepository = $leaveApplicationRepo;
    }
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return DataTables::of((new ApprovalLeavesDataTable())->get($request->all()))->make(true);
        }
        $usersBranches = $this->getUsersBranches();
        return view('approval-leaves.index', compact('usersBranches'));
    }

    public function create()
    {

        $leaves = $this->leaveApplicationRepository->getleaves();
        $employees = $this->leaveApplicationRepository->getAllEmployees();
        $usersBranches = $this->getUsersBranches();
        return view('approval-leaves.create', compact(['employees', 'leaves', 'usersBranches']));
    }

    public function store(LeaveApplicationRequest $request)
    {

        try {
            $status = $this->leaveApplicationRepository->saveLeaveApplication($request);
            activity()->causedBy(getLoggedInUser())
                ->performedOn($status)
                ->useLog('Leave Appplication created.')
                ->log(' Leave Appplication Created');
            Flash::success(__('messages.leave-applications.saved'));
            return  $this->sendResponse($status, __('messages.leave-applications.saved'));
        } catch (Throwable $e) {
            throw $e;
        }
    }
    public function destroy(LeaveApplication $leaveApplication)
    {
        $imagePath = public_path('/uploads/public/leave_applications/' . $leaveApplication->hard_copy);

        if ($leaveApplication->hard_copy && file_exists($imagePath)) {
            unlink($imagePath);
        }
        $leaveApplication->delete();
        activity()->performedOn($leaveApplication)->causedBy(getLoggedInUser())
            ->useLog('Leave Application  deleted.')->log('Leave Application deleted.');
        return $this->sendSuccess('Asset deleted successfully.');
    }

    public function edit(LeaveApplication $leaveApplication)
    {

        $leaves = $this->leaveApplicationRepository->getleaves();
        $employees = $this->leaveApplicationRepository->getAllEmployees();
        $usersBranches = $this->getUsersBranches();
        return view('approval-leaves.edit', compact(['employees', 'leaves', 'leaveApplication', 'usersBranches']));
    }
    public function view(LeaveApplication $application)
    {
        $leaves = $this->leaveApplicationRepository->getleaves();
        $employees = $this->leaveApplicationRepository->getAllEmployees();
        $totalLeaves = $this->leaveApplicationRepository->getTotalLeaveDays();
        $application->load(['employee', 'leave','approvedBy']);
        return view('approval-leaves.view', compact(['application','leaves','employees','totalLeaves']));
    }

    public function update(LeaveApplication $application)
    {
        $application->update([
            'status' => 1,
            'approved_by' => auth()->id() // Get the logged-in user's ID
        ]);

        activity()->causedBy(getLoggedInUser())
            ->performedOn($application)
            ->useLog('Leave Application Approved.')
            ->log('Leave Application Approved.');

        Flash::success(__('messages.leave-applications.saved'));

        return $this->sendResponse($application, __('messages.approval-leaves.saved'));
    }
}
