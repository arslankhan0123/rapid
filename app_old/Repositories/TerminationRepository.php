<?php

namespace App\Repositories;

use App\Models\AssetCategory;
use App\Models\ProductUnit;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use App\Models\SubDepartment;
use App\Models\Termination;
use App\Models\Employee;
use Carbon\Carbon;

/**
 * Class CustomerRepository
 *
 * @version April 3, 2020, 6:37 am UTC
 */
class TerminationRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'name',
        'description',
        'employee_id',
        'date'
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
        return Termination::class;
    }

    // public function create($input)
    // {
    //     return Termination::create(Arr::only($input, ['name', 'description', 'employee_id', 'status', 'date']));
    // }

    // public function create($input)
    // {
    //     // Create termination record
    //     $termination = Termination::create(
    //         Arr::only($input, ['name', 'description', 'employee_id', 'date'])
    //     );

    //     // Find employee
    //     $employee = Employee::find($termination->employee_id);

    //     if ($employee) {
    //         $terminationDate = Carbon::parse($termination->date);

    //         if ($terminationDate->isToday() || $terminationDate->isPast()) {
    //             $employee->status = 0; // inactive
    //             $employee->save();
    //         }
    //     }

    //     return $termination;
    // }

    // Update your create method to use consistent logic
    public function create($input)
    {
        // Create termination record
        $termination = Termination::create(
            Arr::only($input, ['name', 'description', 'employee_id', 'date', 'status'])
        );

        // Handle employee status
        $this->handleEmployeeStatus($termination);

        return $termination;
    }

    private function handleEmployeeStatus($termination)
    {
        $employee = Employee::find($termination->employee_id);

        if (!$employee) {
            return;
        }

        // If termination is active
        if ((int)$termination->status === 1) {
            $terminationDate = Carbon::parse($termination->date)->startOfDay();
            $today = Carbon::today()->startOfDay();

            // If termination date is today or in the past
            if ($terminationDate->lte($today)) {
                $employee->status = false; // inactive
            } else {
                $employee->status = true; // active (future termination)
            }
        } else {
            // Termination is inactive, so employee should be active
            $employee->status = true;
        }

        $employee->save();
    }

    // public function create($input)
    // {
    //     // Create termination record
    //     $termination = Termination::create(
    //         Arr::only($input, ['name', 'description', 'employee_id', 'date', 'status'])
    //     );

    //     // Find employee
    //     $employee = Employee::find($termination->employee_id);

    //     if ($employee && $termination->status == 1) { // check termination is active
    //         $terminationDate = Carbon::parse($termination->date);

    //         if ($terminationDate->isToday() || $terminationDate->isPast()) {
    //             $employee->status = 0; // inactive
    //             $employee->save();
    //         }
    //     }

    //     return $termination;
    // }

    // In your TerminationRepository.php
    public function update($input, $terminationId)
    {
        $termination = Termination::findOrFail($terminationId);

        // Store old employee ID and status for comparison
        $oldEmployeeId = $termination->employee_id;
        $oldStatus = $termination->status;

        // Update termination record
        $termination->update(
            Arr::only($input, ['name', 'description', 'employee_id', 'date', 'status'])
        );

        // Handle employee status changes
        $this->handleEmployeeStatusOnUpdate($termination, $oldEmployeeId, $oldStatus);

        return $termination;
    }

    private function handleEmployeeStatusOnUpdate($termination, $oldEmployeeId, $oldStatus)
    {
        // If employee changed, revert old employee status to active
        if ($oldEmployeeId && $oldEmployeeId != $termination->employee_id) {
            $oldEmployee = Employee::find($oldEmployeeId);
            if ($oldEmployee && (int)$oldStatus === 1) {
                $oldEmployee->status = true; // Set back to active
                $oldEmployee->save();
            }
        }

        // Now handle the new/current employee
        $employee = Employee::find($termination->employee_id);

        if (!$employee) {
            return;
        }

        // If termination is active
        if ((int)$termination->status === 1) {
            $terminationDate = Carbon::parse($termination->date)->startOfDay();
            $today = Carbon::today()->startOfDay();

            // If termination date is today or in the past
            if ($terminationDate->lte($today)) {
                $employee->status = false; // inactive
            } else {
                $employee->status = true; // active (future termination)
            }
        } else {
            // Termination is inactive, so employee should be active
            $employee->status = true;
        }

        $employee->save();
    }

    public function getEmplyee()
    {
        return  Employee::with(['department', 'subDepartment', 'designation', 'branch'])->get();
    }
}
