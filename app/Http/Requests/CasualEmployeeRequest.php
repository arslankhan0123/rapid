<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CasualEmployeeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Validation rules.
     */
    public function rules(): array
    {
        // dd($_POST);
        return [
            'name'          => 'required|string|max:255',
            'code'          => 'required|numeric',
            'file.*'        => 'nullable|file|mimes:pdf|max:2048',     // max 2 MB
            'expiry_date.*' => 'nullable|date',
            'image.*'       => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'iqama_no'      => 'required|unique:employees,iqama_no',
            'phone'         => ['required', 'regex:/^\+?[0-9]{7,15}$/'],
        ];
    }

    /**
     * Custom validation messages.
     */
    public function messages(): array
    {
        return [
            'name.required'           => 'Please enter the employee name.',
            'name.string'             => 'The name must be a valid string.',
            'name.max'                => 'The name may not be greater than 255 characters.',

            'code.required'           => 'Employee code is required.',
            'code.numeric'            => 'Employee code must be a number.',

            'file.*.file'             => 'Each uploaded file must be a valid file.',
            'file.*.mimes'            => 'Files must be in PDF format.',
            'file.*.max'              => 'Each file may not exceed 2 MB.',

            'expiry_date.*.date'      => 'Each expiry date must be a valid date.',

            'image.*.image'           => 'Each image must be an actual image file.',
            'image.*.mimes'           => 'Images must be JPG, JPEG, or PNG format.',
            'image.*.max'             => 'Each image may not exceed 2 MB.',

            'iqama_no.required'       => 'Iqama number is required.',
            'iqama_no.unique'         => 'This Iqama number has already been taken.',

            'phone.required'          => 'Phone number is required.',
            'phone.regex'             => 'Phone number must contain 7–15 digits and may start with a + sign.',
        ];
    }
}
