<?php

namespace App\Http\Controllers\Clients;

use App\Http\Controllers\AppBaseController;
use App\Models\Project;
use App\Repositories\ProjectRepository;
use Illuminate\Contracts\View\Factory;
use Illuminate\View\View;

/**
 * Class ProjectController
 */
class ProjectController extends AppBaseController
{
    /**
     * @var ProjectRepository
     */
    private $projectRepository;

    public function __construct(ProjectRepository $projectRepository)
    {
        $this->projectRepository = $projectRepository;
    }

    /**
     * Display a listing of the Project.
     *
     * @return Factory|View
     */
    public function index()
    {
        $data['statusArr'] = Project::STATUS;
        $data['billingType'] = Project::BILLING_TYPES;

        return view('clients.projects.index', $data);
    }

    /**
     * Display the specified Project.
     *
     * @param  Project  $project
     * @return Factory|View
     */
    public function show(Project $project)
    {
        $project = $this->projectRepository->getProjectDetailClient($project->id);
        $groupName = (request('group') == null) ? 'project_details' : (request('group'));

        return view("clients.projects.views.$groupName", compact('project', 'groupName'));
    }
}
