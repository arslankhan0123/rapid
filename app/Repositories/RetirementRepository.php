<?php

namespace App\Repositories;

use App\Models\AssetCategory;
use App\Models\ProductUnit;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use App\Models\SubDepartment;
use App\Models\Retirement;
use App\Models\Employee;

/**
 * Class CustomerRepository
 *
 * @version April 3, 2020, 6:37 am UTC
 */
class RetirementRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'name',
        'description',
        'employee_id',
        'date'
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
        return Retirement::class;
    }

    public function create($input)
    {
        return Retirement::create(Arr::only($input, ['name', 'description', 'employee_id', 'status','date']));
    }
    public function getEmplyee()
    {
        return  Employee::with(['department', 'subDepartment', 'designation','branch'])->get();
    }

}
