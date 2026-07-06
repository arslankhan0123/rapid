<?php

namespace App\Queries;

use App\Models\Holiday;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Expense;

/**
 * Class TagDataTable
 */
class ExpenseDataTable
{
    /**
     * @param  array  $input
     * @return Expense
     */
    public function get($input = [])
    {
        /** @var Expense $query */
        $query = Expense::with(['paymentMode:id,account_name,branch_id', 'expenseCategory', 'branch:id,name', 'expenseSubCategory','supplier']);


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

        // Apply filters based on the input
        if (!empty($input['account'])) {
            $query->where('payment_mode_id', $input['account']);
        }

        if (!empty($input['expense_sub_category'])) {
            $query->where('sub_category_id', $input['expense_sub_category']);
        }

        if (!empty($input['expense_category'])) {
            $query->where('expense_category_id', $input['expense_category']);
        }

        if (!empty($input['start_date'])) {
            $query->where('expense_date', '>=', $input['start_date']);
        }

        if (!empty($input['end_date'])) {
            $query->where('expense_date', '<=', $input['end_date']);
        }

        if (!empty($input['month'])) {
            $query->whereMonth('expense_date', date('m', strtotime($input['month'])));
        }

        // Order the query results by created_at in descending order
        $query = $query->orderBy('created_at', 'desc')->get();

        return $query;
    }
}
