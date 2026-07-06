<?php

namespace App\Http\Controllers;

use App\Queries\TaskAssignDataTable;
use Illuminate\Http\Request;
use App\Repositories\TaskAssignRepository;
use Yajra\DataTables\DataTables;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Exception;
use App\Http\Requests\TaskAssignReqeust;
use App\Models\Currency;
use App\Http\Requests\UpdateCurrencyRequest;
use Illuminate\Database\QueryException;
use Laracasts\Flash\Flash;
use Throwable;
use App\Models\TaskAssign;

class TaskAssignController extends AppBaseController
{
    /**
     * @var TaskAssignRepository
     */
    private $taskAssignRepository;
    public function __construct(TaskAssignRepository $taskAssignRepo)
    {
        $this->taskAssignRepository = $taskAssignRepo;
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
            return DataTables::of((new TaskAssignDataTable())->get($request->all()))->make(true);
        }
        return view('task-assign.index');
    }

    public function create()
    {
        $departments = $this->taskAssignRepository->getDepartments();
        $designations = $this->taskAssignRepository->getDesignations();
        
        $employees = $this->taskAssignRepository->getEmployees();
        return view('task-assign.create', compact(['departments', 'designations', 'employees']));
    }

    public function store(TaskAssignReqeust $request)
    {

        $input = $request->all();

        try {
            $currency = $this->taskAssignRepository->create($input);
            activity()->causedBy(getLoggedInUser())
                ->performedOn($currency)
                ->useLog('Task Assigned.')
                ->log($currency->name . ' Task Assigned');
            Flash::success(__('messages.task-assign.saved'));
            return $this->sendResponse($currency, __('messages.task-assign.saved'));
        } catch (Throwable $e) {
            throw $e;
        }
    }
    public function destroy(TaskAssign $task)
    {
        try {
            $task->delete();
            activity()->performedOn($task)->causedBy(getLoggedInUser())
                ->useLog('Task deleted.')->log($task->name . ' Task deleted.');
            return $this->sendSuccess(__('messages.task-assign.deleted'));
        } catch (QueryException $e) {
            return $this->sendError('Failed To delete!! Already in use.');
        }
    }

    public function edit(TaskAssign $task)
    {
        $task->load(['department', 'designation', 'employee']);

        $departments = $this->taskAssignRepository->getDepartments();
        $designations = $this->taskAssignRepository->getDesignations();
        $employees = $this->taskAssignRepository->getEmployees();
        return view('task-assign.edit', compact(['task', 'departments', 'designations', 'employees']));
    }
    public function view(TaskAssign $task)
    {
        $task->load(['department', 'designation', 'employee']);
        return view('task-assign.view', compact(['task']));
    }
    public function update(TaskAssign $task, UpdateCurrencyRequest $updateCurrencyRequest)
    {
        $input = $updateCurrencyRequest->all();
        $updateCurrency = $this->taskAssignRepository->update($input, $updateCurrencyRequest->id);
        activity()->performedOn($updateCurrency)->causedBy(getLoggedInUser())
            ->useLog('Task Updated')->log($updateCurrency->name . ' Task updated.');
        Flash::success(__('messages.task-assign.saved'));
        return $this->sendSuccess(__('messages.task-assign.saved'));
    }
}
