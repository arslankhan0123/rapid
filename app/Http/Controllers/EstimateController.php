<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateEstimateRequest;
use App\Http\Requests\UpdateEstimateRequest;
use App\Models\Estimate;
use App\Models\Setting;
use App\Models\Task;
use App\Repositories\EstimateRepository;
use Barryvdh\DomPDF\Facade as PDF;
use Dompdf\FontMetrics;
use Exception;
use Flash;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use App\Models\EstimateAddress;
use App\Models\DocumentNextNumber;
use App\Models\Customer;
use Contao\ImagineSvg\Svg;
use App\Models\Bank;
use Mpdf\Mpdf;

class EstimateController extends AppBaseController
{
    /** @var EstimateRepository */
    private $estimateRepository;

    public function __construct(EstimateRepository $estimateRepo)
    {
        $this->estimateRepository = $estimateRepo;
    }

    /**
     * Display a listing of the Estimate.
     *
     * @return Factory|View
     */
    public function index()
    {
        $statusArr = Estimate::STATUS;
        $usersBranches = $this->estimateRepository->getUsersBranches();

        return view('estimates.index', compact('statusArr', 'usersBranches'));
    }

    /**
     * Show the form for creating a new Estimate.
     *
     * @param  null  $customerId
     * @return Factory|View
     */
    public function create($customerId = null)
    {
        $data = $this->estimateRepository->getSyncList();
        $settings = Setting::pluck('value', 'key');
        $services = $data['items'];
        $categories = $this->estimateRepository->getServiceCategories();
        // dd($services->toArray());
        $terms = $this->estimateRepository->getTerms();
        $customers = $this->estimateRepository->getCustomers();
        $nextNumber = DocumentNextNumber::getNextNumber('quotation');
        $usersBranches = $this->estimateRepository->getUsersBranches();
        $isRound = $settings['is_round'] ?? 0;
        return view('estimates.create', compact('data', 'services', 'isRound', 'customerId', 'settings', 'nextNumber', 'terms', 'categories', 'customers', 'usersBranches'));
    }

    /**
     * Store a newly created Estimate in storage.
     *
     * @param  CreateEstimateRequest  $request
     * @return JsonResponse
     */
    public function store(CreateEstimateRequest $request)
    {

        try {
            DB::beginTransaction();
            $input = $request->all();
            $input['total_amount'] = $input['total_amount_new'];

            if ($input['status'] != 1) {
                $nextNumber = DocumentNextNumber::getNextNumber('quotation_draft_number');
                $input['estimate_number'] = "DQ-" . $nextNumber;
            }

            $estimate = $this->estimateRepository->store($input);

            if ($input['status'] != 1) {
                DocumentNextNumber::updateNumber('quotation_draft_number');
            } else {
                DocumentNextNumber::updateNumber('quotation');
            }

            DB::commit();

            Flash::success(__('messages.estimate.estimate_saved_successfully'));

            return $this->sendResponse($estimate, __('messages.estimate.estimate_saved_successfully'));
        } catch (Exception $e) {
            DB::rollBack();

            return $this->sendError($e->getMessage());
        }
    }


    /**
     * Display the specified Estimate.
     *
     * @param  Estimate  $estimate
     * @return Factory|View
     */
    public function show(Estimate $estimate)
    {
        $estimate->load(['terms', 'estimateAddresses', 'branch']);

        $estimate = $this->estimateRepository->getSyncForEstimateDetail($estimate->id);

        $subtotal = 0;
        $totalTaxable = 0;
        $totalVat = 0;
        // Loop through the sales items to calculate totals
        foreach ($estimate->salesItems as $item) {
            //$itemVATAmount = $itemSubtotalExcludingVAT * ($item->tax / 100);
            $subtotal += ($item->quantity * $item->rate);
            $totalTaxable += ($item->quantity * $item->rate) - $item->discount;
            $totalVat += (($item->quantity * $item->rate) - $item->discount) * .15;
        }

        $words = $this->amountToWords($estimate->total_amount);

        $status = Task::STATUS;
        $priorities = Task::PRIORITY;
        $groupName = (request('group') == null) ? 'estimate_details' : (request('group'));
        $settings = Setting::pluck('value', 'key');
        $isRound = $settings['is_round'] ?? 0;
        // dd($estimate->toArray());
        return view("estimates.views.$groupName", compact('estimate', 'isRound', 'status', 'priorities', 'groupName', 'words', 'subtotal', 'totalTaxable', 'totalVat'));
    }

    /**
     * Show the form for editing the specified Estimate.
     *
     * @param  Estimate  $estimate
     * @return Application|Factory|\Illuminate\Contracts\View\View|RedirectResponse
     */
    public function edit(Estimate $estimate)
    {
        $estimate = Estimate::with(['salesItems.taxes', 'terms', 'estimateAddresses'])->findOrFail($estimate->id);

        $settings = Setting::pluck('value', 'key');

        $isRound = $settings['is_round'] ?? 0;


        if ($estimate->status == Estimate::STATUS_EXPIRED || $estimate->status == Estimate::STATUS_DECLINED) {
            return redirect()->back();
        }

        $data = $this->estimateRepository->getSyncList();
        $addresses = [];
        $services = $data['items'];
        foreach ($estimate->estimateAddresses as $index => $address) {
            $addresses[$index] = $address;
        }
        $terms = $this->estimateRepository->getTerms();
        $categories = $this->estimateRepository->getServiceCategories();
        $customers = $this->estimateRepository->getCustomers();
        $usersBranches = $this->estimateRepository->getUsersBranches();
        return view('estimates.edit', compact('data', 'isRound', 'services', 'estimate', 'addresses', 'terms', 'categories', 'customers', 'usersBranches'));
    }

    /**
     * Update the specified Estimate in storage.
     *
     * @param  Estimate  $estimate
     * @param  UpdateEstimateRequest  $request
     * @return JsonResponse
     */
    public function update(Estimate $estimate, UpdateEstimateRequest $request)
    {
        try {
            DB::beginTransaction();
            $input = $request->all();
            $input['total_amount'] = $input['total_amount_new'];

            $convert_to_invoice = 0;
            if ($estimate->status < 1 && $input['status'] == 1) {
                $nextNumber = DocumentNextNumber::getNextNumber('quotation');
                $input['estimate_number'] = $nextNumber;
                $convert_to_invoice = 1;
            }

            $estimate = $this->estimateRepository->update($input, $estimate);


            if ($convert_to_invoice) {
                DocumentNextNumber::updateNumber('quotation');
            }

            DB::commit();

            Flash::success(__('messages.estimate.estimate_updated_successfully'));

            return $this->sendResponse($estimate, __('messages.estimate.estimate_updated_successfully'));
        } catch (Exception $e) {
            DB::rollBack();

            return $this->sendError($e->getMessage());
        }
    }

    /**
     * Remove the specified Estimate from storage.
     *
     * @param  Estimate  $estimate
     * @return JsonResponse
     */
    public function destroy(Estimate $estimate)
    {
        try {
            DB::beginTransaction();
            $this->estimateRepository->deleteEstimate($estimate);
            DB::commit();

            return $this->sendSuccess('Estimate deleted successfully.');
        } catch (Exception $e) {
            DB::rollBack();

            return $this->sendError($e->getMessage());
        }
    }

    /**
     * @param  Estimate  $estimate
     * @param  Request  $request
     * @return mixed
     */
    public function changeStatus(Estimate $estimate, Request $request)
    {
        $this->estimateRepository->changeEstimateStatus($estimate->id, $request->get('status'));

        return $this->sendSuccess(__('messages.estimate.estimate_status_updated_successfully'));
    }

    /**
     * @param  Estimate  $estimate
     * @return Factory|View
     */
    public function viewAsCustomer(Estimate $estimate)
    {
        $estimate = $this->estimateRepository->getSyncForEstimateDetail($estimate->id);
        $totalPaid = 0;

        $settings = Setting::pluck('value', 'key')->toArray();

        return view('estimates.view_as_customer', compact('estimate', 'totalPaid', 'settings'));
    }

    public function convertToPdf(Estimate $estimate)
    {
        $estimate->load(['estimateAddresses', 'customer', 'branch', 'branch.bank']);

        $estimate = $this->estimateRepository->getSyncForEstimateDetail($estimate->id);
        $currency = Customer::CURRENCIES[$estimate->currency];
        $settings = Setting::all()->pluck('value', 'key')->toArray();

        $isRound = $settings['is_round'] ?? 0;
        $bankDetails = Bank::first();



        $subtotal = 0;
        $totalTaxable = 0;
        $totalVat = 0;
        // Loop through the sales items to calculate totals
        foreach ($estimate->salesItems as $item) {
            //$itemVATAmount = $itemSubtotalExcludingVAT * ($item->tax / 100);
            $subtotal += ($item->quantity * $item->rate);
            $totalTaxable += ($item->quantity * $item->rate) - $item->discount;
            $totalVat += (($item->quantity * $item->rate) - $item->discount) * .15;
        }

        $words = $this->amountToWords($estimate->total_amount);
        $wordsAr = $this->amountToWords($estimate->total_amount, 'ar');
        // Data to pass to the view

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

        $format = $estimate->branch->print_format ?? 1;
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

            'estimate' => $estimate,
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
        ];
        $mpdf->SetHTMLHeader('
            <header style="width: 100%; height: 110px;">
                <!-- Header Image -->
                <img src="' . $headerImage . '" style="width: 100%; height: 110px;">

                <!-- Additional Content Below the Image -->
                <div class="content-header" style="width: 100%; height: 1.80cm; margin: 0; position: relative;">
                    <div class="vat" style="display: inline-block; padding: 5px; font-size: 12pt; padding-left: 0.26cm; text-align: left; padding-top: 20px; width: 30%; float: left;">
                        Vat No. : 310973271700003
                    </div>
                    <div class="content_header_title" style="vertical-align: middle; margin-top: 10px; float: left; display: inline-block; width: 31%; height: 1.13cm; text-align: center; line-height: 40px; border: 1px solid #e2e2e2; background: #fff7f2;">
                        <table style="width: 100%; border-collapse: collapse; margin-top: 5px; margin-left: 5px;">
                            <tr>
                                <td style="text-align: left; font-size: 17pt;">
                                   Quotation
                                </td>
                                <td style="text-align: right; font-size: 17pt; padding-right: 10px;">
                                عرض السعر
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
        $html = view('estimates.estimate_pdf_arabic', $data)->render();
        $mpdf->WriteHTML($html);

        // Output the PDF
        return $mpdf->Output(__('messages.invoice.invoice_prefix') . $estimate->estimate_number . '.pdf', 'D');
    }



    /**
     * @param  Estimate  $estimate
     * @return JsonResponse
     */
    public function convertToInvoice(Estimate $estimate)
    {
        $invoice = $this->estimateRepository->convertToInvoice($estimate);

        return $this->sendResponse($invoice, __('messages.estimate.convert_estimate_to_invoice'));
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

    public function saveAddress(Request $request)
    {
        $input = $request->all(); // Get all the input data
        $estimateId = $request->input('estimate_id'); // Get the estimate ID
        return $this->estimateRepository->addEstimateAddresses($input, $estimateId);
    }
}
