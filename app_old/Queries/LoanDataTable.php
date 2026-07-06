<?php

namespace App\Queries;
use App\Models\Loan;

/**
 * Class TagDataTable
 */
class LoanDataTable
{
    /**
     * @param  array  $input
     * @return Loan
     */
    public function get($input = [])
    {
        /** @var Loan $query */
        $query = Loan::with(['employee', 'permittedBy','employee.branch'])->orderBy('created_at','desc')->get();
        return $query;
    }
}
