<?php

namespace App\Http\Livewire;

use App\Models\Lead;
use App\Repositories\LeadRepository;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\Factory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use App\Models\User;

class Leads extends SearchableComponent
{
    public $search = '';

    public $statusFilter = '';

    public $leadSourceFilter = '';

    /**
     * @var string[]
     */
    protected $listeners = [
        'refresh' => '$refresh',
        'filterStatus',
        'filterLeadSource',
    ];

    /**
     * @return Application|Factory
     */
    public function render()
    {
        $leads = $this->search();
        $leadRepo = app(LeadRepository::class);
        $data = $leadRepo->getLeadStatusCounts();
        return view('livewire.leads', ['leads' => $leads], compact('data'))->with('search');
    }

    /**
     * @return LengthAwarePaginator
     */
    public function search()
    {
        $query = $this->getQuery()->with(['leadSource', 'leadStatus', 'assignedTo.media']);

        if (!auth()->user()->is_admin ) {
            $query->where('inserted_by', auth()->user()->id);
        }
        $this->setQuery($query->orderByDesc('name'));

        $this->getQuery()->where(function (Builder $query) {
            $this->filterResults();
        });

        $this->getQuery()->when($this->statusFilter !== '', function (Builder $q) {
            $q->where('status_id', $this->statusFilter);
        });

        $this->getQuery()->when($this->leadSourceFilter !== '', function (Builder $q) {
            $q->where('source_id', $this->leadSourceFilter);
        });


        return $this->paginate();
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

    /**
     * @param  int  $id
     */
    public function filterStatus($id)
    {
        $this->statusFilter = $id;
        $this->resetPage();
    }

    /**
     * @param $id
     */
    public function filterLeadSource($id)
    {
        $this->leadSourceFilter = $id;
        $this->resetPage();
    }

    /**
     * @return string
     */
    public function model()
    {
        return Lead::class;
    }

    /**
     * @return string[]
     */
    public function searchableFields()
    {
        return [
            'name'
        ];
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }
}
