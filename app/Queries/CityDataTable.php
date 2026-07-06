<?php

namespace App\Queries;


use App\Models\City;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class TagDataTable
 */
class CityDataTable
{
    /**
     * @param  array  $input
     * @return City
     */
    public function get($input = [])
    {
        /** @var City $query */
        $query = City::with(['country','state'])->orderBy('created_at', 'desc')->get();
        return $query;
    }
}
