<?php

namespace App\Repositories;

use App\Models\Comment;

use App\Models\TaskAssign;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use App\Models\Department;
use App\Models\Designation;
use App\Models\Employee;

/**
 * Class TaskRepository
 *
 * @version April 13, 2020, 10:21 am UTC
 */
class TaskAssignRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'name',
        'description',
        'department_id',
        'designation_id',
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
        return TaskAssign::class;
    }

    public function create($input)
    {
        return TaskAssign::create(Arr::only($input, $this->getFieldsSearchable()));
    }
    public function getDepartments()
    {
        return  Department::pluck('name', 'id'); // Retrieves departments as key-value pairs
    }
    public function getDesignations()
    {
        return  Designation::all(); // Retrieves departments as key-value pairs
    }
    public function getEmployees()
    {
        return  Employee::all(); // Retrieves departments as key-value pairs
    }
}
