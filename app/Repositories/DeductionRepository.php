<?php

namespace App\Repositories;

use App\Models\AssetCategory;
use App\Models\ProductUnit;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use App\Models\Deduction;
use App\Models\Employee;
use App\Models\DeductionType;

/**
 * Class CustomerRepository
 *
 * @version April 3, 2020, 6:37 am UTC
 */
class DeductionRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'employee_id',
        'amount',
        'description',
        'deduction_type_id',
        'month',
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
        return Deduction::class;
    }

    public function create($input)
    {

        return Deduction::create(Arr::only($input, [
            'employee_id',
            'amount',
            'description',
            'deduction_type_id',
            'month',
            'date',
            'posted'
        ]));
    }

    public function  getAllEmployees()
    {
        return  Employee::with(['department', 'subDepartment','designation', 'branch'])->where('status', 1)->get();
    }
    public function getDeductionTypes()
    {
        return  DeductionType::pluck('name', 'id');
    }
}
