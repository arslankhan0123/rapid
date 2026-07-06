<?php

namespace App\Http\Controllers;

use App\Queries\StateDataTable;
use Illuminate\Http\Request;
use App\Repositories\StateRepository;
use Yajra\DataTables\DataTables;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Exception;
use App\Http\Requests\StateRequest;
use App\Models\State;
use App\Http\Requests\UpdateStateRequest;
use Illuminate\Database\QueryException;
use Throwable;

class StateController extends AppBaseController
{
    /**
     * @var StateRepository
     */
    private $stateRepository;
    public function __construct(StateRepository $stateRepo)
    {
        $this->stateRepository = $stateRepo;
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
            return DataTables::of((new StateDataTable())->get($request->only(['group'])))->make(true);
        }
        return view('states.index');
    }

    public function create()
    {
        $countries=$this->stateRepository->getCountries();
        return view('states.create',compact(['countries']));
    }

    public function store(StateRequest $request)
    {

        $input = $request->all();
        try {
            $state = $this->stateRepository->create($input);
            activity()->causedBy(getLoggedInUser())
                ->performedOn($state)
                ->useLog('State created.')
                ->log($state->name . ' State Created');
            return $this->sendResponse($state, __('messages.states.saved'));
        } catch (Throwable $e) {
            throw $e;
        }
    }
    public function destroy(State $state)
    {
        try {
            $state->delete();
            activity()->performedOn($state)->causedBy(getLoggedInUser())
                ->useLog('State deleted.')->log($state->name . ' State deleted.');
            return $this->sendSuccess(__('messages.states.delete'));
        } catch (QueryException $e) {
            return $this->sendError('Failed To delete!! Already in use.');
        }
    }

    public function edit(State $state)
    {
        $countries = $this->stateRepository->getCountries();
        return view('states.edit', compact(['state', 'countries']));
    }
    public function view(State $state)
    {
        $state->load('country');
        return view('states.view', compact(['state']));
    }
    public function update(State $state, UpdateStateRequest $updateStateRequest)
    {
        $input = $updateStateRequest->all();
        $state = $this->stateRepository->update($input, $updateStateRequest->id);
        activity()->performedOn($state)->causedBy(getLoggedInUser())
            ->useLog('State Updated')->log($state->name . ' State updated.');
        return $this->sendSuccess(__('messages.states.saved'));
    }
}
