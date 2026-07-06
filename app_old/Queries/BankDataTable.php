<?php

namespace App\Queries;

use App\Models\Bank;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class TagDataTable
 */
class BankDataTable
{
    /**
     * @param  array  $input
     * @return Bank;
     */
    public function get($input = [])
    {
        /** @var Bank $query */
        $query = Bank::orderBy('created_at', 'desc')->get();
        return $query;
    }
}
