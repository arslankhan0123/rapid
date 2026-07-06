<?php

namespace App\Queries;

use App\Models\SampleCategory;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class SampleCategoryDataTable
 */
class SampleCategoryDataTable
{
    /**
     * @param  array  $input
     * @return Builder
     */
    public function get($input = []): Builder
    {
        /** @var Builder $query */
        $query = SampleCategory::query();

        // Apply filter for group if present
        if (!empty($input['group'])) {
            $query->where('group', $input['group']);
        }

        // List of allowed columns for sorting
        $allowedColumns = ['id', 'title', 'updated_at'];

        // Handle dynamic sorting
        if (!empty($input['order']) && isset($input['columns']) ) {
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
