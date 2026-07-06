<?php

namespace App\Repositories;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use App\Models\SalaryGenerate;
use App\Models\Employee;
use App\Models\SalarySheet;
use App\Models\SalaryAdvance;
use App\Models\Loan;
use App\Models\Deduction;
use App\Models\Allowance;
use App\Models\Bonus;
use App\Models\Setting;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


/**
 * Class CustomerRepository
 *
 * @version April 3, 2020, 6:37 am UTC
 */
class SalaryGenerateRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'salary_month',
        'generate_date',
        'generated_by',
        'approved_by',
        'status',
        'approved_date'
    ];

    /**
     * Return searchable fields
     *
     * @return array
     */
    public function getFieldsSearchable()
    {
        return $this->fieldSearchable;
    }

    /**
     * Configure the Model
     **/
    public function model()
    {
        return SalaryGenerate::class;
    }

    public function create($input)
    {
        
        try {
            return DB::transaction(function () use ($input) {
                $salaryMonth = $input['salary_month'];
                $branchId = $input['branch_id'];

                // Check if a record with the same salary_month, branch_id, and status = 1 exists
                $existingApproved = SalaryGenerate::where('salary_month', $salaryMonth)
                    ->where('branch_id', $branchId)
                    ->where('status', 1)
                    ->exists();

                if ($existingApproved) {

                    return false;
                }

                // Check if a record with the same salary_month and branch_id but status = 0 exists
                $existingPending = SalaryGenerate::where('salary_month', $salaryMonth)
                    ->where('branch_id', $branchId)
                    ->where('status', 0)
                    ->first();

                if ($existingPending) {
                    // Delete related salary sheets first
                    $existingPending->salarySheets()->delete();
                    // Then delete the salary generate record
                    $existingPending->delete();
                }

                // Set default values
                $input['status'] = 0;
                $input['approved_date'] = now();

                // Create a new record
                return SalaryGenerate::create(Arr::only($input, [
                    'salary_month',
                    'generate_date',
                    'generated_by',
                    'approved_by',
                    'status',
                    'approved_date',
                    'branch_id'
                ]));
            });
        } catch (Exception $e) {
            dd($e);
            return false;
        }
    }
    public function create_salary($salary_sheets)
    {
        SalarySheet::insert($salary_sheets);
    }

    public function approve_salary(SalaryGenerate $salaryGenerate)
    {
        $salaryGenerate->status = 1;
        $salaryGenerate->save();
        return $salaryGenerate;
    }

    public function generateSalary($month)
    {

        $settings = Setting::pluck('value', 'key')->toArray();
        $overTimeRate = isset($settings['overtime_rate'])
            ? (float) $settings['overtime_rate'] / 100
            : 1.0;

        $salaryAmount = 0;
        $salaryGenerateId = $month->id;
        $branch_id = $month->branch_id;
        // Define working hours per day and weekend days (friday and sunday as weekend)
        $workingHoursPerDay = 8;
        $weekendDays = ['friday']; // Specify weekend days

        $salaryMonth = $month['salary_month']; // Example: '2024-11'
        $year = substr($salaryMonth, 0, 4); // Extract year
        $monthNum = substr($salaryMonth, 5, 2); // Extract month
        
        $checkDate = date("Y-m-d", strtotime($year . '-' . $monthNum . '-01'));


        $lastDayOfMonth = date('Y-m-t', strtotime($salaryMonth . '-01'));
        $daysInMonth = (int) date('t', strtotime($salaryMonth . '-01'));


        $employees = Employee::with([
            'attendance' => function ($query) use ($year, $monthNum, $branch_id) {
                $query->whereYear('date', $year)
                    ->whereMonth('date', $monthNum)
                    ->where('branch_id', $branch_id);
            },
            'allowances' => function ($query) use ($year) {
                $query->whereYear('date', $year);
            },
            'deductions' => function ($query) use ($year, $monthNum) {
                $query->whereYear('date', $year)
                    ->whereMonth('date', $monthNum);
            },
            'advances' => function ($query) use ($year, $monthNum) {
                $query->whereYear('date', $year)
                    ->whereMonth('date', $monthNum);
            },
            'loans',
            // 'loans' => function ($query) use ($year, $monthNum) {
            //     $query->whereYear('date', $year)
            //         ->whereMonth('date', $monthNum);
            // },
            'bonuses' => function ($query) use ($year, $monthNum) {
                $query->whereYear('date', $year)
                    ->whereMonth('date', $monthNum);
            },
            // 'insurances' => function ($query) use ($year, $monthNum) {
            //     $query->whereYear('date', $year)
            //         ->whereMonth('date', $monthNum);
            // },
            'insurances',
            'leaveApplications',
        ])
            ->whereDate('join_date', '<=', $lastDayOfMonth)
            ->where('branch_id', $branch_id)->where('status', 1)->get();


        //dd($employees->toArray());

        $salary_sheets = [];

        // Iterate through each employee
        foreach ($employees as $employee) {
            // Base salary of the employee (assumed gross salary column)
            $gross_salary = $employee->gross_salary;

            // Calculate total allowances for the specified month
            $totalAllowances = $employee->allowances->sum('amount');
            $totalDeductions = $employee->deductions->sum('amount');
            $totalAdvances = $employee->advances->sum('amount');
            $totalInsuranceSum = $employee->insurances->sum('insurance');
            
            //$totalLoans = $employee->loans->sum('installment');
            $totalLoans = 0;
            
            foreach ($employee->loans as $loan) {
                // Here installment_period is actually a date
                $startDate = date("Y-m-1", strtotime($loan->repayment_from));
                // + installment_period months, then -1 day so it includes the last month fully
                $endDate   = date("Y-m-d", strtotime("+" . $loan->installment_period . " months - 1 day", strtotime($startDate)));
                //dd($checkDate, $startDate);
                // If current date is within the repayment period
                if (strtotime($checkDate) >= strtotime($startDate) && strtotime($checkDate) <= strtotime($endDate) && ($loan->status == 'active')) {
                    //dd($loan->installment);
                    $totalLoans += $loan->installment;
                }
            }
            // find leave dates.
            $leaveDates = [];
            $leaveAbsentHours = 0;
            foreach ($employee->leaveApplications as $leave) {
                // Only count approved leaves (status = 1)
                if ($leave->status == 1) {
                    $start = \Carbon\Carbon::parse($leave->from_date);
                    $end = \Carbon\Carbon::parse($leave->end_date);

                    // Generate each date between from_date and end_date
                    for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
                        // Check if the date is within the generated salary month
                        if ($date->format('Y-m') === "$year-$monthNum") {
                            $formattedDate = $date->format('Y-m-d');
                            $leaveDates[] = $formattedDate;

                            $dayOfWeek = date('l', strtotime($formattedDate));
                            if (!in_array(strtolower($dayOfWeek), $weekendDays)) {
                                $leaveAbsentHours += $workingHoursPerDay; // 8 hours per day
                            }
                        }
                    }
                }
            }

            // Add leave dates to the employee object
            $employee->leave_dates = $leaveDates;
            
            //print_r($employee->leave_dates);
            
            
            $totalBonuses = $employee->bonuses->sum('amount');
            $total_overtimes = 0;
            $absense_hours = $leaveAbsentHours;
            $overtimeHours = 0;
 
            // Calculate total working hours in the month (excluding weekend days)
            $workingHours = 0;
            $workingDaysCount = 0;
            $IdealworkingDaysCount = 0;
            $presentDays = 0;

            // Loop through attendance records and calculate working hours
            foreach ($employee->attendance as $attendance) {
                if ($attendance['hours'] > 0) {
                    $presentDays++;
                }
                $join_date = date("Y-m-d", strtotime($employee->join_date));
                
                 $attendanceDate = $attendance['date'];
                $dayOfWeek = date('l', strtotime($attendanceDate)); // Get day of the week (e.g., 'Monday', 'Friday')
                
                // if($join_date > $attendance['date']) {
                //     if (!in_array(strtolower($dayOfWeek), $weekendDays)) {
                //         $IdealworkingDaysCount +=1 ;
                //     }
                //     continue;
                // }
                if((in_array($attendance['date'], $employee->leave_dates))) {
                    if (!in_array(strtolower($dayOfWeek), $weekendDays)) {
                        $IdealworkingDaysCount +=1 ;
                    }
                    continue;
                }
               

                // Check if it's not a weekend day (in the $weekendDays array)
                if (!in_array(strtolower($dayOfWeek), $weekendDays)) {
                    //$workingHours += $attendance['hours'];
                    $workingDaysCount++;
                    $IdealworkingDaysCount +=1;
                    
                    if( $attendance['hours'] < $workingHoursPerDay) {
                        $absense_hours += ($workingHoursPerDay - $attendance['hours']);
                    }
                    
                    if( $attendance['hours'] > $workingHoursPerDay) {
                        $overtimeHours += ($attendance['hours'] - $workingHoursPerDay);
                    }
                
                } else {
                    $overtimeHours += $attendance['hours'];
                } 
                
            }

            // Calculate basic salary based on working days and working hours per day
            $totalWorkingHoursThisMonth = ($workingDaysCount * $workingHoursPerDay); // 8 hours per working day
            $IdealWorkingHoursThisMonth = ($IdealworkingDaysCount * $workingHoursPerDay); // 8 hours per working day
            //dd($totalWorkingHoursThisMonth);
            if($totalWorkingHoursThisMonth != $IdealWorkingHoursThisMonth) {
                $IdealhourlyRate = $employee->basic_salary / $IdealWorkingHoursThisMonth;
                //$employee->basic_salary = $IdealhourlyRate * $totalWorkingHoursThisMonth;
            }
            
            
            
            $workingHours = ($totalWorkingHoursThisMonth + $overtimeHours) - $absense_hours;
            // Avoid division by zero by checking if total working hours are greater than 0
            if ($totalWorkingHoursThisMonth > 0) {
                $hourlyRate = $employee->basic_salary / $totalWorkingHoursThisMonth;
            } else {
                $hourlyRate = 0; 
            }
            $calculatedSalary = $workingHours * $hourlyRate;
            $hourlyDeduction = 0;

 

            //$over_time_after_absent = max(0, ($overtimeHours - $absense_hours));
            // $total_overtimes = ($overtimeHours) * $hourlyRate * $overTimeRate;
            
            $overtimeHourlyRate = ($employee->basic_salary / 30) / 8;
            $total_overtimes = $overtimeHours * $overtimeHourlyRate * $overTimeRate;
            $hourlyDeduction += ($absense_hours * $hourlyRate);
            Log::info('Payroll Calculation Debug', [
                'basic_salary' => $employee->basic_salary,
                'daysInMonth' => $daysInMonth,
                'overtimeHourlyRate' => $overtimeHourlyRate,
                'overtimeHours' => $overtimeHours,
                'overTimeRate' => $overTimeRate,
                'total_overtimes' => $total_overtimes,
                'absense_hours' => $absense_hours,
                'hourlyRate' => $hourlyRate,
                'hourlyDeduction' => $hourlyDeduction,
            ]);
            $absentHourlyRate = ($employee->basic_salary / 30) / 8;
            $hourlyDeduction = $absense_hours * $absentHourlyRate;
            //$hourlyDeduction = $employee->basic_salary - $calculatedSalary;

            if ($workingHours > $totalWorkingHoursThisMonth) {
                //$overtimeHours = $workingHours - $totalWorkingHoursThisMonth;
                //$total_overtimes = $overtimeHours * $hourlyRate * $overTimeRate;
            } else if ($workingHours == $totalWorkingHoursThisMonth) {
                //$absense_hours = 0;
                //$hourlyDeduction = 0;
            } else {
                //$absense_hours = $totalWorkingHoursThisMonth - $workingHours;
                //$hourlyDeduction = $employee->basic_salary - $calculatedSalary;
            }


            // echo $workingHours . " " . $totalWorkingHoursThisMonth . " " . $hourlyDeduction;
            // echo "\n";
            // Additional fields for the new structure
            $totalCommission = 0; // Replace with the actual commission calculation if available
            $totalInsurance = round((($totalInsuranceSum / $IdealWorkingHoursThisMonth) * $workingHours), 2);  // Replace with the actual insurance calculation if available
            $totalLoan = $totalLoans;
            $totalBonus = $totalBonuses;
            // Allowance Calculation:
            // Per day allowance  = totalAllowances / 30
            // Per hour allowance = perDayAllowance / 8
            // If employee has absence hours → deduct (perHourAllowance * absense_hours) from totalAllowances
            // Overtime is NEVER included in allowance
            $perDayAllowance  = $totalAllowances / 30;
            $perHourAllowance = $perDayAllowance / 8;
            $allowanceDeduction = $perHourAllowance * $absense_hours;
            $totalAllowance = round(max(0, $totalAllowances - $allowanceDeduction), 2);
            Log::info('Allowance Calculation Debug', [
                'iqama'               => $employee->iqama_no,
                'totalAllowances'     => $totalAllowances,
                'perDayAllowance'     => $perDayAllowance,
                'perHourAllowance'    => $perHourAllowance,
                'absense_hours'       => $absense_hours,
                'allowanceDeduction'  => $allowanceDeduction,
                'totalAllowance'      => $totalAllowance,
            ]);
            $totalDeduction = $totalDeductions + $hourlyDeduction;


            $netSalary = $employee->basic_salary + $totalAllowance + $totalBonus - $totalDeduction - $totalAdvances - $totalLoan
                - $totalInsurance
                + $total_overtimes;
            $gross_salary = $employee->basic_salary + $totalAllowance + $totalBonus
                + $total_overtimes;
            // Structure the data to match the desired output
            $tmp = [
                'employee_id' => $employee->id,
                'salary_generate_id' => $salaryGenerateId,
                'basic_salary' => $employee->basic_salary ?? 0,
                'salary_advance' => $totalAdvances ?? 0,
                'gross_salary' => $gross_salary ?? 0,
                'state_income_tax' => 0, // Adjust this if there's a tax calculation
                'loan' => $totalLoan,
                'total_bonus' => $totalBonus ?? 0,
                'total_allowances' => $totalAllowances,
                'total_commission' => $totalCommission,
                'total_insurance' => $totalInsurance,
                'total_deduction' => $totalDeduction,
                'net_salary' => $netSalary,
                'hourly_deduction' => $hourlyDeduction,
                'allowance_deduction' => round($allowanceDeduction, 2),
                'total_overtimes' => $total_overtimes,
                'overtime_hours' => $overtimeHours ?? 0,
                'absence_hours' => $absense_hours,
                'worked_hours' => $workingHours ?? 0,
                'working_hours' => $IdealWorkingHoursThisMonth ?? 0,
                'branch_id' => $branch_id,
                'working_days' => $presentDays,
                'manual_deduction' => $totalDeductions ?? 0
            ];


            $salaryAmount += $netSalary;
            // Add to the salary sheets array
            array_push($salary_sheets, $tmp);
        }

        // dd($salary_sheets);

        // allowance_deduction, working_days, and manual_deduction are not DB columns — strip them before insert
        $salary_sheets_for_db = array_map(function ($sheet) {
            return Arr::except($sheet, ['allowance_deduction', 'working_days', 'manual_deduction']);
        }, $salary_sheets);

        SalarySheet::insert($salary_sheets_for_db);

        SalaryGenerate::where('id', $salaryGenerateId)  // Ensure 'id' is provided to identify the record
            ->update(['amount' => round($salaryAmount, 2)]);



        $salary_sheets_with_employees = collect($salary_sheets)->map(function ($sheet) {
            $employee = Employee::with(['designation'])->find($sheet['employee_id']); // Fetch employee data
            $sheet['employee'] = $employee; // Attach employee data to the sheet
            return $sheet;
        });

        return $salary_sheets_with_employees;
    }
}
