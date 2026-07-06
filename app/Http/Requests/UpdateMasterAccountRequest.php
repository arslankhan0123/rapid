<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMasterAccountRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $masterAccount = $this->route('masterAccount');
        $masterAccountId = $masterAccount ? $masterAccount->id : null;

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('master_accounts')->ignore($masterAccountId),
            ],
            'account_level' => 'required|in:level-1,level-2,level-3,level-4',
            'account_type' => 'nullable|in:Assets,Liabilities,Equity',
            'description' => 'nullable|string',
            'status' => 'boolean',
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
