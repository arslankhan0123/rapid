<?php

namespace App\Queries;

use App\Models\MonthlyAttendanceInvoice;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Payment;
use App\Models\Invoice;

/**
 * Class TagDataTable
 */
class CustomerStatementDataTableNew
{
    public function get($input = [])
    {
        /** @var Invoice $query */
        $query = Invoice::with(['payments', 'customer', 'project', 'creditNotes', 'branch']) // Load related payments, customer, and project
            ->select('invoices.*'); // Select invoice columns



        $query  =  $query->when(!empty($input['filterBranch']), function ($q) use ($input) {
            // Filter by a specific branch if provided
            $q->where('branch_id', $input['filterBranch']);
        }, function ($q) {
            // Otherwise, filter by the user's associated branches
            $q->whereHas('branch', function ($branchQuery) {
                $branchQuery->whereIn('id', function ($subQuery) {
                    $subQuery->select('branch_id')
                        ->from('users_branches')
                        ->where('user_id', auth()->id());
                });
            });
        });
        $query->when(
            isset($input['from_date']) && $input['from_date'] && isset($input['to_date']) && $input['to_date'],
            function (Builder $q) use ($input) {
                $q->whereBetween('invoice_date', [$input['from_date'], $input['to_date']]);
            }
        );
        // Apply filtering conditions based on the input

        // Filter by customer_id
        $query->when(
            isset($input['customer_select']) && $input['customer_select'],
            function (Builder $q) use ($input) {
                $q->where('customer_id', '=', $input['customer_select']);
            }
        );

        // Filter by project_id
        $query->when(
            isset($input['project_select']) && $input['project_select'],
            function (Builder $q) use ($input) {
                $q->where('project_id', '=', $input['project_select']);
            }
        );

        // Filter by payment_status
        $query->when(
            isset($input['payment_status']) && $input['payment_status'],
            function (Builder $q) use ($input) {
                $q->where('payment_status', '=', $input['payment_status']);
            }
        );

        // Filter by payment_date range (from_date to to_date) in related payments
        $query->when(
            isset($input['from_date']) && $input['from_date'] && isset($input['to_date']) && $input['to_date'],
            function (Builder $q) use ($input) {
                $q->whereHas('payments', function (Builder $query) use ($input) {
                    $query->whereBetween('payment_date', [$input['from_date'], $input['to_date']]);
                });
            }
        );

        // Filter by owner_id in related payments
        $query->when(
            isset($input['owner_id']),
            function (Builder $q) use ($input) {
                $q->whereHas('payments', function (Builder $query) use ($input) {
                    $query->where('owner_id', '=', $input['owner_id']);
                });
            }
        );

        // Filter by owner_type in related payments
        $query->when(
            isset($input['owner_type']),
            function (Builder $q) use ($input) {
                $q->whereHas('payments', function (Builder $query) use ($input) {
                    $query->where('owner_type', '=', $input['owner_type']);
                });
            }
        );

        $query->when(
            isset($input['from_date']) && $input['from_date'] && isset($input['to_date']) && $input['to_date'],
            function (Builder $q) use ($input) {
                $q->whereHas('creditNotes', function (Builder $query) use ($input) {
                    $query->whereBetween('credit_note_date', [$input['from_date'], $input['to_date']]);
                })
                    ->orWhereDoesntHave('creditNotes'); // Include invoices that have no creditNotes
            }
        );



        $invoices = $query->get()->toArray(); // Fetch the data as an array

        return $this->transformInvoices($invoices);
    }

    // public function transformInvoices($invoices)
    // {
    //     $result = [];

    //     foreach ($invoices as $invoice) {
    //         $last_balance = 0;
    //         // Add a row for the invoice itself
    //         $result[] = [
    //             'invoice_date' => date('d-m-Y', strtotime($invoice['invoice_date'])),
    //             'invoice_number' => $invoice['invoice_number'],
    //             'receipt_date' => date('d-m-Y', strtotime($invoice['invoice_date'])), // No receipt_date for the invoice itself
    //             'month' => date('F, Y', strtotime($invoice['invoice_date'])),
    //             'project_name' => $invoice['project']['project_name'] ?? null,
    //             'debit' => $invoice['total_amount'],
    //             'credit' => null,
    //             'balance' => $invoice['total_amount'],
    //             'type' => "Invoice",
    //             'branch' => $invoice['branch']['name'] ?? null
    //         ];




    //         foreach ($invoice['payments'] as $payment) {
    //             $last_balance = $invoice['total_amount'] - $payment['amount_received'];
    //             $result[] = [
    //                 'invoice_date' => date('d-m-Y', strtotime($invoice['invoice_date'])),
    //                 'invoice_number' => $invoice['invoice_number'],
    //                 'receipt_date' => date('d-m-Y', strtotime($payment['payment_date'])),
    //                 'month' => date('F, Y', strtotime($payment['payment_date'])),
    //                 'project_name' => $invoice['project']['project_name'] ?? null,
    //                 'debit' => null,
    //                 'credit' => $payment['amount_received'],
    //                 'balance' => $last_balance, // Adjust the balance accordingly
    //                 'type' => "Invoice Payment"
    //             ];
    //         }


    //         foreach ($invoice['credit_notes'] as $creditNote) {
    //             $last_balance =  $last_balance - $creditNote['total_amount'];
    //             $result[] = [
    //                 'invoice_date' => date('d-m-Y', strtotime($invoice['invoice_date'])),
    //                 'invoice_number' => $invoice['invoice_number'],
    //                 'receipt_date' => date('d-m-Y', strtotime($creditNote['credit_note_date'])),
    //                 'month' => date('F, Y', strtotime($creditNote['credit_note_date'])),
    //                 'project_name' => $invoice['project']['project_name'] ?? null,
    //                 'debit' => null,
    //                 'credit' => $creditNote['total_amount'],
    //                 'balance' => $last_balance, // Adjust the balance accordingly
    //                 'type' => "Credit Note"
    //             ];
    //         }
    //     }

    //     return $result;
    // }

    public function transformInvoices($invoices)
    {
        $result = [];

        foreach ($invoices as $invoice) {
            $last_balance = 0;
            $branchName = $invoice['branch']['name'] ?? null;
            $projectName = $invoice['project']['project_name'] ?? null;

            // Add a row for the invoice itself
            $result[] = [
                'invoice_date' => date('d-m-Y', strtotime($invoice['invoice_date'])),
                'invoice_number' => $invoice['invoice_number'],
                'receipt_date' => date('d-m-Y', strtotime($invoice['invoice_date'])),
                'month' => date('F, Y', strtotime($invoice['invoice_date'])),
                'project_name' => $projectName,
                'debit' => $invoice['total_amount'],
                'credit' => null,
                'balance' => $invoice['total_amount'],
                'type' => "Invoice",
                'branch' => $branchName,
            ];

            // Payments
            foreach ($invoice['payments'] as $payment) {
                $last_balance = $invoice['total_amount'] - $payment['amount_received'];
                $result[] = [
                    'invoice_date' => date('d-m-Y', strtotime($invoice['invoice_date'])),
                    'invoice_number' => $invoice['invoice_number'],
                    'receipt_date' => date('d-m-Y', strtotime($payment['payment_date'])),
                    'month' => date('F, Y', strtotime($payment['payment_date'])),
                    'project_name' => $projectName,
                    'debit' => null,
                    'credit' => $payment['amount_received'],
                    'balance' => $last_balance,
                    'type' => "Invoice Payment",
                    'branch' => $branchName,
                ];
            }

            // Credit Notes
            foreach ($invoice['credit_notes'] as $creditNote) {
                $last_balance -= $creditNote['total_amount'];
                $result[] = [
                    'invoice_date' => date('d-m-Y', strtotime($invoice['invoice_date'])),
                    'invoice_number' => $invoice['invoice_number'],
                    'receipt_date' => date('d-m-Y', strtotime($creditNote['credit_note_date'])),
                    'month' => date('F, Y', strtotime($creditNote['credit_note_date'])),
                    'project_name' => $projectName,
                    'debit' => null,
                    'credit' => $creditNote['total_amount'],
                    'balance' => $last_balance,
                    'type' => "Credit Note",
                    'branch' => $branchName,
                ];
            }
        }

        return $result;
    }
}