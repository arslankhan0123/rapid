<?php

namespace App\Http\Controllers;

use App\Queries\JobRecruiterDataTable;
use Illuminate\Http\Request;
use App\Repositories\JobRecruiterRepository;
use Yajra\DataTables\DataTables;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Exception;
use App\Http\Requests\JobRecruiterRequest;
use App\Models\JobRecruiter;
use App\Http\Requests\UpdateJobRecruiterRequest;
use Illuminate\Database\QueryException;
use Throwable;

class JobRecruiterController extends AppBaseController
{
    /**
     * @var JobRecruiterRepository
     */
    private $jobRecruiterRepository;
    
    public function __construct(JobRecruiterRepository $jobRecruiterRepo)
    {
        $this->jobRecruiterRepository = $jobRecruiterRepo;
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
            return DataTables::of((new JobRecruiterDataTable())->get($request->only(['group'])))->make(true);
        }
        return view('job_recruiters.index');
    }

    public function create()
    {
        return view('job_recruiters.create');
    }

    public function store(JobRecruiterRequest $request)
    {
        $input = $request->all();
        try {
            $jobRecruiter = $this->jobRecruiterRepository->create($input);
            activity()->causedBy(getLoggedInUser())
                ->performedOn($jobRecruiter)
                ->useLog('JobRecruiter created.')
                ->log($jobRecruiter->recruiter_name . ' Job Recruiter Created');
            return $this->sendResponse($jobRecruiter, __('messages.job_recruiters.saved') ?? 'Job Recruiter saved successfully.');
        } catch (Throwable $e) {
            throw $e;
        }
    }
    
    public function destroy(JobRecruiter $jobRecruiter)
    {
        try {
            $jobRecruiter->delete();
            activity()->performedOn($jobRecruiter)->causedBy(getLoggedInUser())
                ->useLog('JobRecruiter deleted.')->log($jobRecruiter->recruiter_name . ' Job Recruiter deleted.');
            return $this->sendSuccess(__('messages.job_recruiters.delete') ?? 'Job Recruiter deleted successfully.');
        } catch (QueryException $e) {
            return $this->sendError('Failed To delete!! Already in use.');
        }
    }

    public function edit(JobRecruiter $jobRecruiter)
    {
        return view('job_recruiters.edit', compact(['jobRecruiter']));
    }
    
    public function view(JobRecruiter $jobRecruiter)
    {
        return view('job_recruiters.view', compact(['jobRecruiter']));
    }
    
    public function update(JobRecruiter $jobRecruiter, UpdateJobRecruiterRequest $updateJobRecruiterRequest)
    {
        $input = $updateJobRecruiterRequest->all();
        $jobRecruiter = $this->jobRecruiterRepository->update($input, $updateJobRecruiterRequest->id);
        activity()->performedOn($jobRecruiter)->causedBy(getLoggedInUser())
            ->useLog('JobRecruiter Updated')->log($jobRecruiter->recruiter_name . ' Job Recruiter updated.');
        return $this->sendSuccess(__('messages.job_recruiters.saved') ?? 'Job Recruiter updated successfully.');
    }
}
