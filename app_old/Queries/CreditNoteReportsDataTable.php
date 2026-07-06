<?php

namespace App\Queries;

use App\Models\CreditNote;
use App\Models\Customer;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class CreditNoteReportsDataTable
{
    public function get($input = [])
    {
        $query = CreditNote::with(['customer', 'salesItems'])
            ->select('credit_notes.*');

        // Filter by customer
        if (!empty($input['customer_name'])) {
            $query->where('customer_id', $input['customer_name']);
        }

        // Filter by date range
        if (!empty($input['from_date']) && !empty($input['to_date'])) {
            $query->whereDate('credit_notes.credit_note_date', '>=', Carbon::parse($input['from_date'])->startOfDay())
                ->whereDate('credit_notes.credit_note_date', '<=', Carbon::parse($input['to_date'])->endOfDay());
        }

        return $query;
    }
}
