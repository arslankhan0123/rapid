<?php

namespace App\Queries;

use Illuminate\Support\Facades\DB;

class CasualEmployeeTimesheetDataTable
{
    public function get($input = [])
    {
        $query = DB::table(function ($unionQuery) {
            $unionQuery->select(
                'batch_id',
                DB::raw("MIN(timesheet_date) as date"),
                DB::raw("'Hourly' as type"),
                DB::raw("MAX(client_name) as client_name"),
                DB::raw("MAX(project_name) as project_name"),
                DB::raw("COUNT(DISTINCT casual_employee_id) as employee_count"),
                DB::raw("SUM(net_amount) as total_amount"),
                DB::raw("MAX(created_at) as created_at")
            )
                ->from('hourly_timesheets')
                ->groupBy('batch_id')
                ->unionAll(
                    DB::table('monthly_timesheets')
                        ->select(
                            'batch_id',
                            DB::raw("MIN(month_year) as date"),
                            DB::raw("'Monthly' as type"),
                            DB::raw("MAX(client_name) as client_name"),
                            DB::raw("MAX(project_name) as project_name"),
                            DB::raw("COUNT(DISTINCT casual_employee_id) as employee_count"),
                            DB::raw("SUM(net_amount) as total_amount"),
                            DB::raw("MAX(created_at) as created_at")
                        )
                        ->groupBy('batch_id')
                );
        }, 'combined_timesheets')
            ->select(
                'batch_id',
                DB::raw("MIN(date) as date"),
                DB::raw("'Hourly & Monthly' as type"),
                DB::raw("MAX(client_name) as client_name"),
                DB::raw("MAX(project_name) as project_name"),
                DB::raw("SUM(employee_count) as employee_count"),
                DB::raw("SUM(total_amount) as total_amount"),
                DB::raw("MAX(created_at) as created_at")
            )
            ->groupBy('batch_id')
            ->when(!empty($input['client_name']), function ($q) use ($input) {
                $q->where('client_name', $input['client_name']);
            })
            ->when(!empty($input['project_name']), function ($q) use ($input) {
                $q->where('project_name', $input['project_name']);
            });

        // Month filter (fix: use "date" column from combined query)
        if (!empty($input['month'])) {
            $month = $input['month']; // format: YYYY-MM
            $query->whereRaw("DATE_FORMAT(date, '%Y-%m') = ?", [$month]);
        }

        $query->orderBy('created_at', 'desc');

        return $query;
    }
}
