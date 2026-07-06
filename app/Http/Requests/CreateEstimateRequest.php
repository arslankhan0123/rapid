<?php

namespace App\Http\Requests;

use App\Models\Estimate;
use Illuminate\Foundation\Http\FormRequest;

class CreateEstimateRequest extends FormRequest
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
        return Estimate::$rules;
    }

    /**
     * @return array
     */
    public function messages()
    {
        return Estimate::$messages;
    }

    // public function withValidator($validator)
    // {
    //     $validator->after(function ($validator) {
    //         $items = $this->input('itemsArr');

    //         if (!is_array($items) || count($items) === 0) {
    //             $validator->errors()->add('itemsArr', 'At least one item is required.');
    //             return;
    //         }

    //         $hasValidItem = false;
    //         foreach ($items as $item) {
    //             $total = isset($item['total']) ? floatval($item['total']) : 0;
    //             if ($total > 0) {
    //                 $hasValidItem = true;
    //                 break;
    //             }
    //         }

    //         if (!$hasValidItem) {
    //             $validator->errors()->add('itemsArr', 'Please select at least one valid item.');
    //         }
    //     });
    // }
}
