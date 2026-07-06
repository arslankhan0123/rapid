<?php

namespace App\Queries;


use App\Models\Area;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class TagDataTable
 */
class AreaDataTable
{
    /**
     * @param  array  $input
     * @return Area
     */
    public function get($input = [])
    {
        /** @var Area $query */
        $query = Area::with(['country','city','state'])->orderBy('created_at', 'desc')->get();
        return $query;
    }
}
