<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Retirement;
use Illuminate\Validation\Rule;

class UpdateTerminationRequest extends FormRequest
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
        $id = $this->route('termination');
        return [
            'employee_id' => [
                'required',
                Rule::unique('terminations', 'employee_id')->ignore($id),
            ],
        ];
    }
    public function messages()
    {
        return [
            'employee_id.required' => 'The employee is mandatory.',
            'employee_id.unique' => 'This employee has already been added in Retirement List',
        ];
    }

}
