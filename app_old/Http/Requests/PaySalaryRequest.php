<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Models\SalaryPayment;
use App\Models\SalarySheet;

class PaySalaryRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return SalaryPayment::rules();
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {

            $salarySheet = SalarySheet::find($this->salary_sheet_id);

            if (!$salarySheet) {
                return;
            }

            $dueAmount = $salarySheet->net_salary;
            $alreadyPaid = $salarySheet->salaryPayment->amount ?? 0;

            $newPay = (float) $this->amount;
            $totalAfterPayment = $alreadyPaid + $newPay;
            $remaining = $dueAmount - $alreadyPaid;

            if ($totalAfterPayment > $dueAmount) {
                $validator->errors()->add(
                    'amount',
                    'Payment cannot exceed remaining salary. Remaining: ' . $remaining
                );
            }
        });
    }

    /**
     * Make validation return JSON with your custom message
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => $validator->errors()->first(), // main message to show
                'errors'  => $validator->errors(),          // all validation errors
            ], 422)
        );
    }

    public function messages()
    {
        return [
            'salary_sheet_id.required' => 'Salary sheet is required.',
            'salary_sheet_id.exists'   => 'Invalid salary sheet.',
            'payment_type.required'    => 'Payment type is required.',
            'payment_type.in'          => 'Payment type must be cash or bank.',
            'bank_id.exists'           => 'Invalid bank.',
            'amount.required'          => 'Amount is required.',
            'amount.numeric'           => 'Amount must be numeric.',
        ];
    }
}
