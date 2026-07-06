<?php

namespace App\Repositories;

use App\Models\MonthlyAttendanceInvoice;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use App\Models\Customer;
use App\Models\Project;
use App\Models\NewAttendance;
use Carbon\Carbon;
use App\Models\PaymentMode;
use App\Models\ProjectMember;
use App\Models\EmployeeRate;
use App\Models\State;
use App\Models\Country;
use App\Models\Setting;

/**
 * Class CustomerRepository
 *
 * @version April 3, 2020, 6:37 am UTC
 */
class ProjectInvoiceRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'customer_id',
        'project_id',
        'month',
        'posted_by',
        'updated_by',
        'posted_at',
        'total_employees',
        'total_amount',
        'paid_amount',
        'status',
        'total_hours',
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
        return MonthlyAttendanceInvoice::class;
    }
    public function getCustomers()
    {
        return Customer::pluck('company_name', 'id');
    }
    public function getProjects()
    {
        return Project::get();
    }
    public function getBankDetails()
    {
        return Setting::where('key', 'bank_details')->first();
    }

    public function getInvoiceEmployeeList(MonthlyAttendanceInvoice $monthlyAttendanceInvoice, $vat_percentage = 0)
    {

        // Extract the customer, project, and month from the invoice
        $customer_id = $monthlyAttendanceInvoice->customer_id;
        $project_id = $monthlyAttendanceInvoice->project_id;
        $month = $monthlyAttendanceInvoice->month;

        // Build the attendance query
        $query = NewAttendance::orderBy('created_at', 'desc');

        // Filter by the month if provided
        if (!empty($month)) {
            $query->whereMonth('date', Carbon::parse($month)->month)
                ->whereYear('date', Carbon::parse($month)->year);
        }

        // Filter by customer and project if provided
        if (!empty($customer_id)) {
            $query->where('customer_id', $customer_id);
        }

        if (!empty($project_id)) {
            $query->where('project_id', $project_id);
        }

        // Retrieve all attendance records
        $attendanceRecords = $query->get();

        // Initialize an array to store employee data grouped by department
        $departmentData = [];

        // Loop through each attendance record
        foreach ($attendanceRecords as $record) {
            $employeeId = $record['employee_id'];
            $departmentName = $record->employee->department->name ?? 'No Department'; // Get department name

            // Group employees by their department
            if (!isset($departmentData[$departmentName])) {
                $departmentData[$departmentName] = [];
            }

            // Initialize employee data if not already set
            if (!isset($departmentData[$departmentName][$employeeId])) {
                $departmentData[$departmentName][$employeeId] = [
                    'hours' => 0,          // Store total hours worked
                    'rate' => 0,           // Store hourly rate (fetched from project_member)
                    'total' => 0,          // Store total cost (hours * rate)
                    'actual_hours' => 0,   // Store actual hours (fetched from employee_rates)
                    'overtime_hours' => 0, // Store overtime hours (fetched from employee_rates)
                    'total_hours' => 0,    // Store total hours (fetched from employee_rates)
                    'total_absent' => 0,   // Store total absent (fetched from employee_rates)
                    'net_hours' => 0,      // Store net hours (fetched from employee_rates)
                ];
            }

            // Add the current record's hours to the employee's total hours
            $departmentData[$departmentName][$employeeId]['hours'] += $record['hours'];
        }

        // Fetch employee rates and other data from EmployeeRate table
        foreach ($departmentData as $departmentName => &$employees) {
            foreach ($employees as $employeeId => &$employeeData) {
                // Get the project member data for the employee
                $projectMember = ProjectMember::where('owner_id', $project_id)
                    ->where('user_id', $employeeId)
                    ->first();

                // If the project member exists, fetch the rate and calculate the total
                if ($projectMember) {
                    $rate = $projectMember->hourly_rate; // Assuming hourly_rate exists in project_member table
                    $employeeData['rate'] = $rate;       // Assign the rate to the employee data
                    $employeeData['total'] = $rate * $employeeData['hours']; // Calculate total (hours * rate)
                }

                // Fetch additional data from EmployeeRate for the given employee, customer, project, and month
                $employeeRate = EmployeeRate::where([
                    ['employee_id', '=', $employeeId],
                    ['customer_id', '=', $customer_id],
                    ['project_id', '=', $project_id],
                    ['month', '=', $month]
                ])->first();

                // If employee rate data exists, update the respective fields
                if ($employeeRate) {
                    $employeeData['actual_hours'] = $employeeRate->actual_hours ?? 0;
                    $employeeData['overtime_hours'] = $employeeRate->overtime_hours ?? 0;
                    $employeeData['total_hours'] = $employeeRate->total_hours ?? 0;
                    $employeeData['total_absent'] = $employeeRate->total_absent ?? 0;
                    $employeeData['net_hours'] = $employeeRate->net_hours ?? 0;
                }
            }
        }





        $departmentSummary = [];
        $sl = 1; // Serial number


        $tmpdepartmentData = $departmentData;
        foreach ($tmpdepartmentData as $dName => $department) {



            $totalEmployees = count($department);

            $totalHours = 0;
            $totalAmount = 0;
            $basic_hours = 0;
            $overtimes = 0;
            $rate = 0;

            // print_r($department);

            foreach ($department as $employeeId => $employeeDatas) {
                $totalHours += $employeeDatas['total_hours']; // Sum up the hours for each employee
                $totalAmount += $employeeDatas['total']; // Sum up the total amount for each employee
                $basic_hours += $employeeDatas['actual_hours'];
                $overtimes += $employeeDatas['overtime_hours'];
                $rate += $employeeDatas['rate'];
            }



            $vat = $totalAmount * ($vat_percentage / 100);

            $departmentSummary[] = [
                'sl' => $sl++, // Serial number for the department
                'department' => $dName, // Name of the department
                'total_employees' => $totalEmployees, // Total number of employees in the department
                'basic_hours' => $basic_hours,
                'overtimes' => $overtimes,
                'total_hours' => $totalHours, // Total hours worked by employees in the department
                'total' => $totalAmount, // Total amount for all employees in the department
                'rate' => $rate / $totalEmployees,
                'vat' => $vat, // VAT amount based on the percentage
                'total_with_vat' => $totalAmount + $vat // Total amount including VAT
            ];
        }

        return $departmentSummary;
    }


    public function getPaymentModes()
    {
        return PaymentMode::pluck('name', 'id');
    }
    public function getStates()
    {
        return State::get()->toArray();
    }

}
