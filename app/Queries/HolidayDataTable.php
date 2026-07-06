<?php

namespace App\Queries;

use App\Models\Holiday;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class TagDataTable
 */
class HolidayDataTable
{
    /**
     * @param  array  $input
     * @return Holiday
     */
    public function get($input = [])
    {
        /** @var Holiday $query */
        $query = Holiday::orderBy('created_at', 'desc')->get();
        return $query;
    }
}
