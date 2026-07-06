<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProjectCalculationRequest extends ProjectCalculationRequest
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
    // public function rules()
    // {
    //     $rules = parent::rules();
    //     $rules['code'] = 'required|string|max:50|unique:projects_calculation,code,' . $this->route('project_calculation');

    //     return $rules;
    // }

    public function rules()
    {
        $rules = parent::rules();

        // Make sure we pass the ID, not the object
        $projectId = $this->route('project_calculation');
        if ($projectId instanceof \App\Models\ProjectsCalculation) {
            $projectId = $projectId->id;
        }

        $rules['code'] = 'required|string|max:50|unique:projects_calculation,code,' . $projectId;

        return $rules;
    }
}
