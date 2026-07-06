<?php

namespace App\Queries;

use App\Models\SalarySheet;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Account;
use App\Models\CashTransfer;
use App\Models\JournalVoucher;
use Carbon\Carbon;
use App\Models\SalaryAdvance;
use App\Models\Expense;

/**
 * Class TagDataTable
 */
class AccountStatementDataTable
{

    public function get($input = [])
    {


        /** @var Account $query */
        $query = Account::with(['branch', 'receivedBy', 'from', 'to']);

        // Filter by branch_id
        if (!empty($input['branch_id'])) {
            $query->where('branch_id', $input['branch_id']);
        }

        // Filter by account_id
        if (!empty($input['account_id'])) {
            $query->where('id', $input['account_id']);
        }

        // Get the accounts
        $accounts = $query->get();

        // Process accounts, including related CashTransfer and JournalVoucher data
        $processedResults = [];
        foreach ($accounts as $account) {
            // Include account's opening balance as an initial row
            $openingBalanceRow = [
                'doc_date' => Carbon::parse($account->updated_at)->format('d-m-Y'), // No specific date for opening balance
                'type' => 'Accounts(Opening Balance)',
                'description' => $account->account_name,
                'debit' =>abs( $account->opening_balance),
                'credit' => 0,
                'balance' => abs($account->opening_balance),
            ];
            $processedResults[] = $openingBalanceRow;

            $previousBalance = $account->opening_balance;


            //fetch salary Advance

            $salaryAdvance = SalaryAdvance::where('account_id', $account->id)
                ->when(!empty($input['branch_id']), function ($q) use ($input) {
                    $q->where('branch_id', $input['branch_id']);
                })
                ->when(!empty($input['from_date']) && !empty($input['to_date']), function ($q) use ($input) {
                    $q->whereBetween('date', [$input['from_date'], $input['to_date']]);
                })
                ->when(!empty($input['from_date']), function ($q) use ($input) {
                    $q->where('date', '>=', $input['from_date']);
                })
                ->when(!empty($input['to_date']), function ($q) use ($input) {
                    $q->where('date', '<=', $input['to_date']);
                })
                ->get();

            foreach ($salaryAdvance as $advance) {
                $row = [
                    'doc_date' => $advance->date ? (new \DateTime($advance->date))->format('d-m-Y') : '',
                    'type' => 'Salary Advance',
                    'description' => $advance->description ?? '',
                    'debit' => 0,
                    'credit' => abs( $advance->amount),
                    'balance' =>abs( $previousBalance - $advance->amount),
                ];

                $previousBalance = $row['balance'];
                $processedResults[] = $row;
            }

            //Fetching expense
            $expenses = Expense::where('payment_mode_id', $account->id)
                ->when(!empty($input['branch_id']), function ($q) use ($input) {
                    $q->where('branch_id', $input['branch_id']);
                })
                ->when(!empty($input['from_date']) && !empty($input['to_date']), function ($q) use ($input) {
                    $q->whereBetween('date', [$input['from_date'], $input['to_date']]);
                })
                ->when(!empty($input['from_date']), function ($q) use ($input) {
                    $q->where('date', '>=', $input['from_date']);
                })
                ->when(!empty($input['to_date']), function ($q) use ($input) {
                    $q->where('date', '<=', $input['to_date']);
                })
                ->get();

            foreach ($expenses as $expense) {
                $row = [
                    'doc_date' => $expense->expense_date ? (new \DateTime($expense->expense_date))->format('d-m-Y') : '',
                    'type' => 'Expense',
                    'description' => $expense->name ?? '',
                    'debit' => 0,
                    'credit' => abs($expense->amount),
                    'balance' => abs($previousBalance - $expense->amount),
                ];

                $previousBalance = $row['balance'];
                $processedResults[] = $row;
            }


            // Fetch related CashTransfers (incoming and outgoing)
            $cashTransfers = CashTransfer::where(function ($q) use ($account) {
                $q->where('from_account', $account->id)
                    ->orWhere('to_account', $account->id);
            })
                ->when(!empty($input['branch_id']), function ($q) use ($input) {
                    $q->where('branch_id', $input['branch_id']);
                })
                ->when(!empty($input['from_date']) && !empty($input['to_date']), function ($q) use ($input) {
                    $q->whereBetween('created_at', [$input['from_date'], $input['to_date']]);
                })
                ->when(!empty($input['from_date']), function ($q) use ($input) {
                    $q->where('created_at', '>=', $input['from_date']);
                })
                ->when(!empty($input['to_date']), function ($q) use ($input) {
                    $q->where('created_at', '<=', $input['to_date']);
                })
                ->get();

            foreach ($cashTransfers as $transfer) {
                $isIncoming = $transfer->to_account == $account->id;
                $amount = $transfer->transfer_amount;

                $row = [
                    'doc_date' => $transfer->created_at ? (new \DateTime($transfer->created_at))->format('d-m-Y') : '',
                    'type' => $isIncoming ? 'Cash Transfer (Incoming)' : 'Cash Transfer (Outgoing)',
                    'description' => $account->account_name,
                    'debit' => abs($isIncoming ? $amount : 0),
                    'credit' =>abs( $isIncoming ? 0 : $amount),
                    'balance' =>abs( $isIncoming ? $previousBalance + $amount : $previousBalance - $amount),
                ];

                $previousBalance = $row['balance'];
                $processedResults[] = $row;
            }

            // Fetch related JournalVouchers
            $journalVouchers = JournalVoucher::where('account_id', $account->id)
                ->when(!empty($input['branch_id']), function ($q) use ($input) {
                    $q->where('branch_id', $input['branch_id']);
                })
                ->when(!empty($input['from_date']) && !empty($input['to_date']), function ($q) use ($input) {
                    $q->whereBetween('created_at', [$input['from_date'], $input['to_date']]);
                })
                ->when(!empty($input['from_date']), function ($q) use ($input) {
                    $q->where('created_at', '>=', $input['from_date']);
                })
                ->when(!empty($input['to_date']), function ($q) use ($input) {
                    $q->where('created_at', '<=', $input['to_date']);
                })
                ->get();

            foreach ($journalVouchers as $voucher) {
                $row = [
                    'doc_date' => $voucher->created_at ? (new \DateTime($voucher->created_at))->format('d-m-Y') : '',
                    'type' => 'Journal Voucher',
                    'description' => $voucher->description ?? '',
                    'debit' => 0,
                    'credit' => abs($voucher->amount),
                    'balance' =>abs( $previousBalance - $voucher->amount),
                ];

                $previousBalance = $row['balance'];
                $processedResults[] = $row;
            }
        }

        return $processedResults;
    }
}
