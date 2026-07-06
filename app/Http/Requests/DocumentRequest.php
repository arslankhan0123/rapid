<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DocumentRequest extends FormRequest
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
        $rules = [
            'description'   => 'nullable|array',
            'description.*' => 'nullable|string|max:500',
            'document'      => 'required|array',
            'document.*'    => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png,xls,xlsx,csv|max:10240',
        ];

        // For update, document is optional
        if ($this->isMethod('put') || $this->isMethod('patch')) {
            $rules['document'] = 'nullable|array';
            $rules['document.*'] = 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png,xls,xlsx,csv|max:10240';
        }

        // Only admin can set user_id (now accepts array)
        if (auth()->id() === 1) {
            $rules['user_id'] = 'required|array';
            $rules['user_id.*'] = 'required|exists:users,id';
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'document.required' => 'Please select at least one file to upload',
            'document.*.mimes'  => 'Each file must be PDF, Word, or Image format',
            'document.*.max'    => 'Each file size must be less than 10MB',
            'description.*.string' => 'Each description must be text',
            'user_id.required'  => 'Please select a user',
        ];
    }


    protected function prepareForValidation()
    {
        // Set user_id to current user if not admin
        if (auth()->id() !== 1 && !$this->has('user_id')) {
            $this->merge([
                'user_id' => auth()->id()
            ]);
        }
    }
}
