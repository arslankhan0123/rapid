<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\SalaryGenerate;
use Illuminate\Validation\Rule;

class SalaryGenerateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true; // Adjust authorization logic as needed
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'branch_id' => 'required|exists:branches,id',
            // 'salary_month' => [
            //     'required',
            //     'string',
            //     'size:7',
            //     Rule::unique('salary_generates')
            //         ->where('branch_id', $this->branch_id),
            // ],
            'generate_date' => 'nullable|date',
            'generated_by' => 'nullable|exists:employees,id',
            'approved_by' => 'nullable|exists:employees,id',
            'approved_date' => 'nullable|date|after_or_equal:generate_date',
        ];
    }

    /**
     * Get custom error messages for validation rules.
     *
     * @return array<string, string>
     */
    public function messages()
    {
        return [
            'salary_month.unique' => 'The salary month for the selected branch already exists.',
        ];
    }
}
