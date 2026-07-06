<?php

namespace App\Http\Requests;

use App\Models\LeadSource;
use Illuminate\Foundation\Http\FormRequest;

class UpdateLeadSourceRequest extends FormRequest
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
     * @return array
     */
    public function rules()
    {
        $leadSource = $this->route('leadSource');
        $rules = LeadSource::$rules;
        $rules['name'] = 'required|unique:lead_sources,name,'.$leadSource->id;

        return $rules;
    }
}
