<?php

namespace App\Http\Controllers\Listing;

use App\Http\Controllers\Controller;
use App\Http\Requests\PaymentPurchaseInvoiceRequest;
use App\Http\Requests\UpdatePaymentPurchaseInvoiceRequest;
use App\Models\Payment;
use App\Models\PurchasePayment;
use App\Queries\PaymentDataTable;
use App\Queries\PurchasePaymentDataTable;
use App\Repositories\PurchasePaymentListRepository;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Yajra\DataTables\DataTables;
use App\Repositories\PaymentListRepository;
use App\Http\Requests\PaymentInvoiceRequest;
use App\Http\Requests\UpdatePaymentInvoiceRequest;
use Laracasts\Flash\Flash;
use Throwable;
use App\Http\Controllers\AppBaseController;
use Illuminate\Database\QueryException;

class PurchaseInvoicePaymentListing extends AppBaseController
{


    /**
     * Display a listing of the resource.
     *
     * @param  Request  $request
     * @return Factory|View
     *
     * @throws Exception
     */

    /**
     * @var PurchasePaymentListRepository
     */

    private $paymentListRepository;
    public function __construct(PurchasePaymentListRepository $paymentListRepo)
    {
        $this->paymentListRepository = $paymentListRepo;
    }
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return Datatables::of((new PurchasePaymentDataTable())->get($request->all()))->make(true);
        }
        $usersBranches = $this->getUsersBranches();

        return view('listing.purchase-payment.index', compact('usersBranches'));
    }
    public function create()
    {

        $invoices = $this->paymentListRepository->getInvoices();
        $invoices = $invoices->map(function ($invoice) {
            $totalPayments = $invoice->payments->sum('amount_received');
            $invoice->remaining_amount = $invoice->total_amount - $totalPayments;
            $invoice->remaining_amount = $invoice->remaining_amount ?? 0;
            return $invoice;
        });

        $paymentModes = $this->paymentListRepository->getPaymentModes();
        $usersBranches = $this->getUsersBranches();


        return view('listing.purchase-payment.create', compact(['invoices', 'paymentModes', 'usersBranches']));
    }
    public function edit(PurchasePayment $payment)
    {

        $invoices = $this->paymentListRepository->getInvoices(true);
        $invoices = $invoices->map(function ($invoice) {
            $totalPayments = $invoice->payments->sum('amount_received');
            $invoice->remaining_amount = $invoice->total_amount - $totalPayments;
            $invoice->remaining_amount = $invoice->remaining_amount ?? 0;
            return $invoice;
        });
        $paymentModes = $this->paymentListRepository->getPaymentModes();
        $usersBranches = $this->getUsersBranches();
        return view('listing.purchase-payment.edit', compact(['payment', 'invoices', 'paymentModes', 'usersBranches']));
    }
    public function update(PurchasePayment $payment, UpdatePaymentPurchaseInvoiceRequest $updatePaymentInvoiceRequest)
    {
        $input = $updatePaymentInvoiceRequest->all();
        $payment = $this->paymentListRepository->updatePayment($input, $payment);
        activity()->performedOn($payment)->causedBy(getLoggedInUser())
            ->useLog('Invoice Payment Updated')->log(' Invoice Payment updated.');
        Flash::success(__('messages.invoice-payments.saved'));
        return $this->sendSuccess(__('messages.invoice-payments.saved'));
    }
    public function store(PaymentPurchaseInvoiceRequest $request)
    {
        $input = $request->all();
        try {
            $assetCategory = $this->paymentListRepository->create($input);
            activity()->causedBy(getLoggedInUser())
                ->performedOn($assetCategory)
                ->useLog('Payment created.')
                ->log(' Payment Saved.');
            Flash::success(__('invoice-payments.saved'));
            return $this->sendResponse($assetCategory, __('messages.invoice-payments.saved'));
        } catch (Throwable $e) {
            throw $e;
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  Payment  $payment
     * @return Factory|View
     */
    public function show(PurchasePayment $payment)
    {
        $payment->load(['paymentMode', 'invoice']);
        return view('listing.purchase-payment.view', compact(['payment']));
    }

    public function destroy(PurchasePayment $payment)
    {
        try {
            $payment = $this->paymentListRepository->deletePayment($payment);
            $payment->delete();
            activity()->performedOn($payment)->causedBy(getLoggedInUser())
                ->useLog('Payment deleted.')->log('Payment Deleted');
            return $this->sendSuccess(__('messages.invoice-payments.delete'));
        } catch (QueryException $e) {
            return $this->sendError('Failed To delete!! Already in use.');
        }
    }
    public function exportCsv(Request $request)
    {
        // Get all relevant payments based on any filter (if needed)
        $payments = PurchasePayment::with(['invoice', 'paymentMode', 'branch']) // Adjust relationships accordingly
            ->get();

        // Define the CSV headers
        $csvHeaders = [
            'Invoice Number',
            'Payment Mode',
            'Note',
            'Payment Date',
            'Transaction ID',
            'Amount Received (SAR)',
        ];

        // Prepare data for export
        $data = [];
        foreach ($payments as $payment) {
            $data[] = [

                $payment->invoice->estimate_number ?? 'N/A', // Invoice Number
                $payment->paymentMode->name ?? 'N/A', // Payment Mode
                strip_tags($payment->note ?? 'N/A'), // Note
                $payment->payment_date ? \Carbon\Carbon::parse($payment->payment_date)->format('d-m-Y') : 'N/A', // Payment Date formatted as d-m-y
                $payment->transaction_id ?? 'N/A', // Transaction ID
                number_format($payment->amount_received, 2) . ' SAR', // Amount Received with formatting
            ];
        }

        // Open a stream to output the CSV file
        $csvFileName = 'Purchase-invoice-payments.csv';
        $csvOutput = fopen('php://output', 'w');

        // Send CSV headers to the browser
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $csvFileName . '"');

        // Write the CSV headers and data to the file
        fputcsv($csvOutput, $csvHeaders);
        foreach ($data as $row) {
            fputcsv($csvOutput, $row);
        }

        // Close the output stream
        fclose($csvOutput);
        exit;
    }
}
