<?php

namespace App\Repositories;

use App\Models\Product;
use App\Models\ProductGroup;
use App\Models\Project;
use App\Models\TaxRate;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use App\Models\Customer;
use App\Models\Employee;
use App\Models\Department;
use App\Models\Designation;
use App\Models\SalaryGenerate;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Class ProductRepository
 *
 * @version October 12, 2021, 10:50 am UTC
 */
class ManageAttendanceRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'title',

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
        return Product::class;
    }

    /**
     * @return mixed
     */
    public function getSyncListForItem()
    {
        $taxes = [];

        $taxRates = TaxRate::get();

        foreach ($taxRates as $tax) {
            $taxes[$tax->id] = $tax->tax_rate . '%';
        }

        $data['taxes'] = $taxes;
        $data['itemGroups'] = ProductGroup::orderBy('name', 'asc')->pluck('name', 'id')->toArray();

        return $data;
    }

    public function getCustomers()
    {
        return Customer::pluck('company_name', 'id'); // Retrieves departments as key-value pairs
    }
    public function getProjects()
    {
        return Project::get(); // Retrieves departments as key-value pairs
    }
    public function getAllEmployees()
    {
        return Employee::where('status', 1)->select('id', 'name', 'iqama_no')->get();
    }
    public function allEmployees()
    {
        return Employee::where('status', 1)->orderBy('name')->get();
    }

    public function salaryEmployees($month = null, $branchId = null)
    {
        $query = Employee::query();

        if ($month && $branchId) {
            $startOfMonth = $month . '-01';
            $endOfMonth = date('Y-m-t', strtotime($startOfMonth));

            // Get employee IDs with salary for this month+branch
            $employeeIdsWithSalary = DB::table('salary_sheets')
                ->select('salary_sheets.employee_id')
                ->join('salary_generates', 'salary_sheets.salary_generate_id', '=', 'salary_generates.id')
                ->where('salary_generates.salary_month', $month)
                ->where('salary_generates.branch_id', $branchId)
                ->groupBy('salary_sheets.employee_id')
                ->pluck('employee_id')
                ->toArray();

            // **UPDATED: Termination logic FIRST, then employee status**
            $query->where(function ($q) use ($startOfMonth, $endOfMonth, $employeeIdsWithSalary) {
                // **GROUP 1: Terminated employees (don't check employee.status)**
                $q->where(function ($terminationQuery) use ($startOfMonth) {
                    $terminationQuery->whereHas('termination', function ($t) use ($startOfMonth) {
                        $t->where('date', '>=', $startOfMonth);
                    });
                })
                    // **GROUP 2: Non-terminated employees (check employee.status OR salary)**
                    ->orWhere(function ($nonTerminationQuery) use ($employeeIdsWithSalary) {
                        $nonTerminationQuery->whereDoesntHave('termination')
                            ->where(function ($statusQuery) use ($employeeIdsWithSalary) {
                                if (!empty($employeeIdsWithSalary)) {
                                    $statusQuery->where('status', 1)
                                        ->orWhereIn('id', $employeeIdsWithSalary);
                                } else {
                                    $statusQuery->where('status', 1);
                                }
                            });
                    });
            });

            if ($branchId) {
                $query->where('branch_id', $branchId);
            }
        } else {
            // No month selected: show only active employees who are not terminated
            $query->where('status', 1)
                ->where(function ($q) {
                    $q->whereDoesntHave('termination')
                        ->orWhereHas('termination', function ($terminationQuery) {
                            $terminationQuery->where('date', '>', Carbon::today());
                        });
                });
        }

        return $query->orderBy('name')->get();
    }
    public function getDepartments()
    {
        return Department::pluck('name', 'id')->toArray();
    }

    public function getDesignation()
    {
        return Designation::select(['name', 'id', 'sub_department_id', 'department_id'])->orderBy('name')->get();
    }
    public function salaryStatus($month, $branch_id)
    {
        // Get the salary records where the salary_month matches the provided month
        $salaryRecords = SalaryGenerate::where('salary_month', $month)
            ->where('branch_id', $branch_id)
            ->where('status', 1)
            ->get();
        // Check if any records are found
        if ($salaryRecords->isNotEmpty()) {
            // If records are found, return true
            return true;
        }

        // If no matching records found, return false
        return false;
    }
}
