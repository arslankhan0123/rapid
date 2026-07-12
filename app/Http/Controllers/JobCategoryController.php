<?php

namespace App\Http\Controllers;

use App\Queries\JobCategoryDataTable;
use Illuminate\Http\Request;
use App\Repositories\JobCategoryRepository;
use Yajra\DataTables\DataTables;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Exception;
use App\Http\Requests\CreateJobCategoryRequest;
use App\Http\Requests\UpdateJobCategoryRequest;
use App\Models\JobCategory;
use Illuminate\Database\QueryException;
use Throwable;

class JobCategoryController extends AppBaseController
{
    /**
     * @var JobCategoryRepository
     */
    private $jobCategoryRepository;
    
    public function __construct(JobCategoryRepository $jobCategoryRepo)
    {
        $this->jobCategoryRepository = $jobCategoryRepo;
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
            return DataTables::of((new JobCategoryDataTable())->get($request->only(['group'])))->make(true);
        }
        return view('job_categories.index');
    }

    public function create()
    {
        $categories = JobCategory::where('is_active', 1)->pluck('name', 'id')->toArray();
        return view('job_categories.create', compact('categories'));
    }

    public function store(CreateJobCategoryRequest $request)
    {
        $input = $request->all();
        // Checkbox handling for boolean
        $input['is_active'] = isset($input['is_active']) ? 1 : 0;
        
        try {
            $jobCategory = $this->jobCategoryRepository->create($input);
            activity()->causedBy(getLoggedInUser())
                ->performedOn($jobCategory)
                ->useLog('JobCategory created.')
                ->log($jobCategory->name . ' Job Category Created');
            return $this->sendResponse($jobCategory, __('messages.job_categories.saved') ?? 'Job Category saved successfully.');
        } catch (Throwable $e) {
            throw $e;
        }
    }
    
    public function destroy(JobCategory $jobCategory)
    {
        try {
            $jobCategory->delete();
            activity()->performedOn($jobCategory)->causedBy(getLoggedInUser())
                ->useLog('JobCategory deleted.')->log($jobCategory->name . ' Job Category deleted.');
            return $this->sendSuccess(__('messages.job_categories.delete') ?? 'Job Category deleted successfully.');
        } catch (QueryException $e) {
            return $this->sendError('Failed To delete!! Already in use.');
        }
    }

    public function edit(JobCategory $jobCategory)
    {
        $categories = JobCategory::where('is_active', 1)->where('id', '!=', $jobCategory->id)->pluck('name', 'id')->toArray();
        return view('job_categories.edit', compact(['jobCategory', 'categories']));
    }
    
    public function view(JobCategory $jobCategory)
    {
        return view('job_categories.view', compact(['jobCategory']));
    }
    
    public function update(JobCategory $jobCategory, UpdateJobCategoryRequest $updateJobCategoryRequest)
    {
        $input = $updateJobCategoryRequest->all();
        $input['is_active'] = isset($input['is_active']) ? 1 : 0;
        
        $jobCategory = $this->jobCategoryRepository->update($input, $jobCategory->id);
        activity()->performedOn($jobCategory)->causedBy(getLoggedInUser())
            ->useLog('JobCategory Updated')->log($jobCategory->name . ' Job Category updated.');
        return $this->sendSuccess(__('messages.job_categories.saved') ?? 'Job Category updated successfully.');
    }
}
