<?php

namespace App\Repositories;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use App\Models\Attendance;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\Customer;
use App\Models\Project;

/**
 * Class CustomerRepository
 *
 * @version April 3, 2020, 6:37 am UTC
 */
class AttendanceRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'employee_id',
        'date',
        'time_in',
        'time_out',
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
        return Attendance::class;
    }

    public function create($input)
    {
        return Attendance::updateOrCreate(
            [
                'employee_id' => $input['employee_id'],
                'date' => $input['date'],
            ],
            Arr::only($input, [
                'employee_id',
                'date',
                'time_in',
                'time_out',
                'customer_id',
                'project_id',
                'hours'
            ])
        );
    }
    public function create_monthly($employeeId, $month, $timeIn, $timeOut)
    {
        // Start and end of the month
        $startDate = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        $endDate = Carbon::createFromFormat('Y-m', $month)->endOfMonth();

        // Generate all dates in the month
        $dates = [];
        $currentDate = $startDate;

        while ($currentDate <= $endDate) {
            $dates[] = $currentDate->format('Y-m-d');
            $currentDate->addDay();
        }

        // Use a transaction to ensure data integrity
        DB::beginTransaction();

        try {
            // Insert or update attendance records for each date
            foreach ($dates as $date) {
                Attendance::updateOrCreate(
                    [
                        'employee_id' => $employeeId,
                        'date' => $date,
                    ],
                    [
                        'time_in' => $timeIn,
                        'time_out' => $timeOut,
                    ]
                );
            }

            // Commit the transaction
            DB::commit();

            // Return success response
            return true;
        } catch (Exception $e) {
            // Rollback the transaction on error
            DB::rollBack();

            // Return error response
            return false;
        }
    }
    public function getEmplyees()
    {
        return  Employee::pluck('name', 'id'); // Retrieves departments as key-value pairs
    }
    public function  getAllEmployees()
    {
        return  Employee::with(['designation', 'department', 'subDepartment'])->get();
    }
    public function getCustomers(){
        return  Customer::pluck('company_name', 'id'); // Retrieves departments as key-value pairs
    }
    public function getProjects()
    {
        return  Project::pluck('project_name', 'id'); // Retrieves departments as key-value pairs
    }
}
