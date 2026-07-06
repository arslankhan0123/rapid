<?php

namespace App\Queries;

use App\Models\MonthlyAttendanceInvoice;
use App\Models\PurchaseInvoice;
use App\Models\PurchaseReturn;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Payment;
use App\Models\Invoice;

/**
 * Class TagDataTable
 */
class SuppliertatementDataTable
{
    public function get($input = [])
    {
        $invoices = PurchaseInvoice::with(['customer'])
            ->select('purchase_invoices.*')
            ->when(isset($input['from_date'], $input['to_date']), function (Builder $q) use ($input) {
                $q->whereBetween('estimate_date', [
                    $input['from_date'] . ' 00:00:00',
                    $input['to_date'] . ' 23:59:59'
                ]);
            })
            ->whereIn('payment_status', [2, 3])
            ->when(!empty($input['customer_select']), function (Builder $q) use ($input) {
                $q->where('customer_id', $input['customer_select']);
            })
            ->get()
            ->toArray();



        $returns = PurchaseReturn::with(['customer'])
            ->select('purchase_returns.*')
            ->when(isset($input['from_date'], $input['to_date']), function (Builder $q) use ($input) {
                $q->whereBetween('estimate_date', [
                    $input['from_date'] . ' 00:00:00',
                    $input['to_date'] . ' 23:59:59'
                ]);
            })
            ->when(!empty($input['customer_select']), function (Builder $q) use ($input) {
                $q->where('customer_id', $input['customer_select']);
            })
            ->get()
            ->toArray();


        $openingBalance = Supplier::when(!empty($input['customer_select']), function (Builder $q) use ($input) {
            $q->where('id', $input['customer_select']); // Assuming `id` is the primary key of suppliers
        })->sum('opening_balance'); // Sum the column



        return $this->transformInvoices($invoices, $returns, $openingBalance);
    }

    // public function transformInvoices($invoices, $returns, $openingBalance = 0)
    // {

    //     $result = [];

    //     foreach ($invoices as $invoice) {
    //         $result[] = [
    //             'invoice_date' => date('d-m-Y', strtotime($invoice['estimate_date'])),
    //             'invoice_number' => $invoice['estimate_number'],
    //             'receipt_date' => date('d-m-Y', strtotime($invoice['updated_at'])),
    //             'month' => date('F, Y', strtotime($invoice['estimate_date'])),
    //             'debit' => null,
    //             'credit' => $invoice['total_amount'], // CREDIT now
    //             'balance' => $invoice['total_amount'],
    //             'type' => "Purchase Invoice",
    //         ];
    //     }

    //     foreach ($returns as $return) {
    //         $result[] = [
    //             'invoice_date' => date('d-m-Y', strtotime($return['estimate_date'])),
    //             'invoice_number' => $return['return_number'],
    //             'receipt_date' => date('d-m-Y', strtotime($return['updated_at'])),
    //             'month' => date('F, Y', strtotime($return['estimate_date'])),
    //             'debit' => $return['total_amount'], // DEBIT now
    //             'credit' => null,
    //             'balance' => -$return['total_amount'],
    //             'type' => "Purchase Return",
    //         ];
    //     }
    //     // Sort all rows by receipt_date
    //     $result = collect($result)->sortBy('receipt_date')->values()->all();

    //     // Prepend the opening balance row
    //     array_unshift($result, [
    //         'invoice_date' => '',
    //         'invoice_number' => '',
    //         'receipt_date' => '',
    //         'month' => '',
    //         'debit' => (float) 0,
    //         'credit' => (float) $openingBalance,
    //         'balance' => (float) $openingBalance,
    //         'type' => 'Opening Balance',
    //     ]);

    //     return $result;
    // }



    public function transformInvoices($invoices, $returns, $openingBalance = 0)
    {
        $result = [];

        foreach ($invoices as $invoice) {
            $result[] = [
                'invoice_date' => date('d-m-Y', strtotime($invoice['estimate_date'])),
                'invoice_number' => $invoice['estimate_number'],
                'receipt_date' => date('d-m-Y', strtotime($invoice['updated_at'])),
                'month' => date('F, Y', strtotime($invoice['estimate_date'])),
                'debit' => null,
                'credit' => (float) $invoice['total_amount'],
                'balance' => 0, // Placeholder
                'type' => "Purchase Invoice",
            ];
        }

        foreach ($returns as $return) {
            $result[] = [
                'invoice_date' => date('d-m-Y', strtotime($return['estimate_date'])),
                'invoice_number' => $return['return_number'],
                'receipt_date' => date('d-m-Y', strtotime($return['updated_at'])),
                'month' => date('F, Y', strtotime($return['estimate_date'])),
                'debit' => (float) $return['total_amount'],
                'credit' => null,
                'balance' => 0, // Placeholder
                'type' => "Purchase Return",
            ];
        }

        // Sort by receipt date
        $result = collect($result)->sortBy('receipt_date')->values()->all();

        // Prepend Opening Balance
        array_unshift($result, [
            'invoice_date' => '',
            'invoice_number' => '',
            'receipt_date' => '',
            'month' => '',
            'debit' => (float) 0,
            'credit' => (float) $openingBalance,
            'balance' => (float) $openingBalance,
            'type' => 'Opening Balance',
        ]);

        // Calculate running balance by adding credit only
        $runningBalance = $openingBalance;
        foreach ($result as $index => &$row) {
            if ($index === 0) {
                continue; // Opening balance already set
            }

            $credit = $row['credit'] ?? 0;
            $runningBalance += $credit; // Only add credit
            $row['balance'] = $runningBalance - $row['debit'] ?? 0;
        }

        return $result;
    }

}
