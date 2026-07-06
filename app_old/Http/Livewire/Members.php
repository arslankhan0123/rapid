<?php

namespace App\Http\Livewire;

use App\Models\User;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\Factory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class Members extends SearchableComponent
{
    public $role = null;

    public $status = null;

    public $statusFilter = '';

    /**
     * @var string[]
     */
    protected $listeners = [
        'refresh' => '$refresh', 'filterStatus',
    ];

    /**
     * @return Application|Factory
     */
    public function render()
    {
        $members = $this->searchUser($this->role);
        $memberStatus = User::STATUS_ARR;

        return view('livewire.members', [
            'members' => $members, 'memberStatus' => $memberStatus,
        ])->with('search');
    }

    /**
     * @param  string  $role
     * @return LengthAwarePaginator
     */
    public function searchUser($role)
    {
        $this->setQuery($this->getQuery()->with(['roles', 'media'])->where('owner_id', null)->where('owner_type',
            null)->orderByDesc('is_enable')->withCount('projects'));

        $this->getQuery()->where(function (Builder $query) {
            $this->filterResults();
        });

        $this->getQuery()->when($this->statusFilter != '', function (Builder $q) {
            $q->where('is_enable', '=', $this->statusFilter);
        });

        if (getLoggedInUser()->hasRole('staff_member')) {
            $this->getQuery()->where('is_admin', '!=', true);
        }

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
                            $query->WhereRaw("lower($field[1]) like ?", $searchString);
                        });
                        $q->where('owner_id', null)->where('owner_type', null);
                    } else {
                        $q->orWhereRaw("lower($field) like ?", $searchString);
                    }
                }
            });
        });

        return $this->getQuery();
    }

    /**
     * @return string[]
     */
    public function searchableFields()
    {
        return [
            'first_name',
            'email',
            'roles.name',
        ];
    }

    /**
     * @return string
     */
    public function model()
    {
        return User::class;
    }

    /**
     * @param $status
     */
    public function filterStatus($status)
    {
        $this->statusFilter = $status;
        $this->resetPage();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }
}
