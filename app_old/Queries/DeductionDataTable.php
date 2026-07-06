<?php

namespace App\Queries;

use App\Models\Deduction;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class TagDataTable
 */
class DeductionDataTable
{
    /**
     * @param  array  $input
     * @return Deduction
     */
    public function get($input = [])
    {
        /** @var Deduction $query */
        $query = Deduction::with(['employee','deductionTypes', 'employee.branch'])->orderBy('updated_at', 'desc');
        return $query;
    }
}
