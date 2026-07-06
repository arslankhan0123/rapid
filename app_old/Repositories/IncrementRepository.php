<?php

namespace App\Repositories;


use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use App\Models\Increment;
use App\Models\Employee;
use Illuminate\Support\Facades\DB;

/**
 * Class CustomerRepository
 *
 * @version April 3, 2020, 6:37 am UTC
 */
class IncrementRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'name',
        'date',
        'description',
        'branch_id',
        'employee_id',
        'amount'
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
        return Increment::class;
    }

    public function create($input)
    {
        try {
            return DB::transaction(function () use ($input) {

                $increment = Increment::create(Arr::only($input, $this->getFieldsSearchable()));
                if ($increment->employee) {
                    $increment->employee->increment('basic_salary', $increment->amount);
                    $increment->employee->increment('gross_salary', $increment->amount);
                }

                return $increment;
            });
        } catch (Exception $e) {

            throw new Exception('Failed to create increment and update salary.');
        }
    }
    public function updateIncrement(array $input, $id)
    {
        try {
            return DB::transaction(function () use ($input, $id) {
                $increment = $this->find($id);
                if (!$increment) {
                    throw new Exception('Increment not found.');
                }
                $difference = $input['amount'] - $increment->amount;

                $increment->update(Arr::only($input, $this->getFieldsSearchable()));
                if ($increment->employee) {
                    $increment->employee->increment('basic_salary',  $difference);
                    $increment->employee->increment('gross_salary', $difference);
                }

                return $increment;
            });
        } catch (Exception $e) {
            \Log::error('Increment update failed: ' . $e->getMessage());
            throw new Exception('Failed to update increment and adjust salary.');
        }
    }
    public function  getAllEmployees()
    {
        return  Employee::with(['department', 'subDepartment', 'designation', 'branch'])->get();
    }
}
