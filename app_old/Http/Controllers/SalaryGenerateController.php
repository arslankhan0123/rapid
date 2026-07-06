<?php

namespace App\Http\Controllers;

use App\Queries\SalaryGenerateDataTable;
use Illuminate\Http\Request;
use App\Repositories\SalaryGenerateRepository;
use Yajra\DataTables\DataTables;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Exception;
use App\Http\Requests\SalaryGenerateRequest;
use App\Http\Requests\UpdateSalaryGenerateRequest;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use Throwable;
use App\Models\SalaryGenerate;
use App\Models\SalarySheet;
use App\Models\Salary;
use App\Models\Bonus;
use App\Models\Loan;
use App\Models\Commission;
use App\Models\Employee;
use App\Models\Insurance;
use Laracasts\Flash\Flash;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade as PDF;
use DateInterval;
use DatePeriod;
use DateTime;
use Illuminate\Support\Facades\DB;


class SalaryGenerateController extends AppBaseController
{
    /**
     * @var SalaryGenerateRepository
     */
    private $salaryGenerateRepositoy;
    public function __construct(SalaryGenerateRepository $salaryGenerateRepo)
    {
        $this->salaryGenerateRepositoy = $salaryGenerateRepo;
    }
    /**
     * @param  Request  $request
     * @return Application|Factory|View
     *
     * @throws Exception
     */
    public function index(Request $request)
    {

        if ($request->ajax()) {
            return DataTables::of((new SalaryGenerateDataTable())->get($request->all()))->make(true);
        }
        $usersBranches = $this->getUsersBranches();

        return view('salary_generates.index', compact('usersBranches'));
    }

    public function create()
    {
        $usersBranches = $this->getUsersBranches();
        return view('salary_generates.create', compact('usersBranches'));
    }


    // public function debugSalaryEmployee91()
    // {
    //     $branch_id = 4;
    //     $employee_id = 91;

    //     $year = 2025;
    //     $monthNum = 1;

    //     $workingHoursPerDay = 8;
    //     $weekendDays = ['friday'];

    //     $firstDay = "$year-$monthNum-01";
    //     $lastDay = date("Y-m-t", strtotime($firstDay));

    //     // Fetch employee with ALL attendance from ALL branches (IMPORTANT!)
    //     $employee = Employee::with([
    //         'attendance' => function ($query) use ($year, $monthNum) {
    //             $query->whereYear('date', $year)
    //                 ->whereMonth('date', $monthNum);
    //         },
    //         'leaveApplications'
    //     ])
    //         ->where('id', $employee_id)
    //         ->first();

    //     if (!$employee) {
    //         return response()->json(["error" => "Employee not found"]);
    //     }

    //     // Leave dates
    //     $leaveDates = [];
    //     foreach ($employee->leaveApplications as $leave) {
    //         $start = Carbon\Carbon::parse($leave->from_date);
    //         $end = Carbon\Carbon::parse($leave->end_date);

    //         for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
    //             $leaveDates[] = $date->format('Y-m-d');
    //         }
    //     }

    //     // Split attendance by branch
    //     $attendance_all = $employee->attendance;
    //     $attendance_branch4 = $attendance_all->where('branch_id', 4)->values();
    //     $attendance_other_branches = $attendance_all->where('branch_id', '!=', 4)->values();

    //     $debug_log = [];
    //     $absence_hours = 0;
    //     $worked_hours = 0;
    //     $overtime_hours = 0;

    //     // Loop all dates
    //     $period = new DatePeriod(
    //         new DateTime($firstDay),
    //         new DateInterval('P1D'),
    //         (new DateTime($lastDay))->modify('+1 day')
    //     );

    //     foreach ($period as $day) {

    //         $date = $day->format('Y-m-d');
    //         $dayName = strtolower($day->format('l'));

    //         $entry = [
    //             "date" => $date,
    //             "day" => $dayName,
    //             "attendance_found" => false,
    //             "attendance_hours" => 0,
    //             "branch_id" => null,
    //             "reason" => [],
    //             "absence_hours_added" => 0
    //         ];

    //         if ($date < $employee->join_date) {
    //             $entry["reason"][] = "SKIP — before join date";
    //             $debug_log[] = $entry;
    //             continue;
    //         }

    //         // Only use branch-4 attendance for calculation
    //         $attendance = $attendance_branch4->first(function ($att) use ($date) {
    //             return $att->date === $date;
    //         });

    //         if ($attendance) {
    //             $entry["attendance_found"] = true;
    //             $entry["attendance_hours"] = (float)$attendance->hours;
    //             $entry["branch_id"] = $attendance->branch_id;
    //         }

    //         // Weekend logic
    //         if ($dayName === 'friday') {
    //             $entry["reason"][] = "Friday — paid day NO absence";

    //             if ($attendance && $attendance->hours > 0) {
    //                 $entry["reason"][] = "Worked on Friday → overtime added";
    //                 $worked_hours += $attendance->hours;
    //                 $overtime_hours += $attendance->hours;
    //             }

    //             $debug_log[] = $entry;
    //             continue;
    //         }

    //         // Leave logic
    //         if (in_array($date, $leaveDates)) {
    //             $entry["reason"][] = "Leave day → 8 hours absent added";
    //             $absence_hours += 8;
    //             $entry["absence_hours_added"] = 8;

    //             $debug_log[] = $entry;
    //             continue;
    //         }

    //         // Attendance exists
    //         if ($attendance) {

    //             if ($attendance->hours > 0) {

    //                 $worked_hours += $attendance->hours;

    //                 if ($attendance->hours < 8) {
    //                     $missing = 8 - $attendance->hours;
    //                     $entry["reason"][] = "Worked less than 8 hours → $missing absent hours";
    //                     $absence_hours += $missing;
    //                     $entry["absence_hours_added"] = $missing;
    //                 }

    //                 if ($attendance->hours > 8) {
    //                     $ot = $attendance->hours - 8;
    //                     $entry["reason"][] = "Overtime → $ot hours";
    //                     $overtime_hours += $ot;
    //                 }
    //             } else {
    //                 $entry["reason"][] = "0 hour attendance → 8 hrs absent";
    //                 $absence_hours += 8;
    //                 $entry["absence_hours_added"] = 8;
    //             }

    //             $debug_log[] = $entry;
    //             continue;
    //         }

    //         // No attendance found → absent
    //         $entry["reason"][] = "No attendance → 8 hrs absent";
    //         $absence_hours += 8;
    //         $entry["absence_hours_added"] = 8;

    //         $debug_log[] = $entry;
    //     }

    //     return response()->json([

    //         "employee_id" => 91,
    //         "name" => $employee->name,
    //         "join_date" => $employee->join_date,
    //         "debug_month" => "2025-01",

    //         "branch4_summary" => [
    //             "worked_hours" => $worked_hours,
    //             "absence_hours" => $absence_hours,
    //             "overtime_hours" => $overtime_hours,
    //         ],

    //         "attendance_all_branches" => $attendance_all->values(),
    //         "attendance_branch_4_only" => $attendance_branch4,
    //         "attendance_other_branches" => $attendance_other_branches,

    //         "leave_dates" => $leaveDates,
    //         "daily_log" => $debug_log
    //     ]);
    // }

    // public function debugSalaryBranch9January2025()
    // {
    //     $branch_id = 9;
    //     $year = 2025;
    //     $monthNum = 1;
    //     $workingHoursPerDay = 8;
    //     $weekendDays = ['friday'];

    //     $firstDay = "$year-$monthNum-01";
    //     $lastDay = date("Y-m-t", strtotime($firstDay));

    //     // Fetch all employees who have attendance in branch 9 in Jan 2025
    //     $employees = Employee::whereHas('attendance', function ($query) use ($branch_id, $year, $monthNum) {
    //         $query->where('branch_id', $branch_id)
    //             ->whereYear('date', $year)
    //             ->whereMonth('date', $monthNum);
    //     })
    //         ->with([
    //             'attendance' => function ($query) use ($year, $monthNum) {
    //                 $query->whereYear('date', $year)
    //                     ->whereMonth('date', $monthNum);
    //             },
    //             'leaveApplications'
    //         ])->get();

    //     $all_debug = [];

    //     foreach ($employees as $employee) {

    //         // Collect leave dates
    //         $leaveDates = [];
    //         foreach ($employee->leaveApplications as $leave) {
    //             $start = Carbon::parse($leave->from_date);
    //             $end = Carbon::parse($leave->end_date);

    //             for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
    //                 $leaveDates[] = $date->format('Y-m-d');
    //             }
    //         }

    //         // Split attendance by branch
    //         $attendance_all = $employee->attendance;
    //         $attendance_target_branch = $attendance_all->where('branch_id', $branch_id)->values();
    //         $attendance_other_branches = $attendance_all->where('branch_id', '!=', $branch_id)->values();

    //         $debug_log = [];
    //         $absence_hours = 0;
    //         $worked_hours = 0;
    //         $overtime_hours = 0;

    //         // Loop all dates in the month
    //         $period = new DatePeriod(
    //             new DateTime($firstDay),
    //             new DateInterval('P1D'),
    //             (new DateTime($lastDay))->modify('+1 day')
    //         );

    //         foreach ($period as $day) {
    //             $date = $day->format('Y-m-d');
    //             $dayName = strtolower($day->format('l'));

    //             $entry = [
    //                 "date" => $date,
    //                 "day" => $dayName,
    //                 "attendance_found" => false,
    //                 "attendance_hours" => 0,
    //                 "branch_id" => null,
    //                 "reason" => [],
    //                 "absence_hours_added" => 0
    //             ];

    //             // Skip before join date
    //             if ($date < $employee->join_date) {
    //                 $entry["reason"][] = "SKIP — before join date";
    //                 $debug_log[] = $entry;
    //                 continue;
    //             }

    //             // Attendance for branch 9
    //             $attendance = $attendance_target_branch->first(function ($att) use ($date) {
    //                 return $att->date === $date;
    //             });

    //             if ($attendance) {
    //                 $entry["attendance_found"] = true;
    //                 $entry["attendance_hours"] = (float)$attendance->hours;
    //                 $entry["branch_id"] = $attendance->branch_id;
    //             }

    //             // Weekend logic
    //             if (in_array($dayName, $weekendDays)) {
    //                 $entry["reason"][] = "Weekend — paid day NO absence";

    //                 if ($attendance && $attendance->hours > 0) {
    //                     $entry["reason"][] = "Worked on weekend → overtime added";
    //                     $worked_hours += $attendance->hours;
    //                     $overtime_hours += $attendance->hours;
    //                 }

    //                 $debug_log[] = $entry;
    //                 continue;
    //             }

    //             // Leave logic
    //             if (in_array($date, $leaveDates)) {
    //                 $entry["reason"][] = "Leave day → 8 hours absent added";
    //                 $absence_hours += $workingHoursPerDay;
    //                 $entry["absence_hours_added"] = $workingHoursPerDay;
    //                 $debug_log[] = $entry;
    //                 continue;
    //             }

    //             // Attendance exists
    //             if ($attendance) {
    //                 $worked_hours += $attendance->hours;

    //                 if ($attendance->hours < $workingHoursPerDay) {
    //                     $missing = $workingHoursPerDay - $attendance->hours;
    //                     $entry["reason"][] = "Worked less than $workingHoursPerDay hours → $missing absent hours";
    //                     $absence_hours += $missing;
    //                     $entry["absence_hours_added"] = $missing;
    //                 }

    //                 if ($attendance->hours > $workingHoursPerDay) {
    //                     $ot = $attendance->hours - $workingHoursPerDay;
    //                     $entry["reason"][] = "Overtime → $ot hours";
    //                     $overtime_hours += $ot;
    //                 }

    //                 $debug_log[] = $entry;
    //                 continue;
    //             }

    //             // No attendance found → absent
    //             $entry["reason"][] = "No attendance → $workingHoursPerDay hrs absent";
    //             $absence_hours += $workingHoursPerDay;
    //             $entry["absence_hours_added"] = $workingHoursPerDay;
    //             $debug_log[] = $entry;
    //         }

    //         $all_debug[] = [
    //             "employee_id" => $employee->id,
    //             "name" => $employee->name,
    //             "join_date" => $employee->join_date,
    //             "debug_month" => "$year-$monthNum",
    //             "branch_summary" => [
    //                 "worked_hours" => $worked_hours,
    //                 "absence_hours" => $absence_hours,
    //                 "overtime_hours" => $overtime_hours,
    //             ],
    //             "attendance_all_branches" => $attendance_all->values(),
    //             "attendance_target_branch_only" => $attendance_target_branch,
    //             "attendance_other_branches" => $attendance_other_branches,
    //             "leave_dates" => $leaveDates,
    //             "daily_log" => $debug_log
    //         ];
    //     }

    //     return response()->json($all_debug);
    // }



    public function store(SalaryGenerateRequest $request)
    {

        $input = $request->all();


        $input['generated_by'] = Auth::id();
        $input['generate_date'] = Carbon::today()->format('Y-m-d');

        try {


            $result = $this->salaryGenerateRepositoy->create($input);
            if ($result) {
                $employees = $this->salaryGenerateRepositoy->generateSalary($result);
                $data = [
                    "salary_info" => $result->load('branch'),
                    "emoloyees_info" => $employees
                ];
                activity()->causedBy(getLoggedInUser())
                    ->performedOn($result)
                    ->useLog('Salary created.')
                    ->log($result->salary_month . ' Salary Created.');
                Flash::success(__('messages.salary_generates.saved'));
                return $this->sendResponse($data, __('messages.salary_generates.saved'));
            } else {
                return $this->sendError(__("Salary Sheet Already Created."));
            }
        } catch (Throwable $e) {
            throw $e;
        }
    }


    // public function updateSalarySheet($last_id)
    // {


    //     $salary_sheets = [];

    //     // Get the current month and year
    //     $currentMonth = Carbon::now()->month;
    //     $currentYear = Carbon::now()->year;
    //     $employee_salary = Salary::with('employee')->where('is_active', 1)
    //         ->whereMonth('month', $currentMonth) // Assuming 'generate_date' is the date column
    //         ->whereYear('month', $currentYear)
    //         ->get();


    //     $employee_loans = Loan::whereMonth('repayment_from', $currentMonth) // Assuming 'generate_date' is the date column
    //         ->whereYear('created_at', $currentYear)
    //         ->get();


    //     foreach ($employee_salary as $emp_salary) {
    //         $basic_salary = 0;
    //         $salary_advance = 0;
    //         $gross_salary = 0;
    //         $state_income_tax = 0;
    //         $total_loan = 0;
    //         $total_bonus = 0;
    //         $total_allowances = 0;
    //         $total_commission = 0;
    //         $total_insurance = 0;
    //         $total_deduction = 0;
    //         $net_salary = 0;




    //         foreach ($employee_loans as $loan) {
    //             if ($emp_salary->employee_id == $loan->employee_id) {
    //                 $total_loan = $loan->installment;
    //             }
    //         }


    //         $net_salary = $emp_salary->salary - $total_loan + $total_bonus + $total_commission + $total_insurance + $total_deduction;

    //         $tmp = [
    //             'employee_id' => $emp_salary->employee_id,
    //             'salary_generate_id' => $last_id,
    //             'basic_salary' => $emp_salary->employee->basic_salary ?? 0,
    //             'salary_advance' => $emp_salary->employee->gross_salary ?? 0,
    //             'gross_salary' => $emp_salary->salary,
    //             'state_income_tax' => 0,
    //             'loan' => $total_loan,
    //             'total_bonus' => $total_bonus,
    //             'total_allowances' => $emp_salary->employee->transport_allowance ?? 0,
    //             'total_commission' => $total_commission,
    //             'total_insurance' => $total_insurance,
    //             'total_deduction' => $total_deduction,
    //             'net_salary' => $net_salary
    //         ];
    //         array_push($salary_sheets, $tmp);
    //     }
    //     return $salary_sheets;
    // }


    // public function destroy(SalaryGenerate $salaryGenerate)
    // {

    //     try {
    //         $salaryGenerate->delete();
    //         activity()->performedOn($salaryGenerate)->causedBy(getLoggedInUser())
    //             ->useLog('Salary deleted.')->log($salaryGenerate->salary_month . '  Salary deleted.');
    //         return $this->sendSuccess(__('messages.salary_generates.delete'));
    //     } catch (QueryException $e) {
    //         return $this->sendError('Failed To delete!! Already in use.');
    //     }
    // }

    function verify(SalaryGenerate $salaryGenerate)
    {

        $salaryGenerate['approved_by'] = Auth::id();
        $salaryGenerate['approved_date'] = Carbon::today()->format('Y-m-d');

        $updateSalaryGenerate = $this->salaryGenerateRepositoy->approve_salary($salaryGenerate);
        activity()->performedOn($updateSalaryGenerate)->causedBy(getLoggedInUser())
            ->useLog('Salary Sheet Approved')->log($updateSalaryGenerate->salary_month . ' Salary Sheet Approved.');
        return $this->sendSuccess(__('messages.salary_generates.verified'));
    }

    function sheet(SalaryGenerate $salaryGenerate)
    {
        $salaryGenerate->load(['salarySheets.employee', 'branch']);
        $sheets = $salaryGenerate->salarySheets;

        return view('salary_generates.salary_sheets', compact(['sheets', 'salaryGenerate']));
    }
    public function export(Request $request)
    {

        $salaryGenerate = SalaryGenerate::with('branch')->find($request['id']);
        $salaryGenerate->load(['salarySheets.employee']);
        $sheets = $salaryGenerate->salarySheets;
        $settings = Setting::pluck('value', 'key')->toArray();


        //    return view('salary_generates.pdf_v3', compact(['sheets', 'salaryGenerate', 'settings']));

        $pdf = PDF::loadView('salary_generates.pdf_v3', compact(['sheets', 'salaryGenerate', 'settings']));

        // Check the action parameter from the request
        if ($request->input('action') === 'download') {
            // Download the PDF
            return $pdf->download("Salary_sheet_{$request['id']}_" . Carbon::now()->format('Y-m-d') . ".pdf");
        } elseif ($request->input('action') === 'print') {
            // Display the PDF for printing in the browser
            return $pdf->stream("Salary_sheet_{$request['id']}_" . Carbon::now()->format('Y-m-d') . ".pdf");
        } elseif ($request->input('action') === 'csv') {
            // Handle CSV export
            // Added CSV export functionality
            $filename = "Salary_sheet_{$request['id']}_" . Carbon::now()->format('Y-m-d') . ".csv";
            $headers = [
                "Content-type" => "text/csv",
                "Content-Disposition" => "attachment; filename=$filename",
                "Pragma" => "no-cache",
                "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
                "Expires" => "0",
            ];

            $columns = [
                '#',
                'Employee ID',
                'Employee Name',
                'Basic Salary',
                'Bonuses',
                'Total Allowances',
                'Gross Salary',
                'Salary Advance',
                'Loan',
                'Total Deduction',
                'Net Salary'
            ];

            // Open output stream to create the CSV content dynamically
            $callback = function () use ($sheets, $columns) {
                $file = fopen('php://output', 'w');

                // Insert the headers
                fputcsv($file, $columns);

                // Insert data rows
                foreach ($sheets as $index => $sheet) {
                    fputcsv($file, [
                        $index + 1,
                        $sheet->employee->iqama_no,
                        $sheet->employee->name,
                        number_format($sheet->basic_salary, 2),
                        number_format($sheet->total_bonus, 2),
                        number_format($sheet->total_allowances, 2),
                        number_format($sheet->gross_salary, 2),
                        number_format($sheet->salary_advance, 2),
                        number_format($sheet->loan, 2),
                        number_format($sheet->total_deduction, 2),
                        number_format($sheet->net_salary, 2),
                    ]);
                }

                fclose($file);
            };

            // Return response for CSV download
            return response()->stream($callback, 200, $headers);
        }

        // Optionally, handle the case where no valid action is provided
        return redirect()->back()->with('error', 'Invalid action specified.');
    }
}
