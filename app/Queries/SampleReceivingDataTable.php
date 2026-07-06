<?php

namespace App\Queries;

use App\Models\SampleReceiving;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

/**
 * Class SampleCategoryDataTable
 */
class SampleReceivingDataTable
{
    /**
     * @param  array  $input
     * @return Builder
     */
    public function get($input = []): Builder
    {
        $query = SampleReceiving::query()->with('branch', 'deliveredBy', 'receivedBy', 'category')
            ->select([
                'id',
                DB::raw("DATE_FORMAT(date, '%d-%b-%Y') as formatted_date"),
                'time',
                'section',
                'client_name',
                'client_reference',
                'type_of_sample',
                'required_tests',
                'number_of_sample',
                'delivered_by',
                'received_by',
                'updated_at',
                'branch_id',


            ]);

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

        // Apply filter for group if present
        if (!empty($input['group'])) {
            $query->where('group', $input['group']);
        }

        // List of allowed columns for sorting
        $allowedColumns = [
            'id',
            'date',
            'time',
            'section',
            'client_name',
            'client_reference',
            'type_of_sample',
            'required_tests',
            'number_of_sample',
            'delivered_by',
            'received_by',
            'updated_at',
            'branch',
            'branch_id',
        ];

        // Handle dynamic sorting
        if (!empty($input['order']) && isset($input['columns'])) {
            $columnIndex = $input['order'][0]['column'];
            $sortDirection = $input['order'][0]['dir'] ?? 'desc';

            // Get column name from the columns array and validate it
            $columnName = $input['columns'][$columnIndex]['data'] ?? 'updated_at';
            $columnName = in_array($columnName, $allowedColumns) ? $columnName : 'updated_at';

            // Apply sorting to query
            $query->orderBy($columnName, $sortDirection);
        } else {
            // Default sorting on initial load
            $query->orderBy('updated_at', 'desc');
        }

        return $query;
    }
}
