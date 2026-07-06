<?php

namespace App\Queries;

use App\Models\PurchasedItem;
use App\Models\Invoice;
use App\Models\SalesItem;
use Carbon\Carbon;

class SalesItemReportsDataTable
{
    public function get($input = [])
    {
        $query = SalesItem::select([
            'sales_items.*',
            'invoices.invoice_number',
            'invoices.customer_id',
            'invoices.invoice_date',
            'invoices.created_at as invoice_created_at'
        ])
            ->join('invoices', 'invoices.id', '=', 'sales_items.owner_id')
            ->where('sales_items.owner_type', Invoice::class)
            ->with(['invoice.customer']);

        // Optional filters
        // In SalesItemReportsDataTable class - update the customer filter
        if (!empty($input['customer_name']) && $input['customer_name'] !== 'undefined') {
            $query->where('invoices.customer_id', $input['customer_name']);
        }

        if (!empty($input['from_date'])) {
            $query->whereDate('invoices.invoice_date', '>=', Carbon::parse($input['from_date'])->startOfDay());
        }

        if (!empty($input['to_date'])) {
            $query->whereDate('invoices.invoice_date', '<=', Carbon::parse($input['to_date'])->endOfDay());
        }

        return $query;
    }
}
