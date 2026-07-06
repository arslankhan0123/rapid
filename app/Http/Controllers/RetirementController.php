<?php

namespace App\Http\Controllers;

use App\Queries\RetirementDataTable;
use Illuminate\Http\Request;
use App\Repositories\RetirementRepository;
use Yajra\DataTables\DataTables;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Exception;
use App\Http\Requests\RetirementRequest;
use App\Models\SubDepartment;
use App\Http\Requests\UpdateRetirementRequest;
use Illuminate\Database\QueryException;
use App\Models\Retirement;
use Laracasts\Flash\Flash;
use Throwable;

class RetirementController extends AppBaseController
{
    /**
     * @var RetirementRepository
     */
    private $retirementRepository;
    public function __construct(RetirementRepository $retirementRepo)
    {
        $this->retirementRepository = $retirementRepo;
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
            return DataTables::of((new RetirementDataTable())->get($request->only(['group'])))->make(true);
        }
        return view('retirements.index');
    }

    public function create()
    {
        $employees = $this->retirementRepository->getEmplyee(); // Retrieves departments as key-value pairs
        $usersBranches = $this->getUsersBranches();
        return view('retirements.create', compact('employees', 'usersBranches'));
    }

    public function store(RetirementRequest $request)
    {

        $input = $request->all();
        try {
            $retirement = $this->retirementRepository->create($input);
            activity()->causedBy(getLoggedInUser())
                ->performedOn($retirement)
                ->useLog('Retirement created.')
                ->log($retirement->name . ' Retirement Created.');
            Flash::success(__('messages.retirements.saved'));
            return $this->sendResponse($retirement, __('messages.retirements.saved'));
        } catch (Throwable $e) {
            throw $e;
        }
    }
    public function destroy(Retirement $retirement)
    {

        try {
            $retirement->delete();
            activity()->performedOn($retirement)->causedBy(getLoggedInUser())
                ->useLog('Retirement deleted.')->log($retirement->name . '  Retirement deleted.');
            return $this->sendSuccess(__('messages.retirements.delete'));
        } catch (QueryException $e) {
            return $this->sendError('Failed To delete!! Already in use.');
        }
    }

    public function edit(Retirement $retirement)
    {
        $employees = $this->retirementRepository->getEmplyee();
        $usersBranches = $this->getUsersBranches();
        return view('retirements.edit', compact(['employees', 'retirement', 'usersBranches']));
    }
    public function update(Retirement $retirement, UpdateRetirementRequest $updateRetirementRequest)
    {

        $input = $updateRetirementRequest->all();
        $input['status'] = $input['status'] ?? 0;
        $updateRetirement = $this->retirementRepository->update($input, $updateRetirementRequest->id);
        activity()->performedOn($updateRetirement)->causedBy(getLoggedInUser())
            ->useLog('Retirement Updated')->log($updateRetirement->name . ' Retirement updated.');
        Flash::success(__('messages.retirements.saved'));
        return $this->sendSuccess(__('messages.retirements.saved'));
    }

    public function view(Retirement $retirement)
    {
        $retirement->load(['employee', 'employee.designation','employee.branch']);
        return view('retirements.view', compact(['retirement']));
    }
}
