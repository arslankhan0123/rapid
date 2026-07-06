<?php

namespace App\Queries;

use App\Models\Product;
use App\Models\ProductGroup;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class ProductDataTable
 */
class ProductDataTable
{
    /**
     * Get the query for retrieving products with optional filtering and sorting.
     *
     * @param  array  $input
     * @return Builder
     */
    public function get($input = []): Builder
    {
        /** @var Builder $query */
        $query = Product::with(['group', 'firstTax', 'secondTax'])
            ->select('items.*');

        // Apply filter by group if specified
        $query->when(!empty($input['group']), function (Builder $q) use ($input) {
            $q->where('item_group_id', '=', $input['group']);
        });
        $query->when(!empty($input['category']), function (Builder $q) use ($input) {
            $q->where('item_group_id', '=', $input['category']);
        });
        // Apply search filter for group name and product title
        if (!empty($input['search']['value'])) {
            $searchValue = $input['search']['value'];
            $query->where(function (Builder $q) use ($searchValue) {
                $q->where('title', 'LIKE', "%{$searchValue}%")
                    ->orWhereHas('group', function (Builder $q) use ($searchValue) {
                        $q->where('name', 'LIKE', "%{$searchValue}%");
                    });
            });
        }
        // Handle dynamic sorting
        if (!empty($input['order']) && isset($input['columns'])) {
            $columnIndex = $input['order'][0]['column'];
            $sortDirection = $input['order'][0]['dir'] ?? 'asc';

            // Get column name from the columns array and validate it
            $columnName = $input['columns'][$columnIndex]['data'] ?? 'updated_at';
            $allowedColumns = ['id', 'group', 'title', 'updated_at']; // Add any other sortable columns here

            // Ensure the column name is valid
            if (in_array($columnName, $allowedColumns)) {
                $query->orderBy($columnName, $sortDirection);
            }
        } else {
            // Default sorting on initial load
            $query->orderBy('updated_at', 'desc');
        }

        return $query;
    }
}
