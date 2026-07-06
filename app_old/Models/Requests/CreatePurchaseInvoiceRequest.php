<?php

namespace App\Http\Requests;

use App\Models\PurchaseInvoice;
use Illuminate\Foundation\Http\FormRequest;

class CreatePurchaseInvoiceRequest extends FormRequest
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
        return PurchaseInvoice::$rules;
    }

    /**
     * @return array
     */
    public function messages()
    {
        return PurchaseInvoice::$messages;
    }
}
