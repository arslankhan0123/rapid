<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateManualSalesRequest;
use App\Http\Requests\UpdateManualSalesRequest;
use App\Models\Item;
use App\Models\ManualSale;
use App\Models\Setting;
use App\Models\Task;
use App\Repositories\ManualSalesRepository;
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
use Yajra\DataTables\DataTables;
use App\Queries\ManulSalesDataTable;


use Salla\ZATCA\GenerateQrCode;
use Salla\ZATCA\Tags\Seller;
use Salla\ZATCA\Tags\TaxNumber;
use Salla\ZATCA\Tags\InvoiceDate;
use Salla\ZATCA\Tags\InvoiceTotalAmount;
use Salla\ZATCA\Tags\InvoiceTaxAmount;

use Intervention\Image\Facades\Image;
use Intervention\Image\ImageManager;


class ManualSaleController extends AppBaseController
{
    /** @var ManualSalesRepository */
    private $invoiceRepository;

    public function __construct(ManualSalesRepository $invoiceRepo)
    {
        $this->invoiceRepository = $invoiceRepo;
    }

    /**
     * Display a listing of the Invoice.
     *
     * @return Factory|Application|View
     */
    // public function index(Request $request)
    // {

    //     if ($request->ajax()) {
    //         return DataTables::of((new ManulSalesDataTable())->get($request->all()))->make(true);
    //     }
    //     $paymentStatuses = ManualSale::PAYMENT_STATUS;
    //     $usersBranches = $this->invoiceRepository->getUsersBranches();
    //     return view('manual-sales.index', compact('paymentStatuses', 'usersBranches'));
    // }
    
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return DataTables::of((new ManulSalesDataTable())->get($request->all()))->make(true);
        }
        $paymentStatuses = ManualSale::PAYMENT_STATUS;
        $usersBranches = $this->invoiceRepository->getUsersBranches();

        // Add months data
        $months = $this->getAvailableMonths();

        // Add customers data
        $customers = \App\Models\Customer::orderBy('company_name')->pluck('company_name', 'id')->toArray();

        return view('manual-sales.index', compact('paymentStatuses', 'usersBranches', 'months', 'customers'));
    }

    private function getAvailableMonths()
    {
        // Get distinct months from invoice_date field
        return ManualSale::selectRaw('DISTINCT DATE_FORMAT(invoice_date, "%Y-%m") as invoice_month,
                                 DATE_FORMAT(invoice_date, "%M %Y") as formatted_month')
            ->whereNotNull('invoice_date')
            ->orderBy('invoice_month', 'desc')
            ->pluck('formatted_month', 'invoice_month')
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


        $projectsOnly = $this->invoiceRepository->getProjectOnly();

        $isRound = $settings['is_round'] ?? 0;
        $usersBranches = $this->invoiceRepository->getUsersBranches();
        return view('manual-sales.create', compact('data', 'projectsOnly', 'customerId', 'isRound', 'settings', 'projects', 'services', 'terms', 'nextNumber', 'categories', 'customers', 'usersBranches'));
    }
    public function getServices(Request $request)
    {
        $groupId = $request->get('item_group_id'); // assuming category_id is sent from frontend

        $services = Item::when($groupId, function ($query, $groupId) {
            return $query->where('item_group_id', $groupId);
        })
            ->orderBy('id', 'desc')
            ->get();

        return $this->sendResponse($services, 'Services');
    }


    /**
     * Store a newly created Invoice in storage.
     *
     * @param  CreateManualSalesRequest  $request
     * @return RedirectResponse|Redirector
     *
     * @throws Throwable
     */
    public function store(CreateManualSalesRequest $request)
    {
        try {
            DB::beginTransaction();
            $input = $request->all();
            $input['total_amount'] = $input['total_amount_new'];


            // if (array_sum($input['quantity']) > 9999999) {
            //     return $this->sendError(__('messages.common.quantity_is_not_greater_than'));
            // }
            
            if($input['payment_status'] != 1){
                $nextNumber = DocumentNextNumber::getNextNumber('manual_invoice_draft_number');
                $input['invoice_number'] = "DMI-" . $nextNumber;
            }

            $invoice = $this->invoiceRepository->saveInvoice($input);
            
            if($input['payment_status'] != 1){
                DocumentNextNumber::updateNumber('manual_invoice_draft_number'); 
            }
            else {
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
     * @param  ManualSale  $invoice
     * @return Application|Factory|View
     */
    public function show(ManualSale $invoice)
    {


        /** @var ManualSale $invoice */
        //$invoice->load(['project']);
        $invoice = $this->invoiceRepository->getSyncListForInvoiceDetail($invoice->id);
        $paymentModes = $invoice->paymentModes->where('active', true)->pluck('name', 'id')->toArray();

        $customers = $this->invoiceRepository->getCustomers();
        // dd($customers);
        /** @var TicketRepository $ticketRepo */
        $ticketRepo = App::make(TicketRepository::class);
        $data = $ticketRepo->getReminderData($invoice->id, ManualSale::class);

        $status = Task::STATUS;
        $priorities = Task::PRIORITY;

        $notes = $this->invoiceRepository->getNotesData($invoice);

        $groupName = (request('group') == null) ? 'invoice_details' : (request('group'));



        $settings = Setting::all()->pluck('value', 'key')->toArray();


        $subtotal = 0;
        $totalTaxable = 0;
        $totalVat = 0;
        // Loop through the sales items to calculate totals
        foreach ($invoice->salesItems as $item) {
            //$itemVATAmount = $itemSubtotalExcludingVAT * ($item->tax / 100);
            $subtotal += ($item->quantity * $item->rate);
            $totalTaxable += ($item->quantity * $item->rate) - $item->discount;
            $totalVat += (($item->quantity * $item->rate) - $item->discount) * .15;
        }

        $words = $this->amountToWords($invoice->total_amount);

        $isRound = $settings['is_round'] ?? 0;

        return view(
            "manual-sales.views.$groupName",
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
    
    public function accept_invoice(ManualSale $invoice)
    {

        
        $check_invoice_already_exists = DB::table('invoices')->where('invoice_number', $invoice->invoice_number)->count();
        
        if($check_invoice_already_exists){
            die("Already in Invoice");
        } 
        
        if((int)$invoice->customer_id < 1){
            return redirect()->back();
        }
        
        $nextinvoiceNumber = $invoice->invoice_number;
        
        if($invoice->payment_status == 0) {
            $nextinvoiceNumber = DocumentNextNumber::getNextNumber('invoice'); 
        }
        
        $new_invoice = [
            'title'              => $invoice->title,
            'customer_id'        => $invoice->customer_id ?? 0,
            'vendor_code'        => $invoice->vendor_code,
            'project_id'         => $invoice->project_id,
            'invoice_number'     => $nextinvoiceNumber,
            'invoice_date'       => $invoice->invoice_date,
            'invoice_month'      => $invoice->invoice_month,
            'due_date'           => $invoice->due_date,
            'sales_agent_id'     => $invoice->sales_agent_id,
            'currency'           => $invoice->currency,
            'discount_type'      => $invoice->discount_type,
            'percentage_discount'=> $invoice->percentage_discount,
            'discount'           => $invoice->discount,
            'admin_text'         => $invoice->admin_text,
            'unit'               => $invoice->unit,
            'client_note'        => $invoice->client_note,
            'term_conditions'    => $invoice->term_conditions,
            'sub_total'          => $invoice->sub_total,
            'adjustment'         => $invoice->adjustment,
            'total_amount'       => $invoice->total_amount,
            'payment_status'     => $invoice->payment_status,
            'discount_symbol'    => $invoice->discount_symbol,
            'user_id'            => $invoice->user_id,
            'branch_id'          => $invoice->branch_id,
            'created_at'         => now(), // Use current timestamp instead of old one
            'updated_at'         => now(),
            'hsn_tax'            => $invoice->hsn_tax,
        ];
        
        $lastInsertId = DB::table('invoices')->insertGetId($new_invoice);
        
        if($lastInsertId > 0) {
            $old_sales_item_details = DB::table('sales_items')->where('owner_id', $invoice->id)->where('owner_type', 'App\Models\ManualSale')->get();
        
            foreach($old_sales_item_details as $single_item) {
                
                
                $new_item = (array) $single_item;
                unset($new_item['id']);
                
                $new_item['owner_id'] = $lastInsertId;
                $new_item['owner_type'] = 'App\Models\Invoice';
                
                DB::table('sales_items')->insert($new_item);
                
            }
            
            $old_sales_taxe_details = DB::table('sales_taxes')->where('owner_id', $invoice->id)->where('owner_type', 'App\Models\ManualSale')->get();
        
            foreach($old_sales_taxe_details as $single_item_tax) {
                
                
                $new_tax_item = (array) $single_item_tax;
                unset($new_tax_item['id']);
                
                $new_tax_item['owner_id'] = $lastInsertId;
                $new_tax_item['owner_type'] = 'App\Models\Invoice';
                
                DB::table('sales_taxes')->insert($new_tax_item);
                
            }
            
            return redirect()->route('invoices.show', $lastInsertId);
                
        }
        
        if($nextinvoiceNumber) {
            
            // update into manual invoice invoice number
            DB::table('manul_sales')->where('id', $invoice->id)->update(['invoice_number' => $nextinvoiceNumber, 'payment_status' => 1]);
            DocumentNextNumber::updateNumber('invoice');
        }
        
        return redirect()->back();
        
         
    }
    
    

    /**
     * Show the form for editing the specified Invoice.
     *
     * @param  ManualSale  $invoice
     * @return Application|Factory|View|RedirectResponse
     */
    public function edit(ManualSale $invoice)
    {

        if ($invoice->payment_status == ManualSale::STATUS_PAID || $invoice->payment_status == ManualSale::STATUS_PARTIALLY_PAID || $invoice->payment_status == ManualSale::STATUS_CANCELLED) {
            return redirect()->back();
        }

        $data = $this->invoiceRepository->getSyncList();

        $invoice = $this->invoiceRepository->getSyncListForInvoiceDetail($invoice->id);

        $addresses = [];

        // foreach ($invoice->invoiceAddresses as $index => $address) {
        //     $addresses[$index] = $address;
        // }
        $projects = $this->invoiceRepository->getProjects();
        // dd($invoice->toArray());
        $services = $data['items'];
        $categories = $this->invoiceRepository->getServiceCategories();
        $terms = $this->invoiceRepository->getTerms();
        $customers = $this->invoiceRepository->getCustomersAll();
        $usersBranches = $this->invoiceRepository->getUsersBranches();

        $settings = Setting::pluck('value', 'key');
        $isRound = $settings['is_round'] ?? 0;

        $projectsOnly = $this->invoiceRepository->getProjectOnly();

        return view('manual-sales.edit', compact('data', 'isRound', 'projectsOnly', 'invoice', 'addresses', 'projects', 'terms', 'services', 'categories', 'customers', 'usersBranches'));
    }

    /**
     * Update the specified Invoice in storage.
     *
     * @param  ManualSale  $invoice
     * @param  UpdateManualSalesRequest  $request
     * @return JsonResponse
     *
     * @throws Throwable
     */
    public function update(ManualSale $invoice, UpdateManualSalesRequest $request)
    {
        try {
            DB::beginTransaction();
            $input = $request->all();
            // dd($input);
            $input['total_amount'] = $input['total_amount_new'];
            
            $convert_to_invoice = 0;
            if($invoice->payment_status < 1 && $input['payment_status'] == 1){
                $nextNumber = DocumentNextNumber::getNextNumber('invoice');
                $input['invoice_number'] = $nextNumber;
                $convert_to_invoice = 1;
            }

            // if (array_sum($input['quantity']) > 9999999) {
            //     return $this->sendError(__('messages.common.quantity_is_not_greater_than'));
            // }
            $invoice = $this->invoiceRepository->updateInvoice($input, $invoice->id);
            
            if($convert_to_invoice){ 
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
     * @param  ManualSale  $invoice
     * @return JsonResponse|RedirectResponse
     *
     * @throws Throwable
     */
    public function destroy(ManualSale $invoice)
    {




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
     * @param  ManualSale  $invoice
     * @return Application|Factory|View
     */
    public function viewAsCustomer(ManualSale $invoice)
    {
        $invoice = $this->invoiceRepository->getSyncListForInvoiceDetail($invoice->id);
        $settings = Setting::pluck('value', 'key')->toArray();
        $totalPaid = 0;

        foreach ($invoice->payments as $payment) {
            $totalPaid += $payment->amount_received;
        }

        return view('manual-sales.view_as_customer', compact('invoice', 'totalPaid', 'settings'));
    }






    public function covertToPdf(ManualSale $invoice)
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
        $subtotal = 0;
        $totalTaxable = 0;
        $totalVat = 0;
        // Loop through the sales items to calculate totals
        foreach ($invoice->salesItems as $item) {
            //$itemVATAmount = $itemSubtotalExcludingVAT * ($item->tax / 100);
            $subtotal += ($item->quantity * $item->rate);
            $totalTaxable += ($item->quantity * $item->rate) - $item->discount;
            $totalVat += (($item->quantity * $item->rate) - $item->discount) * .15;
        }

        $words = $this->amountToWords($invoice->total_amount);

        $wordsAr = $this->amountToWords($invoice->total_amount, 'ar');


        $format = $invoice->branch->print_format ?? 1;
        // Data to pass to the view


        $isRound = $settings['is_round'] ?? 0;

        $mpdf = new Mpdf([
            'format' => 'A4',
            'margin_top' => 45,
            'margin_bottom' => 17,
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
        // $footerPath = $baseImagePath . '/footer.jpg';
        // $imagefooter = file_get_contents($footerPath);
        // $imagefooterPath = base64_encode($imagefooter);
        // $footerImage = 'data:image/jpg;base64,' . $imagefooterPath;
        
        
        $footerPath = $baseImagePath . '/footer_white.jpg';
        $imageWidth = 2482;
        
        $manager = new ImageManager(['driver' => 'imagick']);

        // Load footer image
        $image = Image::make($footerPath);
    
        // Add dynamic text using default font 
        $image->text(strip_tags($invoice->branch->address_ar),
            $imageWidth/2, // x position
            50,  // y position
            function($font) {
                $font->file(public_path('fonts/arabic.ttf'));
                $font->size(40);
                $font->align('center');   // RTL alignment
                $font->color('#be714f');
            }
        );
        
        // Add dynamic text using default font
        $image->text(strip_tags($invoice->branch->address),
            $imageWidth/2, // x position
            110,  // y position
            function($font) {
                $font->file(public_path('fonts/Poppins-Regular.ttf'));
                $font->size(50);
                $font->align('center');   // RTL alignment
                $font->color('#4e7367');
            }
        );
        
        
    
        // Convert image to base64 (no save needed)
        $footerImage = 'data:image/png;base64,' . base64_encode($image->encode('png'));
        
        //return $image->response('png');
        

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
            'isRound' => $isRound
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
                <img src="' . $footerImage . '" style="width: 100%; height: 52px;">

            </footer>
            ');
        // Render the HTML content
        $html = view('manual-sales.invoice_pdf_arabic', $data)->render();
        $mpdf->WriteHTML($html);

        // Output the PDF
        return $mpdf->Output(__('messages.invoice.invoice_prefix') . $invoice->invoice_number . '.pdf', 'D');
    }
    public function downloadPDF($number)
    {

        $invoice = ManualSale::where('invoice_number', $number)->firstOrFail();

        $invoice = $this->invoiceRepository->getSyncListForInvoiceDetail($invoice->id);
        $totalPaid = 0;

        foreach ($invoice->payments as $payment) {
            $totalPaid += $payment->amount_received;
        }



        $settings = Setting::all()->pluck('value', 'key')->toArray();
        $bank = Setting::where('key', 'bank_details')->first();






        // Calculate totals
        $totalExcludingVAT = 0;
        $totalIncludingVAT = 0;
        $totalVATAmount = 0;
        $totalDiscount = $invoice->discount ?? 0;
        $totalNetAmount = 0;
        $afterDiscount = 0;
        $discountAmount = 0;
        // Loop through the sales items to calculate totals
        foreach ($invoice->salesItems as $item) {
            $itemTotalExcludingVAT = ($item->quantity * $item->rate) * (1 - ($item->discount / 100)); // Apply discount %
            $itemVATAmount = $itemTotalExcludingVAT * ($item->tax / 100);
            $totalExcludingVAT += $itemTotalExcludingVAT;
            $totalVATAmount += $itemVATAmount;
            $totalNetAmount += $itemTotalExcludingVAT + $itemVATAmount;
            $totalIncludingVAT += ($item->quantity * $item->rate) * (1 - ($item->discount / 100))
                + $itemVATAmount;
        }

        if (isset($invoice->discount_type) && $invoice->discount_type == 0) {

            $discountAmount = ($totalDiscount / 100) * $totalIncludingVAT;
        } else {
            $discountAmount = $invoice->discount;
        }

        $afterDiscount = $totalIncludingVAT - $discountAmount;
        $totalNetAmount -= $totalDiscount; // Apply additional discount
        $newVat = $afterDiscount * 0.15;
        $newTotal = $afterDiscount;


        $words = $this->amountToWords($totalNetAmount);




        // Data to pass to the view
        $data = [

            'invoice' => $invoice,
            'settings' => $settings,
            'totalIncludingVAT' => $totalIncludingVAT,
            'totalExcludingVAT' => $totalExcludingVAT,
            'totalVATAmount' => $totalVATAmount,
            'totalDiscount' => $totalDiscount,
            'totalNetAmount' => $totalNetAmount,
            'afterDiscount' => $afterDiscount,
            'words' => $words,
            'newVat' => $newVat,
            'newTotal' => $newTotal,
            'bank' => $bank
        ];
        // Load the view and pass data
        // dd($invoice->invoiceAddresses->toArray());
        // dd($invoice->salesItems->toArray());
        $pdf = PDF::loadView('manual-sales.invoice_pdf_final', $data);
        $pdf->setPaper('A4', 'portrait');

        // return $pdf->stream(__('messages.invoice.invoice_prefix') . $invoice->invoice_number . '.pdf');
        return $pdf->download(__('messages.invoice.invoice_prefix') . $invoice->invoice_number . '.pdf');
    }
    /**
     * @param  ManualSale  $invoice
     * @param  Request  $request
     * @return mixed
     */
    public function changeStatus(ManualSale $invoice, Request $request)
    {
        $this->invoiceRepository->changePaymentStatus($invoice->id, $request->get('paymentStatus'));

        return $this->sendSuccess('Payment status updated successfully.');
    }

    /**
     * @param  ManualSale  $invoice
     * @return mixed
     */
    public function getNotesCount(ManualSale $invoice)
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


    public function returnPdf(ManualSale $invoice)
    {

        $invoice = $this->invoiceRepository->getSyncListForInvoiceDetail($invoice->id);
        $totalPaid = 0;

        foreach ($invoice->payments as $payment) {
            $totalPaid += $payment->amount_received;
        }


        $settings = Setting::all()->pluck('value', 'key')->toArray();
        $bank = Setting::where('key', 'bank_details')->first();


        // Calculate totals
        $totalExcludingVAT = 0;
        $totalIncludingVAT = 0;
        $totalVATAmount = 0;
        $totalDiscount = $invoice->discount ?? 0;
        $totalNetAmount = 0;
        $afterDiscount = 0;
        $discountAmount = 0;
        // Loop through the sales items to calculate totals
        foreach ($invoice->salesItems as $item) {
            $itemTotalExcludingVAT = ($item->quantity * $item->rate) * (1 - ($item->discount / 100)); // Apply discount %
            $itemVATAmount = $itemTotalExcludingVAT * ($item->tax / 100);
            $totalExcludingVAT += $itemTotalExcludingVAT;
            $totalVATAmount += $itemVATAmount;
            $totalNetAmount += $itemTotalExcludingVAT + $itemVATAmount;
            $totalIncludingVAT += ($item->quantity * $item->rate) * (1 - ($item->discount / 100))
                + $itemVATAmount;
        }

        if (isset($invoice->discount_type) && $invoice->discount_type == 0) {

            $discountAmount = ($totalDiscount / 100) * $totalIncludingVAT;
        } else {
            $discountAmount = $invoice->discount;
        }

        $afterDiscount = $totalIncludingVAT - $discountAmount;
        $totalNetAmount -= $totalDiscount; // Apply additional discount
        $newVat = $afterDiscount * 0.15;
        $newTotal = $afterDiscount;


        $words = $this->amountToWords($totalNetAmount);



        // Data to pass to the view
        $data = [

            'invoice' => $invoice,
            'settings' => $settings,
            'totalIncludingVAT' => $totalIncludingVAT,
            'totalExcludingVAT' => $totalExcludingVAT,
            'totalVATAmount' => $totalVATAmount,
            'totalDiscount' => $totalDiscount,
            'totalNetAmount' => $totalNetAmount,
            'afterDiscount' => $afterDiscount,
            'words' => $words,
            'newVat' => $newVat,
            'newTotal' => $newTotal,
            'bank' => $bank
        ];
        // Load the view and pass data
        // dd($invoice->invoiceAddresses->toArray());
        // dd($invoice->salesItems->toArray());
        $pdf = PDF::loadView('manual-sales.invoice_pdf_final', $data);
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
        // Calculate totals
        $totalExcludingVAT = 0;
        $totalIncludingVAT = 0;
        $totalVATAmount = 0;
        $totalDiscount = $invoice->discount ?? 0;
        $totalNetAmount = 0;
        $afterDiscount = 0;
        $discountAmount = 0;
        // Loop through the sales items to calculate totals
        foreach ($invoice->salesItems as $item) {
            $itemTotalExcludingVAT = ($item->quantity * $item->rate) * (1 - ($item->discount / 100)); // Apply discount %
            $itemVATAmount = $itemTotalExcludingVAT * ($item->tax / 100);
            $totalExcludingVAT += $itemTotalExcludingVAT;
            $totalVATAmount += $itemVATAmount;
            $totalNetAmount += $itemTotalExcludingVAT + $itemVATAmount;
            $totalIncludingVAT += ($item->quantity * $item->rate) * (1 - ($item->discount / 100))
                + $itemVATAmount;
        }

        if (
            isset($invoice->discount_type) && $invoice->discount_type == 0
        ) {

            $discountAmount = ($totalDiscount / 100) * $totalIncludingVAT;
        } else {
            $discountAmount = $invoice->discount;
        }

        $afterDiscount = $totalIncludingVAT - $discountAmount;
        $totalNetAmount -= $totalDiscount; // Apply additional discount
        $newVat = $afterDiscount * 0.15;
        $newTotal = $afterDiscount;


        // Generate or retrieve your PDF content
        $pdfContent = $this->returnPdf($invoice); // Replace with your actual PDF generation method

        $bodyText = "
        URL: " . url('/download/invoice/' . $invoice->invoice_number ?? 0) . "

        Invoice Number: {$invoice->invoice_number}
        Total Before Discount: " . number_format($totalIncludingVAT, 2) . " SAR
        Discount " . (isset($invoice->discount_type) ? ($invoice->discount_type == 0 ? '%' : '$') : ' ') . ": " . number_format($invoice->discount ?? 0, 2) . "
        Total After Discount: " . number_format($afterDiscount, 2) . " SAR
        Total VAT: " . number_format($totalVATAmount, 2) . " SAR
        Net Total: " . number_format($newTotal, 2) . " SAR
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


    public function tstqrCode()
    {


        $qr_seller_trn = 312766391800003;
        $qr_tax_amount = 11670.20;
        $qr_invoice_amount = 1522.20; // Example fallback if $invoice is undefined
        $qr_code = null;
        $qr_seller_name = "شركة سميتس المحدودة";
        $qr_invoice_date = "2025-05-28 04.00";

        if (
            $qr_seller_name &&
            $qr_seller_trn &&
            $qr_invoice_date &&
            $qr_invoice_amount
        ) {
            $qr_code = GenerateQrCode::fromArray([
                new Seller($qr_seller_name),
                new TaxNumber($qr_seller_trn),
                new InvoiceDate($qr_invoice_date),
                new InvoiceTotalAmount(round($qr_invoice_amount)),
                new InvoiceTaxAmount($qr_tax_amount),
            ])->render();
        }
        // dd($qr_code);

        return view('manual-sales.test', compact('qr_code'));

    }

    public function approve($id)
    {
        try {
            $manualSale = $this->invoiceRepository->find($id);
            if (!$manualSale) {
                return redirect()->route('manual-sales.index')->with('error', 'Sale not found.');
            }

            $this->invoiceRepository->approve($manualSale);
            session()->flash('flash_notification', [
                [
                    'level' => 'success',
                    'message' => 'Manual sale has been approved successfully.',
                ]
            ]);

            return redirect()->route('manual-sales.index')->with('success', 'Manual Sale approved successfully.');
        } catch (Exception $e) {
            session()->flash('flash_notification', [
                [
                    'level' => 'error',
                    'message' => 'Failed',
                ]
            ]);
            return redirect()->route('manual-sales.index')->with('error', 'Something went wrong.');
        }
    }


}
