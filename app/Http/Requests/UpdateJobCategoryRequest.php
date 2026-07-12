<?php

namespace App\Http\Requests;

use App\Models\JobCategory;
use Illuminate\Foundation\Http\FormRequest;

class UpdateJobCategoryRequest extends FormRequest
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
        $rules = JobCategory::$rules;
        $rules['category_code'] = $rules['category_code'] . ',' . $this->route('job_category')->id;
        return $rules;
    }
}
