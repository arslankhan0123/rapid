<?php

namespace App\Http\Requests;

use App\Models\Proposal;
use Illuminate\Foundation\Http\FormRequest;

class CreateProposalRequest extends FormRequest
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
        $phone = ! empty(request()->get('phone')) ? ('+'.request()->get('prefix_code').request()->get('phone')) : null;
        $this->request->set('phone', removeSpaceFromPhoneNumber($phone));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return Proposal::$rules;
    }

    /**
     * @return array
     */
    public function messages()
    {
        return Proposal::$messages;
    }
}
