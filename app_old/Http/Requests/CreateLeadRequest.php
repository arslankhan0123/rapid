<?php

namespace App\Http\Requests;

use App\Models\Lead;
use Illuminate\Foundation\Http\FormRequest;

class CreateLeadRequest extends FormRequest
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
        $phone = ! empty(request()->get('mobile')) ? ('+'.request()->get('prefix_code').request()->get('mobile')) : null;
        $this->request->set('phone', removeSpaceFromPhoneNumber($phone));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return Lead::$rules;
    }
}
