<?php

namespace App\Helpers;

use Carbon\Carbon;

class VatReportHelper
{
    /**
     * Format date to month name and year
     */
    public static function getMonthNameYear($date)
    {
        return Carbon::parse($date)->format('F Y');
    }

    /**
     * Get quarter months
     */
    public static function getQuarterMonths()
    {
        return [
            'q1' => ['January', 'February', 'March'],
            'q2' => ['April', 'May', 'June'],
            'q3' => ['July', 'August', 'September'],
            'q4' => ['October', 'November', 'December'],
        ];
    }

    /**
     * Get quarter short months
     */
    public static function getQuarterShortMonths()
    {
        return [
            'q1' => ['Jan', 'Feb', 'Mar'],
            'q2' => ['Apr', 'May', 'Jun'],
            'q3' => ['Jul', 'Aug', 'Sep'],
            'q4' => ['Oct', 'Nov', 'Dec'],
        ];
    }
}
