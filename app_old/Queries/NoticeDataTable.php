<?php

namespace App\Queries;

use App\Models\Notice;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class TagDataTable
 */
class NoticeDataTable
{
    /**
     * @param  array  $input
     * @return Notice
     */
    public function get($input = [])
    {
        /** @var Notice $query */
        $query = Notice::all();
        return $query;
    }
}
