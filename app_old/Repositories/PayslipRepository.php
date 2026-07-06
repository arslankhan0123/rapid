<?php

namespace App\Repositories;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use App\Models\Attendance;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\Customer;
use App\Models\Project;
use App\Models\SalarySheet;
use App\Models\Department;
use App\Models\Designation;

/**
 * Class CustomerRepository
 *
 * @version April 3, 2020, 6:37 am UTC
 */
class PayslipRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'employee_id',
        'customer_id',
        'project_id',

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
        return SalarySheet::class;
    }


    public function getCustomers()
    {
        return  Customer::pluck('company_name', 'id'); // Retrieves departments as key-value pairs
    }
    public function getProjects()
    {
        return  Project::get(); // Retrieves departments as key-value pairs
    }

    public function deleteSalaryData() {}

    public function getDepartments()
    {
        return Department::pluck('name', 'id')->toArray();
    }
    public function getDesignation()
    {
        return Designation::select(['name', 'id', 'sub_department_id', 'department_id'])->get();
    }
}
