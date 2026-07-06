<?php

namespace App\Queries;

use App\Models\Employee;


/**
 * Class TagDataTable
 */
class EmployeeIDExpireDataTable
{

    public function get($input = [])
    {
        // Extract ID type and month from the input array
        $idType = $input['idType'] ?? 'iqama_no'; // Default to 'iqama_no' if not provided
        $month = $input['month'] ?? null; // Month should be in 'YYYY-MM' format

        // Validate $idType to prevent SQL injection
        $validIdTypes = ['iqama_no', 'tuv_no', 'passport']; // List of valid ID types
        $validExpiryDates = [
            'iqama_no' => 'iqama_no_expiry_date',
            'tuv_no' => 'tuv_no_expiry_date',
            'passport' => 'passport_expiry_date'
        ];

        // Debug: Check received ID type


        // Ensure valid ID type
        if (!in_array($idType, $validIdTypes)) {
            $idType = 'iqama_no'; // Default to 'iqama_no' if invalid
        }

        // Debug: Check resolved ID type


        // Get the appropriate expiry date field based on ID type
        $expiryDateField = $validExpiryDates[$idType];

        // Initialize the query with relationships
        $query = Employee::with(['designation']);

        // Handle month filtering
        if ($month) {
            // Extract the year and month from the given date
            $year = date('Y', strtotime($month));
            $month = date('m', strtotime($month)); // Ensures zero-padding (e.g., '01', '11')

            // Get the last day of the month
            $endOfMonth = date('Y-m-t', strtotime("$year-$month-01"));

            // Debug: Check the end of the month
            // dd($endOfMonth); // Uncomment for debugging

            // Filter by expiry date up to the end of the month
            $query->where($expiryDateField, '<=', $endOfMonth);
        }


        // $query->orderBy($idType, 'desc');

        // Execute the query and return the result
        return $query->orderByDesc('iqama_no_expiry_date')->get();
    }
}
