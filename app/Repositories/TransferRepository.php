<?php

namespace App\Repositories;

use App\Models\AssetCategory;
use App\Models\ProductUnit;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use App\Models\Transfer;
use App\Models\Employee;
use App\Models\BonusType;
use Illuminate\Support\Facades\DB;

/**
 * Class CustomerRepository
 *
 * @version April 3, 2020, 6:37 am UTC
 */
class TransferRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'employee_id',
        'from',
        'to',
        'transfer_date',
        'description',
        'branch_id'
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
        return Transfer::class;
    }

    public function create($input)
    {
        // Start a database transaction
        DB::beginTransaction();

        try {
            // Create the Transfer record
            $transfer = Transfer::create(Arr::only($input, $this->getFieldsSearchable()));
            // Update the employee's branch_id with the 'to' branch from the transfer
            $employee = $transfer->employee;
            $employee->branch_id = $transfer->to; // Assuming the 'to' column is the destination branch
            $employee->save();
            // Commit the transaction
            DB::commit();
            return $transfer; // Return the created transfer
        } catch (Exception $e) {
            // Rollback the transaction if something goes wrong
            DB::rollback();

            // Optionally, you can log the error or rethrow the exception
            throw $e;
        }
    }



    public function update_transfer($input, $id)
    {
        // Start a database transaction
        DB::beginTransaction();
        try {
            // Retrieve the existing transfer record
            $transfer = Transfer::findOrFail($id);
            // Update the transfer record with the input data
            $transfer->update(Arr::only($input, $this->getFieldsSearchable()));
            $employee = $transfer->employee;
            $employee->branch_id = $transfer->to; // Set the employee's branch_id to the 'to' branch
            $employee->save();

            DB::commit();
            return $transfer; // Return the updated transfer record
        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        }
    }


    public function  getAllEmployees()
    {
        return  Employee::with(['department', 'subDepartment', 'designation', 'branch'])->get();
    }
    public function getAllowanceTypes()
    {
        return  BonusType::pluck('name', 'id');
    }
}
