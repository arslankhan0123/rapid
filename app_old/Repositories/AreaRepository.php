<?php

namespace App\Repositories;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use App\Models\Area;
use App\Models\Country;
use App\Models\City;
use App\Models\State;

/**
 * Class CustomerRepository
 *
 * @version April 3, 2020, 6:37 am UTC
 */
class AreaRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'name',
        'country_id',
        'city_id',
        'state_id',
        'description',

    ];

    /**
     * Return searchable fields
     *
     * @return array
     */
    public function getFieldsSearchable()
    {
        return $this->fieldSearchable;
    }

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Area::class;
    }

    public function create($input)
    {
        return Area::create(Arr::only($input, ['name', 'country_id', 'city_id', 'state_id', 'description']));
    }

    public function getCountries()
    {
        return Country::pluck('name', 'id');
    }
    public function getCities()
    {
        return City::get();
    }
    public function getStates()
    {
        return State::get();
    }
}
