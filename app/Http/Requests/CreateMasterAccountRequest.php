<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateMasterAccountRequest extends FormRequest
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
            'name' => 'required|string|max:255|unique:master_accounts,name',
            'account_level' => 'required|in:level-1,level-2,level-3,level-4',
            'account_type' => 'nullable|in:Assets,Liabilities,Equity',
            'description' => 'nullable|string',
            'status' => 'boolean'
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'The account name is required.',
            'name.unique' => 'This account name already exists.',
            'account_level.required' => 'Please select an account level.',
            'account_level.in' => 'Please select a valid account level.',
        ];
    }
}
