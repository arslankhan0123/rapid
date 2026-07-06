<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Termination;
use App\Models\Revoke;
use App\Models\Employee;
use Carbon\Carbon;

class ProcessEmployees extends Command
{
    protected $signature = 'employees:process';
    protected $description = 'Process scheduled terminations and revokes';

    public function handle()
    {
        $today = Carbon::today();

        // 🔴 Process Terminations
        $terminations = Termination::where('status', 1)
            ->whereDate('date', '<=', $today)
            ->get();

        foreach ($terminations as $termination) {
            $employee = Employee::find($termination->employee_id);
            if ($employee && $employee->status != 0) {
                $employee->status = 0; // inactive
                $employee->save();
                $this->info("Employee {$employee->id} terminated on {$termination->date}");
            }
        }

        // 🟢 Process Revokes
        $revokes = Revoke::where('status', 1)
            ->whereDate('date', '<=', $today)
            ->get();

        foreach ($revokes as $revoke) {
            $employee = Employee::find($revoke->employee_id);
            if ($employee && $employee->status != 1) {
                $employee->status = 1; // active
                $employee->save();
                $this->info("Employee {$employee->id} reactivated on {$revoke->date}");
            }
        }
    }
}
