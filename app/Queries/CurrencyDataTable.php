<?php

namespace App\Queries;

use App\Models\Currency;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class TagDataTable
 */
class CurrencyDataTable
{
    /**
     * @param  array  $input
     * @return Currency
     */
    public function get($input = [])
    {
        /** @var Currency $query */
        $query = Currency::orderBy('created_at', 'desc')->get();
        return $query;
    }
}
