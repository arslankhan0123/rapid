<?php

namespace App\Queries;

use App\Models\Account;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class TagDataTable
 */
class AccountDataTable
{
    /**
     * @param  array  $input
     * @return Account;
     */
    public function get($input = [])
    {
        /** @var Account $query */
        $query = Account::with('branch', 'receivedBy', 'from', 'to')->orderBy('created_at', 'desc');
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

        if (!empty($input['account_name'])) {
            $query->where('account_name', 'like', '%' . $input['account_name'] . '%');
        }
        return $query->get();
    }
}
