<?php

namespace App\Repositories;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use App\Models\Insurance;
use App\Models\Employee;

/**
 * Class CustomerRepository
 *
 * @version April 3, 2020, 6:37 am UTC
 */
class InsuranceRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'desciption',
        'employee_id'
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
        return Insurance::class;
    }

    public function create($input)
    {
        return Insurance::create(Arr::only($input, [
            'employee_id',
            'insurance',
            'description',
            'date', 
            'insurance_type',
            'monthly_insurance',
            'hourly_insurance',
            'worked_hours',
        ]));
    }
    public function getEmplyee()
    {
        return  Employee::pluck('name', 'id'); // Retrieves departments as key-value pairs
    }
    public function  getAllEmployees()
    {
        return  Employee::with(['department', 'subDepartment', 'designation','branch'])->where('insurance_apply', 1)->where('status', 1)->get();
    }
}
