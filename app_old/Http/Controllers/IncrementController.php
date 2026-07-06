<?php

namespace App\Http\Controllers;

use App\Queries\IncrementDataTable;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Exception;
use App\Http\Requests\IncrementRequest;
use App\Models\Increment;
use App\Http\Requests\UpdateIncrementRequest;
use App\Repositories\IncrementRepository;
use App\Repositories\OverTimeRepository;
use Illuminate\Database\QueryException;
use Laracasts\Flash\Flash;
use Throwable;

class IncrementController extends AppBaseController
{
    /**
     * @var IncrementRepository;
     */
    private $incrementRepository;
    public function __construct(IncrementRepository $incrementRepo)
    {
        $this->incrementRepository = $incrementRepo;
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
            return DataTables::of((new IncrementDataTable())->get($request->only(['group'])))->make(true);
        }
        return view('increments.index');
    }
    public function create()
    {
        $usersBranches = $this->getUsersBranches();
        $employees = $this->incrementRepository->getAllEmployees();
        return view('increments.create', compact('usersBranches', 'employees'));
    }
    public function store(IncrementRequest $request)
    {
        $input = $request->all();
        try {
            $designation = $this->incrementRepository->create($input);
            activity()->causedBy(getLoggedInUser())
                ->performedOn($designation)
                ->useLog('Increment created.')
                ->log($designation->name . ' Increment created.');
            Flash::success(__('messages.increments.saved'));
            return $this->sendResponse($designation, __('messages.increments.saved'));
        } catch (Throwable $e) {
            throw $e;
        }
    }
    public function destroy(Increment $increment)
    {

        try {
            $increment->delete();
            activity()->performedOn($increment)->causedBy(getLoggedInUser())
                ->useLog(' Increment deleted.')->log($increment->name . ' Increment deleted.');
            return $this->sendSuccess(__('messages.increments.delete'));
        } catch (QueryException $e) {
            return $this->sendError('Failed To delete!! Already in use.');
        }
    }
    public function edit(Increment $increment)
    {
        $usersBranches = $this->getUsersBranches();
        $employees = $this->incrementRepository->getAllEmployees();
        return view('increments.edit', compact(['increment', 'usersBranches', 'employees']));
    }
    public function update(Increment $increment, UpdateIncrementRequest $updateIncrementRequest)
    {
        $input = $updateIncrementRequest->all();
        $designation = $this->incrementRepository->updateIncrement($input, $updateIncrementRequest->id);
        activity()->performedOn($designation)->causedBy(getLoggedInUser())
            ->useLog('Increment Updated')->log($designation->name . ' Increment updated.');
        Flash::success(__('messages.increments.saved'));
        return $this->sendSuccess(__('messages.increments.saved'));
    }
    public function view(Increment $increment)
    {
        return view('increments.view', compact(['increment']));
    }
}
