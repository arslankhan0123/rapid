<?php

namespace App\Http\Controllers;

use App\Queries\TerminationDataTable;
use Illuminate\Http\Request;
use App\Repositories\TerminationRepository;
use Yajra\DataTables\DataTables;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Exception;
use App\Http\Requests\TerminationsRequest;
use App\Models\SubDepartment;
use App\Http\Requests\UpdateTerminationRequest;
use Illuminate\Database\QueryException;
use App\Models\Termination;
use Laracasts\Flash\Flash;
use Throwable;

class TerminationController extends AppBaseController
{
    /**
     * @var TerminationRepository
     */
    private $terminationRepository;
    public function __construct(TerminationRepository $retirementRepo)
    {
        $this->terminationRepository = $retirementRepo;
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
            return DataTables::of((new TerminationDataTable())->get($request->only(['group'])))->make(true);
        }
        return view('terminations.index');
    }

    public function create()
    {
        $employees = $this->terminationRepository->getEmplyee(); // Retrieves departments as key-value pairs
        $usersBranches = $this->getUsersBranches();
        return view('terminations.create', compact('employees', 'usersBranches'));
    }

    public function store(TerminationsRequest $request)
    {

        $input = $request->all();
        try {
            $retirement = $this->terminationRepository->create($input);
            activity()->causedBy(getLoggedInUser())
                ->performedOn($retirement)
                ->useLog('Termination created.')
                ->log($retirement->name . ' Termination Created.');
            Flash::success(__('messages.terminations.saved'));
            return $this->sendResponse($retirement, __('messages.terminations.saved'));
        } catch (Throwable $e) {
            throw $e;
        }
    }
    public function destroy(Termination $termination)
    {

        try {
            $termination->delete();
            activity()->performedOn($termination)->causedBy(getLoggedInUser())
                ->useLog('Termination deleted.')->log($termination->name . '  Termination deleted.');
            return $this->sendSuccess(__('messages.terminations.delete'));
        } catch (QueryException $e) {
            return $this->sendError('Failed To delete!! Already in use.');
        }
    }

    public function edit(Termination $termination)
    {
        $employees = $this->terminationRepository->getEmplyee();
        $usersBranches = $this->getUsersBranches();
        return view('terminations.edit', compact(['employees', 'termination', 'usersBranches']));
    }
    public function update(Termination $termination, UpdateTerminationRequest $updateTerminationRequest)
    {

        $input = $updateTerminationRequest->all();
        $input['status'] = $input['status'] ?? 0;
        $updateRetirement = $this->terminationRepository->update($input, $updateTerminationRequest->id);
        activity()->performedOn($updateRetirement)->causedBy(getLoggedInUser())
            ->useLog('Termination Updated')->log($updateRetirement->name . ' Termination updated.');
        Flash::success(__('messages.terminations.saved'));
        return $this->sendSuccess(__('messages.terminations.saved'));
    }

    public function view(Termination $termination)
    {
        $termination->load(['employee', 'employee.designation', 'employee.department', 'employee.subDepartment']);
        return view('terminations.view', compact(['termination']));
    }
}
