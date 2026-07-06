<?php

namespace App\Queries;

use App\Models\Branch;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class TagDataTable
 */
class BranchDataTable
{
    /**
     * @param  array  $input
     * @return Branch
     */
    public function get($input = [])
    {
        /** @var Branch $query */
        $query = Branch::with(['country', 'currency','bank'])->orderBy('created_at', 'desc')->get();
        return $query;
    }
}
