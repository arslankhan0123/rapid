<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Models\Contact;
use App\Models\Project;
use App\Models\Task;
use App\Repositories\ProjectRepository;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Redirect;
use Laracasts\Flash\Flash;
use App\Repositories\CustomerRepository;
use App\Models\Term;

class ProjectController extends AppBaseController
{
    /** @var ProjectRepository */
    private $projectRepository;
    private $customerRepository;

    public function __construct(ProjectRepository $projectRepo, CustomerRepository $customerRepo)
    {
        $this->projectRepository = $projectRepo;
        $this->customerRepository = $customerRepo;
    }

    /**
     * Display a listing of the Project.
     *
     * @return Application|Factory|View
     */
    public function index()
    {
        $data['statusArr'] = Project::STATUS;
        $data['billingType'] = Project::BILLING_TYPES;

        $usersBranches = $this->getUsersBranches();

        $customers = $this->customerRepository->getCustomersList();

        return view('projects.index', $data, compact('usersBranches', 'customers'));
    }

    /**
     * Show the form for creating a new Project.
     *
     * @param  null  $customerId
     * @return Application|Factory|View
     */
    public function create($customerId = null)
    {
        $data = $this->projectRepository->getSyncList();

        $states = $this->customerRepository->getStates();
        $currencies = $this->customerRepository->getCurrencies();
        $services = $this->projectRepository->getServices();
        $categories = $this->projectRepository->getServiceCategories();
        $terms = $this->projectRepository->getTerms();
        $usersBranches = $this->getUsersBranches();
        return view('projects.create', compact('data', 'customerId', 'states', 'currencies', 'services', 'categories', 'terms', 'usersBranches'));
    }

    public function customerList()
    {
        $list = $this->projectRepository->getCustomers();
        return response()->json($list);
        // return $this->sendResponse($list, 'customer List');
    }

    /**
     * Store a newly created Project in storage.
     *
     * @param  CreateProjectRequest  $request
     * @return Application|RedirectResponse|Redirector
     */
    public function store(CreateProjectRequest $request)
    {

        try {
            DB::beginTransaction();
            $input = $request->all();


            $this->projectRepository->saveProject($input);
            DB::commit();
            Flash::success(__('messages.project.project_saved_successfully'));
            return redirect(route('projects.index'));
        } catch (Exception $e) {
            DB::rollBack();

            return Redirect::back()->withInput()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Display the specified Project.
     *
     * @param  Project  $project
     * @return Application|Factory
     */
    // public function show(Project $project)
    // {


    //     $project = $this->projectRepository->getProjectDetails($project->id);


    //     $status = Task::STATUS;
    //     $priorities = Task::PRIORITY;
    //     $project->load('services.categories');
    //     $groupName = (request('group') == null) ? 'project_details' : (request('group'));
    //     return view("projects.views.$groupName", compact('project', 'status', 'priorities', 'groupName'));
    // }
    
     public function show(Project $project)
    {
        $project = $this->projectRepository->getProjectDetails($project->id);
        $status = Task::STATUS;
        $priorities = Task::PRIORITY;

        // Get services and categories like in edit method
        $services = $this->projectRepository->getServices();
        $categories = $this->projectRepository->getServiceCategories();

        $groupName = (request('group') == null) ? 'project_details' : (request('group'));
        return view("projects.views.$groupName", compact('project', 'status', 'priorities', 'groupName', 'services', 'categories'));
    }

    /**
     * Show the form for editing the specified Project.
     *
     * @param  Project  $project
     * @return Application|Factory|\Illuminate\Contracts\View\View|RedirectResponse
     */
    public function edit(Project $project)
    {
        $project->load(['members', 'customer', 'terms', 'branch']);
        // dd($project->toArray());


        if ($project->status == Project::STATUS_CANCELLED) {
            return redirect()->back();
        }
        $states = $this->customerRepository->getStates();
        $currencies = $this->customerRepository->getCurrencies();
        $data = $this->projectRepository->getSyncList();
        $project = $this->projectRepository->getProjectData($project->id);
        $data['projectContacts'] = $project->projectContacts()->pluck('contact_id')->toArray();
        $services = $this->projectRepository->getServices();
        $categories = $this->projectRepository->getServiceCategories();
        $project->load(['services', 'services.categories']);
        $terms = $this->projectRepository->getTerms();
        $usersBranches = $this->getUsersBranches();
        return view('projects.edit', compact('data', 'project', 'states', 'currencies', 'services', 'categories', 'terms', 'usersBranches'));
    }

    /**
     * Update the specified Project in storage.
     *
     * @param  Project  $project
     * @param  UpdateProjectRequest  $request
     * @return Application|RedirectResponse|Redirector
     */
    public function update(Project $project, UpdateProjectRequest $request)
    {
        $input = $request->all();

        $this->projectRepository->updateProject($project->id, $input);

        Flash::success(__('messages.project.project_updated_successfully'));

        return redirect(route('projects.index'));
    }

    /**
     * @param  Request  $request
     * @return mixed
     */
    public function memberAsPerCustomer(Request $request)
    {
        /** @var Contact $contact */
        $contact = Contact::with('user')->whereCustomerId($request->get('customer_id'))->get();
        $members = $contact->where('user.is_enable', '=', true)->pluck('user.full_name', 'id')->toArray();

        return $this->sendResponse($members, 'member retrieved data success.');
    }
}
