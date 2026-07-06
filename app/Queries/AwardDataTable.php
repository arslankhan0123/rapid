<?php

namespace App\Queries;
use App\Models\Award;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class TagDataTable
 */
class AwardDataTable
{
    /**
     * @param  array  $input
     * @return Award
     */
    public function get($input = [])
    {
        /** @var Award $query */
        $query = Award::with(['employee', 'awardedBy'])->orderBy('created_at','desc')->get();
        return $query;
    }
}
