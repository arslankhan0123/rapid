<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductBrandRequest extends FormRequest
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
        $productBrandId = $this->route('brand');
        return [
            'title' => [
                'required',
                Rule::unique('product_brands', 'title')->ignore($productBrandId),
            ],
        ];
    }
}
