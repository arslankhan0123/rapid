<?php

namespace App\Queries;
use App\Models\Commission;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class TagDataTable
 */
class CommissionDataTable
{
    /**
     * @param  array  $input
     * @return Commission
     */
    public function get($input = [])
    {
        /** @var Commission $query */
        $query = Commission::with(['employee'])->orderBy('created_at','desc')->get();
        return $query;
    }
}
