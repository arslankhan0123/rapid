<?php

namespace App\Http\Livewire;

use App\Models\Country;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\Factory;

class Countries extends SearchableComponent
{
    public $paginate = 15;

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
        return Country::class;
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
        $countries = $this->searchCountry();

        return view('livewire.countries', compact('countries'));
    }

    /**
     * @return LengthAwarePaginator
     */
    public function searchCountry()
    {
        $this->setQuery($this->getQuery());

        return $this->paginate();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }
}
