<?php

namespace App\Queries;

use App\Models\Supplier;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class TagDataTable
 */
class SupplierDataTable
{
    /**
     * @param  array  $input
     * @return Supplier
     */
    public function get($input = [])
    {
        /** @var Supplier $query */
        $query = Supplier::with('country')->orderBy('created_at', 'desc')->get();
        return $query;
    }
}
