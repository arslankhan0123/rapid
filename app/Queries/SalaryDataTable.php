<?php

namespace App\Queries;
use App\Models\Salary;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class TagDataTable
 */
class SalaryDataTable
{
    /**
     * @param  array  $input
     * @return Salary
     */
    public function get($input = [])
    {
        /** @var Salary $query */
        $query = Salary::with('employee')->orderBy('created_at','desc')->get();
        return $query;
    }
}
