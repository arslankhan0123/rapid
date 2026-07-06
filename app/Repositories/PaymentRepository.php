<?php

namespace App\Repositories;

use App\Models\Invoice;
use App\Models\Payment;
use Carbon\Carbon;

/**
 * Class PaymentRepository
 *
 * @version April 22, 2020, 8:39 am UTC
 */
class PaymentRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'owner_id',
        'owner_type',
        'amount_received',
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

    /**
     * @param  Invoice  $invoice
     * @return array
     */
    public function getData($invoice)
    {
        $receivedAmount = self::getTotalReceivedAmount($invoice->id);
        $data['amount'] = $invoice->total_amount - $receivedAmount;
        $data['date'] = Carbon::now()->toDateTimeString();
        $data['id'] = $invoice->id;

        return $data;
    }

    /**
     * @param  int  $invoiceId
     * @return int|mixed|string
     */
    public function getTotalReceivedAmount($invoiceId)
    {
        $payments = Payment::whereOwnerId($invoiceId)->get();

        return $payments->sum('amount_received');
    }

    /**
     * @param  array  $input
     * @return bool
     */
    public function store($input)
    {
        $input['send_mail_to_customer_contacts'] = isset($input['send_mail_to_customer_contacts']) ? 1 : 0;
        $input['owner_type'] = Invoice::class;
        $input['amount_received'] = formatNumber($input['amount_received']);
        $input['transaction_id'] = strtoupper($input['transaction_id']);
        $input['note'] = $input['note'];
        $payment = Payment::create($input);

        activity()->performedOn($payment)->causedBy(getLoggedInUser())
            ->useLog('Payment success.')->log($payment->paymentMode->name.' Payment success.');

        self::updatePaymentStatus($payment->owner_id);

        return true;
    }

    /**
     * @param  int  $invoiceId
     * @return bool
     */
    public function updatePaymentStatus($invoiceId)
    {
        $invoice = Invoice::findOrFail($invoiceId);
        $receivedAmount = self::getTotalReceivedAmount($invoiceId);
        ($invoice->total_amount == $receivedAmount)
            ? $invoice->update(['payment_status' => Invoice::STATUS_PAID]) : $invoice->update(['payment_status' => Invoice::STATUS_PARTIALLY_PAID]);

        return true;
    }
}
