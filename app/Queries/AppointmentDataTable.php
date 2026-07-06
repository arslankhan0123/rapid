<?php

namespace App\Queries;

use App\Models\Appointment;
use Illuminate\Database\Eloquent\Builder;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * Class TagDataTable
 */
class AppointmentDataTable
{
    /**
     * @param  array  $input
     * @return Appointment
     */
    public function get($input = [])
    {
        /** @var Appointment $query */
        $today = Carbon::today()->toDateString();

        $query = Appointment::orderByRaw("
            CASE 
                WHEN appointment_date = ? THEN 0
                WHEN appointment_date > ? THEN 1
                ELSE 2
            END ASC,
            appointment_date ASC,
            appointment_time ASC
        ", [$today, $today])->get();

        return $query;
    }
}
