<?php

namespace App\Queries;

use App\Models\TaskStatus;
use Illuminate\Database\Eloquent\Builder;


/**
 * Class TagDataTable
 */
class TaskStatusDataTable
{

    public function get($input = [])
    {
        /** @var TaskStatus $query */
        $query = TaskStatus::with(['user', 'project', 'customer', 'branch']);

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
        // Check if the authenticated user is an admin
        if (auth()->user()->is_admin) {
            // If admin, filter based on input parameters
            if (!empty($input['start_date'])) {
                $query->where('date', '>=', $input['start_date']);
            }

            if (!empty($input['end_date'])) {
                $query->where('date', '<=', $input['end_date']);
            }

            if (!empty($input['month'])) {
                // Assuming you want to filter by month in the format 'Y-m'
                $query->whereYear('date', date('Y', strtotime($input['month'])))
                    ->whereMonth('date', date('m', strtotime($input['month'])));
            }

            if (!empty($input['user_id'])) {
                $query->where('user_id', $input['user_id']);
            }

            // Get records after filtering
            $query = $query->orderBy('id', 'desc')->get();
        } else {
            // If not admin, get records by the user's ID
            $query = $query->where('user_id', auth()->user()->id)
                ->orderBy('id', 'desc');
        }

        return $query;
    }
}
