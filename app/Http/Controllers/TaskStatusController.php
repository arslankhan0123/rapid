<?php

namespace App\Http\Controllers;

use App\Queries\TaskStatusDataTable;
use Illuminate\Http\Request;
use App\Repositories\TaskStatusRepository;
use Yajra\DataTables\DataTables;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Exception;
use App\Http\Requests\TaskStatusRequest;
use App\Models\TaskStatus;
use App\Http\Requests\UpdateTaskStatusRequest;
use Illuminate\Database\QueryException;
use Laracasts\Flash\Flash;
use Throwable;
use App\Models\Project;
use App\Models\User;
use App\Models\TaskAssign;
use Illuminate\Support\Facades\DB;



class TaskStatusController extends AppBaseController
{
    /**
     * @var TaskStatusRepository
     */
    private $taskStatusRepository;
    public function __construct(TaskStatusRepository $taskStatusRepo)
    {
        $this->taskStatusRepository = $taskStatusRepo;
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
            return DataTables::of((new TaskStatusDataTable())->get($request->all()))->make(true);
        }
        
        
        $employees = $this->taskStatusRepository->getAllEmployees();
        $usersBranches = $this->getUsersBranches();
        
        return view('task_status.index', compact( 'employees', 'usersBranches'));
    }

    public function create()
    {


        $employees=$this->taskStatusRepository->getAllEmployees();
        $customers = $this->taskStatusRepository->getCustomers();
        $projects = $this->taskStatusRepository->getProjects();
        $usersBranches = $this->getUsersBranches();
        $task_list = TaskAssign::pluck('name', 'name');
        
        $departments = DB::table('departments')->pluck('name', 'id');
        $designations = DB::table('designations')->get();
       
        
        return view('task_status.create', compact(['employees', 'customers', 'projects', 'usersBranches', 'task_list', 'departments', 'designations']));
    }

    public function store(TaskStatusRequest $request)
    {

        $input = $request->all();
        
        //dd($input);
        
        $taskValues = (array) $request->input('task');
        $input['task'] = implode(', ', $taskValues);
        
        try {
            $taskStatus = $this->taskStatusRepository->create($input);
            activity()->causedBy(getLoggedInUser())
                ->performedOn($taskStatus)
                ->useLog('TaskStatus created.')
                ->log($taskStatus->task . ' TaskStatus Created');
            Flash::success(__('messages.task-status.saved'));
            return $this->sendResponse($taskStatus->task, __('messages.task-status.saved'));
        } catch (Throwable $e) {
            throw $e;
        }
    }
    public function view(TaskStatus $task)
    {
        $task->load(['project', 'user', 'customer']);
        return view('task_status.view', compact(['task']));
    }

    public function edit(TaskStatus $task)
    {
        $employees = $this->taskStatusRepository->getAllEmployees();
        $customers = $this->taskStatusRepository->getCustomers();
        $projects = $this->taskStatusRepository->getProjects();
        $usersBranches = $this->getUsersBranches();
        $task_list = TaskAssign::pluck('name', 'name');
        $selected_task = array_map('trim', explode(',', $task->task ?? ''));
        $taskStatus = $task;
        
        $departments = DB::table('departments')->pluck('name', 'id');
        $designations = DB::table('designations')->get();
        
        return view('task_status.edit', compact(['task', 'employees', 'customers', 'projects', 'usersBranches', 'task_list', 'selected_task', 'taskStatus', 'departments', 'designations']));
    }

    public function update(UpdateTaskStatusRequest $request, TaskStatus $task)
    {
        $input = $request->all();
        $taskValues = (array) $request->input('task');
        $input['task'] = implode(', ', $taskValues);
        
        $task = $this->taskStatusRepository->update($input, $task->id);

        activity()->performedOn($task)->causedBy(getLoggedInUser())
            ->useLog('Country updated.')->log($task->name . ' Task updated.');

        return $this->sendSuccess(__('messages.task-status.saved'));
    }

    public function destroy(TaskStatus $task)
    {
        try {
            $task->delete();
            activity()->performedOn($task)->causedBy(getLoggedInUser())
                ->useLog('Task deleted.')->log($task->name . 'Task deleted.');
            return $this->sendSuccess(__('messages.task-status.delete'));
        } catch (QueryException $e) {
            return $this->sendError('Failed To delete!! Already in use.');
        }
    }
}
