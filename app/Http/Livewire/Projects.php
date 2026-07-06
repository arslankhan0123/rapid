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

    public $customer = '';
    public $branchFilterID = '';


    /**
     * @return Application|Factory
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
     * @return LengthAwarePaginator
     */
    public function searchProjects()
    {
        $this->setQuery($this->getQuery()->with(['customer']));

        $this->getQuery()->where(function (Builder $query) {
            $this->filterResults();
        });

        $this->getQuery()->when($this->statusFilter !== '', function (Builder $q) {
            $q->where('status', $this->statusFilter);
        });

        $this->getQuery()->when($this->billingType !== '', function (Builder $q) {
            $q->where('billing_type', $this->billingType);
        });

        $this->getQuery()->when($this->customer !== '', function (Builder $q) {
            $q->where('customer_id', $this->customer);
        });

        $this->getQuery()->when($this->branchFilterID !== '', function (Builder $q) {
            // If branchFilterID is not empty, filter by branch_id
            $q->where('branch_id', $this->branchFilterID);
        }, function (Builder $q) {
            // If branchFilterID is empty, filter by user's branch_id
            $q->whereIn('branch_id', function ($query) {
                $query->select('branch_id')
                ->from('users_branches')
                ->where('user_id', auth()->id()); // Assuming you have the user_id in UsersBranches
            });
        });
        return $this->paginate();
    }

    /**
     * @param $projectId
     */
    public function deleteProject($projectId)
    {
        $project = Project::find($projectId);
        $project->load(['invoices']);
        if ($project->invoices->isNotEmpty()) {
           $this->dispatchBrowserEvent('deleted',false);
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

    public function filterProjectsByStatus($projectId)
    {
        $this->statusFilter = $projectId;
        $this->resetPage();
    }

    public function filterProjectsByBillingType($projectId)
    {
        $this->billingType = $projectId;
        $this->resetPage();
    }

    /**
     * @var string[]
     */
    protected $listeners = [
        'refresh' => '$refresh',
        'deleteProject',
        'filterProjectsByStatus',
        'filterProjectsByBillingType',
        'filterBranch'
    ];

    /**
     * @return string
     */
    public function model()
    {
        return Project::class;
    }

    /**
     * @return string[]
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
     * @return Builder
     */
    public function filterResults()
    {
        $searchableFields = $this->searchableFields();
        $search = $this->search;

        $this->getQuery()->when(! empty($search), function (Builder $q) use ($search, $searchableFields) {
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
    public function filterBranch($id)
    {
        $this->branchFilterID = $id;
        $this->resetPage();
    }
}
