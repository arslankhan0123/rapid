<?php

namespace App\Repositories;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use App\Models\Holiday;
use App\Models\Payment;
use App\Models\Invoice;
use App\Models\PaymentMode;
use Illuminate\Support\Facades\DB;


/**
 * Class CustomerRepository
 *
 * @version April 3, 2020, 6:37 am UTC
 */
class PaymentListRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'owner_id',
        'owner_type',
        'amount_received',
        'payment_date',
        'payment_mode',
        'transaction_id',
        'note',
        'send_mail_to_customer_contacts',
        'stripe_id',
        'meta',
        'branch_id'

    ];

    /**
     * Return searchable fields
     *
     * @return array
     */
    public function getFieldsSearchable()
    {
        return $this->fieldSearchable;
    }

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Payment::class;
    }

    public function create($input)
    {
        DB::beginTransaction();
        try {

            $payment = Payment::create(Arr::only($input, $this->getFieldsSearchable()));
            $invoice = Invoice::findOrFail($payment->owner_id);

            $totalPayments = Payment::where('owner_id', $payment->owner_id)->sum('amount_received');

            if ($totalPayments < $invoice->total_amount) {
                $invoice->payment_status = 3;
            } elseif ($totalPayments >= $invoice->total_amount) {
                $invoice->payment_status = 2;
            }
            $invoice->save();
            DB::commit();

            return $payment;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    public function updatePayment($input, $payment)
    {
        DB::beginTransaction();
        try {
            // Update the payment record with the new data
            $payment->update(Arr::only($input, $this->getFieldsSearchable()));

            // Retrieve the associated invoice based on owner_id
            $invoice = Invoice::findOrFail($payment->owner_id);

            // Calculate the total payments made for this invoice
            $totalPayments = Payment::where('owner_id', $payment->owner_id)->sum('amount_received');

            // Update the payment status of the invoice based on total payments
            if ($totalPayments < $invoice->total_amount) {
                $invoice->payment_status = 3; // Partially paid
            } elseif ($totalPayments >= $invoice->total_amount) {
                $invoice->payment_status = 2; // Fully paid
            }

            // Save the updated invoice status
            $invoice->save();

            // Commit the transaction
            DB::commit();

            return $payment;
        } catch (Exception $e) {
            // Rollback the transaction in case of error
            DB::rollBack();
            throw $e;
        }
    }


    public function getInvoices($isEdit = null)
    {
        // If $isEdit is true, include payment status 2 in the query
        $paymentStatuses = $isEdit ? [1, 2, 3] : [1, 3];

        // Return the invoices with the adjusted payment statuses
        return Invoice::with('payments')->whereIn('payment_status', $paymentStatuses)->get();
    }

    public function getPaymentModes()
    {
        return PaymentMode::where('active', true)->pluck('name', 'id');
    }
    public function deletePayment($payment)
    {
        DB::beginTransaction();
        try {
            // Retrieve the associated invoice based on owner_id
            $invoice = Invoice::findOrFail($payment->owner_id);

            // Get the owner_id of the payment to identify the invoice
            $ownerId = $payment->owner_id;

            // Delete the payment record
            $payment->delete();

            // Calculate the total payments made for this invoice after deletion
            $totalPayments = Payment::where('owner_id', $ownerId)->sum('amount_received');

            // If no payments are left, mark the invoice as unpaid
            if ($totalPayments == 0) {
                $invoice->payment_status = 1; // Unpaid
            } elseif ($totalPayments < $invoice->total_amount) {
                $invoice->payment_status = 3; // Partially paid
            } elseif ($totalPayments >= $invoice->total_amount) {
                $invoice->payment_status = 2; // Fully paid
            }

            // Save the updated invoice status
            $invoice->save();

            // Commit the transaction
            DB::commit();

            return $payment;
        } catch (Exception $e) {
            // Rollback the transaction in case of error
            DB::rollBack();
            throw $e;
        }
    }


}
