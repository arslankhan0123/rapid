<?php

namespace App\Repositories;

use App\Models\AssetCategory;
use App\Models\ProductUnit;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use App\Models\Allowance;
use App\Models\Employee;
use App\Models\AllowanceType;

/**
 * Class CustomerRepository
 *
 * @version April 3, 2020, 6:37 am UTC
 */
class AllowanceRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'employee_id',
        'allowance_type_id',
        'amount',
        'description',
        'date',
        'payment_type',
        'monthly_allowance',
        'hourly_allowance',
        'worked_hours',
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
        return Allowance::class;
    }

    public function create($input)
    {

        return Allowance::create(Arr::only($input, [
            'employee_id',
            'allowance_type_id',
            'amount',
            'description',
            'date',
            'payment_type',
            'monthly_allowance',
            'hourly_allowance',
            'worked_hours',
        ]));
    }
    public function  getAllEmployees()
    {
        return  Employee::with(['department', 'subDepartment','designation','branch'])->where('status', 1)->get();
    }
    public function getAllowanceTypes()
    {
        return  AllowanceType::pluck('name', 'id');
    }
}
