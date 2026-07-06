<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\PurchasePayment;

class UpdatePaymentPurchaseInvoiceRequest extends FormRequest
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
        // Fetch the default rules from the Payment model
        $rules = PurchasePayment::$rules;

        // Check if this is an edit request (based on the presence of a payment ID or other conditions)
        if ($this->route('payment')) {
            // If it's an edit request, remove the transaction_id validation
            unset($rules['transaction_id']);
        }

        return $rules;
    }
}