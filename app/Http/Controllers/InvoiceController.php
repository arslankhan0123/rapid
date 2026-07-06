<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateInvoiceRequest;
use App\Http\Requests\UpdateInvoiceRequest;
use App\Models\Invoice;
use App\Models\Setting;
use App\Models\Task;
use App\Repositories\InvoiceRepository;
use App\Repositories\TicketRepository;
use Barryvdh\DomPDF\Facade as PDF;
use Exception;
use Flash;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Redirect;
use Throwable;
use App\Models\DocumentNextNumber;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendPdfEmail;
use Twilio\Rest\Client;
use App\Models\Bank;
use Mpdf\Mpdf;


class InvoiceController extends AppBaseController
{
    /** @var InvoiceRepository */
    private $invoiceRepository;

    public function __construct(InvoiceRepository $invoiceRepo)
    {
        $this->invoiceRepository = $invoiceRepo;
    }

    /**
     * Display a listing of the Invoice.
     *
     * @return Factory|Application|View
     */
    // public function index()
    // {
    //     $paymentStatuses = Invoice::PAYMENT_STATUS;
    //     $usersBranches = $this->invoiceRepository->getUsersBranches();
    //     return view('invoices.index', compact('paymentStatuses', 'usersBranches'));
    // }

  public function index()
    {
        $paymentStatuses = Invoice::PAYMENT_STATUS;
        $usersBranches = $this->invoiceRepository->getUsersBranches();
        $availableMonths = $this->getAvailableMonths();

        // Just fetch customers — no "All Customers" option
        $customersData = \App\Models\Customer::orderBy('company_name')
            ->get()
            ->map(function ($customer) {
                return [
                    'id' => (string) $customer->id,
                    'text' => $customer->company_name
                ];
            })
            ->toArray();

        return view('invoices.index', compact(
            'paymentStatuses',
            'usersBranches',
            'availableMonths',
            'customersData'
        ));
    }


    private function getAvailableMonths()
    {
        // Get distinct months from invoice_date field only (ignore invoice_month)
        return Invoice::selectRaw('DISTINCT DATE_FORMAT(invoice_date, "%Y-%m") as month_key,
                         DATE_FORMAT(invoice_date, "%M %Y") as formatted_month')
            ->whereNotNull('invoice_date')
            ->orderBy('month_key', 'desc')
            ->pluck('formatted_month', 'month_key')
            ->toArray();
    }
    /**
     * Show the form for creating a new Invoice.
     *
     * @param  null  $customerId
     * @return Application|Factory|View
     */
    public function create($customerId = null)
    {
        $data = $this->invoiceRepository->getSyncList();
        $settings = Setting::pluck('value', 'key');
        $projects = $this->invoiceRepository->getProjects();
        $services = $data['items'];
        $categories = $this->invoiceRepository->getServiceCategories();
        $terms = $this->invoiceRepository->getTerms();
        $customers = $this->invoiceRepository->getCustomersAll();
        // $nextNumber = DocumentNextNumber::getNextNumber('invoice');
         $nextNumber = DocumentNextNumber::initializeNumber('invoice', 150);

        $isRound = $settings['is_round'] ?? 0;
        // dd($nextNumber);
        $usersBranches = $this->invoiceRepository->getUsersBranches();
        return view('invoices.create', compact('data', 'isRound', 'customerId', 'settings', 'projects', 'services', 'terms', 'nextNumber', 'categories', 'customers', 'usersBranches'));
    }

    /**
     * Store a newly created Invoice in storage.
     *
     * @param  CreateInvoiceRequest  $request
     * @return RedirectResponse|Redirector
     *
     * @throws Throwable
     */
    public function store(CreateInvoiceRequest $request)
    {
        try {
            DB::beginTransaction();
            $input = $request->all();

            //$input['payment_status'] == 1 is invoice, 0 is draft
            $input['total_amount'] = $input['total_amount_new'];

            // if (array_sum($input['quantity']) > 9999999) {
            //     return $this->sendError(__('messages.common.quantity_is_not_greater_than'));
            // }
            if ($input['payment_status'] != 1) {
                $nextNumber = DocumentNextNumber::getNextNumber('invoice_draft_number');
                $input['invoice_number'] = "DI-" . $nextNumber;
            }
            $invoice = $this->invoiceRepository->saveInvoice($input);
            if ($input['payment_status'] != 1) {
                DocumentNextNumber::updateNumber('invoice_draft_number');
            } else {
                DocumentNextNumber::updateNumber('invoice');
            }
            DB::commit();

            Flash::success(__('messages.invoice.invoice_saved_successfully'));

            return $this->sendResponse($invoice, __('messages.invoice.invoice_saved_successfully'));
        } catch (Exception $e) {
            DB::rollBack();

            return $this->sendError($e->getMessage());
        }
    }

    /**
     * Display the specified Invoice.
     *
     * @param  Invoice  $invoice
     * @return Application|Factory|View
     */
    public function show(Invoice $invoice)
    {
        /** @var Invoice $invoice */
        //$invoice->load(['project']);
        $invoice = $this->invoiceRepository->getSyncListForInvoiceDetail($invoice->id);
        $paymentModes = $invoice->paymentModes->where('active', true)->pluck('name', 'id')->toArray();

        $customers = $this->invoiceRepository->getCustomers();
        // dd($customers);
        /** @var TicketRepository $ticketRepo */
        $ticketRepo = App::make(TicketRepository::class);
        $data = $ticketRepo->getReminderData($invoice->id, Invoice::class);

        $status = Task::STATUS;
        $priorities = Task::PRIORITY;

        $notes = $this->invoiceRepository->getNotesData($invoice);

        $groupName = (request('group') == null) ? 'invoice_details' : (request('group'));


        $settings = Setting::all()->pluck('value', 'key')->toArray();
        $isRound = $settings['is_round'] ?? 0;

        $subtotal = 0;
        $totalTaxable = 0;
        $totalVat = 0;
        
        // Loop through the sales items to calculate subtotal
        foreach ($invoice->salesItems as $item) {
            $subtotal += $item->taxable;
        }

        // Apply global deductions
        $totalDiscount = $invoice->discount ?? 0;
        $absentDeduction = $invoice->absent_deduction ?? 0;
        $allowanceDeduction = $invoice->allowance_deduction ?? 0;
        $damageDeduction = $invoice->damage_deduction ?? 0;

        $totalTaxable = $subtotal - $totalDiscount - $absentDeduction - $allowanceDeduction - $damageDeduction;
        $totalVat = $totalTaxable * 0.15;

        $words = $this->amountToWords($totalTaxable + $totalVat);



        return view(
            "invoices.views.$groupName",
            compact(
                'invoice',
                'paymentModes',
                'data',
                'status',
                'priorities',
                'notes',
                'groupName',
                'words',
                'subtotal',
                'totalTaxable',
                'totalVat',
                'settings',
                'customers',
                'isRound'
            )
        );
    }

    /**
     * Show the form for editing the specified Invoice.
     *
     * @param  Invoice  $invoice
     * @return Application|Factory|View|RedirectResponse
     */
    public function edit(Invoice $invoice)
    {
        if ($invoice->payment_status == Invoice::STATUS_PAID || $invoice->payment_status == Invoice::STATUS_PARTIALLY_PAID || $invoice->payment_status == Invoice::STATUS_CANCELLED) {
            return redirect()->back();
        }

        $data = $this->invoiceRepository->getSyncList();
        $invoice = $this->invoiceRepository->getSyncListForInvoiceDetail($invoice->id);
        $addresses = [];

        foreach ($invoice->invoiceAddresses as $index => $address) {
            $addresses[$index] = $address;
        }
        $projects = $this->invoiceRepository->getProjects();
        // dd($invoice->toArray());
        $services = $data['items'];
        $categories = $this->invoiceRepository->getServiceCategories();
        $terms = $this->invoiceRepository->getTerms();
        $customers = $this->invoiceRepository->getCustomersAll();
        $usersBranches = $this->invoiceRepository->getUsersBranches();
        // dd($invoice->toArray());

        $settings = Setting::pluck('value', 'key');
        $isRound = $settings['is_round'] ?? 0;
        $salesItems = $invoice->salesItems;

        return view('invoices.edit', compact('data', 'isRound', 'invoice', 'salesItems', 'addresses', 'projects', 'terms', 'services', 'categories', 'customers', 'usersBranches'));
    }

    /**
     * Update the specified Invoice in storage.
     *
     * @param  Invoice  $invoice
     * @param  UpdateInvoiceRequest  $request
     * @return JsonResponse
     *
     * @throws Throwable
     */
    public function update(Invoice $invoice, UpdateInvoiceRequest $request)
    {

        try {
            DB::beginTransaction();
            $input = $request->all();
            // dd($input);
            $convert_to_invoice = 0;
            if ($invoice->payment_status < 1 && $input['payment_status'] == 1) {
                $nextNumber = DocumentNextNumber::getNextNumber('invoice');
                $input['invoice_number'] = $nextNumber;
                $convert_to_invoice = 1;
            }



            $input['total_amount'] = $input['total_amount_new'];

            // if (array_sum($input['quantity']) > 9999999) {
            //     return $this->sendError(__('messages.common.quantity_is_not_greater_than'));
            // }
            $invoice = $this->invoiceRepository->updateInvoice($input, $invoice->id);

            if ($convert_to_invoice) {
                DB::table('invoices')->where('id', $invoice->id)->update(['created_at' => now()]);
                $invoice->created_at = now();
                DocumentNextNumber::updateNumber('invoice');
            }

            DB::commit();
            Flash::success(__('messages.invoice.invoice_updated_successfully'));
            return $this->sendResponse($invoice, __('messages.invoice.invoice_updated_successfully'));
        } catch (Exception $e) {
            DB::rollBack();

            return $this->sendError($e->getMessage());
        }
    }

    /**
     * Remove the specified Invoice from storage.
     *
     * @param  Invoice  $invoice
     * @return JsonResponse|RedirectResponse
     *
     * @throws Throwable
     */
    public function destroy(Invoice $invoice)
    {

        $invoice->load('creditNotes');

        // Check if the project has associated invoices, members, or services
        if ($invoice->creditNotes->isNotEmpty()) {
            return $this->sendError('Already in use');
        }
        try {
            DB::beginTransaction();
            $this->invoiceRepository->deleteInvoice($invoice);
            DB::commit();

            return $this->sendSuccess('Invoice deleted successfully.');
        } catch (Exception $e) {
            DB::rollBack();

            return Redirect::back()->withInput()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * @param  Invoice  $invoice
     * @return Application|Factory|View
     */
    public function viewAsCustomer(Invoice $invoice)
    {
        $invoice = $this->invoiceRepository->getSyncListForInvoiceDetail($invoice->id);
        $settings = Setting::pluck('value', 'key')->toArray();
        $totalPaid = 0;

        foreach ($invoice->payments as $payment) {
            $totalPaid += $payment->amount_received;
        }

        return view('invoices.view_as_customer', compact('invoice', 'totalPaid', 'settings'));
    }


    // public function covertToPdf(Invoice $invoice)
    // {

    //     $invoice->load('branch');
    //     $invoice = $this->invoiceRepository->getSyncListForInvoiceDetail($invoice->id);
    //     $totalPaid = 0;

    //     foreach ($invoice->payments as $payment) {
    //         $totalPaid += $payment->amount_received;
    //     }


    //     $settings = Setting::all()->pluck('value', 'key')->toArray();


    //     $bankDetails = Bank::first();

    //     $subtotal = 0;
    //     $totalTaxable = 0;
    //     $totalVat = 0;
    //     // Loop through the sales items to calculate totals
    //     foreach ($invoice->salesItems as $item) {
    //         //$itemVATAmount = $itemSubtotalExcludingVAT * ($item->tax / 100);
    //         $subtotal += ($item->quantity * $item->rate);
    //         $totalTaxable += ($item->quantity * $item->rate) - $item->discount;
    //         $totalVat += (($item->quantity * $item->rate) - $item->discount) * .15;
    //     }

    //     $words = $this->amountToWords($invoice->total_amount);



    //     // Data to pass to the view
    //     $data = [

    //         'invoice' => $invoice,
    //         'settings' => $settings,
    //         'subtotal' => $subtotal,
    //         'totalTaxable' => $totalTaxable,
    //         'totalVat' => $totalVat,
    //         'words' => $words,
    //         'bank' => $bankDetails
    //     ];
    //     // Load the view and pass data
    //     // dd($invoice->invoiceAddresses->toArray());
    //     // dd($invoice->salesItems->toArray());
    //     $pdf = PDF::loadView('invoices.invoice_pdf_v3', $data);
    //     $pdf->setPaper('A4', 'portrait');
    //     $pdf->setOptions(["isPhpEnabled" => true, 'isHtml5ParserEnabled' => true]);
    //     // return $pdf->stream(__('messages.invoice.invoice_prefix') . $invoice->invoice_number . '.pdf');
    //     return $pdf->stream(__('messages.invoice.invoice_prefix') . $invoice->invoice_number . '.pdf');
    // }




    public function covertToPdf(Invoice $invoice)
    {

        $invoice->load('branch');
        $invoice = $this->invoiceRepository->getSyncListForInvoiceDetail($invoice->id);
        $totalPaid = 0;

        foreach ($invoice->payments as $payment) {
            $totalPaid += $payment->amount_received;
        }


        $settings = Setting::all()->pluck('value', 'key')->toArray();

        // dd($invoice->customer->toArray());
        $bankDetails = Bank::first();
        // Calculate subtotal
        $subtotal = 0;
        foreach ($invoice->salesItems as $item) {
            $subtotal += $item->taxable;
        }

        // Apply global deductions
        $totalDiscount = $invoice->discount ?? 0;
        $absentDeduction = $invoice->absent_deduction ?? 0;
        $allowanceDeduction = $invoice->allowance_deduction ?? 0;
        $damageDeduction = $invoice->damage_deduction ?? 0;

        $totalTaxable = $subtotal - $totalDiscount - $absentDeduction - $allowanceDeduction - $damageDeduction;
        $totalVat = $totalTaxable * 0.15;
        $totalDeductions = $absentDeduction + $allowanceDeduction + $damageDeduction;

        $words = $this->amountToWords($totalTaxable + $totalVat);

        $wordsAr = $this->amountToWords($totalTaxable + $totalVat, 'ar');



        $format = $invoice->branch->print_format ?? 1;
        // Data to pass to the view

        $isRound = $settings['is_round'] ?? 0;


        $mpdf = new Mpdf([
            'format' => 'A4',
            'margin_top' => 45,
            'margin_bottom' => 19,
            'margin_left' => 0,
            'margin_right' => 0,
            'margin_header' => 0,
            'margin_footer' => 0,
            'mode' => 'utf-8',         // Ensure UTF-8 encoding
            'autoLangToFont' => true, // Automatically select font for different languages
            'autoScriptToLang' => true, // Automatically enable Arabic script support
            'directionality' => 'rtl', // Force RTL for Arabic content
        ]);

        $baseImagePath = public_path('print/format_' . $format);

        // Company header image
        $headerPath = $baseImagePath . '/header.jpg';
        $imageheader = file_get_contents($headerPath);
        $headerImage = base64_encode($imageheader);
        $headerImage = 'data:image/jpg;base64,' . $headerImage;

        // Company footer image
        $footerPath = $baseImagePath . '/footer.jpg';
        $imagefooter = file_get_contents($footerPath);
        $imagefooterPath = base64_encode($imagefooter);
        $footerImage = 'data:image/jpg;base64,' . $imagefooterPath;

        $data = [

            'invoice' => $invoice,
            'settings' => $settings,
            'subtotal' => $subtotal,
            'totalTaxable' => $totalTaxable,
            'totalVat' => $totalVat,
            'words' => $words,
            'wordsAr' => $wordsAr,
            'bank' => $bankDetails,
            'headerImage' => $headerImage,
            'footerImage' => $footerImage,
            'isRound' => $isRound,
            'totalDeductions' => $totalDeductions
        ];

        $invoiceText = ($invoice->payment_status === 0)
            ? 'Draft Invoice'
            : 'VAT Invoice';

        $invoiceTextArabic = ($invoice->payment_status === 0)
            ? 'فاتورة مسودة'
            : 'فاتورة ضريبية';


        $mpdf->SetHTMLHeader('
            <header style="width: 100%; height: 110px;">
                <!-- Header Image -->
                <img src="' . $headerImage . '" style="width: 100%; height: 110px;">

                <!-- Additional Content Below the Image -->
                <div class="content-header" style="width: 100%; height: 1.80cm; margin: 0; position: relative;">
                    <div class="vat" style="display: inline-block; padding: 5px; font-size: 12pt; padding-left: 0.26cm; text-align: left; padding-top: 20px; width: 30%; float: left;">
                        Vat No. : ' . ($settings['vat_number'] ?? '311204277500003') . '
                    </div>
                    <div class="content_header_title" style="vertical-align: middle; margin-top: 10px; float: left; display: inline-block; width: 35%; height: 1.13cm; text-align: center; line-height: 40px; border: 1px solid #e2e2e2; background: #fff7f2;">
                        <table style="width: 100%; border-collapse: collapse; margin-top: 5px; margin-left: 5px;">
                            <tr>
                                <td style="text-align: left; font-size: 17pt;">
                                    ' . $invoiceText . '
                                </td>
                                <td style="text-align: right; font-size: 17pt; padding-right: 10px;">
                                    ' . $invoiceTextArabic . '
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </header>
            ');


        // Set the footer
        $mpdf->SetHTMLFooter('
            <footer>
                <div style="text-align: start; font-size: 10px;padding-left:10px;">
                    Page {PAGENO} of {nbpg}
                </div>
                <img src="' . $footerImage . '" style="width: 100%; height: 60px;">

            </footer>
            ');
        // Render the HTML content
        $html = view('invoices.invoice_pdf_arabic', $data)->render();
        $mpdf->WriteHTML($html);

        // Output the PDF
        return $mpdf->Output(__('messages.invoice.invoice_prefix') . $invoice->invoice_number . '.pdf', 'D');
    }
    public function downloadPDF($number)
    {

        $invoice = Invoice::where('invoice_number', $number)->firstOrFail();

        $invoice = $this->invoiceRepository->getSyncListForInvoiceDetail($invoice->id);
        $totalPaid = 0;

        foreach ($invoice->payments as $payment) {
            $totalPaid += $payment->amount_received;
        }



        $settings = Setting::all()->pluck('value', 'key')->toArray();
        $bank = Setting::where('key', 'bank_details')->first();






        // Calculate subtotal
        $totalExcludingVAT = 0;
        foreach ($invoice->salesItems as $item) {
            $totalExcludingVAT += $item->taxable;
        }

        // Apply global deductions
        $totalDiscount = $invoice->discount ?? 0;
        $absentDeduction = $invoice->absent_deduction ?? 0;
        $allowanceDeduction = $invoice->allowance_deduction ?? 0;
        $damageDeduction = $invoice->damage_deduction ?? 0;

        if (isset($invoice->discount_type) && $invoice->discount_type == 0) {
            $discountAmount = ($totalDiscount / 100) * $totalExcludingVAT;
        } else {
            $discountAmount = $invoice->discount;
        }

        $totalTaxable = $totalExcludingVAT - $discountAmount - $absentDeduction - $allowanceDeduction - $damageDeduction;
        $totalVATAmount = $totalTaxable * 0.15;
        $totalNetAmount = $totalTaxable + $totalVATAmount;
        $totalDeductions = $absentDeduction + $allowanceDeduction + $damageDeduction;
        
        $afterDiscount = $totalExcludingVAT - $discountAmount;
        $newVat = $totalVATAmount;
        $newTotal = $totalNetAmount;

        $words = $this->amountToWords($totalNetAmount);

        // Data to pass to the view
        $data = [
            'invoice' => $invoice,
            'settings' => $settings,
            'totalIncludingVAT' => $totalNetAmount, // Matching the PDF requirement for Net Amount
            'subtotal' => $totalExcludingVAT,
            'totalTaxable' => $totalTaxable,
            'totalVat' => $totalVATAmount,
            'totalDiscount' => $discountAmount,
            'totalNetAmount' => $totalNetAmount,
            'afterDiscount' => $afterDiscount,
            'words' => $words,
            'newVat' => $newVat,
            'newTotal' => $newTotal,
            'bank' => $bank,
            'totalDeductions' => $totalDeductions
        ];
        // Load the view and pass data
        // dd($invoice->invoiceAddresses->toArray());
        // dd($invoice->salesItems->toArray());
        $pdf = PDF::loadView('invoices.invoice_pdf_final', $data);
        $pdf->setPaper('A4', 'portrait');

        // return $pdf->stream(__('messages.invoice.invoice_prefix') . $invoice->invoice_number . '.pdf');
        return $pdf->download(__('messages.invoice.invoice_prefix') . $invoice->invoice_number . '.pdf');
    }
    /**
     * @param  Invoice  $invoice
     * @param  Request  $request
     * @return mixed
     */
    public function changeStatus(Invoice $invoice, Request $request)
    {
        $this->invoiceRepository->changePaymentStatus($invoice->id, $request->get('paymentStatus'));

        return $this->sendSuccess('Payment status updated successfully.');
    }

    /**
     * @param  Invoice  $invoice
     * @return mixed
     */
    public function getNotesCount(Invoice $invoice)
    {
        return $this->sendResponse($invoice->notes()->count(), 'Notes count retrieved successfully.');
    }

    /**
     * @param  Request  $request
     * @return mixed
     */
    public function getCustomerAddress(Request $request)
    {
        $address = getAddressOfCustomer($request->customer_id);
        if (!empty($address[0])) {
            $address[0]->country = $address[0]->country != null ? $address[0]->addressCountry->name : 'null';
        }
        if (!empty($address[1])) {
            $address[1]->country = $address[1]->country != null ? $address[1]->addressCountry->name : 'null';
        }

        return $this->sendResponse($address, 'Address retrieved successfully');
    }


    public function returnPdf(Invoice $invoice)
    {

        $invoice = $this->invoiceRepository->getSyncListForInvoiceDetail($invoice->id);
        $totalPaid = 0;

        foreach ($invoice->payments as $payment) {
            $totalPaid += $payment->amount_received;
        }



        $settings = Setting::all()->pluck('value', 'key')->toArray();
        $bank = Setting::where('key', 'bank_details')->first();



        // Calculate subtotal
        $totalExcludingVAT = 0;
        foreach ($invoice->salesItems as $item) {
            $totalExcludingVAT += $item->taxable;
        }

        // Apply global deductions
        $totalDiscount = $invoice->discount ?? 0;
        $absentDeduction = $invoice->absent_deduction ?? 0;
        $allowanceDeduction = $invoice->allowance_deduction ?? 0;
        $damageDeduction = $invoice->damage_deduction ?? 0;

        if (isset($invoice->discount_type) && $invoice->discount_type == 0) {
            $discountAmount = ($totalDiscount / 100) * $totalExcludingVAT;
        } else {
            $discountAmount = $invoice->discount;
        }

        $totalTaxable = $totalExcludingVAT - $discountAmount - $absentDeduction - $allowanceDeduction - $damageDeduction;
        $totalVATAmount = $totalTaxable * 0.15;
        $totalNetAmount = $totalTaxable + $totalVATAmount;
        $totalDeductions = $absentDeduction + $allowanceDeduction + $damageDeduction;
        
        $afterDiscount = $totalExcludingVAT - $discountAmount;
        $newVat = $totalVATAmount;
        $newTotal = $totalNetAmount;

        $words = $this->amountToWords($totalNetAmount);

        // Data to pass to the view
        $data = [
            'invoice' => $invoice,
            'settings' => $settings,
            'totalIncludingVAT' => $totalNetAmount, // Matching the PDF requirement for Net Amount
            'subtotal' => $totalExcludingVAT,
            'totalTaxable' => $totalTaxable,
            'totalVat' => $totalVATAmount,
            'totalDiscount' => $discountAmount,
            'totalNetAmount' => $totalNetAmount,
            'afterDiscount' => $afterDiscount,
            'words' => $words,
            'newVat' => $newVat,
            'newTotal' => $newTotal,
            'bank' => $bank,
            'totalDeductions' => $totalDeductions
        ];
        // Load the view and pass data
        // dd($invoice->invoiceAddresses->toArray());
        // dd($invoice->salesItems->toArray());
        $pdf = PDF::loadView('invoices.invoice_pdf_final', $data);
        $pdf->setPaper('A4', 'portrait');

        // Generate the raw PDF content
        return $pdf = $pdf->output();
        // return $pdf->download(__('messages.invoice.invoice_prefix') . $invoice->invoice_number . '.pdf');
    }
    public function sendEmail(Request $request)
    {

        // Check if both 'ccEmail' and 'invoiceNumber' exist in the request
        if (!$request->has('invoiceNumber')) {
            return response()->json(['error' => 'Missing required fields:  invoiceNumber'], 400);
        }
        $ccEmail = $request->input('ccEmail');
        $bccEmail = $request->input('bccEmail');

        $invoice = $this->invoiceRepository->getInvoice($request->input(['invoiceNumber']));
        // Calculate totals using unified logic
        $totalExcludingVAT = 0;
        foreach ($invoice->salesItems as $item) {
            $totalExcludingVAT += $item->taxable;
        }
        $totalDiscount = $invoice->discount ?? 0;
        $absentDeduction = $invoice->absent_deduction ?? 0;
        $allowanceDeduction = $invoice->allowance_deduction ?? 0;
        $damageDeduction = $invoice->damage_deduction ?? 0;

        if (isset($invoice->discount_type) && $invoice->discount_type == 0) {
            $discountAmount = ($totalDiscount / 100) * $totalExcludingVAT;
        } else {
            $discountAmount = $totalDiscount;
        }

        $totalTaxable = $totalExcludingVAT - $discountAmount - $absentDeduction - $allowanceDeduction - $damageDeduction;
        $totalVATAmount = $totalTaxable * 0.15;
        $totalNetAmount = $totalTaxable + $totalVATAmount;

        // Generate or retrieve your PDF content
        $pdfContent = $this->returnPdf($invoice); // Replace with your actual PDF generation method

        $bodyText = "
        URL: " . url('/download/invoice/' . $invoice->invoice_number ?? 0) . "

        Invoice Number: {$invoice->invoice_number}
        Total Taxable: " . number_format($totalTaxable, 2) . " SAR
        Deductions: " . number_format($absentDeduction + $allowanceDeduction + $damageDeduction, 2) . " SAR
        Total VAT: " . number_format($totalVATAmount, 2) . " SAR
        Net Total: " . number_format($totalNetAmount, 2) . " SAR
        ";


        $link = url('/download/invoice/' . $invoice->invoice_number ?? 0); // Replace with your actual link


        $ccArray = $ccEmail ? explode(',', $ccEmail) : []; // Convert to an array if not empty
        $bccArray = $bccEmail ? explode(',', $bccEmail) : []; // Convert to an array if BCC is not empty

        $mail = Mail::to($invoice->customer->email ?? '');
        if (!empty($ccArray)) {
            $mail->cc($ccArray); // Add CC recipients only if CC array is not empty
        }
        // Add BCC recipients only if BCC array is not empty
        if (!empty($bccArray)) {
            $mail->bcc($bccArray);
        }

        $subject = "Invoice-" . $invoice->invoice_number;
        $mail->send(mailable: new SendPdfEmail($bodyText, $link, $pdfContent, $subject));

        return response()->json(['message' => 'Email sent successfully.']);
    }


    public function sendSms(Request $request)
    {
        // Validate that the required fields exist in the request
        if (!$request->has(['invoiceNumber', 'phone', 'smsData'])) {
            return response()->json(['error' => 'Missing required fields: invoiceNumber, phone, or smsData'], 400);
        }

        // Retrieve the inputs
        $invoiceNumber = $request->input('invoiceNumber');
        $phone = $request->input('phone');
        $phone = preg_replace('/[^\d+]/', '', $phone);

        // $phone= "+8801600214050";
        // dd($phone);
        $bodyText = $request->input('smsData');

        // Check if the bodyText contains the invoiceNumber
        if (strpos($bodyText, $invoiceNumber) === false) {
            return response()->json(['error' => 'Invoice number is missing in the SMS body text.'], 400);
        }

        // Twilio credentials from the .env file
        $sid = env('TWILIO_SID');
        $authToken = env('TWILIO_AUTH_TOKEN');
        $twilioPhoneNumber = env('TWILIO_PHONE_NUMBER');


        // Create a Twilio client
        $client = new Client($sid, $authToken);

        try {
            // Send the SMS
            $message = $client->messages->create(
                $phone, // Recipient's phone number
                [
                    'from' => $twilioPhoneNumber, // Your Twilio phone number
                    'body' => $bodyText, // SMS body
                ]
            );

            // Return success response after SMS is sent
            return response()->json(['message' => 'SMS sent successfully.', 'sid' => $message->sid]);
        } catch (Exception $e) {
            // Catch any exceptions (e.g., network error, authentication issue)
            return response()->json(['error' => 'Failed to send SMS: ' . $e->getMessage()], 500);
        }
    }
    public function sendWhatsApp(Request $request)
    {
        // Validate the required fields in the request, ensuring phone is an array
        $validatedData = $request->validate([
            'invoiceNumber' => 'required|integer',  // Ensure invoiceNumber is provided and is an integer
            'whatsappBody' => 'required|string',  // Ensure whatsappBody is a string
        ]);

        // Retrieve the inputs
        $invoiceNumber = $request->input('invoiceNumber');
        $bodyText = $request->input('whatsappBody');
        $details = $request->input('details', false); // Check if 'details' parameter is true

        // Assuming $allTo is coming from the invoice repository
        $allTo = $this->invoiceRepository->getWhatsAppNumber($request['phone']);  // Fetch the phone numbers

        // Check if we got valid phone numbers from the repository
        if (!$allTo || empty($allTo)) {
            return response()->json(['error' => 'No valid phone numbers found for the given customers'], 400);
        }


        // Clean up phone numbers
        $cleanedPhones = [];
        foreach ($allTo as $phone) {
            $cleanedPhones[] = $phone;
        }


        // dd($allTo);
        // Check if the bodyText contains the invoiceNumber
        if (strpos($bodyText, $invoiceNumber) === false) {
            return response()->json(['error' => 'Invoice number is missing in the message body text.'], 400);
        }

        // Twilio credentials from the .env file
        $sid = env('TWILIO_SID');
        $authToken = env('TWILIO_AUTH_TOKEN');
        $twilioPhoneNumber = env('TWILIO_WHATSAPP_PHONE_NUMBER'); // The WhatsApp-enabled phone number from Twilio

        // Create a Twilio client
        $client = new Client($sid, $authToken);

        // If 'details' is true, generate the PDF attachment (optional)
        $attachmentUrl = null;
        // if ($details) {
        //     // Generate the PDF from a view (you may need to customize the view path)
        //     $pdf = Pdf::loadView('invoices.pdf', compact('invoiceNumber'));
        //     $pdf->save(storage_path('app/public/invoices/invoice_' . $invoiceNumber . '.pdf'));
        //     $attachmentUrl = url('storage/invoices/invoice_' . $invoiceNumber . '.pdf'); // URL to the saved PDF
        // }

        try {
            // Loop through all cleaned phone numbers and send a message for each one
            foreach ($cleanedPhones as $phone) {
                // Prepare message data
                $messageData = [
                    'from' => 'whatsapp:' . $twilioPhoneNumber, // Your Twilio WhatsApp number
                    'body' => $bodyText, // WhatsApp message body
                ];

                // Attach the PDF if 'details' is true
                if ($details && $attachmentUrl) {
                    $messageData['mediaUrl'] = [$attachmentUrl]; // Media URL points to the saved PDF
                }

                // Send the message using the Twilio API
                $message = $client->messages->create(
                    'whatsapp:' . $phone, // Recipient's phone number, formatted for WhatsApp
                    $messageData
                );
            }

            // Return success response after WhatsApp message is sent
            return response()->json(['message' => 'WhatsApp message sent successfully.', 'sid' => $message->sid]);
        } catch (Exception $e) {
            // Catch any exceptions (e.g., network error, authentication issue)
            return response()->json(['error' => 'Failed to send WhatsApp message: ' . $e->getMessage()], 500);
        }
    }
}
