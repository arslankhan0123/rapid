<?php

namespace App\Http\Controllers;

use App\Queries\DeductionDataTable;
use Illuminate\Http\Request;
use App\Repositories\DeductionRepository;
use Yajra\DataTables\DataTables;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Exception;
use App\Http\Requests\DeductionRequest;
use App\Models\Deduction;
use App\Http\Requests\UpdateDeductionRequest;
use Illuminate\Database\QueryException;
use Laracasts\Flash\Flash;
use Throwable;

class DeductionController extends AppBaseController
{
    /**
     * @var DeductionRepository
     */
    private $deductionRepository;
    public function __construct(DeductionRepository $deductionRepo)
    {
        $this->deductionRepository = $deductionRepo;
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
            return DataTables::of((new DeductionDataTable())->get($request->all()))->make(true);
        }
        return view('deductions.index');
    }

    public function create()
    {
        $employees = $this->deductionRepository->getAllEmployees();
        $types = $this->deductionRepository->getDeductionTypes();
        $usersBranches = $this->getUsersBranches();
        return view('deductions.create', compact(['employees','types', 'usersBranches']));
    }

    public function store(DeductionRequest $request)
    {
        $input = $request->all();
        if (isset($input['month']) && !empty($input['month'])) {
            $input['month'] = $input['month'] . '-01 00:00:00';
        }
        try {
            $deduction = $this->deductionRepository->create($input);
            activity()->causedBy(getLoggedInUser())
                ->performedOn($deduction)
                ->useLog('Deduction created.')
                ->log($deduction->name . " Deduction Created");
            Flash::success(__('messages.deductions.saved'));
            return $this->sendResponse($deduction, __('messages.deductions.saved'));
        } catch (Throwable $e) {
            throw $e;
        }
    }
    public function destroy(Deduction $deduction)
    {
        try {
            $deduction->delete();
            activity()->performedOn($deduction)->causedBy(getLoggedInUser())
                ->useLog('Deduction deleted.')->log($deduction->name . ' deleted.');
            return $this->sendSuccess(__('messages.deductions.delete'));
        } catch (QueryException $e) {
            return $this->sendError('Failed To delete!! Already in use.');
        }
    }

    public function edit(Deduction $deduction)
    {
        $employees = $this->deductionRepository->getAllEmployees();
        $types = $this->deductionRepository->getDeductionTypes();
        $usersBranches = $this->getUsersBranches();
        return view('deductions.edit', compact(['deduction', 'employees', 'types', 'usersBranches']));
    }
    public function update(Deduction $deduction, UpdateDeductionRequest $updateDeductionRequest)
    {
        $input = $updateDeductionRequest->all();
        if (isset($input['month']) && !empty($input['month'])) {
            $input['month'] = $input['month'] . '-01 00:00:00';
        }


        $deduction = $this->deductionRepository->update($input, $updateDeductionRequest->id);

        activity()->performedOn($deduction)->causedBy(getLoggedInUser())
            ->useLog('Deduction Updated')->log($deduction->name . ' Deduction updated.');
        Flash::success(__('messages.deductions.saved'));
        return $this->sendSuccess(__('messages.deductions.saved'));
    }
    public function view(Deduction $deduction)
    {
        $deduction->load(['employee', 'employee.designation', 'deductionTypes']);

        return view('deductions.view', compact(['deduction']));
    }
}
