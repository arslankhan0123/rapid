<?php

namespace App\Queries;

use App\Models\CompanyLoan;
use Illuminate\Database\Eloquent\Builder;

class CompanyLoanDataTable
{
    // public function get($input = [])
    // {
    //     $query = CompanyLoan::query()->orderBy('created_at', 'desc');

    //     return $query;
    // }

    public function get($input = [])
    {
        $query = CompanyLoan::query()->orderBy('created_at', 'desc');

        // Filter by type
        if (isset($input['type']) && !empty($input['type'])) {
            $query->where('type', $input['type']);
        }

        // Filter by customer
        if (isset($input['customer_id']) && !empty($input['customer_id'])) {
            $query->where('customer_id', $input['customer_id']);
        }

        // Filter by supplier
        if (isset($input['supplier_id']) && !empty($input['supplier_id'])) {
            $query->where('supplier_id', $input['supplier_id']);
        }

        // Filter by month and year
        if (isset($input['month']) && !empty($input['month']) && isset($input['year']) && !empty($input['year'])) {
            $query->whereYear('date', $input['year'])
                ->whereMonth('date', $input['month']);
        } elseif (isset($input['year']) && !empty($input['year'])) {
            $query->whereYear('date', $input['year']);
        } elseif (isset($input['month']) && !empty($input['month'])) {
            $query->whereMonth('date', $input['month']);
        }

        return $query;
    }
}
