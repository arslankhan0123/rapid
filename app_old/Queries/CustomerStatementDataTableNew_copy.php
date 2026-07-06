<?php

namespace App\Queries;

use App\Models\MonthlyAttendanceInvoice;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Payment;

/**
 * Class TagDataTable
 */
class CustomerStatementDataTableNew_copy
{
    public function get($input = [])
    {
        /** @var Payment $query */
        $query = Payment::with(['paymentMode', 'invoice', 'invoice.customer', 'invoice.project'])->select('payments.*')->latest();

        $query->when(isset($input['owner_id']), function (Builder $q) use ($input) {
            $q->where('owner_id', '=', $input['owner_id']);
        });

        $query->when(isset($input['owner_type']), function (Builder $q) use ($input) {
            $q->where('owner_type', '=', $input['owner_type']);
        });

        // Add condition for payment_date range (from_date to to_date)
        $query->when(
            isset($input['from_date']) && $input['from_date'] && isset($input['to_date']) && $input['to_date'],
            function (Builder $q) use ($input) {
                $q->whereBetween('payment_date', [$input['from_date'], $input['to_date']]);
            }
        );

        // Add condition for customer_id and project_id
        $query->when(
            isset($input['customer_select']) && $input['customer_select'],
            function (Builder $q) use ($input) {
                $q->whereHas('invoice', function (Builder $query) use ($input) {
                    $query->where('customer_id', '=', $input['customer_select']);

                    // Only filter by project_id if it's provided
                    if (isset($input['project_select']) && $input['project_select']) {
                        $query->where('project_id', '=', $input['project_select']);
                    }
                    // Only filter by payment_status if it's provided
                    if (isset($input['payment_status']) && $input['payment_status']) {
                        $query->where('payment_status', '=', $input['payment_status']);
                    }
                });
            }
        );

        return $query;
    }
}
