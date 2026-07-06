<?php

namespace App\Queries;

use App\Models\Invoice;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;


class SalesTaxReportsDataTable
{
    public function get($input = [])
    {
        $query = Invoice::query()
            ->with([
                'customer:id,company_name',
                'salesItems'
            ])
            ->select([
                'invoices.id',
                'invoices.invoice_number',
                'invoices.customer_id',
                'invoices.invoice_date',
                'invoices.total_amount',
                'invoices.created_at'
            ]);

        // Filter by customer - FIXED: Use customer_id instead of company_name
        if (!empty($input['customer_name'])) {
            $query->where('customer_id', $input['customer_name']);
        }

        // Filter by date range
        if (!empty($input['from_date']) && !empty($input['to_date'])) {
            $query->whereBetween('invoice_date', [
                Carbon::createFromFormat('d-m-Y', $input['from_date'])->startOfDay(),
                Carbon::createFromFormat('d-m-Y', $input['to_date'])->endOfDay()
            ]);
        }

        return $query;
    }

    // Helper method to calculate tax amount for an invoice
    public static function calculateTaxAmount($invoice)
    {
        return $invoice->salesItems->sum('tax');
    }

    // Helper method to calculate taxable amount (subtotal before tax)
    public static function calculateTaxableAmount($invoice)
    {
        return $invoice->salesItems->sum('total') - $invoice->salesItems->sum('tax');
    }
}
