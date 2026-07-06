<?php

namespace App\Queries;

use App\Models\SalarySheet;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class TagDataTable
 */
class EmployeeSalaryDataTable
{

    public function get($input = [])
    {
        /** @var SalarySheet $query */
        $query = SalarySheet::with(['employee', 'employee.designation', 'salaryGenerate', 'salaryPayment', 'branch'])
            ->whereHas('salaryGenerate', function ($query) use ($input) {
                // Ensure salaryGenerate status is 1 and match the salary_month
                $query->where('status', 1);
                // $query->where('generated_by', auth()->id());
                // Filter by month if provided (format YYYY-MM)
                if (isset($input['month']) && $input['month']) {
                    $query->where('salary_month', $input['month']);
                }
            });

        $query  =  $query->when(!empty($input['filterBranch']), function ($q) use ($input) {
            // Filter by a specific branch if provided
            $q->where('branch_id', $input['filterBranch']);
        }, function ($q) {
            // Otherwise, filter by the user's associated branches
            $q->whereHas('branch', function ($branchQuery) {
                $branchQuery->whereIn('id', function ($subQuery) {
                    $subQuery->select('branch_id')
                        ->from('users_branches')
                        ->where('user_id', auth()->id());
                });
            });
        });


        // Filter by customer_id
        if (isset($input['customer_id']) && $input['customer_id']) {
            $query->where('customer_id', $input['customer_id']);
        }

        // Filter by project_id only if both customer_id and project_id are provided
        if (isset($input['customer_id']) && $input['customer_id'] && isset($input['project_id']) && $input['project_id']) {
            $query->where('project_id', $input['project_id']);
        }


        // Filter by department
        if (!empty($input['department'])) {
            $query->whereHas('employee', function ($q) use ($input) {
                $q->where('department_id', $input['department']);
            });
        }

        // Filter by designation
        if (!empty($input['department']) && !empty($input['designation'])) {
            $query->whereHas('employee', function ($q) use ($input) {
                $q->where('designation_id', $input['designation']);
            });
        }


        // Order by creation date (latest first)
        $query->orderBy('updated_at', 'desc');

        return $query->get();
    }
}
