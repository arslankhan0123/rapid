<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePurchaseItemRequest extends FormRequest
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
        $category = $this->route('item');
        return [
            'barcode' => [
                'required',
                Rule::unique('purchase_items', 'barcode')->ignore($category),
            ],
            'code' => [
                'required',
                Rule::unique('purchase_items', 'code')->ignore($category),
            ],
        ];
    }
}
