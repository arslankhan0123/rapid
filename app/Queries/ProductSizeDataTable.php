<?php

namespace App\Queries;

use App\Models\Size;

class ProductSizeDataTable
{
    /**
     * @param  array  $input
     * @return Size
     */
    public function get($input = [])
    {
        /** @var Size $query */
        $query = Size::all();
        return $query;
    }
}
