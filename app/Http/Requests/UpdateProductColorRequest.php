<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductColorRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        $productColorId = $this->route('color');
        return [
            'title' => [
                'required',
                Rule::unique('product_colors', 'title')->ignore($productColorId),
            ],
        ];
    }
}
