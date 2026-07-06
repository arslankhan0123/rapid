<?php

namespace App\Http\Requests;

use App\Models\ProductUnit;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductUnitRequest extends FormRequest
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
    public function rules()
    {
        $productUnitId = $this->route('unit');
        return [
            'title' => [
                'required',
                Rule::unique('product_units', 'title')->ignore($productUnitId),
            ],
        ];
    }
}
