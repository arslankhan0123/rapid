<?php

namespace App\Queries;

use App\Models\Transfer;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class TagDataTable
 */
class TransferDataTable
{
    /**
     * @param  array  $input
     * @return Transfer
     */
    public function get($input = [])
    {
        /** @var Transfer $query */
        $query = Transfer::with(['employee', 'fromBranch', 'toBranch', 'branch'])->orderBy('created_at', 'desc');
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
        return $query->get();
    }
}
