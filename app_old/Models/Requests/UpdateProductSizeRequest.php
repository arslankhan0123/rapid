<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductSizeRequest extends FormRequest
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
        $productSizeId = $this->route('size');
        return [
            'title' => [
                'required',
                Rule::unique('product_sizes', 'title')->ignore($productSizeId),
            ],
        ];
    }
}
