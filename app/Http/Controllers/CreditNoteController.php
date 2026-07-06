<?php
 
namespace App\Http\Controllers;

use App\Http\Requests\CreateCreditNoteRequest;
use App\Http\Requests\UpdateCreditNotRequest;
use App\Models\CreditNote;
use App\Models\Customer;
use App\Models\Setting;
use App\Repositories\CreditNoteRepository;
use Barryvdh\DomPDF\Facade as PDF;
use Exception;
use Flash;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\DB;
use Throwable;
use App\Models\DocumentNextNumber;
use App\Models\Bank;
use Mpdf\Mpdf;

class CreditNoteController extends AppBaseController
{
    /** @var CreditNoteRepository */
    private $creditNoteRepository;

    public function __construct(CreditNoteRepository $creditNoteRepo)
    {
        $this->creditNoteRepository = $creditNoteRepo;
    }

    /**
     * Display a listing of the CreditNotes.
     *
     * @return Application|Factory|\Illuminate\Contracts\View\View
     */
    public function index()
    {
        $paymentStatuses = CreditNote::PAYMENT_STATUS;
        $usersBranches = $this->creditNoteRepository->getUsersBranches();
        return view('credit_notes.index', compact('paymentStatuses', 'usersBranches'));
    }

    /**
     * Show the form for creating a new CreditNote.
     *
     * @param  null  $customerId
     * @return Application|Factory|\Illuminate\Contracts\View\View
     */
    public function create($customerId = null)
    {
        $data = $this->creditNoteRepository->getSyncList();
        $settings = Setting::pluck('value', 'key');
        $services = $data['items'];
        $categories = $this->creditNoteRepository->getServiceCategories();
        // dd($services);
        $terms = $this->creditNoteRepository->getTerms();
        $nextNumber = DocumentNextNumber::getNextNumber('credit_note');
        $projects = $this->creditNoteRepository->getProjects();
        $customers = $this->creditNoteRepository->getCustomersAll();
        $usersBranches = $this->creditNoteRepository->getUsersBranches();

        $isRound = $settings['is_round'] ?? 0;

        return view('credit_notes.create', compact('data', 'customerId', 'isRound', 'settings', 'services', 'terms', 'nextNumber', 'projects', 'categories', 'customers', 'usersBranches'));
    }

    public function getInvoice(Request $request)
    {
        if (isset($request['invoice_id']) && $request['invoice_id']) {
            $id = $request['invoice_id'];
            // Ensure repository is working and fetching the invoice
            $invoice = $this->creditNoteRepository->getInvoices($id);

            if ($invoice) {
                return response()->json([
                    'success' => true,
                    'message' => 'Invoice found successfully.',
                    'data' => $invoice
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'No invoice found with this ID.'
                ]);
            }
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Invoice ID is required.'
            ]);
        }
    }

    /**
     * Store a newly created CreditNote in storage.
     *
     * @param  CreateCreditNoteRequest  $request
     * @return RedirectResponse|Redirector
     */
    public function store(CreateCreditNoteRequest $request)
    {
        
        try {
            DB::beginTransaction();
            $input = $request->all();
            $input['total_amount'] = $input['total_amount_new'];
            // dd($input);
            // if (array_sum($input['quantity']) > 9999999) {
            //     return $this->sendError(__('messages.credit_note.quantity_is_not_greater_than'));
            // }
            
            if($input['payment_status'] != 1){
                $nextNumber = DocumentNextNumber::getNextNumber('credit_note_draft_number');
                $input['credit_note_number'] = "DCN-" . $nextNumber;
            }
            
            $creditNote = $this->creditNoteRepository->saveCreditNote($input);
            
            if($input['payment_status'] != 1){
                DocumentNextNumber::updateNumber('credit_note_draft_number'); 
            }
            else {
                DocumentNextNumber::updateNumber('credit_note');
            }
            
            DB::commit();
            Flash::success(__('messages.credit_note.credit_note_saved_successfully'));
            return $this->sendResponse($creditNote, __('messages.credit_note.credit_note_saved_successfully'));
        } catch (Exception $e) {
            DB::rollBack();

            return $this->sendError($e->getMessage());
        }
    }

    /**
     * Display the specified CreditNote.
     *
     * @param  CreditNote  $creditNote
     * @return Application|Factory|\Illuminate\Contracts\View\View
     */
    public function show(creditNote $creditNote)
    {
        $creditNote->load(['invoice', 'invoice.project']);
        $creditNote = $this->creditNoteRepository->getSyncListForCreditNoteDetail($creditNote->id);




        $subtotal = 0;
        $totalTaxable = 0;
        $totalVat = 0;
        // Loop through the sales items to calculate totals
        foreach ($creditNote->salesItems as $item) {
            //$itemVATAmount = $itemSubtotalExcludingVAT * ($item->tax / 100);
            $subtotal += ($item->quantity * $item->rate);
            $totalTaxable += ($item->quantity * $item->rate) - $item->discount;
            $totalVat += (($item->quantity * $item->rate) - $item->discount) * .15;
        }

        $words = $this->amountToWords($creditNote->total_amount);

        $settings = Setting::pluck('value', 'key');

        $isRound = $settings['is_round'] ?? 0;


        return view('credit_notes.show', compact(
            'creditNote',
            'words',
            'subtotal',
            'totalTaxable',
            'totalVat',
            'isRound'
        ));
    }

    /**
     * Update the specified CreditNote in storage.
     *
     * @param  CreditNote  $creditNote
     * @param  UpdateCreditNotRequest  $request
     * @return JsonResponse
     */
    public function update(CreditNote $creditNote, UpdateCreditNotRequest $request)
    {
        try {
            DB::beginTransaction();
            $input = $request->all();
            $input['total_amount'] = $input['total_amount_new'];

            // if (array_sum($input['quantity']) > 9999999) {
            //     return $this->sendError(__('messages.credit_note.quantity_is_not_greater_than'));
            // }
            
            $convert_to_invoice = 0;
            if($creditNote->payment_status < 1 && $input['payment_status'] == 1){
                $nextNumber = DocumentNextNumber::getNextNumber('credit_note');
                $input['credit_note_number'] = $nextNumber;
                $convert_to_invoice = 1;
            }
            
            
            $creditNote = $this->creditNoteRepository->updateCreditNote($input, $creditNote->id);
            
            if($convert_to_invoice){ 
                DocumentNextNumber::updateNumber('credit_note');
            }
            
            DB::commit();

            Flash::success(__('messages.credit_note.credit_note_updated_successfully'));

            return $this->sendResponse($creditNote, __('messages.credit_note.credit_note_updated_successfully'));
        } catch (Exception $e) {
            DB::rollBack();

            return $this->sendError($e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified CreditNote.
     *
     * @param  CreditNote  $creditNote
     * @return Application|Factory|\Illuminate\Contracts\View\View|RedirectResponse
     */
    public function edit(CreditNote $creditNote)
    {
        if ($creditNote->payment_status == CreditNote::PAYMENT_STATUS_CLOSED) {
            return redirect()->back();
        }
        $creditNote = CreditNote::with(['creditNoteAddresses', 'terms', 'salesItems', 'salesItems.taxes', 'salesItems.service', 'invoice.project.services'])->whereId($creditNote->id)->first();
        $data = $this->creditNoteRepository->getSyncList();
        $addresses = [];

        foreach ($creditNote->creditNoteAddresses as $index => $address) {
            $addresses[$index] = $address;
        }

        $serviceIds = [];
        foreach ($creditNote->invoice->project->services as $service) {
            $serviceIds[] = $service->service_id;
        }


        $projects = $this->creditNoteRepository->getProjects();
        $services = $data['items'];
        // dd($creditNote->toArray());
        $categories = $this->creditNoteRepository->getServiceCategories();
        $terms = $this->creditNoteRepository->getTerms();
        $customers = $this->creditNoteRepository->getCustomersAll();
        $usersBranches = $this->creditNoteRepository->getUsersBranches();


        $settings = Setting::pluck('value', 'key');
        $isRound = $settings['is_round'] ?? 0;

        return view('credit_notes.edit', compact('data', 'isRound', 'creditNote', 'addresses', 'projects', 'terms', 'services', 'serviceIds', 'categories', 'customers', 'usersBranches'));
    }

    /**
     * Remove the specified CreditNote from storage.
     *
     * @param  CreditNote  $creditNote
     * @return JsonResponse|RedirectResponse
     *
     * @throws Throwable
     */
    public function destroy(CreditNote $creditNote)
    {
        try {
            DB::beginTransaction();
            $this->creditNoteRepository->deleteCreditNote($creditNote);
            DB::commit();

            return $this->sendSuccess('Credit Note deleted successfully.');
        } catch (Exception $e) {
            DB::rollBack();

            return $this->sendError($e->getMessage());
        }
    }

    /**
     * @param  CreditNote  $creditNote
     * @param  Request  $request
     * @return mixed
     */
    public function changePaymentStatus(CreditNote $creditNote, Request $request)
    {
        $this->creditNoteRepository->changePaymentStatus($creditNote->id, $request->get('paymentStatus'));

        return $this->sendSuccess(__('messages.credit_note.credit_note_details_updated_successfully'));
    }

    /**
     * @param  CreditNote  $creditNote
     * @return Factory|Application|\Illuminate\Contracts\View\View
     */
    public function viewAsCustomer(CreditNote $creditNote)
    {
        $creditNote = $this->creditNoteRepository->getSyncListForCreditNoteDetail($creditNote->id);
        $settings = Setting::pluck('value', 'key')->toArray();
        $currency = Customer::CURRENCIES[$creditNote->currency];

        return view('credit_notes.view_as_customer', compact('creditNote', 'settings', 'currency'));
    }

    public function convertToPdf(CreditNote $creditNote)
    {
        $creditNote->load('branch');
        $creditNote = $this->creditNoteRepository->getSyncListForCreditNoteDetail($creditNote->id);
        $settings = Setting::pluck('value', 'key')->toArray();
        $currency = Customer::CURRENCIES[$creditNote->currency];
        $creditNote->load(['invoice', 'invoice.project', 'invoice.payments.paymentMode']);


        $bankDetails = Bank::first();
        $subtotal = 0;
        $totalTaxable = 0;
        $totalVat = 0;
        // Loop through the sales items to calculate totals
        foreach ($creditNote->salesItems as $item) {
            //$itemVATAmount = $itemSubtotalExcludingVAT * ($item->tax / 100);
            $subtotal += ($item->quantity * $item->rate);
            $totalTaxable += ($item->quantity * $item->rate) - $item->discount;
            $totalVat += (($item->quantity * $item->rate) - $item->discount) * .15;
        }

        $words = $this->amountToWords($creditNote->total_amount);
        $wordsAr = $this->amountToWords($creditNote->total_amount, 'ar');
        $isRound = $settings['is_round'] ?? 0;


        $data = [

            'creditNote' => $creditNote,
            'settings' => $settings,
            'subtotal' => $subtotal,
            'totalTaxable' => $totalTaxable,
            'totalVat' => $totalVat,
            'words' => $words,
            'bank' => $bankDetails
        ];
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

        $format = $creditNote->branch->print_format ?? 1;

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

            'creditNote' => $creditNote,
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




        $mpdf->SetHTMLHeader('
            <header style="width: 100%; height: 110px;">
                <!-- Header Image -->
                <img src="' . $headerImage . '" style="width: 100%; height: 110px;">

                <!-- Additional Content Below the Image -->
                <div class="content-header" style="width: 100%; height: 1.80cm; margin: 0; position: relative;">
                    <div class="vat" style="display: inline-block; padding: 5px; font-size: 12pt; padding-left: 0.26cm; text-align: left; padding-top: 20px; width: 30%; float: left;">
                        Vat No. : ' . ($settings['vat_number'] ?? 'N/A') . '
                    </div>
                    <div class="content_header_title" style="vertical-align: middle; margin-top: 10px; float: left; display: inline-block; width: 37%; height: 1.13cm; text-align: center; line-height: 40px; border: 1px solid #e2e2e2; background: #fff7f2;">
                        <table style="width: 100%; border-collapse: collapse; margin-top: 5px; margin-left: 5px;">
                            <tr>
                                <td style="text-align: left; font-size: 17pt;">
                                    Return Invoice
                                </td>
                             <td style="text-align: right; font-size: 17pt; padding-right: 10px;">
                            فاتورة مرتجع </td>
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
        $html = view('credit_notes.credit_note_pdf_arabic', $data)->render();
        $mpdf->WriteHTML($html);

        // Output the PDF
        return $mpdf->Output("Credit-Note-" . $creditNote->credit_note_number . '.pdf', 'D');
    }


    // public function convertToPdf(CreditNote $creditNote)
    // {
    //     $creditNote->load('branch');
    //     $creditNote = $this->creditNoteRepository->getSyncListForCreditNoteDetail($creditNote->id);
    //     $settings = Setting::pluck('value', 'key')->toArray();
    //     $currency = Customer::CURRENCIES[$creditNote->currency];
    //     $creditNote->load(['invoice', 'invoice.project', 'invoice.payments.paymentMode']);




    //     $bankDetails = Bank::first();

    //     $subtotal = 0;
    //     $totalTaxable = 0;
    //     $totalVat = 0;
    //     // Loop through the sales items to calculate totals
    //     foreach ($creditNote->salesItems as $item) {
    //         //$itemVATAmount = $itemSubtotalExcludingVAT * ($item->tax / 100);
    //         $subtotal += ($item->quantity * $item->rate);
    //         $totalTaxable += ($item->quantity * $item->rate) - $item->discount;
    //         $totalVat += (($item->quantity * $item->rate) - $item->discount) * .15;
    //     }

    //     $words = $this->amountToWords($creditNote->total_amount);

    //     $data = [

    //         'creditNote' => $creditNote,
    //         'settings' => $settings,
    //         'subtotal' => $subtotal,
    //         'totalTaxable' => $totalTaxable,
    //         'totalVat' => $totalVat,
    //         'words' => $words,
    //         'bank' => $bankDetails
    //     ];
    //     // Load the view and pass data



    //     $pdf = PDF::loadView('credit_notes.credit_note_pdf_v3', $data);
    //     $pdf->setPaper('A4', 'portrait');
    //     $pdf->setOptions(["isPhpEnabled" => true, 'isHtml5ParserEnabled' => true]);

    //     return $pdf->stream(__('messages.credit_note.credit_note_prefix') . $creditNote->credit_note_number . '.pdf');
    // }



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
}
