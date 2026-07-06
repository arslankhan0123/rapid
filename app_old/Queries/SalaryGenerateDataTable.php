<?php

namespace App\Queries;

use App\Models\SalaryGenerate;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class TagDataTable
 */
class SalaryGenerateDataTable
{
    /**
     * @param  array  $input
     * @return SalaryGenerate
     */
    public function get($input = [])
    {
        /** @var SalaryGenerate $query */
        $query = SalaryGenerate::with(['generatedBy', 'approvedBy', 'branch'])
            // ->whereHas('generatedBy', function ($q) {
            //     $q->where('generated_by', auth()->id()); // Ensure generatedBy's ID matches the logged-in user's ID
            // })
            ->orderBy('created_at', 'desc');

        $query = $query->where('status', 1);


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
        return $query->get();
    }
}
