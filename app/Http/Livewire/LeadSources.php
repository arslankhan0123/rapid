<?php

namespace App\Http\Livewire;

use App\Models\LeadSource;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\Factory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class LeadSources extends SearchableComponent
{
    /**
     * @var string[]
     */
    protected $listeners = [
        'refresh' => '$refresh',
    ];

    /**
     * @return string
     */
    public function model()
    {
        return LeadSource::class;
    }

    /**
     * @return string[]
     */
    public function searchableFields()
    {
        return ['name'];
    }

    /**
     * @return Application|Factory
     */
    public function render()
    {
        $leadSources = $this->searchLeadSources();

        return view('livewire.lead-sources', compact('leadSources'));
    }

    /**
     * @return LengthAwarePaginator
     */
    public function searchLeadSources()
    {
        $query = $this->getQuery()->withCount('usedLeadSource');

        $query->where(function (Builder $query) {
            $this->filterResults();
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
                $searchString = '%'.$search.'%';
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
