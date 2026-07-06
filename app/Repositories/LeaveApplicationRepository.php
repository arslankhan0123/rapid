<?php

namespace App\Repositories;

use App\Models\Employee;
use App\Models\Setting;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use App\Http\Requests\LeaveApplicationRequest;
use App\Http\Requests\UpdateLeaveApplicationRequest;
use App\Models\LeaveApplication;
use App\Models\Leave;
use App\Models\Holiday;
use Carbon\Carbon;

/**
 * Class ProductRepository
 *
 * @version October 12, 2021, 10:50 am UTC
 */
class LeaveApplicationRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'employee_id',
        'leave_id',
        'from_date',
        'end_date',
        'total_days',
        'hard_copy',
        'description',
        'branch_id',
        'paid_leave_days',
        'paid_leave_amount',
        'ticket_amount',
        'claim_amount'
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
        return LeaveApplication::class;
    }


    /**
     * @param  int  $id
     * @return Builder|Builder[]|Collection|Model|null
     */

    public function getEmployees()
    {
        return Employee::pluck('name', 'id')->toArray();
    }
    public function getleaves()
    {
        return Leave::pluck('name', 'id')->toArray();
    }

    public function saveLeaveApplication(LeaveApplicationRequest $request)
    {
        // Handle file upload for hard copy if applicable
        if ($request->hasFile('hard_copy')) {
            $hardCopy = $request->file('hard_copy');
            $hardCopyName = time() . '_' . $hardCopy->getClientOriginalName(); // Rename file with timestamp prefix
            $hardCopy->storeAs('public/leave_applications', $hardCopyName); // Store in storage/app/public/hard_copies directory
        } else {
            $hardCopyName = null;
        }

        // Create new LeaveApplication instance
        $leaveApplication = new LeaveApplication();
        $leaveApplication->employee_id = $request->input('employee_id');
        $leaveApplication->leave_id = $request->input('leave_id');
        $leaveApplication->from_date = $request->input('from_date');
        $leaveApplication->end_date = $request->input('end_date');
        $leaveApplication->total_days = $request->input('total_days');
        $leaveApplication->description = $request->input('description');
        $leaveApplication->branch_id = $request->input('branch_id');


        // Conditional assignment of attributes
        if ($request->has('employee_id')) {
            $leaveApplication->employee_id = $request->input('employee_id');
        }
        if ($request->has('leave_id')) {
            $leaveApplication->leave_id = $request->input('leave_id');
        }
        if ($request->has('from_date')) {
            $leaveApplication->from_date = $request->input('from_date');
        }
        if ($request->has('end_date')) {
            $leaveApplication->end_date = $request->input('end_date');
        }
        if ($request->has('total_days')) {
            $leaveApplication->total_days = $request->input('total_days');
        }
        if ($request->has('description')) {
            $leaveApplication->description = $request->input('description');
        }

        if ($request->has('paid_leave_days')) {
            $leaveApplication->paid_leave_days = $request->input('paid_leave_days');
        }
        if ($request->has('paid_leave_amount')) {
            $leaveApplication->paid_leave_amount = $request->input('paid_leave_amount');
        }
        if ($request->has('ticket_amount')) {
            $leaveApplication->ticket_amount = $request->input('ticket_amount');
        }
        if ($request->has('claim_amount')) {
            $leaveApplication->claim_amount = $request->input('claim_amount');
        }

        $leaveApplication->hard_copy = $hardCopyName; // Save hard copy file name if uploaded

        // Save the leave application
        $leaveApplication->save();

        // Return the leave application or redirect as needed
        return $leaveApplication;
    }


    // public function updateLeaveApplication(UpdateLeaveApplicationRequest $request, $id)
    // {
    //     // Retrieve the existing LeaveApplication instance
    //     $leaveApplication = LeaveApplication::findOrFail($id);

    //     // Handle file upload for hard copy if applicable
    //     if ($request->hasFile('hard_copy')) {
    //         $hardCopy = $request->file('hard_copy');
    //         $hardCopyName = time() . '_' . $hardCopy->getClientOriginalName(); // Rename file with timestamp prefix
    //         $hardCopy->storeAs('public/leave_applications', $hardCopyName); // Store in storage/app/public/leave_applications directory
    //         $leaveApplication->hard_copy = $hardCopyName; // Update hard copy file name
    //     }

    //     // Update the leave application attributes
    //     $leaveApplication->employee_id = $request->input('employee_id');
    //     $leaveApplication->leave_id = $request->input('leave_id');
    //     $leaveApplication->from_date = $request->input('from_date');
    //     $leaveApplication->end_date = $request->input('end_date');
    //     $leaveApplication->total_days = $request->input('total_days');
    //     $leaveApplication->description = $request->input('description');

    //     // Save the updated leave application
    //     $leaveApplication->save();

    //     // Return the updated leave application model
    //     return $leaveApplication;
    // }

    public function updateLeaveApplication(UpdateLeaveApplicationRequest $request, $id)
    {
        // Retrieve the existing LeaveApplication instance
        $leaveApplication = LeaveApplication::findOrFail($id);

        // Handle file upload for hard copy if applicable
        if ($request->hasFile('hard_copy')) {
            // Delete the old file if it exists
            if ($leaveApplication->hard_copy) {
                $this->deleteOldFile($leaveApplication->hard_copy);
            }

            // Handle the new file upload
            $hardCopy = $request->file('hard_copy');
            $hardCopyName = time() . '_' . $hardCopy->getClientOriginalName(); // Rename file with timestamp prefix
            $hardCopy->storeAs('public/leave_applications', $hardCopyName); // Store in storage/app/public/leave_applications directory
            $leaveApplication->hard_copy = $hardCopyName; // Update hard copy file name
        }

        // Update the leave application attributes
        $leaveApplication->employee_id = $request->input('employee_id');
        $leaveApplication->leave_id = $request->input('leave_id');
        $leaveApplication->from_date = $request->input('from_date');
        $leaveApplication->end_date = $request->input('end_date');
        $leaveApplication->total_days = $request->input('total_days');
        $leaveApplication->description = $request->input('description');
        $leaveApplication->branch_id = $request->input('branch_id');


        if ($request->has('paid_leave_days')) {
            $leaveApplication->paid_leave_days = $request->input('paid_leave_days');
        }
        if ($request->has('paid_leave_amount')) {
            $leaveApplication->paid_leave_amount = $request->input('paid_leave_amount');
        }
        if ($request->has('ticket_amount')) {
            $leaveApplication->ticket_amount = $request->input('ticket_amount');
        }
        if ($request->has('claim_amount')) {
            $leaveApplication->claim_amount = $request->input('claim_amount');
        }
        $leaveApplication->save();
        return $leaveApplication;
    }

    public function updateLeaveApplicationAnnual(UpdateLeaveApplicationRequest $request, $id)
    {

        $leaveApplication = LeaveApplication::findOrFail($id);


        if ($request->has('paid_leave_days')) {
            $leaveApplication->paid_leave_days = $request->input('paid_leave_days');
        }
        if ($request->has('paid_leave_amount')) {
            $leaveApplication->paid_leave_amount = $request->input('paid_leave_amount');
        }
        if ($request->has('ticket_amount')) {
            $leaveApplication->ticket_amount = $request->input('ticket_amount');
        }
        if ($request->has('claim_amount')) {
            $leaveApplication->claim_amount = $request->input('claim_amount');
        }

        $leaveApplication->save();

        return $leaveApplication;
    }

    protected function deleteOldFile($filename)
    {
        $filePath = public_path('/uploads/public/leave_applications/' . $filename);
        if (file_exists($filePath)) {
            unlink($filePath); // Delete the old file
        }
    }

    public function getAllEmployees()
    {
        return Employee::with(['department', 'subDepartment', 'designation'])->get();
    }
    public function getTotalLeaveDays()
    {
        $holidays = Holiday::where('from_date', '>=', now()->startOfYear())
            ->where('end_date', '<=', now()->endOfYear())
            ->get();

        $totalLeaveDays = $holidays->sum(function ($holiday) {
            return Carbon::parse($holiday->from_date)
                ->diffInDays(Carbon::parse($holiday->end_date)) + 1;
        });

        return $totalLeaveDays;
    }
}
