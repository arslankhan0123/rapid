<?php

namespace App\Http\Controllers;

use App\Queries\LeaveApplicationDataTable;
use App\Repositories\LeaveApplicationRepository;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Http\Requests\LeaveApplicationRequest;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\UpdateLeaveApplicationRequest;
use App\Models\LeaveApplication;
use Laracasts\Flash\Flash;
use Throwable;
use Barryvdh\DomPDF\Facade\Pdf; // Import the PDF facade

class LeaveApplicationController extends AppBaseController
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
            return DataTables::of((new LeaveApplicationDataTable())->get($request->all()))->make(true);
        }
        $usersBranches = $this->getUsersBranches();
        return view('leave_applications.index', compact('usersBranches'));
    }

    public function create()
    {

        $leaves = $this->leaveApplicationRepository->getleaves();
        $employees = $this->leaveApplicationRepository->getAllEmployees();
        $usersBranches = $this->getUsersBranches();
        $totalLeaves = $this->leaveApplicationRepository->getTotalLeaveDays();

        return view('leave_applications.create', compact(['employees', 'leaves', 'usersBranches', 'totalLeaves']));
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
            return $this->sendResponse($status, __('messages.leave-applications.saved'));
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
        $totalLeaves = $this->leaveApplicationRepository->getTotalLeaveDays();
        return view('leave_applications.edit', compact(['employees', 'leaves', 'leaveApplication', 'usersBranches', 'totalLeaves']));
    }
    public function view(LeaveApplication $leaveApplication)
    {
        $leaveApplication->load(['employee', 'leave']);
        return view('leave_applications.view', compact(['leaveApplication']));
    }

    public function update(LeaveApplication $leaveApplication, UpdateLeaveApplicationRequest $updateLeaveApplicationRequest)
    {



        $updateStatus = $this->leaveApplicationRepository->updateLeaveApplication($updateLeaveApplicationRequest, $leaveApplication->id);
        activity()->causedBy(getLoggedInUser())
            ->performedOn($leaveApplication)
            ->useLog('Leave Application updated.')
            ->log(' Leave Application Updated.');
        Flash::success(__('messages.leave-applications.saved'));
        return $this->sendResponse($leaveApplication, __('messages.leave_applications.saved'));
    }
    public function updateAnnual(LeaveApplication $leaveApplication, UpdateLeaveApplicationRequest $updateLeaveApplicationRequest)
    {




        $updateStatus = $this->leaveApplicationRepository->updateLeaveApplicationAnnual($updateLeaveApplicationRequest, $leaveApplication->id);
        activity()->causedBy(getLoggedInUser())
            ->performedOn($leaveApplication)
            ->useLog('Leave Application updated.')
            ->log(' Leave Application Updated.');
        Flash::success(__('messages.leave-applications.saved'));
        return $this->sendResponse($leaveApplication, __('messages.leave_applications.saved'));
    }

    public function downloadPDF(LeaveApplication $leaveApplication)
    {

        // Retrieve data using the repository
        $leaveApplication->load(['employee', 'leave', 'branch', 'approvedBy']);
        $pdf = Pdf::loadView('leave_applications.pdf', compact('leaveApplication'));
        $name = $leaveApplication->employee->name ?? 'Unknown';
        $filename = 'Leave-application_' . str_replace(' ', '_', $name) . '.pdf';
        return $pdf->stream($filename);
    }
}
