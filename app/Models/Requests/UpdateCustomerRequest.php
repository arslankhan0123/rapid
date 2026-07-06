<?php

namespace App\Http\Requests;

use App\Models\Customer;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCustomerRequest extends FormRequest
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

    public function prepareForValidation()
    {
        $phone = ! empty(request()->get('phone')) ? ('+' . request()->get('prefix_code') . request()->get('phone')) : null;
        $this->request->set('phone', removeSpaceFromPhoneNumber($phone));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = Customer::$editRules;
        $rules['company_name'] = 'required|unique:customers,company_name,' . $this->route('customer')->id;
        $rules['phone'] = 'nullable|unique:customers,phone,' . $this->route('customer')->id;
        $rules['code'] = 'required|alpha_num|unique:customers,code,' . $this->route('customer')->id;


        return $rules;
    }
}
