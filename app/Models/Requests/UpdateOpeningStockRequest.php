<?php

namespace App\Http\Requests;

use App\Models\OpeningStock;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateOpeningStockRequest extends FormRequest
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
    public function rules($orderId = null)
    {


        $stock = $this->route('stock');
        return [
            'po_number' => [
                'required',
                Rule::unique('opening_stocks', 'po_number')->ignore($stock),
            ],
        ];
    }



    /**
     * @return array
     */
    public function messages()
    {
        return OpeningStock::$messages;
    }
}
