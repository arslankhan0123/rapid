<?php

namespace App\Queries;


use App\Models\Asset;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class TagDataTable
 */
class AssetDataTable
{
    /**
     * @param  array  $input
     * @return Asset
     */
    public function get($input = [])
    {
        /** @var Asset $query */
        $query = Asset::with(['category', 'employee', 'branch']);
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
