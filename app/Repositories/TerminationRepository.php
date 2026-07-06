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

    public function create($input)
    {
        // Create termination record
        $termination = Termination::create(
            Arr::only($input, ['name', 'description', 'employee_id', 'date', 'status'])
        );

        // Find employee
        $employee = Employee::find($termination->employee_id);

        if ($employee && $termination->status == 1) { // check termination is active
            $terminationDate = Carbon::parse($termination->date);

            if ($terminationDate->isToday() || $terminationDate->isPast()) {
                $employee->status = 0; // inactive
                $employee->save();
            }
        }

        return $termination;
    }

    public function getEmplyee()
    {
        return  Employee::with(['department', 'subDepartment', 'designation', 'branch'])->get();
    }
}
