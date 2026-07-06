<?php

namespace App\Queries;


use App\Models\Check;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class TagDataTable
 */
class CheckDataTable
{
    /**
     * @param  array  $input
     * @return Check
     */
    public function get($input = [])
    {
        /** @var Check $query */
        $query = Check::with(['branch','bank']);
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
        $checks = $query->get();

        // Add formatted_issue_date to each Check model in the collection
        return $checks->map(function ($check) {
            $check->formatted_date = $check->formatted_date; // Add formatted issue_date
            return $check;
        });
    }
}
