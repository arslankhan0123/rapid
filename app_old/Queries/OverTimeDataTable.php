<?php

namespace App\Queries;

use App\Models\OverTime;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class TagDataTable
 */
class OverTimeDataTable
{
    /**
     * @param  array  $input
     * @return OverTime;
     */
    public function get($input = [])
    {
        /** @var OverTime $query */
        $query = OverTime::with(['employee', 'overtimeTypes'])->orderBy('created_at', 'desc')->get();
        return $query;
    }
}
