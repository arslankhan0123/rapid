<?php

namespace App\Http\Controllers;

use App\Queries\LeaveDataTable;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Exception;
use App\Http\Requests\LeaveRequest;
use App\Models\Leave;
use App\Http\Requests\UpdateLeaveRequest;
use App\Repositories\LeaveRepository;
use App\Repositories\OverTimeRepository;
use Illuminate\Database\QueryException;
use Laracasts\Flash\Flash;

class LeaveController extends AppBaseController
{
    /**
     * @var LeaveRepository;
     */
    private $leaveRepository;
    public function __construct(LeaveRepository $leaveRepo)
    {
        $this->leaveRepository = $leaveRepo;
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
            return DataTables::of((new LeaveDataTable())->get($request->only(['group'])))->make(true);
        }
        return view('leaves.index');
    }

    public function create()
    {
        return view('leaves.create');
    }

    public function store(LeaveRequest $request)
    {
        $input = $request->all();
        try {
            $designation = $this->leaveRepository->create($input);
            activity()->causedBy(getLoggedInUser())
                ->performedOn($designation)
                ->useLog('Leave created.')
                ->log($designation->name);
            Flash::success(__('messages.leaves.saved'));
            return $this->sendResponse($designation, __('messages.leaves.saved'));
        } catch (Throwable $e) {
            throw $e;
        }
    }

    public function destroy(Leave $leave)
    {

        try {
            $leave->delete();
            activity()->performedOn($leave)->causedBy(getLoggedInUser())
                ->useLog('Leave deleted.')->log($leave->name . 'leave deleted.');
            return $this->sendSuccess(__('messages.leaves.delete'));
        } catch (QueryException $e) {
            return $this->sendError('Failed To delete!! Already in use.');
        }
    }

    public function edit(Leave $leave)
    {

        return view('leaves.edit', compact(['leave']));
    }
    public function update(Leave $leave, UpdateLeaveRequest $updateLeaveRequest)
    {
        $input = $updateLeaveRequest->all();
        $designation = $this->leaveRepository->update($input, $updateLeaveRequest->id);
        activity()->performedOn($designation)->causedBy(getLoggedInUser())
            ->useLog('Leave Updated')->log($designation->name . 'Leave updated.');
        Flash::success(__('messages.leaves.saved'));
        return $this->sendSuccess(__('messages.leaves.saved'));
    }

    public function view(Leave $leave)
    {

        return view('leaves.view', compact(['leave']));
    }
}
