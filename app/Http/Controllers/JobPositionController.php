<?php

namespace App\Http\Controllers;

use App\Queries\JobPositionDataTable;
use Illuminate\Http\Request;
use App\Repositories\JobPositionRepository;
use Yajra\DataTables\DataTables;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Exception;
use App\Http\Requests\CreateJobPositionRequest;
use App\Http\Requests\UpdateJobPositionRequest;
use App\Models\JobPosition;
use App\Models\JobCategory;
use App\Models\Department;
use App\Models\JobSkill;
use Illuminate\Database\QueryException;
use Throwable;

class JobPositionController extends AppBaseController
{
    /**
     * @var JobPositionRepository
     */
    private $jobPositionRepository;
    
    public function __construct(JobPositionRepository $jobPositionRepo)
    {
        $this->jobPositionRepository = $jobPositionRepo;
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
            return DataTables::of((new JobPositionDataTable())->get($request->only(['group'])))->make(true);
        }
        return view('job_positions.index');
    }

    public function create()
    {
        $categories = JobCategory::where('is_active', 1)->pluck('name', 'id')->toArray();
        $departments = Department::pluck('name', 'id')->toArray();
        $employmentTypes = JobPosition::$employmentTypes;
        $statuses = JobPosition::$statuses;
        $skills = JobSkill::where('is_active', 1)->pluck('name', 'id')->toArray();

        return view('job_positions.create', compact(['categories', 'departments', 'employmentTypes', 'statuses', 'skills']));
    }

    public function store(CreateJobPositionRequest $request)
    {
        $input = $request->all();
        
        try {
            $jobPosition = $this->jobPositionRepository->create($input);
            activity()->causedBy(getLoggedInUser())
                ->performedOn($jobPosition)
                ->useLog('JobPosition created.')
                ->log($jobPosition->title . ' Job Position Created');
            return $this->sendResponse($jobPosition, __('messages.job_positions.saved') ?? 'Job Position saved successfully.');
        } catch (Throwable $e) {
            throw $e;
        }
    }
    
    public function destroy(JobPosition $jobPosition)
    {
        try {
            $jobPosition->delete();
            activity()->performedOn($jobPosition)->causedBy(getLoggedInUser())
                ->useLog('JobPosition deleted.')->log($jobPosition->title . ' Job Position deleted.');
            return $this->sendSuccess(__('messages.job_positions.deleted') ?? 'Job Position deleted successfully.');
        } catch (QueryException $e) {
            return $this->sendError('Failed To delete!! Already in use.');
        }
    }

    public function edit(JobPosition $jobPosition)
    {
        $jobPosition->load('skills');
        $categories = JobCategory::where('is_active', 1)->pluck('name', 'id')->toArray();
        $departments = Department::pluck('name', 'id')->toArray();
        $employmentTypes = JobPosition::$employmentTypes;
        $statuses = JobPosition::$statuses;
        $skills = JobSkill::where('is_active', 1)->pluck('name', 'id')->toArray();
        
        $selectedSkills = $jobPosition->skills->pluck('id')->toArray();

        return view('job_positions.edit', compact(['jobPosition', 'categories', 'departments', 'employmentTypes', 'statuses', 'skills', 'selectedSkills']));
    }
    
    public function view(JobPosition $jobPosition)
    {
        $jobPosition->load(['category', 'department', 'skills']);
        return view('job_positions.view', compact(['jobPosition']));
    }
    
    public function update(JobPosition $jobPosition, UpdateJobPositionRequest $updateJobPositionRequest)
    {
        $input = $updateJobPositionRequest->all();
        
        $jobPosition = $this->jobPositionRepository->update($input, $jobPosition->id);
        activity()->performedOn($jobPosition)->causedBy(getLoggedInUser())
            ->useLog('JobPosition Updated')->log($jobPosition->title . ' Job Position updated.');
        return $this->sendSuccess(__('messages.job_positions.saved') ?? 'Job Position updated successfully.');
    }
}
