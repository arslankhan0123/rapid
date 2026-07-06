<?php

namespace App\Http\Controllers;

use App\Queries\ShiftDataTable;
use Illuminate\Http\Request;
use App\Repositories\ShiftRepository;
use Yajra\DataTables\DataTables;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Exception;
use App\Http\Requests\ShiftRequest;
use App\Models\Shift;
use App\Http\Requests\UpdateShiftRequest;
use Illuminate\Database\QueryException;
use Laracasts\Flash\Flash;

class ShiftController extends AppBaseController
{
    /**
     * @var ShiftRepository
     */
    private $shiftRepository;
    public function __construct(ShiftRepository $shiftRepo)
    {
        $this->shiftRepository = $shiftRepo;
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
            return DataTables::of((new ShiftDataTable())->get($request->only(['group'])))->make(true);
        }
        return view('shifts.index');
    }

    public function create()
    {
        return view('shifts.create');
    }

    public function store(ShiftRequest $request)
    {
        $input = $request->all();

        try {
            $shift = $this->shiftRepository->create($input);
            activity()->causedBy(getLoggedInUser())
                ->performedOn($shift)
                ->useLog('Shift created.')
                ->log($shift->name);
            Flash::success(__('messages.shifts.saved'));

            return $this->sendResponse($shift->name, __('messages.shifts.saved'));
        } catch (Throwable $e) {
            throw $e;
        }
    }
    public function destroy(Shift $shift)
    {
        try {
            $shift->delete();
            activity()->performedOn($shift)->causedBy(getLoggedInUser())
                ->useLog('Shift deleted.')->log($shift->name . ' deleted.');
            return $this->sendSuccess(__('messages.shifts.delete'));
        } catch (QueryException $e) {
            return $this->sendError('Failed To delete!! Already in use.');
        }
    }

    public function view(Shift $shift)
    {
        return view('shifts.view', compact(['shift']));
    }
    public function edit(Shift $shift)
    {
        return view('shifts.edit', compact(['shift']));
    }
    public function update(Shift $shift, UpdateShiftRequest $updateShiftRequest)
    {
        $input = $updateShiftRequest->all();
        $updateShift = $this->shiftRepository->update($input, $updateShiftRequest->id);
        activity()->performedOn($updateShift)->causedBy(getLoggedInUser())
            ->useLog('Shift Updated')->log($updateShift->name . ' Shift updated.');
        Flash::success(__('messages.shifts.saved'));
        return $this->sendSuccess(__('messages.shifts.saved'));
    }
}
