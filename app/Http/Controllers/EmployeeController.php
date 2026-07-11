<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Setting;
use App\Queries\EmployeeDataTable;
use Illuminate\Http\Request;
use App\Repositories\EmployeeRepository;
use Throwable;
use Yajra\DataTables\DataTables;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Exception;
use App\Http\Requests\EmployeeRequest;
use App\Models\Designation;
use App\Http\Requests\UpdateEmployeeRequest;
use Illuminate\Database\QueryException;
use App\Models\EmployeesDoc;
use Illuminate\Support\Facades\Response;
use App\Models\DocumentNextNumber;
use Barryvdh\DomPDF\Facade\Pdf;
use Mpdf\Mpdf;

class EmployeeController extends AppBaseController
{
    /**
     * @var EmployeeRepository
     */
    private $employeeRepository;
    public function __construct(EmployeeRepository $employeeRepo)
    {
        $this->employeeRepository = $employeeRepo;
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
            return DataTables::of((new EmployeeDataTable())->get($request->all()))->make(true);
        }
        $usersBranches = $this->getUsersBranches();

        return view('employees.index', compact('usersBranches'));
    }

    public function create(Request $request)
    {

        $departments = $this->employeeRepository->getDepartments();
        $subDepartments = $this->employeeRepository->getSubDepartment();
        $designations = $this->employeeRepository->getDesignation();
        $company = $this->employeeRepository->getCompanyName();
        $countries = $this->employeeRepository->getCountries();
        $currencies = $this->employeeRepository->getCurrencies();
        $shifts = $this->employeeRepository->getShifts();
        $nextNumber = DocumentNextNumber::getNextNumber('employee');
        $usersBranches = $this->getUsersBranches();
        $settings = Setting::pluck('value', 'key')->toArray();
        $prefix = $settings['employee_code_prefix'] ?? null;

        return view('employees.create', compact(['departments', 'subDepartments', 'designations', 'company', 'countries', 'currencies', 'shifts', 'nextNumber', 'usersBranches', 'prefix']));
    }


    public function store(EmployeeRequest $request)
    {


        $input = $request->all();

        try {
            $employee = $this->employeeRepository->create($input);
            DocumentNextNumber::updateNumber('employee');
            activity()->causedBy(getLoggedInUser())
                ->performedOn($employee)
                ->useLog('Employee created.')
                ->log($employee->title . ' Designtion.');
            return $this->sendResponse($employee, __('messages.employees.saved_employee'));
        } catch (Throwable $e) {
            throw $e;
        }
    }
    public function destroy(Employee $employee)
    {

        $status = $this->employeeRepository->delete_employee($employee);
        if ($status === true) {
            // Log the activity
            activity()->performedOn($employee)
                ->causedBy(getLoggedInUser())
                ->useLog('Employee deleted.')
                ->log($employee->name . ' Employee deleted.');
            return $this->sendSuccess('Employee deleted successfully.');
        } else {
            return $this->sendError($status);
        }
    }

    public function view(Employee $employee)
    {
        $employee->load(['department', 'subDepartment', 'designation', 'countryEmployee', 'documents', 'shifts', 'branch']);

        return view('employees.view', compact('employee'));
    }
    public function cardView(Employee $employee)
    {
        $employee->load(['department', 'subDepartment', 'designation', 'countryEmployee', 'documents', 'shifts', 'branch']);

        // dd($employee->toArray());
        $settings = Setting::pluck('value', 'key')->toArray();





        if ($employee->image) {
            $employeeImagePath = public_path('uploads/public/employee_images/' . $employee->image);

            if (file_exists($employeeImagePath)) {
                $employeeImage = file_get_contents($employeeImagePath);
            } else {
                // If employee image file doesn't exist, use placeholder
                $employeeImage = file_get_contents(public_path('img/user.png'));
            }
        } else {
            // If employee image is null, use placeholder
            $employeeImage = file_get_contents(public_path('img/user.png'));
        }

        // Convert to base64
        $employeeImage = base64_encode($employeeImage);
        $employeeImage = 'data:image/png;base64,' . $employeeImage;


        $compnayName = $settings['company'] ?? '';

        // $com_image = public_path('img/company/company_logo.png');
        // $imageheader = file_get_contents($com_image);
        // $headerImage = base64_encode($imageheader);
        // $companyImage = 'data:image/jpg;base64,' . $headerImage;



        $imagePath = Setting::getLogoLocalPath();
        $imageData = $imagePath && file_exists($imagePath) ? file_get_contents($imagePath) : '';
        $base64 = base64_encode($imageData);
        $companyImage = 'data:image/png;base64,' . $base64;


        $layout = public_path('img/company/card_layout.jpg');
        $layout = file_get_contents($layout);
        $layout = base64_encode($layout);
        $layout = 'data:image/jpg;base64,' . $layout;

        //  dd($companyImage,$employeeImage,$compnayName,$layout);

        $pdf = Pdf::loadView('employees.card_pdf', compact('employee', 'employeeImage', 'companyImage', 'compnayName', 'layout'));
        return $pdf->download('id_card_' . $employee->name . '.pdf');
    }

    public function export($status, $branch = null)
    {
        // Get employees based on the status
        $employees = $this->employeeRepository->getEmployeesByStatus($status, $branch);
        // Prepare CSV data
        $csvData = [];
        $csvData[] = ['SL', 'Branch', 'Iqama No', 'Name', 'Department', 'Designation', 'Employment Type', 'Absent Allowance Deduction', 'Status', 'Created At'];

        foreach ($employees as $index => $employee) {
            $csvData[] = [
                $index + 1, // Serial number
                $employee->branch?->name ?? '',
                $employee->iqama_no,
                $employee->name,
                $employee->department->name ?? 'N/A',
                $employee->designation->name ?? 'N/A',
                $employee->employment_type,
                $employee->absent_allowance_deduction ?? 0,
                $employee->status ? 'Active' : 'Inactive', // Status conversion
                \Carbon\Carbon::parse($employee->created_at)->format('d-m-Y') // Created At formatted
            ];
        }

        // Set the headers for the response
        $filename = 'employees_export_' . now()->format('Y-m-d_H-i') . '.csv';
        $handle = fopen('php://output', 'w');

        // Send the headers to the browser
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        // Write each row of the CSV to the output
        foreach ($csvData as $row) {
            fputcsv($handle, $row);
        }

        fclose($handle);
        exit; // Terminate the script
    }

    public function edit(Employee $employee)
    {
        $employee->load('documents');
        $departments = $this->employeeRepository->getDepartments();
        $subDepartments = $this->employeeRepository->getSubDepartment();
        $designations = $this->employeeRepository->getDesignation();
        $company = $this->employeeRepository->getCompanyName();
        $countries = $this->employeeRepository->getCountries();
        $currencies = $this->employeeRepository->getCurrencies();
        $shifts = $this->employeeRepository->getShifts();
        $usersBranches = $this->getUsersBranches();
        return view('employees.edit', compact(['departments', 'subDepartments', 'designations', 'company', 'employee', 'countries', 'currencies', 'shifts', 'usersBranches']));
    }
    public function update(Employee $employee, UpdateEmployeeRequest $updateEmployeeRequest)
    {

        $input = $updateEmployeeRequest->all();
        $employee = $this->employeeRepository->update_employee($employee, $input);
        activity()->performedOn($employee)->causedBy(getLoggedInUser())
            ->useLog('Designation Updated')->log($employee->name . ' Employee updated.');
        return $this->sendSuccess(__('messages.employees.saved_employee'));
    }

    function file_delete($id)
    {

        $employee = $this->employeeRepository->delete_file($id);
        activity()->performedOn($employee)->causedBy(getLoggedInUser())
            ->useLog('File Deleted')->log(' Employee File deleted.');
        return $this->sendSuccess(__('messages.employees.delete_file'));
    }
}
