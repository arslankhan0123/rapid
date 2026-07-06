<?php

namespace App\Repositories;

use App\Models\Loan;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use App\Models\Employee;

/**
 * Class CustomerRepository
 *
 * @version April 3, 2020, 6:37 am UTC
 */
class LoanRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
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
        'posted'
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
        return Loan::class;
    }

    public function create($input)
    {
        return Loan::create(Arr::only($input, [
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
            'posted'
        ]));
    }
    function getEmployee()
    {
        return  Employee::with(['department', 'subDepartment', 'designation','branch'])->where('status', 1)->get();
    }
}
