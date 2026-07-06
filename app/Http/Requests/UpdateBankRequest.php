<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBankRequest extends FormRequest
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
        $id = $this->route('bank'); // Get the bank id from route

        return [
            'name' => 'required|string|max:191',
            'account_number' => [
                'required',
                'string',
                'max:50',
                Rule::unique('banks', 'account_number')->ignore($id),
            ],
            'branch_name' => 'nullable|string|max:191',
            'swift_code' => 'nullable|string|max:20',
            'description' => 'nullable|string',
            'opening_balance' => 'required|numeric|min:0.01',
            'iban_number' => 'nullable|string|max:34',
            'address' => 'nullable|string|max:255',
        ];
    }
}
