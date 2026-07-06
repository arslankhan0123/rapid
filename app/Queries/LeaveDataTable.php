<?php

namespace App\Queries;

use App\Models\Leave;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class TagDataTable
 */
class LeaveDataTable
{
    /**
     * @param  array  $input
     * @return Leave;
     */
    public function get($input = [])
    {
        /** @var Leave $query */
        $query = Leave::orderBy('created_at', 'desc')->get();
        return $query;
    }
}
