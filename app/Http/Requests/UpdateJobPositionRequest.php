<?php

namespace App\Http\Requests;

use App\Models\JobPosition;
use Illuminate\Foundation\Http\FormRequest;

class UpdateJobPositionRequest extends FormRequest
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
        $rules = JobPosition::$rules;
        $rules['position_code'] = $rules['position_code'] . ',' . $this->route('jobPosition')->id;
        return $rules;
    }
}
