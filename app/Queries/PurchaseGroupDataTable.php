<?php

namespace App\Queries;


use App\Models\AssetCategory;
use Illuminate\Database\Eloquent\Builder;
use App\Models\PurchaseGroup;

/**
 * Class TagDataTable
 */
class PurchaseGroupDataTable
{
    /**
     * @param  array  $input
     * @return PurchaseGroup
     */
    public function get($input = [])
    {
        /** @var PurchaseGroup $query */
        $query = PurchaseGroup::query();

        // Map the DataTables column index to actual database column names
        $columns = ['name', 'description', 'updated_at']; // Adjust this based on the columns you're displaying in your table

        // Check if dynamic sorting is requested
        if (!empty($input['order'][0]['column'])) {
            $columnIndex = $input['order'][0]['column']; // Get the column index that the user clicked for sorting
            $direction = $input['order'][0]['dir'] ?? 'desc'; // Default to 'desc' if no direction is provided

            // Determine which column to order by using the column index, with fallback to 'updated_at'
            $orderByColumn = $columns[$columnIndex] ?? 'updated_at';

            // Apply the order to the query
            $query->orderBy($orderByColumn, $direction);
        } else {
            // If no sorting is provided, default to sorting by 'updated_at' in descending order
            $query->orderBy('updated_at', 'desc');
        }

        return $query;
    }
}
