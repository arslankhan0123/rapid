<?php

namespace App\Queries;


use App\Models\AssetCategory;
use Illuminate\Database\Eloquent\Builder;
use App\Models\PurchaseCategory;

/**
 * Class TagDataTable
 */
class PurchaseCategoryDataTable
{

    public function get($input = [])
    {
        /** @var PurchaseCategory $query */
        $query = PurchaseCategory::query()->with('group');

        // Map the column index to actual database column names
        $columns = ['name', 'description', 'updated_at']; // Adjust this based on your displayed columns

        // Check if the 'order' input is provided for dynamic sorting
        if (isset($input['order'][0]['column'])) {
            $columnIndex = $input['order'][0]['column']; // Get the column index for sorting
            $direction = $input['order'][0]['dir'] ?? 'desc'; // Default to 'desc' if no direction is provided

            // Apply the order based on the requested column index, fallback to 'updated_at' if index is out of bounds
            $orderByColumn = $columns[$columnIndex] ?? 'updated_at';
            $query->orderBy($orderByColumn, $direction);
        } else {
            // If no 'order' input is provided, default sorting by 'updated_at'
            $query->orderBy('updated_at', 'desc');
        }

        $query = $query->get();
        return $query;
    }
}
