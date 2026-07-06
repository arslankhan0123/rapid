<?php

namespace App\Queries;

use App\Models\Employee;


/**
 * Class TagDataTable
 */
class EmployeeDataTable
{

    public function get($input = [])
    {
        /** @var Employee $query */
        $query = Employee::with(['department', 'subDepartment', 'designation', 'branch'])
            ->orderBy('updated_at', 'desc');
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
        // Apply the status filter directly if it's provided
        if (isset($input['status']) && ($input['status'] === '0' || $input['status'] === '1')) {
            $query->where('status', $input['status']);
        }
        $query->orderBy('updated_at', 'desc');
        //  dd($query->toSql(), $query->getBindings());
        return $query->get();
    }
}
