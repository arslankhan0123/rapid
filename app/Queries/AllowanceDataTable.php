<?php

namespace App\Queries;

use App\Models\Allowance;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class TagDataTable
 */
class AllowanceDataTable
{
    /**
     * @param  array  $input
     * @return Allowance
     */
    public function get($input = [])
    {
        /** @var Allowance $query */
        $query = Allowance::with(['employee', 'allowanceTypes','employee.branch'])->orderBy('created_at', 'desc')->get();

        // Map the payment type name
        $query->transform(function ($allowance) {
            $allowance->payment_type_name = Allowance::PAYMENT_TYPES[$allowance->payment_type] ?? null;
            return $allowance;
        });
        return $query;
    }
}
