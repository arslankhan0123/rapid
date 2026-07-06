<?php

namespace App\Queries;
use App\Models\Rental;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class TagDataTable
 */
class RentalDataTable
{
    /**
     * @param  array  $input
     * @return Rental
     */
    public function get($input = [])
    {
        /** @var Rental $query */
        //
        $query = Rental::with('supplier')->orderBy('created_at', 'desc')->get();
        return $query;
    }
}
