<?php

namespace App\Queries;

use App\Models\ProductGroup;

/**
 * Class TagDataTable
 */
class ProductGroupDataTable
{
    /**
     * @return ProductGroup
     */
    public function get()
    {
        /** @var ProductGroup $query */
        $query = ProductGroup::query()->select('item_groups.*')->withCount('products')->latest();

        return $query;
    }
}
