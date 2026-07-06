<?php

namespace App\Queries;

use App\Models\Term;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class TagDataTable
 */
class TermDataTable
{
    /**
     * @param  array  $input
     * @return Term
     */
    public function get($input = [])
    {
        /** @var Term $query */
        $query = Term::orderBy('created_at', 'desc')->get();
        return $query;
    }
}
