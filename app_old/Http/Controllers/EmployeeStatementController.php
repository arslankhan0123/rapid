<?php

namespace App\Http\Controllers;

use App\Queries\EmployeeStatementDataTable;
use Illuminate\Http\Request;
use App\Repositories\EmployeeStatementRepository;
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
use App\Models\Insurance;
use Barryvdh\DomPDF\Facade as PDF;
use App\Models\Setting;
use App\Models\Customer;
use Illuminate\Support\Facades\Response;
use App\Repositories\ManageAttendanceRepository;
use App\Models\Employee;

class EmployeeStatementController extends AppBaseController
{

    /**
     * @var EmployeeStatementRepository
     */
    private $employeeStatementRepository;
    private $manageAttendanceRepository;
    public function __construct(EmployeeStatementRepository $employeeStatementRepo, ManageAttendanceRepository $manageAttendanceRepo)
    {
        $this->employeeStatementRepository = $employeeStatementRepo;
        $this->manageAttendanceRepository = $manageAttendanceRepo;
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            return DataTables::of((new EmployeeStatementDataTable())->get($request->all()))->make(true);
        }



        $departments = $this->manageAttendanceRepository->getDepartments();
        $designations = $this->manageAttendanceRepository->getDesignation();
        $allEmployees = $this->manageAttendanceRepository->allEmployees();

        // dd($departments,$designations->toArray(),$employees->toArray());

        return view('employee-statements.index', compact(['departments', 'designations', 'allEmployees']));
    }

    public function export(Request $request)
    {


        // Validate that employee_id is provided
        if (!$request->has('employee_id') || empty($request->employee_id)) {
            return response()->json(['error' => 'Employee ID is required for export.'], 400);
        }

        $data = (new EmployeeStatementDataTable())->get($request->all());

        if ($request->type === 'csv') {
            $csvData = [];

            // Add the header row for the CSV export
            $csvData[] = [
                'Doc Date',
                'Doc Number',
                'Doc Type',
                'Narration',
                'Debit',
                'Credit',
                'Balance',
            ];

            // Initialize variables for the calculations
            $totalCredit = 0;
            $totalDebit= 0;  // Placeholder for any received amount
            $balance = 0;   // Initial balance is 0
            $sl = 1;         // Initialize serial number for each row

            // Loop through the statements to add the data rows
            foreach ($data as $statement) {


                $csvData[] = [
                    $statement['doc_date'] ?? '',
                    $sl,  // Serial number
                    $statement['type'] ?? '',  // Fixed value for salary
                    $statement['month'] ?? '',
                    number_format($statement['debit'] ?? 0.00, 2),  // Debit placeholder
                    number_format($statement['credit'] ?? 0.00, 2),  // Credit (net salary) formatted
                    number_format($balance, 2),  // Running balance formatted
                ];
                $sl++;  // Increment serial number for next row
                $totalCredit+=$statement['credit']??0;
                $totalDebit+=$statement['debit']??0;
                $balance=($totalCredit-$totalDebit)??0;
            }

            // Add the totals row at the end of the CSV
            $csvData[] = [
                '',
                '',
                '',
                'Total:',
                number_format($totalDebit, 2),  // Total debit (no debit values in this example)
                number_format($totalCredit, 2),  // Total credit (net amount)
                number_format($balance, 2),  // Total balance
            ];

            // Prepare CSV filename
            $filename = 'employee_statements.csv';

            // Set the appropriate headers for the CSV download
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => "attachment; filename=\"$filename\"",
                'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0', // Disable caching
                'Pragma' => 'no-cache', // Disable caching for HTTP/1.0
                'Expires' => '0', // Ensure immediate expiration
            ];

            // Stream the CSV content to the browser
            return response()->stream(function () use ($csvData) {
                // Open output stream for writing CSV data
                $handle = fopen('php://output', 'w');

                // Write rows to the CSV output
                foreach ($csvData as $row) {
                    fputcsv($handle, $row);
                }

                // Close the handle after writing
                fclose($handle);
            }, 200, $headers);
        } elseif ($request->type === 'pdf') {

            $settings = Setting::all()->pluck('value', 'key')->toArray();
            $employee = Employee::with(['department', 'designation'])->where('id', $request['employee_id'])->first();
            // Data to pass to the view
            $data = [
                'settings' => $settings,
                'statement' => $data,
                'employee' => $employee,
                'start_date' => $request['from_date'] ?? '',
                'end_date' => $request['to_date']
            ];

            $pdf = PDF::loadView('employee-statements.pdf_v3', $data);
            $pdf->setPaper('A4', 'portrait');
            $pdf->setOptions(["isPhpEnabled" => true, 'isHtml5ParserEnabled' => true]);
            return $pdf->download(__('messages.invoice.invoice_prefix') . "employee_statements" . '.pdf');
        }
    }
    public function downloadPDF($id)
    {


        $settings = Setting::all()->pluck('value', 'key')->toArray();
        // Retrieve data from DataTables based on the user's input
        $employee = Employee::with(['department', 'designation'])->where('id', $id)->first();

        $data = SalarySheet::with(['salaryGenerate'])
            ->whereHas('employee', function ($query) use ($id) {
                $query->where('id', $id);
            })
            ->orderBy('created_at', 'desc')
            ->get();




        // Data to pass to the view
        $data = [
            'settings' => $settings,
            'statement' => $data,
            'employee' => $employee
        ];
        // Load the view and pass data
        // dd($invoice->invoiceAddresses->toArray());
        // dd($invoice->salesItems->toArray());
        $pdf = PDF::loadView('employee-statements.pdf_v3', $data);
        $pdf->setPaper('A4', 'portrait');
        $pdf->setOptions(["isPhpEnabled" => true, 'isHtml5ParserEnabled' => true]);
        return $pdf->download(__('messages.invoice.invoice_prefix') . "employee_statements" . '.pdf');
    }
}
