<?php

namespace App\Http\Requests;

use App\Models\DeliveryOrder;
use Illuminate\Foundation\Http\FormRequest;

class UpdateDeliveryOrderRequest extends FormRequest
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
        $rules = DeliveryOrder::$rules;

        return $rules;
    }

    /**
     * @return array
     */
    public function messages()
    {
        return DeliveryOrder::$messages;
    }
}
