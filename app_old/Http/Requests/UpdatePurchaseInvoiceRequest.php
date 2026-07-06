<?php

namespace App\Http\Requests;

use App\Models\PurchaseInvoice;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePurchaseInvoiceRequest extends FormRequest
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


        $order = $this->route('invoice');
        return [
            'po_number' => [
                'required',
                Rule::unique('purchase_invoices', 'po_number')->ignore($order),
            ],
        ];
    }



    /**
     * @return array
     */
    public function messages()
    {
        return PurchaseInvoice::$messages;
    }
}
