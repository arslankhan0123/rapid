<?php

namespace App\Repositories;

use App\Models\HourlyTimesheet;
use App\Models\MonthlyTimesheet;
use Illuminate\Support\Arr;

class CasualEmployeeTimesheetRepository
{
    public function createHourlyTimesheet($input)
    {
        return HourlyTimesheet::create(Arr::only($input, [
            'casual_employee_id',
            'timesheet_date',
            'working_hours',
            'rate_per_hour',
            'total_amount',
            'safety',
            'absent_deduction',
            'advance',
            'net_amount',
            'batch_id',
            'client_name',
            'project_name',
        ]));
    }

    public function createMonthlyTimesheet($input)
    {
        return MonthlyTimesheet::create(Arr::only($input, [
            'casual_employee_id',
            'month_year',
            'total_days',
            'overtime',
            'basic_salary',
            'ot_amount',
            'safety',
            'absent_deduction',
            'advance',
            'net_amount',
            'batch_id',
            'client_name',
            'project_name',
        ]));
    }

    public function updateHourlyTimesheet($input, $hourlyTimesheet)
    {
        // Calculate amounts before updating
        // $workingHours = $input['working_hours'] ?? 0;
        // $ratePerHour = $input['rate_per_hour'] ?? 0;
        // $safety = $input['safety'] ?? 0;
        // $absentDeduction = $input['absent_deduction'] ?? 0;
        // $advance = $input['advance'] ?? 0;

        // $totalAmount = $workingHours * $ratePerHour;
        // $netAmount = $totalAmount + $safety - $absentDeduction - $advance;

        // $input['total_amount'] = $totalAmount;
        // $input['net_amount'] = $netAmount;

        $hourlyTimesheet->update(Arr::only($input, [
            'batch_id', // Add this
            'casual_employee_id',
            'timesheet_date',
            'working_hours',
            'rate_per_hour',
            'total_amount',
            'safety',
            'absent_deduction',
            'advance',
            'net_amount',
            'client_name',
            'project_name'
        ]));

        return $hourlyTimesheet;
    }

    public function updateMonthlyTimesheet($input, $monthlyTimesheet)
    {
        // Calculate amounts before updating
        // $totalDays = $input['total_days'] ?? 0;
        // $overtime = $input['overtime'] ?? 0;
        // $basicSalary = $input['basic_salary'] ?? 0;
        // $safety = $input['safety'] ?? 0;
        // $absentDeduction = $input['absent_deduction'] ?? 0;
        // $advance = $input['advance'] ?? 0;

        // // Calculate OT amount
        // $otAmount = $overtime * ($basicSalary / 30 / 8) * 1.5;
        // $netAmount = $basicSalary + $otAmount + $safety - $absentDeduction - $advance;

        // $input['ot_amount'] = $otAmount;
        // $input['net_amount'] = $netAmount;

        $monthlyTimesheet->update(Arr::only($input, [
            'batch_id', // Add this
            'casual_employee_id',
            'month_year',
            'total_days',
            'overtime',
            'basic_salary',
            'ot_amount',
            'safety',
            'absent_deduction',
            'advance',
            'net_amount',
            'client_name',
            'project_name'
        ]));

        return $monthlyTimesheet;
    }
}
