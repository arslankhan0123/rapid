<?php

namespace App\Http\Controllers;

use App\Queries\AttendaceDataTable;
use Illuminate\Http\Request;
use App\Repositories\AttendanceRepository;
use Yajra\DataTables\DataTables;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Exception;
use App\Http\Requests\AttendanceRequest;
use App\Models\SubDepartment;
use App\Http\Requests\UpdateAwardRequest;
use Illuminate\Database\QueryException;
use App\Models\Award;
use Throwable;
use App\Models\Attendance;
use App\Models\Employee;
use League\Csv\Reader;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Requests\UpdateAttendanceRequest;

class AttendanceController extends AppBaseController
{
    /**
     * @var AttendanceRepository
     */
    private $attendanceRepository;
    public function __construct(AttendanceRepository $attendanceRepo)
    {
        $this->attendanceRepository = $attendanceRepo;
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
            return DataTables::of((new AttendaceDataTable())->get($request->all()))->make(true);
        }
        $employees = $this->attendanceRepository->getAllEmployees();
        $customers=$this->attendanceRepository->getCustomers();
        $projects=$this->attendanceRepository->getProjects();
        return view('attendances.index', compact(['employees','customers','projects']));
    }


    public function daily_store(AttendanceRequest $request)
    {

        $input = $request->all();
        try {
            $award = $this->attendanceRepository->create($input);
            activity()->causedBy(getLoggedInUser())
                ->performedOn($award)
                ->useLog('Attendance created.')
                ->log(' Attendance Created.');
            return $this->sendResponse($award, __('messages.attendances.saved'));
        } catch (Throwable $e) {
            throw $e;
        }
    }
    public function monthly_store(AttendanceRequest $request)
    {

        $input = $request->all();
        $monthly = $this->attendanceRepository->create_monthly($input['employee_id'], $input['month'], $input['time_in'], $input['time_out']);
        $employee = Employee::findOrFail($input['employee_id']);
        activity()->causedBy(getLoggedInUser())
            ->performedOn($employee)
            ->useLog('Attendance created.')
            ->log($employee->name . ' Attendance Created.');
        return $this->sendResponse($employee->name, __('messages.attendances.saved'));
    }
    public function import_attendances(Request $request)
    {
        // Validate the uploaded file
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt',
        ]);

        $file = $request->file('csv_file');
        $data = [];
        $errors = [];

        $columnIndex = 0;

        if (($handle = fopen($file->getRealPath(), 'r')) !== false) {
            // Get the header row
            $header = fgetcsv($handle, 1000, ',');

            // Loop through the file and process each row
            while (($row = fgetcsv($handle, 1000, ',')) !== false) {
                $rowData = array_combine($header, $row);

                // Validate the row data
                if (empty($rowData['Date']) || empty($rowData['CustomerID']) || empty($rowData['ProjectID']) || empty($rowData['EmployeeID']|| empty($rowData['Hours']))) {
                    $errors[] = $columnIndex + 2;
                    continue;
                }

                // Convert date and time to database format
                try {
                    $rowData['Date'] = Carbon::createFromFormat('d/m/Y', $rowData['Date'])->format('Y-m-d');
                    // $rowData['time_in'] = Carbon::createFromFormat('h.i A', $rowData['time_in'])->format('H:i:s');
                    // $rowData['time_out'] = Carbon::createFromFormat('h.i A', $rowData['time_out'])->format('H:i:s');
                } catch (Exception $e) {
                    $errors[] = "Error in row: " . json_encode($rowData) . ". Invalid date or time format.";
                    continue;
                }

                // Push the validated and converted data into the array
                $data[] = $rowData;

                $columnIndex++;
            }

            fclose($handle);

        }

        $insertd_rows = 0;


        foreach ($data as $item) {

             $insertArray=[
                'employee_id'=>$item['EmployeeID'],
                'date' => $item['Date'],
                'customer_id' => $item['CustomerID'],
                'project_id' => $item['ProjectID'],
                'hours' => $item['Hours'],
             ];
            $attendance = $this->attendanceRepository->create($insertArray);
            activity()->causedBy(getLoggedInUser())
                ->performedOn($attendance)
                ->useLog('Attendance Imported.')
                ->log(' Attendance Imported.');
            $insertd_rows++;
        }

        return response()->json(
            [
                'success' => 'Attendances imported',
                'inserted' => $insertd_rows,
                'errors'=>$errors
            ]
        );
    }

    public function destroy(Attendance $attendance)
    {

        try {
            $attendance->delete();
            activity()->performedOn($attendance)->causedBy(getLoggedInUser())
                ->useLog('Attendacnes deleted.')->log(' Attendance deleted.');
            return $this->sendSuccess(__('messages.attendances.delete'));
        } catch (QueryException $e) {
            return $this->sendError('Failed To delete!! Already in use.');
        }
    }

    public function edit(Attendance $attendance)
    {
        return $attendance;
    }
    public function view(Attendance $attendance)
    {
        $attendance->load(['iqmaEmployee', 'iqmaEmployee.department', 'iqmaEmployee.subDepartment', 'iqmaEmployee.designation']);
        return $attendance;
    }
    public function update(Attendance $attendance, UpdateAttendanceRequest $updateAttendanceRequest)
    {

        $input = $updateAttendanceRequest->all();
        $udpateAward = $this->attendanceRepository->update($input, $updateAttendanceRequest->id);
        activity()->performedOn($udpateAward)->causedBy(getLoggedInUser())
            ->useLog('Attendance Updated')->log($udpateAward->name . ' Attendance updated.');
        return $this->sendSuccess(__('messages.attendances.saved'));
    }
}
