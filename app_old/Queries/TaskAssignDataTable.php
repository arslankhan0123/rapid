<?php

namespace App\Queries;

use App\Models\TaskAssign;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class ServiceCategoryDataTable
 */
class TaskAssignDataTable
{
    /**
     * @param  array  $input
     * @return Builder
     */
    public function get($input = []): Builder
    {
        /** @var Builder $query */
        $query = TaskAssign::query()->with(['department', 'designation', 'employee']); // Eager load relationships

        // Apply filter for group if present
        if (!empty($input['group'])) {
            $query->where('group', $input['group']);
        }

        // List of allowed columns for sorting
        $allowedColumns = ['id', 'name', 'updated_at'];

        // Handle dynamic sorting
        if (!empty($input['order']) && isset($input['columns'])) {
            $columnIndex = $input['order'][0]['column'] ?? null;
            $sortDirection = $input['order'][0]['dir'] ?? 'desc';

            // Ensure columnIndex is valid
            if ($columnIndex !== null && isset($input['columns'][$columnIndex])) {
                // Get column name from the columns array and validate it
                $columnName = $input['columns'][$columnIndex]['data'] ?? 'updated_at';
                $columnName = in_array($columnName, $allowedColumns) ? $columnName : 'updated_at';

                // Apply sorting to query
                $query->orderBy($columnName, $sortDirection);
            } else {
                // If no valid sorting input, use default sorting
                $query->orderBy('updated_at', 'desc');
            }
        } else {
            // Default sorting on initial load
            $query->orderBy('updated_at', 'desc');
        }

        return $query;
    }
}
