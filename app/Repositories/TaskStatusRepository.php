<?php

namespace App\Repositories;

use App\Models\AssetCategory;
use App\Models\ProductUnit;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use App\Models\TaskStatus;
use App\Models\Project;
use App\Models\Customer;
use App\Models\Employee;

/**
 * Class CustomerRepository
 *
 * @version April 3, 2020, 6:37 am UTC
 */
class TaskStatusRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'user_id',
        'date',
        'task',
        'description',
        'duration',
        'project_id',
        'project_name',
        'customer_id',
        'branch_id',
        'task_distribution'
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
        return TaskStatus::class;
    }

    public function create($input)
    {
        return TaskStatus::create(Arr::only($input, $this->getFieldsSearchable()));
    }
    public function getCustomers(){
        return Customer::pluck('company_name','id');
    }
    public function getProjects(){
        return Project::orderBy('updated_at','desc')->get();
    }

    public function  getAllEmployees()
    {
        return  Employee::with(['department', 'subDepartment', 'designation'])->get();
    }
}
