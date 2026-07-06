<?php

namespace App\Queries;

use App\Models\VatReport;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class ServiceCategoryDataTable
 */

class VatreportDataTable
{
    public function get($input = []): Builder
    {
        $query = VatReport::query();

        // Remove branch relationship since we don't need branch data
        // $query = VatReport::query()->with('branch'); // Remove this line

        $query->whereNull('branch_id');
        // Apply filter for group if present
        if (!empty($input['group'])) {
            $query->where('group', $input['group']);
        }

        // Apply filter for year if present
        if (!empty($input['year'])) {
            $query->where('year', $input['year']);
        }

        // Group by year and period to aggregate data
        // Generate period_description dynamically instead of using MAX(period_description)
        $query->selectRaw('
           MAX(id) as id,
            year,
            period,
            CASE
                WHEN period = "q1" THEN "Quarter 1 (Jan-Mar)"
                WHEN period = "q2" THEN "Quarter 2 (Apr-Jun)"
                WHEN period = "q3" THEN "Quarter 3 (Jul-Sep)"
                WHEN period = "q4" THEN "Quarter 4 (Oct-Dec)"
                ELSE period
            END as period_description,
            SUM(input) as input,
            SUM(output) as output,
            SUM(net) as net,
            SUM(paid) as paid,
            SUM(unpaid) as unpaid
        ')
            ->groupBy('year', 'period');

        // List of allowed columns for sorting
        $allowedColumns = ['period', 'period_description', 'input', 'output', 'net', 'paid', 'unpaid', 'year'];

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
            $query->orderBy('year', 'desc')->orderBy('period', 'asc');
        }

        return $query;
    }
}
// class VatreportDataTable
// {
//     /**
//      * @param  array  $input
//      * @return Builder
//      */
//     public function get($input = []): Builder
//     {
//         /** @var Builder $query */
//         $query = VatReport::query()->with('branch');

//         // Apply filter for group if present
//         if (!empty($input['group'])) {
//             $query->where('group', $input['group']);
//         }

//         // List of allowed columns for sorting
//         $allowedColumns = ['period', 'period_description', 'input', 'output', 'net', 'paid', 'unpaid', 'year','branch_id'];


//         $query  =  $query->when(
//             !empty($input['filterBranch']),
//             function ($q) use ($input) {
//                 // Filter by a specific branch if provided
//                 $q->where('branch_id', $input['filterBranch']);
//             },
//             function ($q) {
//                 // Otherwise, filter by the user's associated branches
//                 $q->whereHas('branch', function ($branchQuery) {
//                     $branchQuery->whereIn('id', function ($subQuery) {
//                         $subQuery->select('branch_id')
//                             ->from('users_branches')
//                             ->where('user_id', auth()->id());
//                     });
//                 });
//             }
//         );
//         // Apply filter for year if present
//         if (!empty($input['year'])) {
//             // Assuming you have a 'period' column in the format 'YYYY-MM' or a separate 'year' column
//             $query->where('year', $input['year']);
//         }
//         // Handle dynamic sorting
//         if (!empty($input['order']) && isset($input['columns'])) {
//             $columnIndex = $input['order'][0]['column'];
//             $sortDirection = $input['order'][0]['dir'] ?? 'desc';

//             // Get column name from the columns array and validate it
//             $columnName = $input['columns'][$columnIndex]['data'] ?? 'updated_at';
//             $columnName = in_array($columnName, $allowedColumns) ? $columnName : 'updated_at';

//             // Apply sorting to query
//             $query->orderBy($columnName, $sortDirection);
//         } else {
//             // Default sorting on initial load
//             $query->orderBy('updated_at', 'desc');
//         }

//         return $query;
//     }
// }