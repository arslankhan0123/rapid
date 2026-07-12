<?php

namespace App\Http\Requests;

use App\Models\JobSkill;
use Illuminate\Foundation\Http\FormRequest;

class UpdateJobSkillRequest extends FormRequest
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
        $rules = JobSkill::$rules;
        $rules['skill_code'] = $rules['skill_code'] . ',' . $this->route('jobSkill')->id;
        return $rules;
    }
}
