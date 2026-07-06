<?php

namespace App\Queries;
use App\Models\Retirement;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class TagDataTable
 */
class RetirementDataTable
{
    /**
     * @param  array  $input
     * @return Retirement
     */
    public function get($input = [])
    {
        /** @var Retirement $query */
        $query = Retirement::with('employee','employee.branch')->orderBy('created_at','desc')->get();
        return $query;
    }
}
