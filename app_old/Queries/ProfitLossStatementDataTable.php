<?php

namespace App\Queries;

use App\Models\SalarySheet;
use Illuminate\Database\Eloquent\Builder;

use Carbon\Carbon;
use App\Models\Invoice;
use App\Models\CreditNote;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\SalaryGenerate;

/**
 * Class TagDataTable
 */
class ProfitLossStatementDataTable
{
    public function get($input = [])
    {
        // Validate if 'from_date' is set and in the correct format (YYYY-MM)
        if (empty($input['from_date'])) {
            return [];
        }

        // Extract year and month from 'from_date'
        $date = Carbon::createFromFormat('Y-m', $input['from_date']);
        $year = $date->year;
        $month = $date->month;


        // Get Invoices with payment_status = 2 or 3 for the given year and month
        $invoices = Invoice::whereIn('payment_status', [2, 3])
            ->whereYear('invoice_date', $year)
            ->whereMonth('invoice_date', $month)
            ->when(!empty($input['filterBranch']), function ($q) use ($input) {
                // Filter by a specific branch if provided
                $q->where('branch_id', $input['filterBranch']);
            }, function ($q) {
                // Otherwise, filter by the user's associated branches
                $q->whereIn('branch_id', function ($subQuery) {
                    $subQuery->select('branch_id')
                        ->from('users_branches')
                        ->where('user_id', auth()->id());
                });
            })
            ->get();

        // Get CreditNotes with payment_status = 2 or 3 for the given year and month
        $creditNotes = CreditNote::whereYear('credit_note_date', $year)
            ->whereMonth('credit_note_date', $month)
            ->when(!empty($input['filterBranch']), function ($q) use ($input) {
                // Filter by a specific branch if provided
                $q->where('branch_id', $input['filterBranch']);
            }, function ($q) {
                // Otherwise, filter by the user's associated branches
                $q->whereIn('branch_id', function ($subQuery) {
                    $subQuery->select('branch_id')
                        ->from('users_branches')
                        ->where('user_id', auth()->id());
                });
            })
            ->get();
        $salaries = SalaryGenerate::whereYear('generate_date', $year)
            ->whereMonth('generate_date', $month)
            ->get();


        // Get Expenses grouped by category for the given year and month
        $expenses = Expense::whereYear('expense_date', $year)
            ->whereMonth('expense_date', $month)
            ->when(!empty($input['filterBranch']), function ($q) use ($input) {
                // Filter by a specific branch if provided
                $q->where('branch_id', $input['filterBranch']);
            }, function ($q) {
                // Otherwise, filter by the user's associated branches
                $q->whereIn('branch_id', function ($subQuery) {
                    $subQuery->select('branch_id')
                        ->from('users_branches')
                        ->where('user_id', auth()->id());
                });
            })
            ->get()
            ->groupBy('expense_category_id');


        // Initialize totals for Sales and Returns
        $totalSalesCredit = $invoices->sum('total_amount'); // Sales total as credit
        $totalReturnsDebit = $creditNotes->sum('total_amount'); // Returns total as debit

        $totalSalaries = $salaries->sum('amount'); // Returns total as debit

        // Initialize an array to store consolidated results for the data table
        $mergedResults = [];



        // Add consolidated row for Sales
        $mergedResults[] = [
            'type' => 'Sales',
            'debit' => 0,
            'credit' => $totalSalesCredit,
            'date' => $date->format('Y-m'), // Month-Year format for consolidated row
            'order_key' => 1,
        ];

        // Add consolidated row for Returns
        $mergedResults[] = [
            'type' => 'Return',
            'debit' => $totalReturnsDebit,
            'credit' => 0,
            'date' => $date->format('Y-m'), // Month-Year format for consolidated row
            'order_key' => 2,
        ];
        // Add consolidated row for Salaries
        $mergedResults[] = [
            'type' => 'Salaries',
            'debit' => $totalSalaries,
            'credit' => 0,
            'date' => $date->format('Y-m'), // Month-Year format for consolidated row
            'order_key' => 3,
        ];

        // Process Expenses by categorycrepcre
        foreach ($expenses as $categoryId => $expenseGroup) {
            $totalExpense = $expenseGroup->sum('amount');

            // Get category name (assuming a relationship exists in the Expense model)
            $categoryName = ExpenseCategory::find($categoryId)->name ?? 'Unknown';

            $mergedResults[] = [
                'type' => 'Expense',
                'category' => $categoryName,
                'debit' => $totalExpense,
                'credit' => 0,
                'date' => $date->format('Y-m'), // Month-Year format for consolidated row
                'order_key' => 4,
            ];
        }

        // Sort the array by `order_key`
        usort(
            $mergedResults,
            function ($a, $b) {
                return $a['order_key'] <=> $b['order_key'];
            }
        );
        // Return consolidated results for the data table
        return $mergedResults;
    }
}
