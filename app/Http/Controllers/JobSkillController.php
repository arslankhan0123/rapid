<?php

namespace App\Http\Controllers;

use App\Queries\JobSkillDataTable;
use Illuminate\Http\Request;
use App\Repositories\JobSkillRepository;
use Yajra\DataTables\DataTables;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Exception;
use App\Http\Requests\CreateJobSkillRequest;
use App\Http\Requests\UpdateJobSkillRequest;
use App\Models\JobSkill;
use App\Models\JobCategory;
use Illuminate\Database\QueryException;
use Throwable;

class JobSkillController extends AppBaseController
{
    /**
     * @var JobSkillRepository
     */
    private $jobSkillRepository;
    
    public function __construct(JobSkillRepository $jobSkillRepo)
    {
        $this->jobSkillRepository = $jobSkillRepo;
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
            return DataTables::of((new JobSkillDataTable())->get($request->only(['group'])))->make(true);
        }
        return view('job_skills.index');
    }

    public function create()
    {
        $categories = JobCategory::where('is_active', 1)->pluck('name', 'id')->toArray();
        $levels = JobSkill::$levels;
        return view('job_skills.create', compact(['categories', 'levels']));
    }

    public function store(CreateJobSkillRequest $request)
    {
        $input = $request->all();
        $input['is_active'] = isset($input['is_active']) ? 1 : 0;
        
        try {
            $jobSkill = $this->jobSkillRepository->create($input);
            activity()->causedBy(getLoggedInUser())
                ->performedOn($jobSkill)
                ->useLog('JobSkill created.')
                ->log($jobSkill->name . ' Job Skill Created');
            return $this->sendResponse($jobSkill, __('messages.job_skills.saved') ?? 'Job Skill saved successfully.');
        } catch (Throwable $e) {
            throw $e;
        }
    }
    
    public function destroy(JobSkill $jobSkill)
    {
        try {
            $jobSkill->delete();
            activity()->performedOn($jobSkill)->causedBy(getLoggedInUser())
                ->useLog('JobSkill deleted.')->log($jobSkill->name . ' Job Skill deleted.');
            return $this->sendSuccess(__('messages.job_skills.deleted') ?? 'Job Skill deleted successfully.');
        } catch (QueryException $e) {
            return $this->sendError('Failed To delete!! Already in use.');
        }
    }

    public function edit(JobSkill $jobSkill)
    {
        $categories = JobCategory::where('is_active', 1)->pluck('name', 'id')->toArray();
        $levels = JobSkill::$levels;
        return view('job_skills.edit', compact(['jobSkill', 'categories', 'levels']));
    }
    
    public function view(JobSkill $jobSkill)
    {
        return view('job_skills.view', compact(['jobSkill']));
    }
    
    public function update(JobSkill $jobSkill, UpdateJobSkillRequest $updateJobSkillRequest)
    {
        $input = $updateJobSkillRequest->all();
        $input['is_active'] = isset($input['is_active']) ? 1 : 0;
        
        $jobSkill = $this->jobSkillRepository->update($input, $jobSkill->id);
        activity()->performedOn($jobSkill)->causedBy(getLoggedInUser())
            ->useLog('JobSkill Updated')->log($jobSkill->name . ' Job Skill updated.');
        return $this->sendSuccess(__('messages.job_skills.saved') ?? 'Job Skill updated successfully.');
    }
}
