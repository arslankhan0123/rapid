<?php

namespace App\Http\Controllers;

use App\Queries\AppointmentDataTable;
use Illuminate\Http\Request;
use App\Repositories\AppointmentRepository;
use Yajra\DataTables\DataTables;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Exception;
use App\Http\Requests\AppointmentRequest;
use App\Models\Appointment;
use App\Http\Requests\UpdateAppointmentRequest;
use Illuminate\Database\QueryException;
use Laracasts\Flash\Flash;
use App\Models\Employee;
use Throwable;

class AppointmentController extends AppBaseController
{
    /**
     * @var AppointmentRepository
     */
    private $appointmentRepository;
    public function __construct(AppointmentRepository $appointmentRepo)
    {
        $this->appointmentRepository = $appointmentRepo;
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
            return DataTables::of((new AppointmentDataTable())->get($request->only(['group'])))->make(true);
        }
        return view('appointments.index');
    }

    public function create()
    {
        
        $employee_list = Employee::pluck('name', 'name');
        return view('appointments.create', compact(['employee_list']));
    }

    public function store(AppointmentRequest $request)
    {
        $input = $request->all();
        
        $input['mobile'] = '+' . $input['prefix_code'] . $input['mobile'];

        try {
            $appointment = $this->appointmentRepository->create($input);
            activity()->causedBy(getLoggedInUser())
                ->performedOn($appointment)
                ->useLog('Appointment created.')
                ->log($appointment->name);
            Flash::success(__('messages.appointments.saved'));
            return $this->sendResponse($appointment->name, __('messages.appointments.saved'));
        } catch (Throwable $e) {
            throw $e;
        }
    }
    public function destroy($appointment_id = 0)
    {
        try {
            $appointment = Appointment::find($appointment_id); 
            $appointment->delete(); 
            activity()->performedOn($appointment)->causedBy(getLoggedInUser())
                ->useLog('Appointment deleted.')->log($appointment->name . ' deleted.');
            return $this->sendSuccess(__('messages.appointments.delete'));
        } catch (QueryException $e) {
            return $this->sendError('Failed To delete!! Already in use.');
        }
    }

    public function edit($appointment_id = 0)
    {
        
        $appointment = Appointment::find($appointment_id); 
        $employee_list = Employee::pluck('name', 'name');
        return view('appointments.edit', compact(['appointment', 'employee_list']));
    }
    public function update(Appointment $appointment, UpdateAppointmentRequest $updateAppointmentRequest)
    {
        $input = $updateAppointmentRequest->all();
        $input['mobile'] = '+' . $input['prefix_code'] . $input['mobile'];
        $updateAppointment = $this->appointmentRepository->update($input, $updateAppointmentRequest->id);
        activity()->performedOn($updateAppointment)->causedBy(getLoggedInUser())
            ->useLog('Appointment Updated')->log($updateAppointment->name . 'Appointment updated.');
        Flash::success(__('messages.appointments.saved'));
        return $this->sendSuccess(__('messages.appointments.saved'));
    }
    public function view($appointment_id = 0)
    {
        $appointment = Appointment::find($appointment_id); 
        return view('appointments.view', compact(['appointment']));
    }
}
