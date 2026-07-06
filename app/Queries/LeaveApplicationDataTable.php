<?php

namespace App\Queries;


use App\Models\LeaveApplication;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class TagDataTable
 */
class LeaveApplicationDataTable
{
    /**
     * @param  array  $input
     * @return LeaveApplication
     */
    public function get(array $input = [])
    {
        // Initialize the query with necessary relationships
        $query = LeaveApplication::with(['leave', 'employee', 'branch']);

        // Apply filtering based on the input or user's associated branches
        $query->when(
            !empty($input['filterBranch']),
            function ($q) use ($input) {
                // Filter by the specific branch if provided
                $q->where('branch_id', $input['filterBranch']);

            },
            function ($q) {
                // Otherwise, filter by the user's associated branches
                $q->whereHas('branch', function ($branchQuery) {
                    $branchQuery->whereIn('id', function ($subQuery) {
                        $subQuery->select('branch_id')
                            ->from('users_branches')
                            ->where('user_id', auth()->id());
                    });
                });
            }
        );

        // Apply sorting and get results
        return $query->get();
    }
}
