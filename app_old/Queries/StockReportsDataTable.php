<?php

namespace App\Queries;


use App\Models\AssetCategory;
use Illuminate\Database\Eloquent\Builder;
use App\Models\PurchaseItem;

/**
 * Class TagDataTable
 */
class StockReportsDataTable
{

    public function get($input = [])
    {
        /** @var PurchaseItem $query */
        $query = PurchaseItem::query()->with(['group', 'category', 'subCategory', 'brand', 'size', 'color', 'brand', 'unit']);




        if (!empty($input['filterCategory'])) {
            $query->where('purchase_category_id', $input['filterCategory']);
        }

        if (!empty($input['filterGroup'])) {
            $query->where('purchase_group_id', $input['filterGroup']);
        }

        if (!empty($input['filterSubCategory'])) {
            $query->where('purchase_sub_category_id', $input['filterSubCategory']);
        }

        if (!empty($input['filterItem'])) {
            $query->where('id', $input['filterItem']);
        }
        $query = $query->get();
        return $query;
    }
}
