<?php

namespace App\Queries;

use App\Models\Increment;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class TagDataTable
 */
class IncrementDataTable
{
    /**
     * @param  array  $input
     * @return Increment;
     */
    public function get($input = [])
    {
        /** @var Increment $query */
        $query = Increment::with('branch','employee','employee.designation')->orderBy('created_at', 'desc')->get();
        return $query;
    }
}
