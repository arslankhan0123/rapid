<?php

namespace App\Http\Requests;

use App\Models\ManualSale;
use Illuminate\Foundation\Http\FormRequest;

class CreateManualSalesRequest extends FormRequest
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
        return ManualSale::$rules;
    }

    /**
     * @return array
     */
    public function messages()
    {
        return ManualSale::$messages;
    }
}
