<?php

namespace App\Queries;


use App\Models\SupplierGroup;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class TagDataTable
 */
class SupplierGroupDataTable
{
    /**
     * @param  array  $input
     * @return SupplierGroup
     */
    public function get($input = [])
    {
        /** @var SupplierGroup $query */
        $query =  SupplierGroup::with('suppliers')->orderBy('created_at', 'desc')->get();;
        return $query;
    }
}
