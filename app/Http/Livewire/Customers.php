<?php

namespace App\Http\Livewire;

use App\Models\Customer;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;

class Customers extends SearchableComponent
{
    public $searchByCustomer = '';

    public $paginate = 15;

    /**
     * @return string[]
     */
    public function searchableFields()
    {
        return [
            'company_name',
        ];
    }

    /**
     * @return string
     */
    public function model()
    {
        return Customer::class;
    }

    /**
     * @var string[]
     */
    protected $listeners = [
        'refresh' => '$refresh',
    ];

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|View
     */
    public function render()
    {
        $customers = $this->searchCustomer();

        return view('livewire.customers', compact('customers'))->with('searchByCustomer');
    }

    /**
     * @return LengthAwarePaginator
     */
    public function searchCustomer()
    {
        $query = $this->getQuery()->withCount(['contact', 'project', 'customerGroups'])->latest();

        $query->when(! empty($this->searchByCustomer != ''), function (Builder $query) {
            $query->Where('company_name', 'like', '%'.strtolower($this->searchByCustomer).'%');
        });

        return $this->paginate();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }
}
