<?php

namespace App\Queries;
use App\Models\Insurance;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class TagDataTable
 */
class InsuranceDataTable
{
    /**
     * @param  array  $input
     * @return Insurance
     */
    public function get($input = [])
    {
        /** @var Insurance $query */
        $query = Insurance::with(['employee','employee.branch'])->orderBy('created_at','desc')->get();
        return $query;
    }
}
