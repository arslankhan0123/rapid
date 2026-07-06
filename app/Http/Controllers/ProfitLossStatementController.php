<?php

namespace App\Http\Controllers;

use App\Queries\ProfitLossStatementDataTable;
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
use App\Models\Branch;

class ProfitLossStatementController extends AppBaseController
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
            return DataTables::of((new ProfitLossStatementDataTable())->get($request->all()))->make(true);
        }

        // dd($departments,$designations->toArray(),$employees->toArray());
        $usersBranches = $this->getUsersBranches();
        return view('profit-loss.index', compact('usersBranches'));
    }

    public function export(Request $request)
    {


        $branch_name = '';
        if (isset($request->filterBranch) && !empty($request->filterBranch)) {
            $branch_id = $request->filterBranch;
            $branch = Branch::where('id', $branch_id)->first();
            if ($branch) {
                $branch_name = "_" . str_replace(' ', '_', $branch->name);
            }
        }

        // Retrieve data from DataTables based on the user's input
        $data = (new ProfitLossStatementDataTable())->get($request->all());
        if ($request->type === 'csv') {
            $csvData = [];
            $csvData[] = [
                'Particulars',  // Column for item/description
                'Debit',        // Column for Debit amount
                'Credit',       // Column for Credit amount
            ];

            // Initialize totals
            $totalDebit = 0;
            $totalCredit = 0;

            // Loop through the data to add rows
            foreach ($data as $row) {
                $debit = $row['debit'] ?? 0;
                $credit = $row['credit'] ?? 0;

                // Update totals
                $totalDebit += $debit;
                $totalCredit += $credit;

                // Determine the type string (including category name if available)
                $particulars = $row['type'] ?? '';
                if (!empty($row['category'])) {
                    $particulars .= ' - ' . $row['category'];
                }

                // Add a row to the CSV
                $csvData[] = [
                    $particulars, // Particulars (e.g., Sales, Return, or Expense with category name)
                    number_format($debit, 2), // Debit amount
                    number_format($credit, 2), // Credit amount
                ];
            }

            // Add the Gross Profit row
            $grossProfit = $totalCredit - $totalDebit;
            $csvData[] = [
                'Gross Profit', // Label for Gross Profit
                number_format($grossProfit, 2), // Gross Profit (Credit - Debit)
                '', // No Credit for Gross Profit
            ];

            // Add the totals row for Debit and Credit
            $csvData[] = [
                'Total', // Label for Total row
                number_format($totalDebit, 2), // Total Debit
                number_format($totalCredit, 2), // Total Credit
            ];

            // Retrieve the `from_date` from the request, or default to the current date if not provided
            $fromDate = $request->input('from_date', date('Y-m')); // Default to current year-month if not provided
            $dateParts = explode('-', $fromDate); // Split the `from_date` into year and month
            $year = $dateParts[0]; // Year is the first part
            $month = $dateParts[1]; // Month is the second part

            // Prepare the CSV filename with year and month from `from_date`
            $filename = 'profit_loss_statement_' . $year . '_' . $month  . $branch_name . '.csv';
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
        } else if ($request->type === 'pdf') {


            $fromDate = $request->input('from_date', date('Y-m')); // Default to current year-month if not provided
            $dateParts = explode('-', $fromDate); // Split the `from_date` into year and month
            $year = $dateParts[0]; // Year is the first part
            $month = $dateParts[1]; // Month is the second part
            $settings = Setting::all()->pluck('value', 'key')->toArray();

            $data = [
                'settings' => $settings,
                'month' => $fromDate,
                'statement' => $data,
                'branch_name'=>$branch?->name??'',

            ];

            $pdf = PDF::loadView('profit-loss.pdf_v3', $data);
            $pdf->setPaper('A4', 'portrait');
            $pdf->setOptions(["isPhpEnabled" => true, 'isHtml5ParserEnabled' => true]);


            // Prepare the CSV filename with year and month from `from_date`
            $filename = 'profit_loss_statement_' . $year . '_' . $month . $branch_name . '.pdf';
            return $pdf->download($filename);
        }
    }
}
