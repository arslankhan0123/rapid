<?php

namespace App\Queries;
use App\Models\Restore;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class TagDataTable
 */
class RestoreDataTable
{
    /**
     * @param  array  $input
     * @return Restore
     */
    public function get($input = [])
    {
        /** @var Restore $query */
        //
        $query = Restore::with('user')->orderBy('created_at', 'desc')->get();
        return $query;
    }
}
