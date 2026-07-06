<?php

namespace App\Queries;


use App\Models\State;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class TagDataTable
 */
class StateDataTable
{
    /**
     * @param  array  $input
     * @return State
     */
    public function get($input = [])
    {
        /** @var State $query */
        $query = State::with(['country'])->orderBy('created_at', 'desc')->get();
        return $query;
    }
}
