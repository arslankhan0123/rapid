<?php

namespace App\Queries;


use App\Models\ProductUnit;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class TagDataTable
 */
class ProductUnitDataTable
{
    /**
     * @param  array  $input
     * @return ProductUnit
     */
    public function get($input = [])
    {
        /** @var ProductUnit $query */
        $query = ProductUnit::all();
        return $query;
    }
}
