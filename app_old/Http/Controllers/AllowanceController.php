<?php

namespace App\Http\Controllers;

use App\Queries\AllowanceDataTable;
use Illuminate\Http\Request;
use App\Repositories\AllowanceRepository;
use Yajra\DataTables\DataTables;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Exception;
use App\Models\Employee;
use App\Http\Requests\AllowanceRequest;
use App\Models\Allowance;
use App\Http\Requests\UpdateAllowanceRequest;
use Illuminate\Database\QueryException;
use Laracasts\Flash\Flash;
use Throwable;

class AllowanceController extends AppBaseController
{
    /**
     * @var AllowanceRepository
     */
    private $allowanceRepository;
    public function __construct(AllowanceRepository $allowanceRepo)
    {
        $this->allowanceRepository = $allowanceRepo;
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
            return DataTables::of((new AllowanceDataTable())->get($request->only(['group'])))->make(true);
        }
        $usersBranches = $this->getUsersBranches();
        return view('allowances.index',compact('usersBranches'));
    }


    public function create()
    {
        $employees=$this->allowanceRepository->getAllEmployees();
        $types=$this->allowanceRepository->getAllowanceTypes();
        $usersBranches = $this->getUsersBranches();
        $payment_types=Allowance::PAYMENT_TYPES;
        return view('allowances.create',compact(['employees', 'payment_types','types', 'usersBranches']));
    }
    
    public function get_employee_working_days($employee_id, $date)
    {
        
        $year  = date("Y", strtotime($date)); // 2025
        $month = date("m", strtotime($date)); // 05
        
        $lastDayOfMonth = date('Y-m-t', strtotime($month . '-01'));
        
         $employee = Employee::with([
            'attendance' => function ($query) use ($year, $month) {
                $query->whereYear('date', $year)
                    ->whereMonth('date', $month);
            }
        ])->where('id', $employee_id)->first();
        
        $workingHoursPerDay = 8;
        $weekendDays = ['friday']; // Specify weekend days
        
        $total_overtimes = 0;
        $absense_hours = 0;
        $overtimeHours = 0;

        // Calculate total working hours in the month (excluding weekend days)
        $workingHours = 0;
        $workingDaysCount = 0;
        $IdealworkingDaysCount = 0;

        // Loop through attendance records and calculate working hours
        foreach ($employee->attendance as $attendance) {
            $join_date = date("Y-m-d", strtotime($employee->join_date));
            
             $attendanceDate = $attendance['date'];
            $dayOfWeek = date('l', strtotime($attendanceDate)); // Get day of the week (e.g., 'Monday', 'Friday')
            
            if($join_date > $attendance['date']) {
                if (!in_array(strtolower($dayOfWeek), $weekendDays)) {
                    $IdealworkingDaysCount +=1 ;
                }
                continue;
            }
           

            // Check if it's not a weekend day (in the $weekendDays array)
            if (!in_array(strtolower($dayOfWeek), $weekendDays)) {
                //$workingHours += $attendance['hours'];
                $workingDaysCount++;
                $IdealworkingDaysCount +=1;
                
                if( $attendance['hours'] < $workingHoursPerDay) {
                    $absense_hours += ($workingHoursPerDay - $attendance['hours']);
                }
                
                if( $attendance['hours'] > $workingHoursPerDay) {
                    $overtimeHours += ($attendance['hours'] - $workingHoursPerDay);
                }
            
            } else {
                $overtimeHours += $attendance['hours'];
            }
            
            
            
            
            
        }
        
        //dd($employee);

        // Calculate basic salary based on working days and working hours per day
        $totalWorkingHoursThisMonth = ($workingDaysCount * $workingHoursPerDay); // 8 hours per working day
        $workingHours = ($totalWorkingHoursThisMonth + $overtimeHours) - $absense_hours;
        $IdealWorkingHoursThisMonth = ($IdealworkingDaysCount * $workingHoursPerDay); // 8 hours per working day
        
        $resultdata['total_worked'] = $workingHours;
        $resultdata['ideal_worked'] = $totalWorkingHoursThisMonth;
        return response()->json($resultdata);
         
        
        
    }

    public function store(AllowanceRequest $request)
    {
        $input = $request->all();
        try {
            $designation = $this->allowanceRepository->create($input);
            activity()->causedBy(getLoggedInUser())
                ->performedOn($designation)
                ->useLog('Allowance created.')
                ->log($designation->name . " Allowance created");
            Flash::success(__('messages.allowances.saved'));
            return $this->sendResponse($designation, __('messages.allowances.saved'));
        } catch (Throwable $e) {
            throw $e;
        }
    }
    public function destroy(Allowance $allowance)
    {
        try {
            $allowance->delete();
            activity()->performedOn($allowance)->causedBy(getLoggedInUser())
                ->useLog('Allowance deleted.')->log($allowance->name . ' deleted.');
            return $this->sendSuccess(__('messages.allowance.delete'));
        } catch (QueryException $e) {
            return $this->sendError('Failed To delete!! Already in use.');
        }
    }

    public function edit(Allowance $allowance)
    {
        $allowance->load(['employee.branch']);
        $employees = $this->allowanceRepository->getAllEmployees();
        $types = $this->allowanceRepository->getAllowanceTypes();
        $usersBranches = $this->getUsersBranches();
        $payment_types = Allowance::PAYMENT_TYPES;
        return view('allowances.edit', compact(['allowance', 'employees', 'types', 'usersBranches', 'payment_types']));
    }
    public function update(Allowance $allowance, UpdateAllowanceRequest $updateAllowanceRequest)
    {
        $input = $updateAllowanceRequest->all();
        $allowance = $this->allowanceRepository->update($input, $updateAllowanceRequest->id);
        activity()->performedOn($allowance)->causedBy(getLoggedInUser())
            ->useLog('Allowance Updated')->log($allowance->name . ' Allowance updated.');
        Flash::success(__('messages.allowances.saved'));
        return $this->sendSuccess(__('messages.allowances.saved'));
    }
    public function view(Allowance $allowance)
    {
        $allowance->load(['employee','employee.designation', 'allowanceTypes','employee.branch']);
        $payment_types = Allowance::PAYMENT_TYPES;
        return view('allowances.view', compact(['allowance', 'payment_types']));
    }
}
