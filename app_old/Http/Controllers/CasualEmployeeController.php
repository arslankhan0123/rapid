<?php

namespace App\Http\Controllers;

use Throwable;
use Carbon\Carbon;
use App\Models\Setting;
use Illuminate\Http\Request;
use App\Models\CasualEmployee;
use Barryvdh\DomPDF\Facade\Pdf;
use Yajra\DataTables\DataTables;
use App\Models\DocumentNextNumber;
use App\Queries\CasualEmployeeDataTable;
use App\Http\Controllers\AppBaseController;
use App\Http\Requests\CasualEmployeeRequest;
use App\Repositories\CasualEmployeeRepository;
use App\Http\Requests\UpdateCasualEmployeeRequest;

class CasualEmployeeController extends AppBaseController
{
    private $casualEmployeeRepository;

    public function __construct(CasualEmployeeRepository $casulemployeeRepo)
    {
        $this->casualEmployeeRepository = $casulemployeeRepo;
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            return DataTables::of((new CasualEmployeeDataTable())->get($request->all()))->make(true);
        }

        $usersBranches = $this->getUsersBranches();
        return view('casual-employees.index', compact('usersBranches'));
    }

    public function create(Request $request)
    {
        $company = $this->casualEmployeeRepository->getCompanyName();
        $countries = $this->casualEmployeeRepository->getCountries();
        $shifts = $this->casualEmployeeRepository->getShifts();
        $nextNumber = DocumentNextNumber::getNextNumber('employee');
        $usersBranches = $this->getUsersBranches();

        return view('casual-employees.create', compact([
            'company',
            'countries',
            'shifts',
            'nextNumber',
            'usersBranches'
        ]));
    }

    public function store(CasualEmployeeRequest $request)
    {
        $input = $request->all();

        try {
            $employee = $this->casualEmployeeRepository->create($input);
            DocumentNextNumber::updateNumber('employee');

            activity()->causedBy(getLoggedInUser())
                ->performedOn($employee)
                ->useLog('Casual Employee created.');

            return $this->sendResponse($employee, __('messages.employees.saved_employee'), 200);
        } catch (Throwable $e) {
            // Return error with HTTP 500
            return $this->sendError($e->getMessage(), 500);
        }
    }

    public function destroy(CasualEmployee $employee)
    {
        $status = $this->casualEmployeeRepository->delete_employee($employee);

        if ($status === true) {
            activity()->performedOn($employee)
                ->causedBy(getLoggedInUser())
                ->useLog('Employee deleted.')
                ->log($employee->name . ' Employee deleted.');

            return $this->sendSuccess('Employee deleted successfully.', 200);
        } else {
            // Make sure the HTTP code is int
            return $this->sendError($status, 400);
        }
    }

    public function view(CasualEmployee $employee)
    {
        $employee->load(['countryEmployee', 'documents', 'shifts', 'branch']);
        return view('casual-employees.view', compact('employee'));
    }

    public function cardView(CasualEmployee $employee)
    {
        $employee->load(['countryEmployee', 'documents', 'shifts', 'branch']);
        $settings = Setting::pluck('value', 'key')->toArray();

        $employeeImage = public_path('img/user.png');
        if ($employee->image && file_exists(public_path('uploads/public/employee_images/' . $employee->image))) {
            $employeeImage = public_path('uploads/public/employee_images/' . $employee->image);
        }
        $employeeImage = 'data:image/png;base64,' . base64_encode(file_get_contents($employeeImage));

        $companyImage = 'data:image/jpg;base64,' . base64_encode(file_get_contents(public_path('img/company/company_logo.png')));
        $layout = 'data:image/jpg;base64,' . base64_encode(file_get_contents(public_path('img/company/card_layout.png')));
        $companyName = $settings['company'] ?? '';

        $pdf = Pdf::loadView('casual-employees.card_pdf', compact('employee', 'employeeImage', 'companyImage', 'companyName', 'layout'));
        return $pdf->download('id_card_' . $employee->name . '.pdf');
    }

    public function export($branch = null)
    {
        $employees = $this->casualEmployeeRepository->getEmployeesByStatus($branch);
        $csvData = [];
        $csvData[] = ['SL', 'Branch', 'Iqama No', 'Name', 'Created At'];

        foreach ($employees as $index => $employee) {
            $csvData[] = [
                $index + 1,
                $employee->branch?->name ?? '',
                $employee->iqama_no,
                $employee->name,
                \Carbon\Carbon::parse($employee->created_at)->format('d-m-Y')
            ];
        }

        $filename = 'employees_export_' . now()->format('Y-m-d_H-i') . '.csv';

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        $handle = fopen('php://output', 'w');
        foreach ($csvData as $row) {
            fputcsv($handle, $row);
        }
        fclose($handle);
        exit;
    }


    public function edit(CasualEmployee $employee)
    {
        $employee->load('documents');
        // $departments = $this->casualEmployeeRepository->getDepartments();
        // $subDepartments = $this->casualEmployeeRepository->getSubDepartment();
        // $designations = $this->casualEmployeeRepository->getDesignation();
        $company = $this->casualEmployeeRepository->getCompanyName();
        $countries = $this->casualEmployeeRepository->getCountries();
        $shifts = $this->casualEmployeeRepository->getShifts();
        $usersBranches = $this->getUsersBranches();

        return view('casual-employees.edit', compact([
            'company',
            'employee',
            'countries',
            'shifts',
            'usersBranches'
        ]));
    }

    public function update(CasualEmployee $employee, UpdateCasualEmployeeRequest $updateEmployeeRequest)
    {
        $input = $updateEmployeeRequest->all();
        $employee = $this->casualEmployeeRepository->update_employee($employee, $input);

        activity()->performedOn($employee)
            ->causedBy(getLoggedInUser())
            ->useLog('Designation Updated')
            ->log($employee->name . ' Employee updated.');

        return $this->sendSuccess(__('messages.casual_employees.saved_employee'), 200);
    }

    public function file_delete($id)
    {
        $employee = $this->casualEmployeeRepository->delete_file($id);

        activity()->performedOn($employee)
            ->causedBy(getLoggedInUser())
            ->useLog('File Deleted')
            ->log(' Employee File deleted.');

        return $this->sendSuccess(__('messages.employees.delete_file'), 200);
    }
}