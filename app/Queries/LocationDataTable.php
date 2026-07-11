<?php

namespace App\Queries;


use App\Models\Location;
use Illuminate\Database\Eloquent\Builder;

class LocationDataTable
{
    /**
     * @param  array  $input
     * @return Location
     */
    public function get($input = [])
    {
        /** @var Location $query */
        $query = Location::with(['country'])->orderBy('created_at', 'desc')->get();
        return $query;
    }
}
