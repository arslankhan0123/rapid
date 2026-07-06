<?php

namespace App\Http\Controllers;

use App\Queries\EmployeeSalaryDataTable;
use Illuminate\Http\Request;
use App\Repositories\PayslipRepository;
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
use App\Models\Setting;
use Barryvdh\DomPDF\Facade as PDF;
use App\Models\Allowance;
use App\Models\Bank;
use App\Http\Requests\PaySalaryRequest;
use App\Models\SalaryPayment;
use Laracasts\Flash\Flash;
use Illuminate\Support\Facades\DB;
use ZipArchive;
use Illuminate\Support\Facades\Http;


class EmployeeSalaryController extends AppBaseController
{

    /**
     * @var PayslipRepository
     */
    private $payslipRepository;
    public function __construct(PayslipRepository $payslipRepo)
    {
        $this->payslipRepository = $payslipRepo;
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            return DataTables::of((new EmployeeSalaryDataTable())->get($request->all()))->make(true);
        }

        $customers = $this->payslipRepository->getCustomers();
        $projects = $this->payslipRepository->getProjects();
        $usersBranches = $this->getUsersBranches();
        $banks = Bank::pluck('name', 'id');
        $departments = $this->payslipRepository->getDepartments();
        $designations = $this->payslipRepository->getDesignation();
        return view('employee_salaries.employee_salaries', compact(['customers', 'projects', 'usersBranches', 'banks', 'departments', 'designations']));
    }

    public function payslip(SalarySheet $salarySheet)
    {
        $salarySheet->load('branch');
        $settings = Setting::pluck('value', 'key')->toArray();
        $comLogoPath = Setting::getLogoLocalPath();

        $salaryMonth = Carbon::parse($salarySheet->salaryGenerate->salary_month);
        
        $allowances = Allowance::with('allowanceTypes')
    ->where('employee_id', $salarySheet->employee_id)
    ->get()
    ->groupBy('allowanceTypes.name')
    ->map(function ($group, $key) use ($salarySheet) {
        $originalAmount = $group->sum('amount');
        
        // Regular hours only (exclude overtime)
        $regularWorkedHours = max(0, $salarySheet->worked_hours - $salarySheet->overtime_hours);
        
        // Calculate allowance based on regular hours only
        $calculatedAmount = round((($originalAmount / $salarySheet->working_hours) * $regularWorkedHours), 2);
        
        return [
            'type' => $key, 
            'total_amount' => $calculatedAmount,
        ];
    });
            
        $banks = Bank::pluck('name', 'id');
        $salarySheet->load(['salaryPayment', 'salaryGenerate', 'employee.designation']);
        //$salarySheet->net_salary -= $salarySheet->total_overtimes;

        $words = $this->amountToWords($salarySheet->net_salary ?? 0);

        return view('employee_salaries.payslip', compact('salarySheet', 'words', 'allowances', 'banks', 'settings','comLogoPath'));
    }
    public function paySalary(PaySalaryRequest $request)
    {
        try {
            // Update or create a salary payment record
            $salaryPayment = SalaryPayment::updateOrCreate(
                // Condition to find the record
                ['salary_sheet_id' => $request->input('salary_sheet_id')],
                // Values to update or create
                [
                    'payment_type' => $request->input('payment_type'),
                    'bank_id' => $request->input('payment_type') === 'bank' ? $request->input('bank_id') : null,
                    'amount' => $request->input('amount'),
                ]
            );

            activity()->performedOn($salaryPayment)->causedBy(getLoggedInUser())
                ->useLog("Salary Paid")->log($salaryPayment->salarySheet->employee->name . ' Salay Paid.');
            Flash::success(__("Salary payment recorded successfully"));
            // Return success response
            return response()->json([
                'success' => true,
                'message' => 'Salary payment recorded successfully!',
                'data' => $salaryPayment,
            ], 200);
        } catch (Exception $e) {
            // Handle any errors
            return response()->json([
                'success' => false,
                'message' => 'Failed to record salary payment.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    public function payslipPDF(SalarySheet $salarySheet, $overtime_status = null, $isFullSalary = null)
    {



        $salaryMonth = Carbon::parse($salarySheet->salaryGenerate->salary_month);
        $allowances = Allowance::with('allowanceTypes')
    ->where('employee_id', $salarySheet->employee_id)
    ->get()
    ->groupBy('allowanceTypes.name')
    ->map(function ($group, $key) use ($salarySheet) {
        $originalAmount = $group->sum('amount');
        
        // Regular hours only (exclude overtime)
        $regularWorkedHours = max(0, $salarySheet->worked_hours - $salarySheet->overtime_hours);
        
        // Calculate allowance based on regular hours only
        $calculatedAmount = round((($originalAmount / $salarySheet->working_hours) * $regularWorkedHours), 2);
        
        return [
            'type' => $key, 
            'total_amount' => $calculatedAmount,
        ];
    });
        // $allowances = Allowance::with('allowanceTypes')
        //     ->where('employee_id', $salarySheet->employee_id)
        //     // ->whereYear('date', $salaryMonth->year)
        //     // ->whereMonth('date', $salaryMonth->month)
        //     ->get()
        //     ->groupBy('allowanceTypes.name') // Group by allowance type name
        //     ->map(function ($group, $key) use ($salarySheet) {
        //         return [
        //             'type' => $key, 
        //             'total_amount' => round((($group->sum('amount')/$salarySheet->working_hours) * $salarySheet->worked_hours), 2),
        //         ];
        //     });

        $settings = Setting::pluck('value', 'key')->toArray();
        $comLogoPath = Setting::getLogoLocalPath();

        $salarySheet->load(['salaryGenerate', 'employee.designation', 'branch']);

        //$salarySheet->net_salary -= $salarySheet->total_overtimes;
        $words = $this->amountToWords($salarySheet->net_salary ?? 0);

        if ($overtime_status) {
            $words = $this->amountToWords($salarySheet->total_overtimes ?? 0);
            $pdf = PDF::loadView('employee_salaries.payslip_v3_overtime', compact('salarySheet', 'words', 'allowances', 'settings', 'comLogoPath'));
        } else {
            $pdf = PDF::loadView('employee_salaries.payslip_v3', compact('salarySheet', 'words', 'allowances', 'settings', 'comLogoPath'));
        }
        $pdf->setPaper('A4', 'portrait');
        return $pdf->download("Payslip_" . $salarySheet->employee->iqama_no . '.pdf');
    }
    public function pdfView(SalarySheet $salarySheet, $overtime_status = null)
    {

        $salarySheet->load('branch');


        $salaryMonth = Carbon::parse($salarySheet->salaryGenerate->salary_month);
        
        // $allowances = Allowance::with('allowanceTypes')
        //     ->where('employee_id', $salarySheet->employee_id)
        //     // ->whereYear('date', $salaryMonth->year)
        //     // ->whereMonth('date', $salaryMonth->month)
        //     ->get()
        //     ->groupBy('allowanceTypes.name') // Group by allowance type name
        //     ->map(function ($group, $key) {
        //         return [
        //             'type' => $key,
        //             'total_amount' => $group->sum('amount'),
        //         ];
        //     });
            
            
     $allowances = Allowance::with('allowanceTypes')
    ->where('employee_id', $salarySheet->employee_id)
    ->get()
    ->groupBy('allowanceTypes.name')
    ->map(function ($group, $key) use ($salarySheet) {
        $originalAmount = $group->sum('amount');
        
        // Regular hours only (exclude overtime)
        $regularWorkedHours = max(0, $salarySheet->worked_hours - $salarySheet->overtime_hours);
        
        // Calculate allowance based on regular hours only
        $calculatedAmount = round((($originalAmount / $salarySheet->working_hours) * $regularWorkedHours), 2);
        
        return [
            'type' => $key, 
            'total_amount' => $calculatedAmount,
        ];
    });



        $settings = Setting::pluck('value', 'key')->toArray();

        $comLogoPath = Setting::getLogoLocalPath();

        $salarySheet->load(['salaryGenerate', 'employee.department', 'employee.designation']);
        //$salarySheet->net_salary -= $salarySheet->total_overtimes;
        $words = $this->amountToWords($salarySheet->net_salary ?? 0);
        
        
        if ($overtime_status) {
            $words = $this->amountToWords($salarySheet->total_overtimes ?? 0);
            $pdf = PDF::loadView('employee_salaries.payslip_v3_overtime', compact('salarySheet', 'words', 'allowances', 'settings', 'comLogoPath'));
        } else {

            $pdf = PDF::loadView('employee_salaries.payslip_v3', compact('salarySheet', 'words', 'allowances', 'settings', 'comLogoPath'));
        }
        $pdf->setPaper('A4', 'portrait');
        return $pdf->stream(__('messages.invoice.invoice_prefix') . 12 . '.pdf');
    }
    public function export(SalarySheet $salarySheet)
    {

        $settings = Setting::pluck('value', 'key')->toArray();
        $com_logo = base64_encode(file_get_contents('https://placehold.co/200x200/jpg'));
        // $com_logo = isset($settings['logo']) && !empty($settings['logo']) ? base64_encode(file_get_contents($settings['logo'])) : ' ';
        $salarySheet->load(['salaryGenerate', 'employee.designation']);

        // return view('employee_salaries.payslip_export', compact(['salarySheet', 'com_logo', 'settings']));

        $pdf = PDF::loadview('employee_salaries.payslip_export', compact(['salarySheet', 'com_logo', 'settings']));
        return $pdf->download("Payslip_{$salarySheet->employee->name}_" . Carbon::now()->format('Y-m-d') . ".pdf");
    }


    public function destroy($id)
    {
        try {
            DB::beginTransaction(); // Start transaction

            $salary = SalaryGenerate::with('salarySheets')->findOrFail($id);
            $salary->salarySheets()->delete();
            $salary->delete();
            DB::commit();


            activity()->performedOn($salary)->causedBy(getLoggedInUser())
                ->useLog("Salary Paid")->log(' Salary Sheet Deleted');
            Flash::success(__("Salary Sheet Deleted"));
            return $this->sendSuccess('Salary record deleted successfully.');
        } catch (Exception $e) {
            DB::rollBack();
            return $this->sendError('Failed to delete salary record.');
        }
    }




    public function bulkExport(Request $request)
    {

        // Get data from the EmployeeSalaryDataTable
        $data = DataTables::of((new EmployeeSalaryDataTable())->get($request->all()))->make(true);
        $data = $data->getData();

        // Check if data exists
        if (empty($data->data)) {
            return redirect()->back()->with('error', 'No files to export');
        }

        // Determine ZIP file name based on the export type
        $zipFileName = (isset($request['type']) && $request['type'] == "overtime")
            ? 'overtime_reports.zip'
            : ((isset($request['type']) && $request['type'] == "full_salary")
                ? 'full_salary_report.zip'
                : 'salary_report.zip');
        $zipPath = storage_path("app/public/$zipFileName");
        $zip = new ZipArchive;

        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            return response()->json(['error' => 'Failed to create ZIP file'], 500);
        }

        $filesAdded = false; // Flag to track if any files were added to the ZIP

        // Loop through each data row and generate PDF
        foreach ($data->data as $row) {
            $salarySheetId = $row->id;
            $salarySheet = SalarySheet::find($salarySheetId);

            if ($salarySheet) {
                if (isset($request['type']) && $request['type'] == "overtime") {
                    if ($salarySheet->total_overtimes > 0) {
                        $overtimeStatus = true;
                        $pdfFileName = "overtime_{$salarySheetId}.pdf";
                        $pdfResponse = $this->payslipPDF($salarySheet, $overtimeStatus);
                        $pdfContent = $pdfResponse->getOriginalContent();
                        $pdfPath = storage_path("app/public/$pdfFileName");
                        file_put_contents($pdfPath, $pdfContent);
                        $zip->addFile($pdfPath, $pdfFileName);
                        $filesAdded = true; // Set flag to true when files are added
                    }
                } elseif (isset($request['type']) && $request['type'] === "full_salary") {
                    // Added for full_salary to include both payslip and overtime
                    // Payslip PDF
                    $overtimeStatus = false;
                    $pdfFileName = "payslip_{$salarySheetId}.pdf";
                    $pdfResponse = $this->payslipPDF($salarySheet, $overtimeStatus);
                    $pdfContent = $pdfResponse->getOriginalContent();
                    $pdfPath = storage_path("app/public/$pdfFileName");
                    file_put_contents($pdfPath, $pdfContent);
                    $zip->addFile($pdfPath, $pdfFileName);
                    $filesAdded = true;

                    // Overtime PDF (if applicable)
                    if ($salarySheet->total_overtimes > 0) {
                        $overtimeStatus = true;
                        $pdfFileName = "overtime_{$salarySheetId}.pdf";
                        $pdfResponse = $this->payslipPDF($salarySheet, $overtimeStatus);
                        $pdfContent = $pdfResponse->getOriginalContent();
                        $pdfPath = storage_path("app/public/$pdfFileName");
                        file_put_contents($pdfPath, $pdfContent);
                        $zip->addFile($pdfPath, $pdfFileName);
                        $filesAdded = true;
                    }
                } else {
                    $overtimeStatus = 0;
                    $pdfFileName = "payslip_{$salarySheetId}.pdf";

                    $pdfResponse = $this->payslipPDF($salarySheet, $overtimeStatus);
                    $pdfContent = $pdfResponse->getOriginalContent();
                    $pdfPath = storage_path("app/public/$pdfFileName");
                    file_put_contents($pdfPath, $pdfContent);
                    $zip->addFile($pdfPath, $pdfFileName);
                    $filesAdded = true; // Set flag to true when files are added
                }
            }
        }

        $zip->close();

        // Check if any files were added to the ZIP
        if ($filesAdded) {
            return response()->download($zipPath)->deleteFileAfterSend(true);
        } else {
            return redirect()->back()->with('error', 'No files to export');
        }
    }
}
