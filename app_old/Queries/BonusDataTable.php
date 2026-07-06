<?php

namespace App\Queries;

use App\Models\Bonus;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class TagDataTable
 */
class BonusDataTable
{
    /**
     * @param  array  $input
     * @return Bonus
     */
    public function get($input = [])
    {
        /** @var Bonus $query */
        $query = Bonus::with(['employee', 'bonusTypes','employee.branch'])->orderBy('created_at', 'desc')->get();
        return $query;
    }
}
