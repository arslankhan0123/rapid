<?php

namespace App\Http\Controllers;

use App\Queries\OverTimeDataTable;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Exception;
use App\Http\Requests\OverTimeRequest;
use App\Models\OverTime;
use App\Http\Requests\UpdateOverTimeRequest;
use App\Repositories\OverTimeRepository;
use Illuminate\Database\QueryException;
use Laracasts\Flash\Flash;
use Throwable;

class OverTimeController extends AppBaseController
{
    /**
     * @var OverTimeRepository;
     */
    private $overTimeRepository;
    public function __construct(OverTimeRepository $overTimeRepo)
    {
        $this->overTimeRepository = $overTimeRepo;
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
            return DataTables::of((new OverTimeDataTable())->get($request->only(['group'])))->make(true);
        }
        return view('overtimes.index');
    }

    public function create()
    {
        $employees = $this->overTimeRepository->getAllEmployees();
        $types = $this->overTimeRepository->getOvertimeTypes();
        return view('overtimes.create', compact(['employees', 'types']));
    }

    public function store(OverTimeRequest $request)
    {
        $input = $request->all();
        try {
            $designation = $this->overTimeRepository->create($input);
            activity()->causedBy(getLoggedInUser())
                ->performedOn($designation)
                ->useLog('Overtime created.')
                ->log("Over Time created");
            Flash::success(__('messages.overtimes.saved'));
            return $this->sendResponse($designation, __('messages.overtimes.saved'));
        } catch (Throwable $e) {
            throw $e;
        }
    }

    public function destroy(OverTime $overtime)
    {

        try {
            $overtime->delete();
            activity()->performedOn($overtime)->causedBy(getLoggedInUser())
                ->useLog('Overtime deleted.')->log($overtime->name . 'Overtime deleted.');
            return $this->sendSuccess(__('messages.overtimes.delete'));
        } catch (QueryException $e) {
            return $this->sendError('Failed To delete!! Already in use.');
        }
    }

    public function edit(OverTime $overtime)
    {
        $employees = $this->overTimeRepository->getAllEmployees();
        $types = $this->overTimeRepository->getOvertimeTypes();
        return view('overtimes.edit', compact(['overtime', 'employees', 'types']));
    }
    public function update(OverTime $overTime, UpdateOverTimeRequest $updateOverTimeRequest)
    {
        $input = $updateOverTimeRequest->all();
        $designation = $this->overTimeRepository->update($input, $updateOverTimeRequest->id);
        activity()->performedOn($designation)->causedBy(getLoggedInUser())
            ->useLog('Overtime Updated')->log($designation->name . 'Overtime updated.');
        Flash::success(__('messages.overtimes.saved'));
        return $this->sendSuccess(__('messages.overtimes.saved'));
    }

    public function view(OverTime $overtime)
    {
        $overtime->load(['employee', 'overtimeTypes']);
        return view('overtimes.view', compact(['overtime']));
    }
}
