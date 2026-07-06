<?php

namespace App\Repositories;

use App\Models\CompanyLoan;
use App\Models\Customer;
use App\Models\Supplier;
use Illuminate\Support\Arr;

class CompanyLoanRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'date',
        'type',
        'name',
        'loan_amount',
        'loan_received_date',
        'loan_refund_date',
        'number_of_days',
    ];

    public function getFieldsSearchable()
    {
        return $this->fieldSearchable;
    }

    public function model()
    {
        return CompanyLoan::class;
    }

    public function create($input)
    {
        // Handle name based on type
        if ($input['type'] === 'customer') {
            $customer = Customer::find($input['customer_id']);
            $input['name'] = $customer->company_name;
            $input['supplier_id'] = null;
        } elseif ($input['type'] === 'supplier') {
            $supplier = Supplier::find($input['supplier_id']);
            $input['name'] = $supplier->company_name;
            $input['customer_id'] = null;
        } else {
            // For personal type, use the provided name
            $input['customer_id'] = null;
            $input['supplier_id'] = null;
        }

        return CompanyLoan::create(Arr::only($input, [
            'date',
            'type',
            'name',
            'loan_amount',
            'loan_received_date',
            'loan_refund_date',
            'number_of_days',
            'customer_id',
            'supplier_id'
        ]));
    }

    public function update($input, $id)
    {
        $companyLoan = CompanyLoan::findOrFail($id);

        // Handle name based on type
        if ($input['type'] === 'customer') {
            $customer = Customer::find($input['customer_id']);
            $input['name'] = $customer->company_name;
            $input['supplier_id'] = null;
        } elseif ($input['type'] === 'supplier') {
            $supplier = Supplier::find($input['supplier_id']);
            $input['name'] = $supplier->company_name;
            $input['customer_id'] = null;
        } else {
            // For personal type, use the provided name
            $input['customer_id'] = null;
            $input['supplier_id'] = null;
        }

        $companyLoan->update(Arr::only($input, [
            'date',
            'type',
            'name',
            'loan_amount',
            'loan_received_date',
            'loan_refund_date',
            'number_of_days',
            'customer_id',
            'supplier_id'
        ]));

        return $companyLoan;
    }

    public function getCustomers()
    {
        return Customer::pluck('company_name', 'id');
    }

    public function getSuppliers()
    {
        return Supplier::pluck('company_name', 'id');
    }
}
