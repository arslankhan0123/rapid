<?php

namespace App\Queries;

use App\Models\ExpenseCategory;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class TagDataTable
 */
class ExpenseCategoryDataTable
{
    /**
     * @return ExpenseCategory|Builder
     */
    public function get()
    {
        /** @var ExpenseCategory $query */
        $query = ExpenseCategory::query()->select('expense_categories.*')->withCount('expenses')->latest();

        return $query;
    }
}
