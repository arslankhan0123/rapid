<?php

namespace App\Queries;

use App\Models\Shift;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class TagDataTable
 */
class ShiftDataTable
{
    /**
     * @param  array  $input
     * @return Shift
     */
    public function get($input = [])
    {
        /** @var Shift $query */
        $query = Shift::orderBy('created_at', 'desc')->get();
        return $query;
    }
}
