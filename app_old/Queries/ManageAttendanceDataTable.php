<?php

namespace App\Queries;


use App\Models\Employee;
use Illuminate\Database\Eloquent\Builder;
use App\Models\ProjectMember;
use App\Models\NewAttendance;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Class TagDataTable
 */
class ManageAttendanceDataTable
{
    /**
     * @param  array  $input
     * @return Employee
     */

    // In ManageAttendanceDataTable.php - Even simpler version
    public function get($input = [])
    {
        try {
            $month = $input['month'] ?? null;
            $branchId = $input['branch_id'] ?? null;

            Log::info('ManageAttendanceDataTable - Input:', [
                'month' => $month,
                'branch_id' => $branchId,
                'all_input' => $input
            ]);

            // Build base query with relationships
            $query = Employee::with([
                'employeeRates' => function ($q) use ($input) {
                    if (isset($input['customer_id'], $input['project_id'], $input['month'])) {
                        $q->where('customer_id', $input['customer_id'])
                            ->where('project_id', $input['project_id'])
                            ->whereBetween('month', [
                                $input['month'] . '-01',
                                date('Y-m-t', strtotime($input['month'] . '-01'))
                            ]);
                    }
                },
                'projectMember',
                'projectMember.project',
                'projectMember.project.customer',
                'department',
                'designation',
                'attendance' => function ($q) use ($input) {
                    if (isset($input['month']) && $input['month']) {
                        $year = substr($input['month'], 0, 4);
                        $monthNum = substr($input['month'], 5, 2);
                        $q->whereYear('date', $year)
                            ->whereMonth('date', $monthNum);
                    }
                },
                'branch',
                'leaveApplications' => function ($q) use ($input) {
                    if (isset($input['month']) && $input['month']) {
                        $startOfMonth = $input['month'] . '-01';
                        $endOfMonth = date('Y-m-t', strtotime($startOfMonth));
                        $q->where(function ($query) use ($startOfMonth, $endOfMonth) {
                            $query->where('from_date', '<=', $endOfMonth)
                                ->where('end_date', '>=', $startOfMonth)
                                ->where('status', 1);
                        });
                    }
                },
                'termination' // Added termination relationship
            ]);

            // Apply branch filter
            if ($branchId) {
                $query->where('branch_id', $branchId);
            }

            // Apply other filters
            if (isset($input['department_id']) && $input['department_id']) {
                $query->where('department_id', $input['department_id']);
            }

            if (isset($input['desgnation_id']) && $input['desgnation_id']) {
                $query->where('designation_id', $input['desgnation_id']);
            }

            if (isset($input['employee_id']) && $input['employee_id']) {
                $query->where('id', $input['employee_id']);
            }

            // **CRITICAL LOGIC: Month filtering**
            if ($month) {
                $startOfMonth = $month . '-01';
                $endOfMonth = date('Y-m-t', strtotime($startOfMonth));

                // Get employee IDs who have salary for this month+branch
                $employeeIdsWithSalary = DB::table('salary_sheets')
                    ->select('salary_sheets.employee_id')
                    ->join('salary_generates', 'salary_sheets.salary_generate_id', '=', 'salary_generates.id')
                    ->where('salary_generates.salary_month', $month)
                    ->where('salary_generates.branch_id', $branchId)
                    ->groupBy('salary_sheets.employee_id')
                    ->pluck('employee_id')
                    ->toArray();

                Log::info("Employee IDs with salary for {$month}, branch {$branchId}:", $employeeIdsWithSalary);

                // **UPDATED: Show employees based on termination status FIRST**
                $query->where(function ($q) use ($startOfMonth, $endOfMonth, $employeeIdsWithSalary) {
                    // **GROUP 1: Employees with termination records**
                    $q->where(function ($terminationQuery) use ($startOfMonth, $endOfMonth) {
                        // Show terminated employees who were terminated on or after month start
                        // (meaning they were employed during at least part of this month)
                        $terminationQuery->whereHas('termination', function ($t) use ($startOfMonth) {
                            $t->where('date', '>=', $startOfMonth);
                        });
                    })
                        // **GROUP 2: Employees WITHOUT termination records**
                        ->orWhere(function ($nonTerminationQuery) use ($employeeIdsWithSalary) {
                            // For non-terminated employees, check status OR salary
                            $nonTerminationQuery->whereDoesntHave('termination')
                                ->where(function ($statusQuery) use ($employeeIdsWithSalary) {
                                    if (!empty($employeeIdsWithSalary)) {
                                        $statusQuery->where('status', 1)
                                            ->orWhereIn('id', $employeeIdsWithSalary);
                                    } else {
                                        $statusQuery->where('status', 1);
                                    }
                                });
                        });
                });

                // Filter by join date (must have joined before or during this month)
                $query->whereDate('join_date', '<=', $endOfMonth);
            } else {
                // No month selected: show only active employees who are not terminated
                $query->where('status', 1)
                    ->where(function ($q) {
                        // Employee either has NO termination record
                        $q->whereDoesntHave('termination')
                            // OR termination date is in future (not yet terminated)
                            ->orWhereHas('termination', function ($terminationQuery) {
                                $terminationQuery->where('date', '>', Carbon::today());
                            });
                    });
            }

            // Search by iqama no or name
            if (isset($input['iqama_no']) && $input['iqama_no']) {
                $query->where(function ($q) use ($input) {
                    $q->where('iqama_no', 'LIKE', '%' . $input['iqama_no'] . '%')
                        ->orWhere('name', 'LIKE', '%' . $input['iqama_no'] . '%');
                });
            }

            $employees = $query->get();

            Log::info("Total employees found: " . $employees->count());

            // Process leave dates AND termination logic for attendance
            $employees->map(function ($employee) use ($month) {
                $leaveDates = [];
                foreach ($employee->leaveApplications as $leave) {
                    $start = Carbon::parse($leave->from_date);
                    $end = Carbon::parse($leave->end_date);
                    for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
                        $leaveDates[] = $date->format('Y-m-d');
                    }
                }
                $employee->leave_dates = $leaveDates;

                // **NEW: Add termination date for attendance processing**
                if ($employee->termination) {
                    $employee->termination_date = $employee->termination->date;
                    $employee->termination_year_month = substr($employee->termination->date, 0, 7);
                }

                return $employee;
            });

            return $employees;
        } catch (\Exception $e) {
            Log::error('ManageAttendanceDataTable Error: ' . $e->getMessage());
            Log::error('File: ' . $e->getFile() . ' Line: ' . $e->getLine());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return collect([]);
        }
    }

    // public function get($input = [])
    // {
    //     /** @var Employee $query */
    //     $query = Employee::with([
    //         'employeeRates' => function ($q) use ($input) {
    //             // Ensure all three parameters are present
    //             if (isset($input['customer_id'], $input['project_id'], $input['month'])) {
    //                 $q->where('customer_id', $input['customer_id'])
    //                     ->where('project_id', $input['project_id'])
    //                     ->whereBetween('month', [
    //                         $input['month'] . '-01', // Start of the month
    //                         date('Y-m-t', strtotime($input['month'] . '-01')) // End of the month
    //                     ]);
    //             }
    //         },
    //         'projectMember',
    //         'projectMember.project',
    //         'projectMember.project.customer',
    //         'department',
    //         'designation',
    //         'attendance',
    //         'branch',
    //         'leaveApplications' => function ($q) use ($input) {
    //             if (isset($input['month']) && $input['month']) {
    //                 $startOfMonth = $input['month'] . '-01';
    //                 $endOfMonth = date('Y-m-t', strtotime($startOfMonth));

    //                 $q->where(function ($query) use ($startOfMonth, $endOfMonth) {
    //                     $query->where('from_date', '<=', $endOfMonth)
    //                         ->where('end_date', '>=', $startOfMonth)->where('status', 1);
    //                 });
    //             }
    //         }
    //     ]);
    //     if (isset($input['branch_id'])) {
    //         $query->where('branch_id', $input['branch_id']);
    //     }



    //     // ->whereHas('projectMember') // This ensures only employees with project members are fetched


    //     // if (isset($input['project_id']) && $input['project_id']) {
    //     //     $query->whereHas('projectMember', function ($q) use ($input) {
    //     //         $q->where('owner_id', $input['project_id']);
    //     //     });
    //     // }

    //     // if (isset($input['customer_id']) && $input['customer_id']) {
    //     //     $query->whereHas('projectMember.project.customer', function ($q) use ($input) {
    //     //         $q->where('id', $input['customer_id']);
    //     //     });
    //     // }

    //     if (isset($input['department_id']) && $input['department_id']) {
    //         $query->whereHas('department', function ($q) use ($input) {
    //             $q->where('id', $input['department_id']);
    //         });
    //     }

    //     if (isset($input['desgnation_id']) && $input['desgnation_id']) {
    //         $query->whereHas('designation', function ($q) use ($input) {
    //             $q->where('id', $input['desgnation_id']);
    //         });
    //     }
    //     if (isset($input['employee_id']) && $input['employee_id']) {
    //         $query->where('id', $input['employee_id']);
    //     }

    //     // Filter by month and ensure attendance records are included
    //     if (isset($input['month']) && $input['month']) {
    //         $query->with('attendance', function ($q) use ($input) {
    //             $q->whereYear('date', date('Y', strtotime($input['month'])))
    //                 ->whereMonth('date', date('m', strtotime($input['month'])));
    //         });


    //         $lastDayOfMonth = date('Y-m-t', strtotime($input['month'] . '-01'));
    //         $query->whereDate('join_date', '<=', $lastDayOfMonth);

    //         $query->with([
    //             'attendance' => function ($q) use ($input) {
    //                 $q->whereYear('date', date('Y', strtotime($input['month'])))
    //                     ->whereMonth('date', date('m', strtotime($input['month'])));
    //             }
    //         ]);
    //     }
    //     if (isset($input['iqama_no']) && $input['iqama_no']) {
    //         $query->where(function ($q) use ($input) {
    //             $q->where('iqama_no', 'LIKE', '%' . $input['iqama_no'] . '%')
    //                 ->orWhere('name', 'LIKE', '%' . $input['iqama_no'] . '%');
    //         });
    //     }



    //     $query->where('status', 1);

    //     $query->check = "Test";

    //     $query->get();
    //     $employees = $query->get();

    //     $employees->map(function ($employee) {
    //         $leaveDates = [];

    //         foreach ($employee->leaveApplications as $leave) {
    //             $start = \Carbon\Carbon::parse($leave->from_date);
    //             $end = \Carbon\Carbon::parse($leave->end_date);

    //             // Generate each date between from_date and end_date
    //             for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
    //                 $leaveDates[] = $date->format('Y-m-d');
    //             }
    //         }

    //         // Add leave dates to the employee object
    //         $employee->leave_dates = $leaveDates;

    //         return $employee;
    //     });

    //     return $employees;
    // }
}
