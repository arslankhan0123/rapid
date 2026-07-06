<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SupplierRequest extends FormRequest
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
            'company_name'     => ['required', 'string', 'max:255'],
            'vat_number'       => ['nullable', 'string', 'min:4', 'max:15'],
            'website'          => ['nullable', 'url', 'max:255'],
            'phone'            => ['nullable', 'string', 'max:20'],
            'prefix_code'      => ['nullable', 'string', 'max:10'],
            'currency'         => ['nullable', 'string', 'max:10'],
            'country'          => ['nullable', 'string', 'max:100'],
            'default_language' => ['nullable', 'string', 'max:50'],
            'street'           => ['nullable', 'string', 'max:255'],
            'zip'              => ['nullable', 'numeric', 'digits_between:4,10'],
            'opening_balance'  => ['nullable', 'numeric', 'min:0.01'],

            // documents (arrays)
            'doc_name'         => ['nullable', 'array'],
            'doc_name.*'       => ['nullable', 'string', 'max:255'],

            'file'             => ['nullable', 'array'],
            'file.*'           => ['nullable', 'file', 'mimes:pdf', 'max:2048'], // 2MB

            'expiry_date'      => ['nullable', 'array'],
            'expiry_date.*'    => ['nullable', 'date', 'after:today'],
        ];
    }

    public function messages()
    {
        return [
            'company_name.required' => 'Company name is required.',
            'phone.required'        => 'Phone number is required.',
            'currency.required'     => 'Currency selection is required.',
            'file.*.mimes'          => 'Documents must be in PDF format.',
            'file.*.max'            => 'Each document must not exceed 2MB.',
            'expiry_date.*.after'   => 'Expiry date must be a future date.',
        ];
    }
}
