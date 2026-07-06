<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SafetyMaterialRequest extends FormRequest
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
            'date' => 'required|date',
            'employee_id' => 'required|exists:employees,id',
            'category' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'duration' => 'required|in:1month,2m,3m,6m,9m,1year',
        ];
    }

    public function messages()
    {
        return [
            'employee_id.required' => 'Please select an employee',
            'employee_id.exists' => 'The selected employee does not exist',
        ];
    }
}
