<?php

namespace App\Queries;

use App\Models\ProductBrand;

class ProductBrandDataTable
{
    /**
     * @param  array  $input
     * @return ProductBrand
     */
    public function get($input = [])
    {
        /** @var ProductBrand $query */
        $query = ProductBrand::all();
        return $query;
    }
}
