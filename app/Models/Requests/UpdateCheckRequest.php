<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Check;
use Illuminate\Validation\Rule;
class UpdateCheckRequest extends FormRequest
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



    public function rules()
    {
        $id = $this->route('check');
        return [
            'check_number' => [
                'required',
                Rule::unique('checks', 'check_number')->ignore($id),
            ],
        ];
    }
}
