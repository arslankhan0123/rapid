<?php

namespace App\Repositories;

use App\Models\AssetCategory;
use App\Models\ProductUnit;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use App\Models\SubDepartment;
use App\Models\Salary;
use App\Models\Employee;
use Illuminate\Support\Facades\DB;
use App\Models\SalaryAdvance;
use App\Models\Bank;
use App\Models\Account;


/**
 * Class CustomerRepository
 *
 * @version April 3, 2020, 6:37 am UTC
 */
class SalaryAdvanceRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = ['employee_id', 'permitted_by', 'description', 'amount', 'approved_date', 'repayment_from', 'interest_percentage', 'installment_period', 'repayment_amount', 'installment', 'status', 'account_id', 'branch_id'];

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
        return SalaryAdvance::class;
    }

    // public function create($input)
    // {
    //     $insertData = Arr::only($input, ['employee_id', 'permitted_by', 'description', 'amount', 'approved_date', 'repayment_from', 'interest_percentage', 'installment_period', 'repayment_amount', 'installment', 'status', 'date', 'account_id', 'branch_id']);

    //     return SalaryAdvance::create($insertData);
    // }


    public function create($input)
    {
        DB::beginTransaction();

        try {
            $insertData = Arr::only($input, [
                'employee_id',
                'permitted_by',
                'description',
                'amount',
                'approved_date',
                'repayment_from',
                'interest_percentage',
                'installment_period',
                'repayment_amount',
                'installment',
                'status',
                'date',
                'account_id',
                'branch_id'
            ]);

            $totalAmount = formatNumber($input['amount']);
            $branchId = $input['branch_id'];

            $account = Account::where('id', $input['account_id'])
                ->where('branch_id', $branchId)
                ->first();

            if ($account) {
                $account->opening_balance -= $totalAmount;
                $account->save();
            }

            $salaryAdvance = SalaryAdvance::create($insertData);

            DB::commit();
            return $salaryAdvance;
        } catch (Exception $e) {
            DB::rollBack();
            // You can log the error or rethrow it if needed
            throw $e;
        }
    }

    function getEmployee()
    {
        return Employee::with(['designation', 'department', 'subDepartment', 'branch'])->where('status', 1)->get();
    }
    public function getAccounts()
    {
        return Account::orderBy('id', 'desc')->get();
    }
}
