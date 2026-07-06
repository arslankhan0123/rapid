<?php

namespace App\Queries;

use App\Models\ProductColor;

class ProductColorDataTable
{
    /**
     * @param  array  $input
     * @return ProductColor
     */
    public function get($input = [])
    {
        /** @var ProductColor $query */
        $query = ProductColor::all();
        return $query;
    }
}
