<?php

namespace App\Repositories;

use App\Models\AssetCategory;
use App\Models\ProductUnit;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use App\Models\State;
use App\Models\Country;
use App\Models\City;

/**
 * Class CustomerRepository
 *
 * @version April 3, 2020, 6:37 am UTC
 */
class StateRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'name',
        'country_id',
        'description'
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
        return State::class;
    }

    public function create($input)
    {
        return State::create(Arr::only($input, ['name','country_id','description']));
    }
    public function getCountries(){
        return Country::pluck('name','id');
    }

}
