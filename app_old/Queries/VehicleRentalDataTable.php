<?php

namespace App\Queries;


use App\Models\VehicleRental;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class TagDataTable
 */
class VehicleRentalDataTable
{

    public function get($input = [])
    {
        /** @var VehicleRental $query */
        $query = VehicleRental::query();

        // Filter by vehicle type if provided
        // if (!empty($input['filterType'])) {
        //     $query->where('type', $input['filterType']);
        // }



        // Filter by agreement date range if provided
        // if (!empty($input['startDate']) && !empty($input['endDate'])) {
        //     $query->whereBetween('agreement_date', [$input['startDate'], $input['endDate']]);
        // }

        // Filter by expiry date if provided

        // if (!empty($input['expiryDate'])) {
        //     $query->where('expiry_date', $input['expiryDate']);
        // }

        // Filter expiry_date based on startMonth and endMonth
        // if (!empty($input['startMonth']) && !empty($input['endMonth'])) {
        //     // If both startMonth and endMonth are provided, filter within the range
        //     $query->whereRaw('MONTH(expiry_date) BETWEEN ? AND ?', [$input['startMonth'], $input['endMonth']]);
        // } elseif (!empty($input['startMonth'])) {
        //     // If only startMonth is provided, filter expiry_date for that exact month
        //     $query->whereRaw('MONTH(expiry_date) = ?', [$input['startMonth']]);
        // } elseif (!empty($input['endMonth'])) {
        //     // If only endMonth is provided, filter expiry_date for that exact month
        //     $query->whereRaw('MONTH(expiry_date) = ?', [$input['endMonth']]);
        // }


        if (!empty($input['startMonth'])) {
            $query->whereRaw('? BETWEEN MONTH(agreement_date) AND MONTH(expiry_date)', [$input['startMonth']]);
        }

        // Filter by year if provided
        if (!empty($input['year']) && $input['year'] != 0) {
            $query->whereYear('agreement_date', $input['year']);
        }

        if (!empty($input['agreement_type']) && $input['agreement_type'] != null) {
            $query->where('agreement_type', $input['agreement_type']);
        }

        // Retrieve data and calculate month difference and monthly amount
        $result = $query->get()->map(function ($item) {
            $start = \Carbon\Carbon::parse($item->agreement_date);
            $end = \Carbon\Carbon::parse($item->expiry_date);

            // Calculate the number of months (inclusive)
            $months = $start->diffInMonths($end) + 1;

            // Calculate the monthly amount
            $item->monthly_amount = $item->amount / $months;

            return $item;
        });
        return $result;
    }
}
