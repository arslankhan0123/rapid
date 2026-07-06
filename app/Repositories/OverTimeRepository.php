<?php

namespace App\Repositories;


use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use App\Models\OverTime;
use App\Models\Employee;
use App\Models\OvertimeType;

/**
 * Class CustomerRepository
 *
 * @version April 3, 2020, 6:37 am UTC
 */
class OverTimeRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'employee_id',
        'amount',
        'overtime_type_id',
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
        return OverTime::class;
    }

    public function create($input)
    {

        return OverTime::create(Arr::only($input, [
            'employee_id',
            'amount',
            'overtime_type_id',
            'description'
        ]));
    }
    public function  getAllEmployees()
    {
        return  Employee::with(['department', 'subDepartment', 'designation'])->get();
    }
    public function getOvertimeTypes()
    {
        return  OvertimeType::pluck('name', 'id');
    }
}
