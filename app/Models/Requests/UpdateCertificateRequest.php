<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCertificateRequest extends FormRequest
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
        $category = $this->route('category');
        return [
            'date' => 'required|date',
            'employee' => 'required|string|max:255',
            'lab_manager' => 'required|string|max:255',
            'general_manager' => 'required|string|max:255',
            'description' => 'nullable|string',
        ];
    }
}
