<?php

namespace App\Queries;

use App\Models\MonthlyAttendanceInvoice;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class TagDataTable
 */
class CustomerStatementDataTable
{

    public function get($input = [])
    {
        /** @var MonthlyAttendanceInvoice $query */
        $query = MonthlyAttendanceInvoice::with(['customer', 'project']);

        if (isset($input['from_date']) && $input['from_date'] && isset($input['to_date']) && $input['to_date']) {
            // Both from_date and to_date are provided, use whereBetween
            $query->whereBetween('posted_at', [$input['from_date'], $input['to_date']]);
        } elseif (isset($input['from_date']) && $input['from_date']) {
            // Only from_date is provided, get records from that date onward
            $query->where('posted_at', '>=', $input['from_date']);
        } elseif (isset($input['to_date']) && $input['to_date']) {
            // Only to_date is provided, get records up to that date
            $query->where('posted_at', '<=', $input['to_date']);
        }

        // Filter by customer_id
        if (isset($input['customer_id']) && $input['customer_id']) {
            $query->where('customer_id', $input['customer_id']);
        }

        // Filter by project_id only if both customer_id and project_id are provided
        if (isset($input['customer_id']) && $input['customer_id'] && isset($input['project_id']) && $input['project_id']) {
            $query->where('project_id', $input['project_id']);
        }

        if (isset($input['payment_status']) && $input['payment_status']) {
            $query->where('status', $input['payment_status']);
        }
        // Order by creation date (latest first)
        $query->orderBy('created_at', 'desc');

        // Fetch the results
        $results = $query->get();

        // Calculate totals for each invoice
        foreach ($results as $invoice) {

            $vatAmount = ($invoice->total_amount * $invoice->vat) / 100;
            $invoice->net_amount = $invoice->total_amount - $invoice->discount + $vatAmount;
            $invoice->balance_due = $invoice->net_amount - $invoice->paid_amount;
        }

        return $results;
    }
}
