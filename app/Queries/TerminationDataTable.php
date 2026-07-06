<?php

namespace App\Queries;
use App\Models\Termination;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class TagDataTable
 */
class TerminationDataTable
{
    /**
     * @param  array  $input
     * @return Termination
     */
    public function get($input = [])
    {
        /** @var Termination $query */
        $query = Termination::with('employee','employee.branch')->orderBy('created_at','desc')->get();
        return $query;
    }
}
