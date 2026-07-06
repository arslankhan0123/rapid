<?php

namespace App\Http\Livewire;

use App\Models\Project;
use App\Repositories\ProjectRepository;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\Factory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class Projects extends SearchableComponent
{
    public $statusFilter = '';
    public $billingType = '';
    public $customer = ''; // 👈 Already declared
    public $branchFilterID = '';

    /**
     * @var string[]
     */
    protected $listeners = [
        'refresh' => '$refresh',
        'deleteProject',
        'filterProjectsByStatus',
        'filterProjectsByBillingType',
        'filterBranch',
        'filterByCustomer', // 👈 New event listener for customer filter
    ];

    /**
     * Render the projects list with applied filters
     */
    public function render()
    {
        $projects = $this->searchProjects();
        $projectRepo = app(ProjectRepository::class);

        $data['statusCount'] = $projectRepo->getProjectsStatusCount($this->customer);
        $data['search'] = '';
        $data['customer'] = $this->customer;
        $data['projectStatusArr'] = Project::STATUS;

        return view('livewire.projects', [
            'projects' => $projects,
        ])->with($data);
    }

    /**
     * Main project query with filters
     */
    public function searchProjects(): LengthAwarePaginator
    {
        $this->setQuery($this->getQuery()->with(['customer']));

        $this->getQuery()->where(function (Builder $query) {
            $this->filterResults();
        });

        // ✅ Filter by Status
        $this->getQuery()->when($this->statusFilter !== '', function (Builder $q) {
            $q->where('status', $this->statusFilter);
        });

        // ✅ Filter by Billing Type
        $this->getQuery()->when($this->billingType !== '', function (Builder $q) {
            $q->where('billing_type', $this->billingType);
        });

        // ✅ Filter by Customer
        $this->getQuery()->when($this->customer !== '', function (Builder $q) {
            $q->where('customer_id', $this->customer);
        });

        // ✅ Filter by Branch (user’s branch or selected branch)
        $this->getQuery()->when($this->branchFilterID !== '', function (Builder $q) {
            $q->where('branch_id', $this->branchFilterID);
        }, function (Builder $q) {
            $q->whereIn('branch_id', function ($query) {
                $query->select('branch_id')
                    ->from('users_branches')
                    ->where('user_id', auth()->id());
            });
        });

        return $this->paginate();
    }

    /**
     * Delete a project (with invoice check)
     */
    public function deleteProject($projectId)
    {
        $project = Project::find($projectId);
        $project->load(['invoices']);

        if ($project->invoices->isNotEmpty()) {
            $this->dispatchBrowserEvent('deleted', false);
            return false;
        }

        activity()->performedOn($project)->causedBy(getLoggedInUser())
            ->useLog('Project deleted.')->log($project->project_name . ' Project deleted.');

        $project->delete();
        $project->members()->delete();
        $project->services()->delete();
        $project->terms()->delete();

        $this->dispatchBrowserEvent('deleted');
        $this->searchProjects();
    }

    /**
     * Status filter
     */
    public function filterProjectsByStatus($projectId)
    {
        $this->statusFilter = $projectId;
        $this->resetPage();
    }

    /**
     * Billing Type filter
     */
    public function filterProjectsByBillingType($projectId)
    {
        $this->billingType = $projectId;
        $this->resetPage();
    }

    /**
     * Branch filter
     */
    public function filterBranch($id)
    {
        $this->branchFilterID = $id;
        $this->resetPage();
    }

    /**
     * 👇 New Customer filter method
     */
    public function filterByCustomer($customerId)
    {
        $this->customer = $customerId;
        $this->resetPage();
    }

    /**
     * Model definition
     */
    public function model()
    {
        return Project::class;
    }

    /**
     * Fields to search in
     */
    public function searchableFields()
    {
        return [
            'project_name',
            'customer.company_name',
            'branch_id',
        ];
    }

    /**
     * Search logic
     */
    public function filterResults()
    {
        $searchableFields = $this->searchableFields();
        $search = $this->search;

        $this->getQuery()->when(!empty($search), function (Builder $q) use ($search, $searchableFields) {
            $this->getQuery()->where(function (Builder $q) use ($search, $searchableFields) {
                $searchString = '%' . $search . '%';
                foreach ($searchableFields as $field) {
                    if (Str::contains($field, '.')) {
                        $field = explode('.', $field);
                        $q->orWhereHas($field[0], function (Builder $query) use ($field, $searchString) {
                            $query->whereRaw("lower($field[1]) like ?", $searchString);
                        });
                    } else {
                        $q->orWhereRaw("lower($field) like ?", $searchString);
                    }
                }
            });
        });

        return $this->getQuery();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }
}
