<?php

namespace App\Queries;

use App\Models\Invoice;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class SalesReportsDataTable
{
    // public function get($input = [])
    // {
    //     /** @var SalesInvoice $query */
    //     // $query = SalesInvoice::with(['user']);
    //     $query = SalesInvoice::with(['user'])
    //         ->select('sales_invoices.*');

    //     if (!empty($input['customer_name'])) {
    //         $query->where('customer_name', 'like', '%' . $input['customer_name'] . '%');
    //     }

    //     // User (sales agent) filter
    //     if (!empty($input['sales_agent_id'])) {
    //         $query->where('sales_agent_id', $input['sales_agent_id']);
    //     }

    //     if (!empty($input['from_date']) && !empty($input['to_date'])) {
    //         $query->whereDate('sales_invoices.created_at', '>=', Carbon::parse($input['from_date'])->startOfDay())
    //             ->whereDate('sales_invoices.created_at', '<=', Carbon::parse($input['to_date'])->endOfDay());
    //     }
    //     return $query;
    // }
    public function get($input = [])
    {
        $query = Invoice::with(['customer', 'salesItems'])
            ->select('invoices.*');

        if (!empty($input['customer_name'])) {
            $query->where('customer_id', $input['customer_name']);
        }

        if (!empty($input['from_date']) && !empty($input['to_date'])) {
            try {
                $fromDate = Carbon::createFromFormat('d-m-Y', $input['from_date'])->startOfDay();
                $toDate = Carbon::createFromFormat('d-m-Y', $input['to_date'])->endOfDay();

                $query->whereBetween('invoices.invoice_date', [$fromDate, $toDate]);
            } catch (\Exception $e) {
                // Handle date parsing error
                logger()->error('Date parsing error in sales report: ' . $e->getMessage());
            }
        } elseif (!empty($input['from_date'])) {
            // Only from date provided
            try {
                $fromDate = Carbon::createFromFormat('d-m-Y', $input['from_date'])->startOfDay();
                $query->where('invoices.invoice_date', '>=', $fromDate);
            } catch (\Exception $e) {
                logger()->error('Date parsing error in sales report: ' . $e->getMessage());
            }
        } elseif (!empty($input['to_date'])) {
            // Only to date provided
            try {
                $toDate = Carbon::createFromFormat('d-m-Y', $input['to_date'])->endOfDay();
                $query->where('invoices.invoice_date', '<=', $toDate);
            } catch (\Exception $e) {
                logger()->error('Date parsing error in sales report: ' . $e->getMessage());
            }
        }

        return $query;
    }
}
