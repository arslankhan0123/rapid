<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoanRequest extends FormRequest
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
        return [
            'approved_date' => ['required', 'date'],
            'repayment_from' => ['required', 'date', 'after_or_equal:approved_date'],
            // add other rules if needed
        ];
    }

    public function messages()
    {
        return [
            'repayment_from.after_or_equal' => 'Repayment start date should not be before the loan approval date.',
        ];
    }
}
