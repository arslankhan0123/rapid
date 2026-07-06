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
    //old one
    // public function generateSalary($month)
    // {

    //     $settings = Setting::pluck('value', 'key')->toArray();
    //     $overTimeRate = isset($settings['overtime_rate'])
    //         ? (float) $settings['overtime_rate'] / 100
    //         : 1.0;

    //     $salaryAmount = 0;
    //     $salaryGenerateId = $month->id;
    //     $branch_id = $month->branch_id;
    //     // Define working hours per day and weekend days (friday and sunday as weekend)
    //     $workingHoursPerDay = 8;
    //     $weekendDays = ['friday']; // Specify weekend days

    //     $salaryMonth = $month['salary_month']; // Example: '2024-11'
    //     $year = substr($salaryMonth, 0, 4); // Extract year
    //     $monthNum = substr($salaryMonth, 5, 2); // Extract month

    //     $checkDate = date("Y-m-d", strtotime($year . '-' . $monthNum . '-01'));


    //     $lastDayOfMonth = date('Y-m-t', strtotime($salaryMonth . '-01'));


    //     $employees = Employee::with([
    //         'attendance' => function ($query) use ($year, $monthNum) {
    //             $query->whereYear('date', $year)
    //                 ->whereMonth('date', $monthNum);
    //         },
    //         // 'allowances' => function ($query) use ($year, $monthNum) {
    //         //     $query->whereYear('date', $year)
    //         //         ->whereMonth('date', $monthNum);
    //         // },
    //         'allowances',
    //         'deductions' => function ($query) use ($year, $monthNum) {
    //             $query->whereYear('date', $year)
    //                 ->whereMonth('date', $monthNum);
    //         },
    //         'advances' => function ($query) use ($year, $monthNum) {
    //             $query->whereYear('date', $year)
    //                 ->whereMonth('date', $monthNum);
    //         },
    //         'loans',
    //         // 'loans' => function ($query) use ($year, $monthNum) {
    //         //     $query->whereYear('date', $year)
    //         //         ->whereMonth('date', $monthNum);
    //         // },
    //         'bonuses' => function ($query) use ($year, $monthNum) {
    //             $query->whereYear('date', $year)
    //                 ->whereMonth('date', $monthNum);
    //         },
    //         // 'insurances' => function ($query) use ($year, $monthNum) {
    //         //     $query->whereYear('date', $year)
    //         //         ->whereMonth('date', $monthNum);
    //         // },
    //         'insurances',
    //         'leaveApplications',
    //     ])
    //         ->whereDate('join_date', '<=', $lastDayOfMonth)
    //         ->where('branch_id', $branch_id)->where('status', 1)->get();


    //     //dd($employees->toArray());

    //     $salary_sheets = [];

    //     // Iterate through each employee
    //     foreach ($employees as $employee) {
    //         // Base salary of the employee (assumed gross salary column)
    //         $gross_salary = $employee->gross_salary;

    //         // Calculate total allowances for the specified month
    //         $totalAllowances = $employee->allowances->sum('amount');
    //         $totalDeductions = $employee->deductions->sum('amount');
    //         $totalAdvances = $employee->advances->sum('amount');
    //         $totalInsuranceSum = $employee->insurances->sum('insurance');

    //         //$totalLoans = $employee->loans->sum('installment');
    //         $totalLoans = 0;

    //         foreach ($employee->loans as $loan) {
    //             // Here installment_period is actually a date
    //             $startDate = date("Y-m-1", strtotime($loan->repayment_from));
    //             // + installment_period months, then -1 day so it includes the last month fully
    //             $endDate   = date("Y-m-d", strtotime("+" . $loan->installment_period . " months - 1 day", strtotime($startDate)));
    //             //dd($checkDate, $startDate);
    //             // If current date is within the repayment period
    //             if (strtotime($checkDate) >= strtotime($startDate) && strtotime($checkDate) <= strtotime($endDate) && ($loan->status == 'active')) {
    //                 //dd($loan->installment);
    //                 $totalLoans += $loan->installment;
    //             }
    //         }
    //         // find leave dates.
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

    //         //print_r($employee->leave_dates);


    //         $totalBonuses = $employee->bonuses->sum('amount');
    //         $total_overtimes = 0;
    //         $absense_hours = 0;
    //         $overtimeHours = 0;

    //         // Calculate total working hours in the month (excluding weekend days)
    //         $workingHours = 0;
    //         $workingDaysCount = 0;
    //         $IdealworkingDaysCount = 0;

    //         // Loop through attendance records and calculate working hours
    //         foreach ($employee->attendance as $attendance) {
    //             $join_date = date("Y-m-d", strtotime($employee->join_date));

    //             $attendanceDate = $attendance['date'];
    //             $dayOfWeek = date('l', strtotime($attendanceDate)); // Get day of the week (e.g., 'Monday', 'Friday')

    //             // if($join_date > $attendance['date']) {
    //             //     if (!in_array(strtolower($dayOfWeek), $weekendDays)) {
    //             //         $IdealworkingDaysCount +=1 ;
    //             //     }
    //             //     continue;
    //             // }
    //             if ((in_array($attendance['date'], $employee->leave_dates))) {
    //                 if (!in_array(strtolower($dayOfWeek), $weekendDays)) {
    //                     $IdealworkingDaysCount += 1;
    //                 }
    //                 continue;
    //             }


    //             // Check if it's not a weekend day (in the $weekendDays array)
    //             if (!in_array(strtolower($dayOfWeek), $weekendDays)) {
    //                 //$workingHours += $attendance['hours'];
    //                 $workingDaysCount++;
    //                 $IdealworkingDaysCount += 1;

    //                 if ($attendance['hours'] < $workingHoursPerDay) {
    //                     $absense_hours += ($workingHoursPerDay - $attendance['hours']);
    //                 }

    //                 if ($attendance['hours'] > $workingHoursPerDay) {
    //                     $overtimeHours += ($attendance['hours'] - $workingHoursPerDay);
    //                 }
    //             } else {
    //                 $overtimeHours += $attendance['hours'];
    //             }
    //         }

    //         // Calculate basic salary based on working days and working hours per day
    //         $totalWorkingHoursThisMonth = ($workingDaysCount * $workingHoursPerDay); // 8 hours per working day
    //         $IdealWorkingHoursThisMonth = ($IdealworkingDaysCount * $workingHoursPerDay); // 8 hours per working day
    //         //dd($totalWorkingHoursThisMonth);
    //         if ($totalWorkingHoursThisMonth != $IdealWorkingHoursThisMonth) {
    //             $IdealhourlyRate = $employee->basic_salary / $IdealWorkingHoursThisMonth;
    //             //$employee->basic_salary = $IdealhourlyRate * $totalWorkingHoursThisMonth;
    //         }



    //         $workingHours = ($totalWorkingHoursThisMonth + $overtimeHours) - $absense_hours;
    //         // Avoid division by zero by checking if total working hours are greater than 0
    //         if ($totalWorkingHoursThisMonth > 0) {
    //             $hourlyRate = $employee->basic_salary / $totalWorkingHoursThisMonth;
    //         } else {
    //             $hourlyRate = 0;
    //         }
    //         $calculatedSalary = $workingHours * $hourlyRate;
    //         $hourlyDeduction = 0;



    //         //$over_time_after_absent = max(0, ($overtimeHours - $absense_hours));
    //         $total_overtimes = ($overtimeHours) * $hourlyRate * $overTimeRate;
    //         $hourlyDeduction += ($absense_hours * $hourlyRate);
    //         //$hourlyDeduction = $employee->basic_salary - $calculatedSalary;

    //         if ($workingHours > $totalWorkingHoursThisMonth) {
    //             //$overtimeHours = $workingHours - $totalWorkingHoursThisMonth;
    //             //$total_overtimes = $overtimeHours * $hourlyRate * $overTimeRate;
    //         } else if ($workingHours == $totalWorkingHoursThisMonth) {
    //             //$absense_hours = 0;
    //             //$hourlyDeduction = 0;
    //         } else {
    //             //$absense_hours = $totalWorkingHoursThisMonth - $workingHours;
    //             //$hourlyDeduction = $employee->basic_salary - $calculatedSalary;
    //         }


    //         // echo $workingHours . " " . $totalWorkingHoursThisMonth . " " . $hourlyDeduction;
    //         // echo "\n";
    //         // Additional fields for the new structure
    //         $totalCommission = 0; // Replace with the actual commission calculation if available
    //         $totalInsurance = round((($totalInsuranceSum / $IdealWorkingHoursThisMonth) * $workingHours), 2);  // Replace with the actual insurance calculation if available
    //         $totalLoan = $totalLoans;
    //         $totalBonus = $totalBonuses;
    //         $totalAllowance = round((($totalAllowances / $IdealWorkingHoursThisMonth) * $workingHours), 2);
    //         $totalDeduction = $totalDeductions;


    //         $netSalary = $employee->basic_salary + $totalAllowance + $totalBonus - $totalDeduction - $totalAdvances - $totalLoan
    //             - $hourlyDeduction - $totalInsurance
    //             + $total_overtimes;
    //         $gross_salary = $employee->basic_salary + $totalAllowance + $totalBonus
    //             + $total_overtimes;
    //         // Structure the data to match the desired output
    //         $tmp = [
    //             'employee_id' => $employee->id,
    //             'salary_generate_id' => $salaryGenerateId,
    //             'basic_salary' => $employee->basic_salary ?? 0,
    //             'salary_advance' => $totalAdvances ?? 0,
    //             'gross_salary' => $gross_salary ?? 0,
    //             'state_income_tax' => 0, // Adjust this if there's a tax calculation
    //             'loan' => $totalLoan,
    //             'total_bonus' => $totalBonus ?? 0,
    //             'total_allowances' => $totalAllowance,
    //             'total_commission' => $totalCommission,
    //             'total_insurance' => $totalInsurance,
    //             'total_deduction' => $totalDeduction,
    //             'net_salary' => $netSalary,
    //             'hourly_deduction' => $hourlyDeduction ?? 0,
    //             'total_overtimes' => $total_overtimes,
    //             'overtime_hours' => $overtimeHours ?? 0,
    //             'absence_hours' => $absense_hours,
    //             'worked_hours' => $workingHours ?? 0,
    //             'working_hours' => $IdealWorkingHoursThisMonth ?? 0,
    //             'branch_id' => $branch_id
    //         ];


    //         $salaryAmount += $netSalary;
    //         // Add to the salary sheets array
    //         array_push($salary_sheets, $tmp);
    //     }

    //     // dd($salary_sheets);

    //     SalarySheet::insert($salary_sheets);

    //     SalaryGenerate::where('id', $salaryGenerateId)  // Ensure 'id' is provided to identify the record
    //         ->update(['amount' => round($salaryAmount, 2)]);



    //     $salary_sheets_with_employees = collect($salary_sheets)->map(function ($sheet) {
    //         $employee = Employee::with(['designation'])->find($sheet['employee_id']); // Fetch employee data
    //         $sheet['employee'] = $employee; // Attach employee data to the sheet
    //         return $sheet;
    //     });

    //     return $salary_sheets_with_employees;
    // }




    // first new updatede one
    // public function generateSalary($month)
    // {
    //     $settings = Setting::pluck('value', 'key')->toArray();
    //     $overTimeRate = isset($settings['overtime_rate']) ? (float) $settings['overtime_rate'] / 100 : 1.0;

    //     $salaryAmount = 0;
    //     $salaryGenerateId = $month->id;
    //     $branch_id = $month->branch_id;

    //     // Configuration - Fixed 30 days month
    //     $workingHoursPerDay = 8;
    //     $fixedMonthDays = 30; // Always 30 days for calculation
    //     $weekendDays = ['friday']; // Exclude Fridays

    //     $salaryMonth = $month['salary_month'];
    //     $year = substr($salaryMonth, 0, 4);
    //     $monthNum = substr($salaryMonth, 5, 2);

    //     $firstDayOfMonth = date('Y-m-01', strtotime($salaryMonth . '-01'));
    //     $lastDayOfMonth = date('Y-m-t', strtotime($salaryMonth . '-01'));

    //     // Get employees
    //     $employees = Employee::with([
    //         'attendance' => function ($query) use ($year, $monthNum) {
    //             $query->whereYear('date', $year)
    //                 ->whereMonth('date', $monthNum);
    //         },
    //         'allowances',
    //         'deductions' => function ($query) use ($year, $monthNum) {
    //             $query->whereYear('date', $year)
    //                 ->whereMonth('date', $monthNum);
    //         },
    //         'advances' => function ($query) use ($year, $monthNum) {
    //             $query->whereYear('date', $year)
    //                 ->whereMonth('date', $monthNum);
    //         },
    //         'loans',
    //         'bonuses' => function ($query) use ($year, $monthNum) {
    //             $query->whereYear('date', $year)
    //                 ->whereMonth('date', $monthNum);
    //         },
    //         'insurances',
    //         'leaveApplications' => function ($query) use ($year, $monthNum) {
    //             $query->whereYear('from_date', $year)
    //                 ->whereMonth('from_date', $monthNum)
    //                 ->where('status', 'approved');
    //         },
    //     ])
    //         ->whereDate('join_date', '<=', $lastDayOfMonth)
    //         ->where('branch_id', $branch_id)
    //         ->where('status', 1)
    //         ->get();

    //     $salary_sheets = [];

    //     foreach ($employees as $employee) {
    //         // Calculate total monthly amounts
    //         $totalAllowances = $employee->allowances->sum('amount');
    //         $totalDeductions = $employee->deductions->sum('amount');
    //         $totalAdvances = $employee->advances->sum('amount');
    //         $totalInsurance = $employee->insurances->sum('insurance');
    //         $totalBonuses = $employee->bonuses->sum('amount');

    //         // Calculate active loan installments
    //         $totalLoans = $this->calculateActiveLoans($employee->loans, $firstDayOfMonth);

    //         // Get leave dates
    //         $leaveDates = $this->getLeaveDates($employee->leaveApplications);

    //         // Calculate attendance metrics
    //         $attendanceData = $this->calculateAttendanceMetrics(
    //             $employee->attendance,
    //             $employee->join_date,
    //             $leaveDates,
    //             $weekendDays,
    //             $workingHoursPerDay,
    //             $firstDayOfMonth,
    //             $lastDayOfMonth,
    //             $fixedMonthDays
    //         );

    //         // Calculate hourly rate for basic salary deduction
    //         $hourlyRate = $fixedMonthDays > 0 ?
    //             $employee->basic_salary / ($fixedMonthDays * $workingHoursPerDay) : 0;

    //         // Calculate basic salary absence deduction
    //         $basicAbsenceDeduction = $attendanceData['absence_hours'] * $hourlyRate;

    //         // Calculate allowance deduction (based on absence hours)
    //         $dailyAllowanceRate = $totalAllowances / $fixedMonthDays; // Allowance per day
    //         $hourlyAllowanceRate = $dailyAllowanceRate / $workingHoursPerDay; // Allowance per hour
    //         $allowanceDeduction = $attendanceData['absence_hours'] * $hourlyAllowanceRate;

    //         // Calculate insurance deduction (based on absence hours)
    //         $dailyInsuranceRate = $totalInsurance / $fixedMonthDays; // Insurance per day
    //         $hourlyInsuranceRate = $dailyInsuranceRate / $workingHoursPerDay; // Insurance per hour
    //         $insuranceDeduction = $attendanceData['absence_hours'] * $hourlyInsuranceRate;

    //         // Payable amounts (full amounts minus deductions)
    //         $payableAllowances = $totalAllowances - $allowanceDeduction;
    //         $payableInsurance = $totalInsurance - $insuranceDeduction;

    //         // Base salary should be FULL basic salary (no deduction from basic salary)
    //         $baseSalary = $employee->basic_salary; // Full 10,000 SAR

    //         // Calculate overtime
    //         $overtimeAmount = $attendanceData['overtime_hours'] * $hourlyRate * $overTimeRate;

    //         // Calculate final amounts - use payable amounts in calculation
    //         $grossSalary = $baseSalary + $payableAllowances + $totalBonuses + $overtimeAmount;

    //         $netSalary = $grossSalary
    //             - $totalDeductions
    //             - $totalAdvances
    //             - $totalLoans
    //             - $basicAbsenceDeduction // Deduct basic salary absence
    //             - $payableInsurance; // Use payable insurance (already deducted)

    //         $salarySheet = [
    //             'employee_id' => $employee->id,
    //             'salary_generate_id' => $salaryGenerateId,
    //             'basic_salary' => round($baseSalary, 2), // Shows 10,000.00 (full)
    //             'salary_advance' => round($totalAdvances, 2),
    //             'gross_salary' => round($grossSalary, 2),
    //             'state_income_tax' => 0,
    //             'loan' => round($totalLoans, 2),
    //             'total_bonus' => round($totalBonuses, 2),
    //             'total_allowances' => round($totalAllowances, 2), // Show FULL allowances
    //             'total_commission' => 0,
    //             'total_insurance' => round($totalInsurance, 2), // Show FULL insurance
    //             'total_deduction' => round($totalDeductions + $allowanceDeduction, 2), // Regular deductions + allowance cut due to absence
    //             'net_salary' => round($netSalary, 2),
    //             'hourly_deduction' => round($basicAbsenceDeduction, 2), // Only basic salary absence deduction
    //             'total_overtimes' => round($overtimeAmount, 2),
    //             'overtime_hours' => round($attendanceData['overtime_hours'], 2),
    //             'absence_hours' => round($attendanceData['absence_hours'], 2),
    //             'worked_hours' => round($attendanceData['worked_hours'], 2),
    //             'working_hours' => round($fixedMonthDays * $workingHoursPerDay, 2),
    //             'branch_id' => $branch_id
    //         ];

    //         $salaryAmount += $netSalary;
    //         $salary_sheets[] = $salarySheet;
    //     }

    //     // Insert salary sheets and update total
    //     SalarySheet::insert($salary_sheets);
    //     SalaryGenerate::where('id', $salaryGenerateId)
    //         ->update(['amount' => round($salaryAmount, 2)]);

    //     return $this->getSalarySheetsWithEmployees($salary_sheets);
    // }

    // public function generateSalary($month)
    // {
    //     $settings = Setting::pluck('value', 'key')->toArray();
    //     $overTimeRate = isset($settings['overtime_rate']) ? (float) $settings['overtime_rate'] / 100 : 1.0;

    //     $salaryAmount = 0;
    //     $salaryGenerateId = $month->id;
    //     $branch_id = $month->branch_id;

    //     // Configuration - Fixed 30 days month
    //     $workingHoursPerDay = 8;
    //     $fixedMonthDays = 30; // Always 30 days for calculation
    //     $weekendDays = ['friday']; // Specify weekend days

    //     $salaryMonth = $month['salary_month'];
    //     $year = substr($salaryMonth, 0, 4);
    //     $monthNum = substr($salaryMonth, 5, 2);

    //     $checkDate = date("Y-m-d", strtotime($year . '-' . $monthNum . '-01'));
    //     $lastDayOfMonth = date('Y-m-t', strtotime($salaryMonth . '-01'));

    //     $employees = Employee::with([
    //         'attendance' => function ($query) use ($year, $monthNum) {
    //             $query->whereYear('date', $year)
    //                 ->whereMonth('date', $monthNum);
    //         },
    //         'allowances',
    //         'deductions' => function ($query) use ($year, $monthNum) {
    //             $query->whereYear('date', $year)
    //                 ->whereMonth('date', $monthNum);
    //         },
    //         'advances' => function ($query) use ($year, $monthNum) {
    //             $query->whereYear('date', $year)
    //                 ->whereMonth('date', $monthNum);
    //         },
    //         'loans',
    //         'bonuses' => function ($query) use ($year, $monthNum) {
    //             $query->whereYear('date', $year)
    //                 ->whereMonth('date', $monthNum);
    //         },
    //         'insurances',
    //         'leaveApplications',
    //     ])
    //         ->whereDate('join_date', '<=', $lastDayOfMonth)
    //         ->where('branch_id', $branch_id)->where('status', 1)->get();

    //     $salary_sheets = [];

    //     foreach ($employees as $employee) {
    //         // Calculate total monthly amounts
    //         $totalAllowances = $employee->allowances->sum('amount');
    //         $totalDeductions = $employee->deductions->sum('amount');
    //         $totalAdvances = $employee->advances->sum('amount');
    //         $totalInsuranceSum = $employee->insurances->sum('insurance');
    //         $totalBonuses = $employee->bonuses->sum('amount');

    //         // Calculate active loan installments
    //         $totalLoans = 0;
    //         foreach ($employee->loans as $loan) {
    //             $startDate = date("Y-m-1", strtotime($loan->repayment_from));
    //             $endDate = date("Y-m-d", strtotime("+" . $loan->installment_period . " months - 1 day", strtotime($startDate)));
    //             if (strtotime($checkDate) >= strtotime($startDate) && strtotime($checkDate) <= strtotime($endDate) && ($loan->status == 'active')) {
    //                 $totalLoans += $loan->installment;
    //             }
    //         }

    //         // Get leave dates
    //         $leaveDates = [];
    //         foreach ($employee->leaveApplications as $leave) {
    //             $start = \Carbon\Carbon::parse($leave->from_date);
    //             $end = \Carbon\Carbon::parse($leave->end_date);
    //             for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
    //                 $leaveDates[] = $date->format('Y-m-d');
    //             }
    //         }
    //         $employee->leave_dates = $leaveDates;

    //         // Calculate attendance metrics using your old logic
    //         $absense_hours = 0;
    //         $overtimeHours = 0;
    //         $workingDaysCount = 0;
    //         $IdealworkingDaysCount = 0;
    //         $workedHours = 0;

    //         foreach ($employee->attendance as $attendance) {
    //             $join_date = date("Y-m-d", strtotime($employee->join_date));
    //             $attendanceDate = $attendance['date'];
    //             $dayOfWeek = date('l', strtotime($attendanceDate));

    //             if ((in_array($attendance['date'], $employee->leave_dates))) {
    //                 if (!in_array(strtolower($dayOfWeek), $weekendDays)) {
    //                     $IdealworkingDaysCount += 1;
    //                 }
    //                 continue;
    //             }

    //             if (!in_array(strtolower($dayOfWeek), $weekendDays)) {
    //                 $workingDaysCount++;
    //                 $IdealworkingDaysCount += 1;
    //                 $workedHours += $attendance['hours'];

    //                 if ($attendance['hours'] < $workingHoursPerDay) {
    //                     $absense_hours += ($workingHoursPerDay - $attendance['hours']);
    //                 }

    //                 if ($attendance['hours'] > $workingHoursPerDay) {
    //                     $overtimeHours += ($attendance['hours'] - $workingHoursPerDay);
    //                 }
    //             } else {
    //                 $overtimeHours += $attendance['hours'];
    //                 $workedHours += $attendance['hours'];
    //             }
    //         }

    //         //     // FIXED: Use FIXED 30 DAYS for hourly rate calculation
    //         //     $totalExpectedHours = $fixedMonthDays * $workingHoursPerDay; // 30 × 8 = 240 hours
    //         //     $hourlyRate = $employee->basic_salary / $totalExpectedHours;

    //         //     // Calculate overtime and absence using FIXED hourly rate
    //         //     $total_overtimes = $overtimeHours * $hourlyRate * $overTimeRate;
    //         //     $hourlyDeduction = $absense_hours * $hourlyRate;

    //         //     // Calculate allowance deduction based on absence hours (using fixed 30 days)
    //         //     $dailyAllowanceRate = $totalAllowances / $fixedMonthDays; // Allowance per day
    //         //     $hourlyAllowanceRate = $dailyAllowanceRate / $workingHoursPerDay; // Allowance per hour
    //         //     $allowanceDeduction = $absense_hours * $hourlyAllowanceRate;

    //         //     // Calculate working hours for pro-rating
    //         //     $totalWorkingHoursThisMonth = ($workingDaysCount * $workingHoursPerDay);
    //         //     $IdealWorkingHoursThisMonth = ($IdealworkingDaysCount * $workingHoursPerDay);
    //         //     $workingHours = ($totalWorkingHoursThisMonth + $overtimeHours) - $absense_hours;

    //         //     // Calculate pro-rated amounts using your old logic
    //         //     $totalInsurance = round((($totalInsuranceSum / $IdealWorkingHoursThisMonth) * $workingHours), 2);
    //         //     $totalAllowance = round((($totalAllowances / $IdealWorkingHoursThisMonth) * $workingHours), 2);

    //         //     // Calculate net salary using your old logic but add allowance deduction
    //         //     $netSalary = $employee->basic_salary + $totalAllowance + $totalBonuses - $totalDeductions - $totalAdvances - $totalLoans
    //         //         - $hourlyDeduction - $totalInsurance
    //         //         + $total_overtimes;

    //         //     $gross_salary = $employee->basic_salary + $totalAllowance + $totalBonuses + $total_overtimes;

    //         //     // Structure the data - show full allowances but add deduction to total_deduction
    //         //     $tmp = [
    //         //         'employee_id' => $employee->id,
    //         //         'salary_generate_id' => $salaryGenerateId,
    //         //         'basic_salary' => $employee->basic_salary ?? 0,
    //         //         'salary_advance' => $totalAdvances ?? 0,
    //         //         'gross_salary' => $gross_salary ?? 0,
    //         //         'state_income_tax' => 0,
    //         //         'loan' => $totalLoans,
    //         //         'total_bonus' => $totalBonuses ?? 0,
    //         //         'total_allowances' => $totalAllowances, // Show FULL allowances (not pro-rated)
    //         //         'total_commission' => 0,
    //         //         'total_insurance' => $totalInsurance,
    //         //         'total_deduction' => round($totalDeductions + $allowanceDeduction, 2), // Add allowance deduction to total
    //         //         'net_salary' => $netSalary,
    //         //         'hourly_deduction' => $hourlyDeduction ?? 0,
    //         //         'total_overtimes' => $total_overtimes,
    //         //         'overtime_hours' => $overtimeHours ?? 0,
    //         //         'absence_hours' => $absense_hours,
    //         //         'worked_hours' => $workingHours ?? 0,
    //         //         'working_hours' => $IdealWorkingHoursThisMonth ?? 0,
    //         //         'branch_id' => $branch_id
    //         //     ];

    //         //     $salaryAmount += $netSalary;
    //         //     $salary_sheets[] = $tmp;
    //         // FIXED: Use FIXED 30 DAYS for hourly rate calculation
    //         $totalExpectedHours = $fixedMonthDays * $workingHoursPerDay; // 30 × 8 = 240 hours
    //         $hourlyRate = $employee->basic_salary / $totalExpectedHours;

    //         // Calculate overtime and absence using FIXED hourly rate
    //         $total_overtimes = $overtimeHours * $hourlyRate * $overTimeRate;
    //         $hourlyDeduction = $absense_hours * $hourlyRate;

    //         // Calculate allowance deduction based on absence hours (using fixed 30 days)
    //         $dailyAllowanceRate = $totalAllowances / $fixedMonthDays; // Allowance per day
    //         $hourlyAllowanceRate = $dailyAllowanceRate / $workingHoursPerDay; // Allowance per hour
    //         $allowanceDeduction = $absense_hours * $hourlyAllowanceRate;

    //         // Calculate working hours for pro-rating
    //         $totalWorkingHoursThisMonth = ($workingDaysCount * $workingHoursPerDay);
    //         $IdealWorkingHoursThisMonth = ($IdealworkingDaysCount * $workingHoursPerDay);
    //         $workingHours = ($totalWorkingHoursThisMonth + $overtimeHours) - $absense_hours;

    //         // Calculate pro-rated amounts using your old logic
    //         $totalInsurance = round((($totalInsuranceSum / $IdealWorkingHoursThisMonth) * $workingHours), 2);
    //         $totalAllowance = round((($totalAllowances / $IdealWorkingHoursThisMonth) * $workingHours), 2);

    //         // FIXED: Use FULL amounts for gross salary calculation
    //         $gross_salary = $employee->basic_salary + $totalAllowances + $totalBonuses + $total_overtimes;

    //         // Calculate net salary - use pro-rated amounts in calculation but deduct allowance deduction
    //         $netSalary = $gross_salary
    //             - $totalDeductions
    //             - $totalAdvances
    //             - $totalLoans
    //             - $hourlyDeduction
    //             - $allowanceDeduction  // Deduct the allowance cut due to absence
    //             - $totalInsurance;

    //         // Structure the data - show full allowances but add deduction to total_deduction
    //         $tmp = [
    //             'employee_id' => $employee->id,
    //             'salary_generate_id' => $salaryGenerateId,
    //             'basic_salary' => $employee->basic_salary ?? 0,
    //             'salary_advance' => $totalAdvances ?? 0,
    //             'gross_salary' => $gross_salary ?? 0, // Now shows 10,600 instead of 10,023.08
    //             'state_income_tax' => 0,
    //             'loan' => $totalLoans,
    //             'total_bonus' => $totalBonuses ?? 0,
    //             'total_allowances' => $totalAllowances, // Show FULL allowances (not pro-rated)
    //             'total_commission' => 0,
    //             'total_insurance' => $totalInsurance,
    //             'total_deduction' => round($totalDeductions + $allowanceDeduction, 2), // Add allowance deduction to total
    //             'net_salary' => $netSalary,
    //             'hourly_deduction' => $hourlyDeduction ?? 0,
    //             'total_overtimes' => $total_overtimes,
    //             'overtime_hours' => $overtimeHours ?? 0,
    //             'absence_hours' => $absense_hours,
    //             'worked_hours' => $workingHours ?? 0,
    //             'working_hours' => $IdealWorkingHoursThisMonth ?? 0,
    //             'branch_id' => $branch_id
    //         ];

    //         $salaryAmount += $netSalary;
    //         $salary_sheets[] = $tmp;
    //     }

    //     SalarySheet::insert($salary_sheets);
    //     SalaryGenerate::where('id', $salaryGenerateId)
    //         ->update(['amount' => round($salaryAmount, 2)]);

    //     $salary_sheets_with_employees = collect($salary_sheets)->map(function ($sheet) {
    //         $employee = Employee::with(['designation'])->find($sheet['employee_id']);
    //         $sheet['employee'] = $employee;
    //         return $sheet;
    //     });

    //     return $salary_sheets_with_employees;
    // }

    public function generateSalary($month)
    {
        $settings = Setting::pluck('value', 'key')->toArray();
        $overTimeRate = isset($settings['overtime_rate']) ? (float)$settings['overtime_rate'] / 100 : 1.0;

        $salaryAmount = 0;
        $salaryGenerateId = $month->id;
        $branch_id = $month->branch_id;

        // Configuration
        $workingHoursPerDay = 8;
        $fixedMonthDays = 30;
        $weekendDays = ['friday']; // Friday = Paid Off Day

        $salaryMonth = $month['salary_month'];
        $year = substr($salaryMonth, 0, 4);
        $monthNum = substr($salaryMonth, 5, 2);

        $firstDayOfMonth = date('Y-m-01', strtotime($salaryMonth . '-01'));
        $lastDayOfMonth = date('Y-m-t', strtotime($salaryMonth . '-01'));
        $checkDate = date("Y-m-d", strtotime($year . '-' . $monthNum . '-01'));

        $employees = Employee::with([
            'attendance' => function ($query) use ($year, $monthNum) {
                $query->whereYear('date', $year)
                    ->whereMonth('date', $monthNum);
            },
            'allowances',
            'deductions' => function ($query) use ($year, $monthNum) {
                $query->whereYear('date', $year)
                    ->whereMonth('date', $monthNum);
            },
            'advances' => function ($query) use ($year, $monthNum) {
                $query->whereYear('date', $year)
                    ->whereMonth('date', $monthNum);
            },
            'loans',
            'bonuses' => function ($query) use ($year, $monthNum) {
                $query->whereYear('date', $year)
                    ->whereMonth('date', $monthNum);
            },
            'insurances',
            'leaveApplications',
        ])
            ->whereDate('join_date', '<=', $lastDayOfMonth)
            ->where('branch_id', $branch_id)
            ->where('status', 1)
            ->get();

        $salary_sheets = [];

        foreach ($employees as $employee) {
            // --- Fixed & Totals ---
            $totalAllowances = $employee->allowances->sum('amount');
            $totalDeductions = $employee->deductions->sum('amount');
            $totalAdvances = $employee->advances->sum('amount');
            $totalInsuranceSum = $employee->insurances->sum('insurance');
            $totalBonuses = $employee->bonuses->sum('amount');

            // --- Active Loans ---
            $totalLoans = 0;
            foreach ($employee->loans as $loan) {
                $startDate = date("Y-m-1", strtotime($loan->repayment_from));
                $endDate = date("Y-m-d", strtotime("+" . $loan->installment_period . " months - 1 day", strtotime($startDate)));
                if (
                    strtotime($checkDate) >= strtotime($startDate)
                    && strtotime($checkDate) <= strtotime($endDate)
                    && ($loan->status == 'active')
                ) {
                    $totalLoans += $loan->installment;
                }
            }

            // --- Leave Dates ---
            $leaveDates = [];
            foreach ($employee->leaveApplications as $leave) {
                $start = \Carbon\Carbon::parse($leave->from_date);
                $end = \Carbon\Carbon::parse($leave->end_date);
                for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
                    $leaveDates[] = $date->format('Y-m-d');
                }
            }
            $employee->leave_dates = $leaveDates;

            // --- Variables ---
            $absense_hours = 0;
            $overtimeHours = 0;
            $workingDaysCount = 0;
            $IdealworkingDaysCount = 0;
            $workedHours = 0;

            // --- Process attendance records ---
            foreach ($employee->attendance as $attendance) {
                $attendanceDate = $attendance['date'];
                $dayOfWeek = strtolower(date('l', strtotime($attendanceDate)));

                // Skip before join date
                if (strtotime($attendanceDate) < strtotime($employee->join_date)) {
                    continue;
                }

                $IdealworkingDaysCount++;

                // If Friday → Paid Off (no deduction)
                if (in_array($dayOfWeek, $weekendDays)) {
                    // Count worked hours as overtime if worked
                    if ($attendance['hours'] > 0) {
                        $overtimeHours += $attendance['hours'];
                        $workedHours += $attendance['hours'];
                    }
                    continue; // Skip absence deduction
                }

                // Leave day → treat as absent (except Friday)
                if (in_array($attendanceDate, $employee->leave_dates)) {
                    $absense_hours += $workingHoursPerDay;
                    continue;
                }

                // Normal attendance
                if ($attendance['hours'] > 0) {
                    $workedHours += $attendance['hours'];
                    $workingDaysCount++;

                    if ($attendance['hours'] < $workingHoursPerDay) {
                        $absense_hours += ($workingHoursPerDay - $attendance['hours']);
                    }

                    if ($attendance['hours'] > $workingHoursPerDay) {
                        $overtimeHours += ($attendance['hours'] - $workingHoursPerDay);
                    }
                } else {
                    // No hours = full absent (non-Friday)
                    $absense_hours += $workingHoursPerDay;
                }
            }

            // --- Handle missing attendance days ---
            $monthDates = new \DatePeriod(
                new \DateTime($firstDayOfMonth),
                new \DateInterval('P1D'),
                (new \DateTime($lastDayOfMonth))->modify('+1 day')
            );

            foreach ($monthDates as $date) {
                $dateStr = $date->format('Y-m-d');
                $dayOfWeek = strtolower($date->format('l'));

                if (strtotime($dateStr) < strtotime($employee->join_date)) continue;

                $IdealworkingDaysCount++;

                // $hasAttendance = $employee->attendance->contains('date', $dateStr);
                $hasAttendance = $employee->attendance->contains(function ($att) use ($dateStr) {
                    return date('Y-m-d', strtotime($att->date)) === $dateStr;
                });


                // Skip Fridays (paid)
                if (in_array($dayOfWeek, $weekendDays)) continue;

                if (!$hasAttendance) {
                    if (in_array($dateStr, $employee->leave_dates)) {
                        $absense_hours += $workingHoursPerDay; // Leave = absent deduction
                    } else {
                        $absense_hours += $workingHoursPerDay; // Normal absent
                    }
                }
            }

            // --- Salary Calculations ---
            $totalExpectedHours = $fixedMonthDays * $workingHoursPerDay;
            $hourlyRate = $employee->basic_salary / $totalExpectedHours;

            // Deduction caps
            $maxAbsenceDeduction = $employee->basic_salary;
            $calculatedAbsenceDeduction = $absense_hours * $hourlyRate;
            $hourlyDeduction = min($calculatedAbsenceDeduction, $maxAbsenceDeduction);

            // Overtime
            $total_overtimes = $overtimeHours * $hourlyRate * $overTimeRate;

            // Allowance deduction (pro-rata)
            $dailyAllowanceRate = $totalAllowances / $fixedMonthDays;
            $hourlyAllowanceRate = $dailyAllowanceRate / $workingHoursPerDay;
            $calculatedAllowanceDeduction = $absense_hours * $hourlyAllowanceRate;
            $allowanceDeduction = min($calculatedAllowanceDeduction, $totalAllowances);

            // Insurance & Allowance prorated by worked hours
            $IdealWorkingHoursThisMonth = $IdealworkingDaysCount * $workingHoursPerDay;
            $totalInsurance = $IdealWorkingHoursThisMonth > 0
                ? round((($totalInsuranceSum / $IdealWorkingHoursThisMonth) * $workedHours), 2)
                : 0;
            $totalAllowance = $IdealWorkingHoursThisMonth > 0
                ? round((($totalAllowances / $IdealWorkingHoursThisMonth) * $workedHours), 2)
                : 0;

            // Gross & Net Salary
            $gross_salary = $employee->basic_salary + $totalAllowances + $totalBonuses + $total_overtimes;

            $netSalary = $gross_salary
                - $totalDeductions
                - $totalAdvances
                - $totalLoans
                - $hourlyDeduction
                - $allowanceDeduction
                - $totalInsurance;

            $netSalary = max(0, $netSalary);

            // --- Build salary sheet row ---
            $tmp = [
                'employee_id' => $employee->id,
                'salary_generate_id' => $salaryGenerateId,
                'basic_salary' => $employee->basic_salary ?? 0,
                'salary_advance' => $totalAdvances ?? 0,
                'gross_salary' => $gross_salary ?? 0,
                'state_income_tax' => 0,
                'loan' => $totalLoans,
                'total_bonus' => $totalBonuses ?? 0,
                'total_allowances' => $totalAllowances,
                'total_commission' => 0,
                'total_insurance' => $totalInsurance,
                'total_deduction' => round($totalDeductions + $allowanceDeduction, 2),
                'net_salary' => $netSalary,
                'hourly_deduction' => $hourlyDeduction ?? 0,
                'total_overtimes' => $total_overtimes,
                'overtime_hours' => $overtimeHours ?? 0,
                'absence_hours' => $absense_hours,
                'worked_hours' => $workedHours ?? 0,
                'working_hours' => $IdealWorkingHoursThisMonth ?? 0,
                'branch_id' => $branch_id,
            ];

            $salaryAmount += $netSalary;
            $salary_sheets[] = $tmp;
        }

        // --- Insert records ---
        SalarySheet::insert($salary_sheets);
        SalaryGenerate::where('id', $salaryGenerateId)
            ->update(['amount' => round($salaryAmount, 2)]);

        $salary_sheets_with_employees = collect($salary_sheets)->map(function ($sheet) {
            $employee = Employee::with(['designation'])->find($sheet['employee_id']);
            $sheet['employee'] = $employee;
            return $sheet;
        });

        return $salary_sheets_with_employees;
    }


    /**
     * Calculate attendance metrics for fixed 30 days month - FIXED: Don't count Fridays as absence
     */
    private function calculateAttendanceMetrics($attendance, $joinDate, $leaveDates, $weekendDays, $workingHoursPerDay, $firstDayOfMonth, $lastDayOfMonth, $fixedMonthDays = 30)
    {
        $actualWorkingDays = 0;
        $totalWorkingDays = 0;
        $workedHours = 0;
        $overtimeHours = 0;
        $absenceHours = 0;

        // Calculate based on fixed 30 days but exclude weekends
        $current = \Carbon\Carbon::parse($firstDayOfMonth);
        $end = \Carbon\Carbon::parse($lastDayOfMonth);

        $daysProcessed = 0;

        while ($current->lte($end) && $daysProcessed < $fixedMonthDays) {
            $dayOfWeek = strtolower($current->format('l'));
            $attendanceForDay = $attendance->firstWhere('date', $current->format('Y-m-d'));
            $isOnLeave = in_array($current->format('Y-m-d'), $leaveDates);

            // Only count days after join date
            if ($current->gte($joinDate)) {
                // Check if it's a working day (not Friday)
                if (!in_array($dayOfWeek, $weekendDays)) {
                    $totalWorkingDays++; // Count as potential working day (excluding Fridays)
                    $actualWorkingDays++; // Count as working day for salary calculation

                    if ($attendanceForDay && !$isOnLeave) {
                        $workedHours += $attendanceForDay->hours;

                        // Calculate overtime: any hours beyond standard 8 hours
                        if ($attendanceForDay->hours > $workingHoursPerDay) {
                            $overtimeHours += ($attendanceForDay->hours - $workingHoursPerDay);
                        } elseif ($attendanceForDay->hours < $workingHoursPerDay) {
                            // Only count absence for hours less than 8 on working days
                            $absenceHours += ($workingHoursPerDay - $attendanceForDay->hours);
                        }
                    } elseif (!$isOnLeave) {
                        // Absent but not on leave - full day absence (only for working days)
                        $absenceHours += $workingHoursPerDay;
                    }
                    // If on leave, no hours counted (neither worked nor absent)
                } else {
                    // Friday (weekend) - check for overtime but don't count as absence
                    if ($attendanceForDay) {
                        // All hours on Friday count as overtime
                        $overtimeHours += $attendanceForDay->hours;
                        $workedHours += $attendanceForDay->hours;
                    }
                    // FIXED: No absence counted for Fridays at all
                    // Friday is not a working day, so no absence should be counted
                }
            }

            $current->addDay();
            $daysProcessed++;
        }

        return [
            'actual_working_days' => $actualWorkingDays, // Working days excluding Fridays
            'total_working_days' => $totalWorkingDays, // Total working days (excluding Fridays)
            'worked_hours' => $workedHours,
            'overtime_hours' => $overtimeHours,
            'absence_hours' => $absenceHours,
        ];
    }

    private function calculateActiveLoans($loans, $checkDate)
    {
        $totalLoans = 0;

        foreach ($loans as $loan) {
            if ($loan->status != 'active') {
                continue;
            }

            $startDate = date("Y-m-01", strtotime($loan->repayment_from));
            $endDate = date("Y-m-t", strtotime("+" . ($loan->installment_period - 1) . " months", strtotime($startDate)));

            if (strtotime($checkDate) >= strtotime($startDate) && strtotime($checkDate) <= strtotime($endDate)) {
                $totalLoans += $loan->installment;
            }
        }

        return $totalLoans;
    }

    /**
     * Get all leave dates from approved leave applications
     */
    private function getLeaveDates($leaveApplications)
    {
        $leaveDates = [];

        foreach ($leaveApplications as $leave) {
            $start = \Carbon\Carbon::parse($leave->from_date);
            $end = \Carbon\Carbon::parse($leave->end_date);

            for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
                $leaveDates[] = $date->format('Y-m-d');
            }
        }

        return $leaveDates;
    }

    private function getSalarySheetsWithEmployees($salary_sheets)
    {
        return collect($salary_sheets)->map(function ($sheet) {
            $employee = Employee::with(['designation'])->find($sheet['employee_id']);
            $sheet['employee'] = $employee;
            return $sheet;
        });
    }
}
