<?php

namespace App\Queries;

use App\Models\SalaryAdvance;

/**
 * Class TagDataTable
 */
class SalaryAdvanceDataTable
{
    /**
     * @param  array  $input
     * @return SalaryAdvance
     */
    public function get($input = [])
    {
        /** @var SalaryAdvance $query */
        $query = SalaryAdvance::with(['employee', 'permittedBy', 'employee.branch'])->orderBy('created_at', 'desc');

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

        if (!empty($input['account'])) {
            $query->where('account_id', $input['account']);
        }

        if (!empty($input['start_date'])) {
            $query->where('date', '>=', $input['start_date']);
        }

        if (!empty($input['end_date'])) {
            $query->where('date', '<=', $input['end_date']);
        }

        if (!empty($input['month'])) {
            $query->whereMonth('date', date('m', strtotime($input['month'])));
        }
       return $query->orderBy('created_at', 'desc')->get();
    }
}
