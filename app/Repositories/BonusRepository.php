<?php

namespace App\Repositories;

use App\Models\AssetCategory;
use App\Models\ProductUnit;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use App\Models\Bonus;
use App\Models\Employee;
use App\Models\BonusType;


/**
 * Class CustomerRepository
 *
 * @version April 3, 2020, 6:37 am UTC
 */
class BonusRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'employee_id',
        'amount',
        'bonus_type_id',
        'description',
        'date',
        'posted'
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
        return Bonus::class;
    }

    public function create($input)
    {
        return Bonus::create(Arr::only($input, [
            'employee_id',
            'amount',
            'bonus_type_id',
            'description',
            'date',
            'posted'
        ]));
    }

    public function  getAllEmployees()
    {
        return  Employee::with(['department', 'subDepartment', 'designation','branch'])->where('status', 1)->get();
    }
    public function getAllowanceTypes()
    {
        return  BonusType::pluck('name', 'id');
    }
}
