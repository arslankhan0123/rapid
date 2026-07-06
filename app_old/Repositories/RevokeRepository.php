<?php

namespace App\Repositories;

use App\Models\Revoke;
use App\Models\Employee;
use App\Models\Termination;
use Illuminate\Support\Arr;
use Carbon\Carbon;

class RevokeRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'reason',
        'date',
        'employee_id',
        'termination_id'
    ];

    public function getFieldsSearchable()
    {
        return $this->fieldSearchable;
    }

    public function model()
    {
        return Revoke::class;
    }


    public function create($input)
    {
        // Check if termination already has a revoke
        $existingRevoke = Revoke::where('termination_id', $input['termination_id'])->first();
        if ($existingRevoke) {
            throw new \Exception('This termination has already been revoked.');
        }

        // Create revoke record
        $revoke = Revoke::create(
            Arr::only($input, ['termination_id', 'employee_id', 'reason', 'date', 'status'])
        );

        // Check revoke date before applying
        if ($revoke->status == 1) {
            $revokeDate = Carbon::parse($revoke->date);

            if ($revokeDate->isToday() || $revokeDate->isPast()) {
                $employee = Employee::find($revoke->employee_id);
                if ($employee) {
                    $employee->status = 1; // active
                    $employee->save();
                }
            }
        }

        return $revoke;
    }

    public function update($input, $id)
    {
        $revoke = Revoke::find($id);

        if (!$revoke) {
            return false;
        }

        $revoke->update(Arr::only($input, ['reason', 'date', 'status']));

        $employee = Employee::find($revoke->employee_id);
        if ($employee) {
            if ($revoke->status == 1) {
                $revokeDate = Carbon::parse($revoke->date);

                if ($revokeDate->isToday() || $revokeDate->isPast()) {
                    $employee->status = 1; // activate
                } else {
                    // future revoke date → keep employee as per termination
                    $termination = Termination::find($revoke->termination_id);
                    if ($termination && $termination->status == 1) {
                        $terminationDate = Carbon::parse($termination->date);
                        if ($terminationDate->isToday() || $terminationDate->isPast()) {
                            $employee->status = 0; // inactive until revoke date arrives
                        }
                    }
                }
            } else {
                // revoke inactive → fall back to termination
                $termination = Termination::find($revoke->termination_id);
                if ($termination && $termination->status == 1) {
                    $terminationDate = Carbon::parse($termination->date);
                    if ($terminationDate->isToday() || $terminationDate->isPast()) {
                        $employee->status = 0;
                    }
                }
            }
            $employee->save();
        }

        return $revoke;
    }
    // public function create($input)
    // {
    //     // Check if termination already has a revoke
    //     $existingRevoke = Revoke::where('termination_id', $input['termination_id'])->first();
    //     if ($existingRevoke) {
    //         throw new \Exception('This termination has already been revoked.');
    //     }

    //     // Create revoke record
    //     $revoke = Revoke::create(
    //         Arr::only($input, ['termination_id', 'employee_id', 'reason', 'date', 'status'])
    //     );

    //     // Update employee status to active if revoke is active
    //     if ($revoke->status == 1) {
    //         $employee = Employee::find($revoke->employee_id);
    //         if ($employee) {
    //             $employee->status = 1; // active
    //             $employee->save();
    //         }
    //     }

    //     return $revoke;
    // }

    // public function update($input, $id)
    // {
    //     $revoke = Revoke::find($id);

    //     if (!$revoke) {
    //         return false;
    //     }

    //     $revoke->update(Arr::only($input, ['reason', 'date', 'status']));

    //     // Update employee status based on revoke status
    //     $employee = Employee::find($revoke->employee_id);
    //     if ($employee) {
    //         if ($revoke->status == 1) {
    //             // Revoke is active, set employee to active
    //             $employee->status = 1;
    //         } else {
    //             // Revoke is inactive, check if termination is still active
    //             $termination = Termination::find($revoke->termination_id);
    //             if ($termination && $termination->status == 1) {
    //                 $terminationDate = Carbon::parse($termination->date);
    //                 if ($terminationDate->isToday() || $terminationDate->isPast()) {
    //                     $employee->status = 0; // inactive
    //                 }
    //             }
    //         }
    //         $employee->save();
    //     }

    //     return $revoke;
    // }

    public function getRevokes()
    {
        return Revoke::with(['employee', 'termination', 'employee.department', 'employee.designation', 'employee.branch'])->get();
    }
}
