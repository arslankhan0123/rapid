<?php

namespace App\Http\Controllers;


use App\Http\Requests\CreateProductRequest;
use App\Http\Requests\ProductUnitRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Requests\UpdateProductUnitRequest;
use App\Models\Product;
use App\Models\ProductUnit;
use App\Queries\ProductDataTable;
use App\Queries\ManageAttendanceDataTable;
use App\Repositories\ProductRepository;
use App\Repositories\ManageAttendanceRepository;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Models\NewAttendance;
use App\Models\EmployeeRate;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Employee;
use App\Models\MonthlyAttendanceInvoice;
use App\Models\ProjectMember;

class ManageAttendanceController extends AppBaseController
{
    private $manageAttendanceRepository;

    public function __construct(ManageAttendanceRepository $manageAttendanceRepo)
    {
        $this->manageAttendanceRepository = $manageAttendanceRepo;
    }
    public function index(Request $request)
    {
        // if ($request->ajax()) {
        //     return DataTables::of((new ManageAttendanceDataTable())->get($request->all()))->make(true);
        // }

        if ($request->ajax()) {
            $dataTable = DataTables::of((new ManageAttendanceDataTable())->get($request->all()));
            $response = $dataTable->addColumn('latest_date', function ($employee) {
                return $employee->latest_date; // Ensure accessor returns a valid value
            })->make(true);
            $responseData = $response->getData(true); // Get the response data as an array

            $salaryStatus = $this->manageAttendanceRepository->salaryStatus($request['month'],$request['branch_id']);

            $responseData['salaryStatus'] = $salaryStatus; // Add your extra parameter

            return response()->json($responseData);
        }

        $customers = $this->manageAttendanceRepository->getCustomers();
        $projects = $this->manageAttendanceRepository->getProjects();
        $departments = $this->manageAttendanceRepository->getDepartments();
        $designations = $this->manageAttendanceRepository->getDesignation();
        $allEmployees = $this->manageAttendanceRepository->allEmployees();

        $usersBranches = $this->getUsersBranches();
        //dd($allEmployees[51]);

        return view('manage_attendances.index_new', compact(['customers', 'projects', 'departments', 'designations', 'allEmployees', 'usersBranches']));
    }
    public function store(Request $request)
    {
        // Sample data structure from request
        $data = $request->input('attendance');


        // Initialize an array to hold the transformed data
        $transformedData = [];

        $month = null;

        // Extract data (Remove customer and project levels)
        foreach ($data as $employeeId => $dates) {

            foreach ($dates as $date => $hours) {

                // Prepare the data for each record
                $transformedData[] = [
                    'employee_id' => $employeeId,
                    'date' => $date,
                    'hours' => $hours,
                    'branch_id' => Employee::find($employeeId)->branch_id
                ];
            }
        }

        // Iterate over transformed data and use updateOrCreate
        foreach ($transformedData as $data) {
            $month = $data['date']; // Keep track of the month

            NewAttendance::updateOrCreate(
                [
                    'employee_id' => $data['employee_id'],
                    'date' => $data['date'],
                    'branch_id' => $data['branch_id']
                ],
                [
                    'hours' => $data['hours'],
                    'branch_id' => $data['branch_id']
                ]
            );
        }

        // Now handle the rates and other details
        $rates = $request->input('rate');
        $actual_hours = $request->input('actual_hours');
        $overtime_hours = $request->input('overtime_hours');
        $total_hours = $request->input('total_hours');
        $total_absent = $request->input('total_absent');
        $net_hours = $request->input('net_hours');
        $branch_id = $request->input('branch_id');

        foreach ($net_hours as $employeeId => $net_hour) {
            // Fetch corresponding values for other fields using the same employee ID
            $actualHour = isset($actual_hours[$employeeId]) ? $actual_hours[$employeeId] : 0;
            $overtimeHour = isset($overtime_hours[$employeeId]) ? $overtime_hours[$employeeId] : 0;
            $totalHour = isset($total_hours[$employeeId]) ? $total_hours[$employeeId] : 0;
            $totalAbsent = isset($total_absent[$employeeId]) ? $total_absent[$employeeId] : 0;
            $netHour = isset($net_hours[$employeeId]) ? $net_hours[$employeeId] : 0;

            // Use updateOrCreate to store all fields without customer and project
            EmployeeRate::updateOrCreate(
                [
                    'employee_id' => $employeeId,
                    'month' => $data['date'],

                ],
                [
                    'rate' => 0,
                    'actual_hours' => $actualHour,
                    'overtime_hours' => $overtimeHour,
                    'total_hours' => $totalHour,
                    'total_absent' => $totalAbsent,
                    'net_hours' => $netHour,
                ]
            );
        }

        // If post flag is set, update monthly invoice
        if ($request->input('post')) {
            // Assuming updateMonthlyInvoice no longer needs customer or project ID
            // $this->updateMonthlyInvoice($month);
            return $this->sendResponse('success', __('messages.manage_attendances.saved_posted'));
        }

        return $this->sendResponse('success', __('messages.manage_attendances.saved'));
    }

    // public function updateMonthlyInvoice($month)
    // {
    //     $customer_i = null;
    //     $project_id = null;
    //     // Initialize variables for total hours and employee IDs
    //     $totalHours = 0;
    //     $employeeIds = [];
    //     $totalAmount = 0;

    //     // Extract year and month from the input
    //     $year = date('Y', strtotime($month));
    //     $monthNumber = date('m', strtotime($month));

    //     // Query the database to get attendance data for the given year and month
    //     $attendanceData = NewAttendance::where('customer_id', $customer_id)
    //         ->where('project_id', $project_id)
    //         ->whereYear('date', $year)
    //         ->whereMonth('date', $monthNumber)
    //         ->get();

    //     // Get employee rates based on project_id
    //     $employeeRates = ProjectMember::where('owner_id', $project_id)->get()->keyBy('user_id');

    //     // Loop through the attendance data to calculate total hours and collect employee IDs
    //     foreach ($attendanceData as $entry) {
    //         $totalHours += $entry->hours; // Sum the hours
    //         $employeeIds[] = $entry->employee_id; // Collect employee IDs
    //     }

    //     // Calculate the total unique employees
    //     $totalEmployees = count(array_unique($employeeIds));

    //     // Calculate the total amount based on hourly rates
    //     foreach ($attendanceData as $entry) {
    //         if (isset($employeeRates[$entry->employee_id])) {
    //             $hourlyRate = $employeeRates[$entry->employee_id]->hourly_rate;
    //             $totalAmount += $entry->hours * $hourlyRate; // Multiply hours by hourly rate
    //         }
    //     }

    //     $this->storeMonthlyInvoice($customer_id, $project_id, $month, $totalHours, $totalAmount, $totalEmployees);
    //     // Prepare the final array

    // }

    // public function storeMonthlyInvoice($customer_id, $project_id, $month, $totalHours, $totalAmount, $totalEmployees)
    // {
    //     // Validate the incoming data as necessary

    //     // Extract year and month from the provided month

    //     $year = date('Y', strtotime($month));
    //     $monthNumber = date('m', strtotime($month));


    //     // Define the attributes to check for existing records
    //     $attributes = [
    //         'customer_id' => $customer_id,
    //         'project_id' => $project_id,
    //         'month' => $month, // Ensure this is in 'YYYY-MM' format
    //     ];



    //     // Define the values to update or create
    //     $values = [
    //         'posted_by' => auth()->id(), // or pass this as a parameter if needed
    //         'posted_at' => now(),
    //         'total_employees' => $totalEmployees,
    //         'total_amount' => $totalAmount,
    //         'paid_amount' => 0, // Set this to your desired default or pass as a parameter
    //         'total_hours' => $totalHours,
    //     ];


    //     // Use updateOrCreate with conditions for year and month
    //     $invoice = MonthlyAttendanceInvoice::where('customer_id', $customer_id)
    //         ->where('project_id', $project_id)
    //         ->whereYear('month', $year)
    //         ->whereMonth('month', $monthNumber)
    //         ->updateOrCreate($attributes, $values);

    //     // Optionally, return the updated or created invoice
    //     return $invoice;
    // }

    // public function import(Request $request)
    // {
    //     // Validate the incoming request
    //     $request->validate([
    //         'csv_file' => 'required|file|mimes:csv,txt',
    //         'customer_id' => 'required|integer',
    //         'project_id' => 'required|integer',
    //         'month' => 'required|date_format:Y-m', // Ensure valid month format (YYYY-MM)
    //     ]);

    //     $file = $request->file('csv_file');
    //     $customer_id = $request->input('customer_id');
    //     $project_id = $request->input('project_id');
    //     $month = $request->input('month');

    //     // Initialize arrays for attendance and rates data
    //     $attendanceData = [];
    //     $ratesData = [];

    //     // Open the CSV file and read it using fgetcsv for better handling of special characters
    //     if (($handle = fopen($file, 'r')) !== false) {
    //         $header = fgetcsv($handle); // Get the header row

    //         // Ensure that the CSV contains valid data
    //         if (!$header) {
    //             return $this->sendResponse('error', __('Invalid CSV format'));
    //         }

    //         // Process each row of employee data
    //         while (($row = fgetcsv($handle)) !== false) {
    //             if (!empty($row) && count($row) > 1) { // Ensure the row is not empty

    //                 // Extract employee details
    //                 $employee_id = trim($row[1]); // Assuming the second column is Employee ID
    //                 $rate = trim($row[9]); // Column 10 is 'Rate'

    //                 // Collect rates
    //                 $ratesData[] = [
    //                     'customer_id' => $customer_id,
    //                     'project_id' => $project_id,
    //                     'employee_id' => $employee_id,
    //                     'month' => $month,
    //                     'rate' => $rate,
    //                 ];

    //                 // Collect attendance for each day, starting from the 11th column
    //                 for ($i = 10; $i < count($row); $i++) {
    //                     $day = $header[$i]; // Get the day from the header
    //                     $hours = $row[$i];  // Get the hours worked on that day

    //                     // Extract the day number from the header (e.g., "01", "02")
    //                     if (preg_match('/(\d+)/', $day, $matches)) {
    //                         $dayNumber = $matches[1];

    //                         // Construct the full date (YYYY-MM-DD)
    //                         $date = $month . '-' . str_pad($dayNumber, 2, '0', STR_PAD_LEFT);

    //                         // Add to attendance data
    //                         $attendanceData[] = [
    //                             'customer_id' => $customer_id,
    //                             'project_id' => $project_id,
    //                             'employee_id' => $employee_id,
    //                             'date' => $date,  // Formatted date
    //                             'hours' => $hours
    //                         ];
    //                     }
    //                 }
    //             }
    //         }
    //         fclose($handle);
    //     } else {
    //         return $this->sendResponse('error', __('Unable to open CSV file'));
    //     }

    //     // Fetch all employees for matching IDs
    //     $allEmployees = $this->manageAttendanceRepository->getAllEmployees();

    //     // Insert or update attendance data
    //     foreach ($attendanceData as $data) {
    //         $employeeId = $this->iqamaToId($allEmployees, $data['employee_id']);
    //         if ($employeeId) {
    //             NewAttendance::updateOrCreate(
    //                 [
    //                     'customer_id' => $data['customer_id'],
    //                     'project_id' => $data['project_id'],
    //                     'employee_id' => $employeeId,
    //                     'date' => $data['date'],
    //                 ],
    //                 [
    //                     'hours' => $data['hours'],
    //                 ]
    //             );
    //         }
    //     }

    //     // Insert or update employee rates
    //     foreach ($ratesData as $data) {
    //         $employeeId = $this->iqamaToId($allEmployees, $data['employee_id']);
    //         if ($employeeId) {
    //             EmployeeRate::updateOrCreate(
    //                 [
    //                     'customer_id' => $data['customer_id'],
    //                     'project_id' => $data['project_id'],
    //                     'employee_id' => $employeeId,
    //                     'month' => $data['month'] . '-01', // Assuming the first day of the month for rates
    //                 ],
    //                 [
    //                     'rate' => $data['rate'],
    //                 ]
    //             );
    //         }
    //     }

    //     return $this->sendResponse('success', __('messages.manage_attendances.saved'));
    // }

    public function import(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt',
        ]);

        // Get the uploaded file and the 'month' column from the CSV
        $file = $request->file('csv_file');
        $customer_id = null;
        $project_id = null;

        // Initialize array to store attendance data
        $attendanceData = [];

        // Open the CSV file and read it using fgetcsv for better handling of special characters
        if (($handle = fopen($file, 'r')) !== false) {
            $header = fgetcsv($handle); // Get the header row

            // Ensure that the CSV contains valid data
            if (!$header) {
                return $this->sendResponse('error', __('Invalid CSV format'));
            }

            // Get the index for the "month" column
            $monthColumnIndex = array_search('month', $header);

            // Ensure the "month" column is present
            if ($monthColumnIndex === false) {
                return $this->sendResponse('error', __('CSV missing "month" column'));
            }

            // Process each row of employee data
            while (($row = fgetcsv($handle)) !== false) {
                if (!empty($row) && count($row) > 1) { // Ensure the row is not empty

                    // Extract month from the respective column (assuming it's in the format YYYY-MM)
                    $month = trim($row[$monthColumnIndex]);  // Get month value from the CSV

                    // Ensure the month format is correct (YYYY-MM)
                    if (!preg_match('/^\d{4}-\d{2}$/', $month)) {
                        return $this->sendResponse('error', __('Invalid month format in CSV. Expected YYYY-MM.'));
                    }

                    // Extract the IQAMA number (assuming it's in the second column)
                    $iqama_no = trim($row[1]);  // Assuming the second column is IQAMA number

                    // Collect attendance for each day, starting from the 3rd column to the end of the row
                    for ($i = 2; $i < count($row); $i++) {
                        // Skip the 'month' and 'iqama_no' columns
                        if ($i == $monthColumnIndex || $i == 1) {
                            continue;
                        }

                        // Extract the day number from the header
                        $day = $header[$i]; // Get the day from the header (e.g., 1, 2, 3, ..., 30)

                        // Get the hours worked on that day
                        $hours = $row[$i];

                        // Construct the full date (YYYY-MM-DD) using the month and day
                        $date = $month . '-' . str_pad($day, 2, '0', STR_PAD_LEFT); // Format: YYYY-MM-DD

                        // Add to attendance data
                        $attendanceData[] = [
                            'customer_id' => $customer_id,
                            'project_id' => $project_id,
                            'iqama_no' => $iqama_no,
                            'date' => $date,  // Formatted date
                            'hours' => $hours
                        ];
                    }
                }
            }
            fclose($handle);
        } else {
            return $this->sendResponse('error', __('Unable to open CSV file'));
        }

        // dd($attendanceData);
        // Fetch all employees for matching IQAMA numbers
        $allEmployees = $this->manageAttendanceRepository->getAllEmployees();

        // Insert or update attendance data
        foreach ($attendanceData as $data) {
            $employeeId = $this->iqamaToId($allEmployees, $data['iqama_no']);
            if ($employeeId) {
                NewAttendance::updateOrCreate(
                    [
                        'employee_id' => $employeeId,
                        'date' => $data['date'],
                    ],
                    [
                        'hours' => $data['hours'],
                    ]
                );
            }
        }

        return $this->sendResponse('success', __('messages.manage_attendances.saved'));
    }



    private function iqamaToId($data, $iqama_no)
    {
        foreach ($data as $employee) {
            if ($employee['iqama_no'] === $iqama_no) {
                return $employee['id'];
            }
        }

        // If no match is found, return null or a default value
        return null; // or return a default value like -1
    }
}
