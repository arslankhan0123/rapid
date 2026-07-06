<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CompanyLoanRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        $type = $this->input('type');

        $rules = [
            'date'               => 'required|date',
            'type'               => 'required|in:customer,supplier,personal',
            'loan_amount'        => 'required|numeric|min:1',
            'loan_received_date' => 'required|date',
            'loan_refund_date'   => 'required|date|after_or_equal:loan_received_date',
            'number_of_days'     => 'nullable|integer|min:0',
        ];

        // Conditional validation based on type
        if ($type === 'customer') {
            $rules['customer_id'] = 'required|exists:customers,id';
            $rules['name'] = 'nullable';
        } elseif ($type === 'supplier') {
            $rules['supplier_id'] = 'required|exists:suppliers,id';
            $rules['name'] = 'nullable';
        } elseif ($type === 'personal') {
            $rules['name'] = 'required|string|max:255';
            $rules['customer_id'] = 'nullable';
            $rules['supplier_id'] = 'nullable';
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'date.required'               => 'Please select a valid loan date.',
            'type.required'               => 'Loan type is required.',
            'type.in'                     => 'Loan type must be either customer, supplier, or personal.',
            'loan_amount.required'        => 'Loan amount is required.',
            'loan_amount.numeric'         => 'Loan amount must be a number.',
            'loan_received_date.required' => 'Received date is required.',
            'loan_refund_date.required'   => 'Refund date is required.',
            'loan_refund_date.after_or_equal' => 'Refund date must be same or after received date.',
            'number_of_days.integer'      => 'Number of days must be an integer.',
            'number_of_days.min'          => 'Number of days cannot be negative.',
            'customer_id.required'        => 'The customer field is required when type is customer.',
            'supplier_id.required'        => 'The supplier field is required when type is supplier.',
            'name.required'               => 'The name field is required when type is personal.',
        ];
    }

    public function prepareForValidation()
    {
        // Set name to null if it's not personal type to avoid validation issues
        if ($this->type !== 'personal') {
            $this->merge([
                'name' => null
            ]);
        }

        // Set customer_id/supplier_id to null based on type
        if ($this->type === 'customer') {
            $this->merge([
                'supplier_id' => null
            ]);
        } elseif ($this->type === 'supplier') {
            $this->merge([
                'customer_id' => null
            ]);
        } else {
            $this->merge([
                'customer_id' => null,
                'supplier_id' => null
            ]);
        }
    }
}
