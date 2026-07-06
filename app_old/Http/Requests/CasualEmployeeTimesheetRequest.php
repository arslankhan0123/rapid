<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CasualEmployeeTimesheetRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            // 'timesheet_date' => 'required|date',
            'hourly_entries' => 'required|array|min:1',
            'hourly_entries.*.casual_employee_id' => 'required|exists:casual_employees,id',
            'hourly_entries.*.working_hours' => 'required|numeric|min:0',
            'hourly_entries.*.rate_per_hour' => 'required|numeric|min:0',
            'hourly_entries.*.safety' => 'required|numeric|min:0',
            'hourly_entries.*.absent_deduction' => 'required|numeric|min:0',
            'hourly_entries.*.advance' => 'required|numeric|min:0',

            // 'month_year' => 'required|date',
            'monthly_entries' => 'required|array|min:1',
            'monthly_entries.*.casual_employee_id' => 'required|exists:casual_employees,id',
            'monthly_entries.*.total_days' => 'required|integer|min:0',
            'monthly_entries.*.overtime' => 'required|numeric|min:0',
            'monthly_entries.*.basic_salary' => 'required|numeric|min:0',
            'monthly_entries.*.safety' => 'required|numeric|min:0',
            'monthly_entries.*.absent_deduction' => 'required|numeric|min:0',
            'monthly_entries.*.advance' => 'required|numeric|min:0',
        ];
    }

    public function attributes()
    {
        return [
            'hourly_entries.*.casual_employee_id' => 'employee',
            'hourly_entries.*.working_hours' => 'working hours',
            'hourly_entries.*.rate_per_hour' => 'rate per hour',
            'monthly_entries.*.casual_employee_id' => 'employee',
            'monthly_entries.*.total_days' => 'total days',
            'monthly_entries.*.basic_salary' => 'basic salary',
        ];
    }
}
