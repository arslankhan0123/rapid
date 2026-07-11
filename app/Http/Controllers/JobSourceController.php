<?php

namespace App\Http\Controllers;

use App\Queries\JobSourceDataTable;
use Illuminate\Http\Request;
use App\Repositories\JobSourceRepository;
use Yajra\DataTables\DataTables;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Exception;
use App\Http\Requests\JobSourceRequest;
use App\Models\JobSource;
use App\Http\Requests\UpdateJobSourceRequest;
use Illuminate\Database\QueryException;
use Throwable;

class JobSourceController extends AppBaseController
{
    /**
     * @var JobSourceRepository
     */
    private $jobSourceRepository;
    
    public function __construct(JobSourceRepository $jobSourceRepo)
    {
        $this->jobSourceRepository = $jobSourceRepo;
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
            return DataTables::of((new JobSourceDataTable())->get($request->only(['group'])))->make(true);
        }
        return view('job_sources.index');
    }

    public function create()
    {
        return view('job_sources.create');
    }

    public function store(JobSourceRequest $request)
    {
        $input = $request->all();
        try {
            $jobSource = $this->jobSourceRepository->create($input);
            activity()->causedBy(getLoggedInUser())
                ->performedOn($jobSource)
                ->useLog('JobSource created.')
                ->log($jobSource->name . ' Job Source Created');
            return $this->sendResponse($jobSource, __('messages.job_sources.saved') ?? 'Job Source saved successfully.');
        } catch (Throwable $e) {
            throw $e;
        }
    }
    
    public function destroy(JobSource $jobSource)
    {
        try {
            $jobSource->delete();
            activity()->performedOn($jobSource)->causedBy(getLoggedInUser())
                ->useLog('JobSource deleted.')->log($jobSource->name . ' Job Source deleted.');
            return $this->sendSuccess(__('messages.job_sources.delete') ?? 'Job Source deleted successfully.');
        } catch (QueryException $e) {
            return $this->sendError('Failed To delete!! Already in use.');
        }
    }

    public function edit(JobSource $jobSource)
    {
        return view('job_sources.edit', compact(['jobSource']));
    }
    
    public function view(JobSource $jobSource)
    {
        return view('job_sources.view', compact(['jobSource']));
    }
    
    public function update(JobSource $jobSource, UpdateJobSourceRequest $updateJobSourceRequest)
    {
        $input = $updateJobSourceRequest->all();
        $jobSource = $this->jobSourceRepository->update($input, $updateJobSourceRequest->id);
        activity()->performedOn($jobSource)->causedBy(getLoggedInUser())
            ->useLog('JobSource Updated')->log($jobSource->name . ' Job Source updated.');
        return $this->sendSuccess(__('messages.job_sources.saved') ?? 'Job Source updated successfully.');
    }
}
