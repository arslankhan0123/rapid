<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateOpeningStockRequest;
use App\Http\Requests\UpdateOpeningStockRequest;
use App\Queries\OpeningStockDatatable;
use App\Models\Estimate;
use App\Models\Setting;
use App\Models\Task;
use App\Repositories\OpeningStockRepository;
use App\Repositories\PurchaseItemRepository;
use Barryvdh\DomPDF\Facade as PDF;
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
use Yajra\DataTables\DataTables;
use App\Models\PurchaseOrder;
use App\Models\OpeningStock;
use App\Models\PurchaseItem;
class OpeningStockController extends AppBaseController
{

    private $purchaseOrderRepository;
    private $purchaseItemRepository;

    public function __construct(OpeningStockRepository $purchaseOrderRepo, PurchaseItemRepository $purchaseItemRepo)
    {
        $this->purchaseOrderRepository = $purchaseOrderRepo;
        $this->purchaseItemRepository = $purchaseItemRepo;
    }

    /**
     * Display a listing of the Estimate.
     *
     * @return Factory|View
     */
    public function index(Request $request)
    {



        $statusArr = Estimate::STATUS;
        if ($request->ajax()) {
            return DataTables::of((new OpeningStockDatatable())->get($request->all()))->make(true);
        }
        return view('opening-stocks.index', compact('statusArr'));
    }

    public function getAllItems(Request $request)
    {

        $query = $request->input('term'); // Get search term from AJAX request

        $items = PurchaseItem::with(['unit'])->where('name', 'LIKE', "%{$query}%")
            ->orWhere('barcode', 'LIKE', "%{$query}%")
            ->orWhere('code', 'LIKE', "%{$query}%")
            ->limit(20)
            ->get();

        // dd($items->toArray());
        // return response()->json($items);

        return response()
            ->json($items)
            ->header('Cache-Control', 'public, max-age=600') // cache for 10 minutes
            ->header('Expires', now()->addMinutes(10)->toRfc7231String());
    }

    /**
     * Show the form for creating a new Estimate.
     *
     * @param  null  $customerId
     * @return Factory|View
     */
    public function create($customerId = null)
    {
        $data = $this->purchaseOrderRepository->getSyncList();

        $settings = Setting::pluck('value', 'key');
        $services = $data['items'];
        $categories = $this->purchaseOrderRepository->getItemCategories();

        $terms = $this->purchaseOrderRepository->getTerms();

        // dd($terms->toArray());
        $nextNumber = DocumentNextNumber::getNextNumber('opening_stock');

        $groups = $this->purchaseItemRepository->getGroups();
        $purchaseCategories = $this->purchaseItemRepository->getCategories();
        $subcategories = $this->purchaseItemRepository->getSubCategories();
        $units = $this->purchaseItemRepository->getUnits();
        $paymentModes = $this->purchaseItemRepository->getPaymentModes();

        return view('opening-stocks.create', compact('data', 'services', 'customerId', 'settings', 'nextNumber', 'terms', 'categories', 'groups', 'purchaseCategories', 'subcategories', 'units', 'paymentModes'));
    }



    public function store(CreateOpeningStockRequest $request)
    {

        try {
            DB::beginTransaction();
            $input = $request->all();
            $estimate = $this->purchaseOrderRepository->store($input);
            DocumentNextNumber::updateNumber('opening_stock');
            DB::commit();

            Flash::success(__('messages.opening-stocks.saved'));
            return $this->sendSuccess(__('messages.opening-stocks.saved'));
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
    public function show(OpeningStock $stock)
    {
        $stock->load(['terms']);

        $estimate = $this->purchaseOrderRepository->getSyncForEstimateDetail($stock->id);



        // Calculate totals
        $totalExcludingVAT = 0;
        $totalIncludingVAT = 0;
        $totalVATAmount = 0;
        $totalDiscount = $estimate->discount ?? 0;
        $totalNetAmount = 0;
        $afterDiscount = 0;
        $discountAmount = 0;
        // Loop through the sales items to calculate totals
        foreach ($estimate->salesItems as $item) {
            $itemTotalExcludingVAT = ($item->quantity * $item->rate) * (1 - ($item->discount / 100)); // Apply discount %
            $itemVATAmount = $itemTotalExcludingVAT * ($item->tax / 100);
            $totalExcludingVAT += $itemTotalExcludingVAT;
            $totalVATAmount += $itemVATAmount;
            $totalNetAmount += $itemTotalExcludingVAT + $itemVATAmount;
            $totalIncludingVAT += ($item->quantity * $item->rate) * (1 - ($item->discount / 100))
                + $itemVATAmount;
        }

        if (isset($estimate->discount_type) && $estimate->discount_type == 0) {

            $discountAmount = ($totalDiscount / 100) * $totalIncludingVAT;
        } else {
            $discountAmount = $estimate->discount;
        }

        $afterDiscount = $totalIncludingVAT - $discountAmount;
        $totalNetAmount -= $totalDiscount; // Apply additional discount
        $newVat = $afterDiscount * 0.15;
        $newTotal = $afterDiscount;


        $words = $this->amountToWords($totalNetAmount);

        $status = Task::STATUS;
        $priorities = Task::PRIORITY;
        $groupName = (request('group') == null) ? 'estimate_details' : (request('group'));
        // dd($estimate->toArray());
        return view("opening-stocks.views.$groupName", compact('estimate', 'status', 'priorities', 'groupName', 'totalIncludingVAT', 'totalExcludingVAT', 'totalVATAmount', 'totalDiscount', 'totalNetAmount', 'afterDiscount', 'words', 'newVat', 'newTotal'));
    }

    /**
     * Show the form for editing the specified Estimate.
     *
     * @param  Estimate  $estimate
     * @return Application|Factory|\Illuminate\Contracts\View\View|RedirectResponse
     */
    public function edit(OpeningStock $stock)
    {


        $stock->load(['salesItems', 'customer', 'customer.supplierCountry', 'customer.supplierState', 'customer.groups', 'customer.groups.group']);

        $customer = $stock->customer;

        if ($customer) {
            $customer->supplierGroups = '';

            if (!empty($customer->groups) && $customer->groups->isNotEmpty()) {
                $customer->supplierGroups = $customer->groups->pluck('group.name')->implode(', ');
            }
        }


        $estimate = OpeningStock::with(['terms', 'salesItems', 'salesItems.service', 'salesItems.service.unit'])->findOrFail($stock->id);

        //    dd($estimate->toArray());


        $data = $this->purchaseOrderRepository->getSyncList();



        $addresses = [];
        $services = $data['items'];

        foreach ($estimate->estimateAddresses as $index => $address) {
            $addresses[$index] = $address;
        }
        $terms = $this->purchaseOrderRepository->getTerms();
        $categories = $this->purchaseOrderRepository->getItemCategories();

        $groups = $this->purchaseItemRepository->getGroups();
        $purchaseCategories = $this->purchaseItemRepository->getCategories();
        $subcategories = $this->purchaseItemRepository->getSubCategories();
        $units = $this->purchaseItemRepository->getUnits();
        $paymentModes = $this->purchaseItemRepository->getPaymentModes();

        return view('opening-stocks.edit', compact('customer', 'data', 'services', 'estimate', 'addresses', 'terms', 'categories', 'groups', 'purchaseCategories', 'subcategories', 'units', 'paymentModes'));
    }


    public function update(OpeningStock $stock, UpdateOpeningStockRequest $request)
    {
        try {
            DB::beginTransaction();
            $input = $request->all();

            // if (array_sum($input['quantity']) > 9999999) {
            //     return $this->sendError(__('messages.common.quantity_is_not_greater_than'));
            // }

            $estimate = $this->purchaseOrderRepository->update($input, $stock);
            DB::commit();

            Flash::success(__('messages.opening-stocks.saved'));

            return $this->sendSuccess(__('messages.opening-stocks.saved'));
        } catch (Exception $e) {
            DB::rollBack();

            return redirect()->route('opening-stocks.index')->with('success', __('messages.purchase.estimate_saved_successfully'));
        }
    }

    /**
     * Remove the specified Estimate from storage.
     *
     * @param  Estimate  $estimate
     * @return JsonResponse
     */
    public function destroy(OpeningStock $stock)
    {
        try {
            DB::beginTransaction();
            $this->purchaseOrderRepository->deleteEstimate($stock);
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
        $this->purchaseOrderRepository->changeEstimateStatus($estimate->id, $request->get('status'));

        return $this->sendSuccess(__('messages.estimate.estimate_status_updated_successfully'));
    }

    /**
     * @param  Estimate  $estimate
     * @return Factory|View
     */
    public function viewAsCustomer(Estimate $estimate)
    {
        $estimate = $this->purchaseOrderRepository->getSyncForEstimateDetail($estimate->id);
        $totalPaid = 0;

        $settings = Setting::pluck('value', 'key')->toArray();

        return view('opening-stocks.view_as_customer', compact('estimate', 'totalPaid', 'settings'));
    }

    /**
     * @param  Estimate  $estimate
     * @return mixed
     */
    public function convertToPdf(OpeningStock $stock)
    {

        $stock->load(['customer']);
        $estimate = $this->purchaseOrderRepository->getSyncForEstimateDetail($stock->id);
        $currency = "SAR";

        // Calculate totals
        $totalExcludingVAT = 0;
        $totalIncludingVAT = 0;
        $totalVATAmount = 0;
        $totalDiscount = $estimate->discount ?? 0;
        $totalNetAmount = 0;
        $afterDiscount = 0;
        $discountAmount = 0;
        // Loop through the sales items to calculate totals
        foreach ($estimate->salesItems as $item) {
            $itemTotalExcludingVAT = ($item->quantity * $item->rate) * (1 - ($item->discount / 100)); // Apply discount %
            $itemVATAmount = $itemTotalExcludingVAT * ($item->tax / 100);
            $totalExcludingVAT += $itemTotalExcludingVAT;
            $totalVATAmount += $itemVATAmount;
            $totalNetAmount += $itemTotalExcludingVAT + $itemVATAmount;
            $totalIncludingVAT += ($item->quantity * $item->rate) * (1 - ($item->discount / 100))
                + $itemVATAmount;
        }

        if (isset($estimate->discount_type) && $estimate->discount_type == 0) {

            $discountAmount = ($totalDiscount / 100) * $totalIncludingVAT;
        } else {
            $discountAmount = $estimate->discount;
        }

        $afterDiscount = $totalIncludingVAT - $discountAmount;
        $totalNetAmount -= $totalDiscount; // Apply additional discount
        $newVat = $afterDiscount * 0.15;
        $newTotal = $afterDiscount;


        $words = $this->amountToWords($totalNetAmount);

        $totalPaid = 0;


        $settings = Setting::pluck('value', 'key')->toArray();

        $data = [
            'estimate' => $estimate,
            'settings' => $settings,
            'totalPaid' => $estimate->total_amount,
            'totalIncludingVAT' => $totalIncludingVAT,
            'totalExcludingVAT' => $totalExcludingVAT,
            'totalVATAmount' => $totalVATAmount,
            'totalDiscount' => $totalDiscount,
            'totalNetAmount' => $totalNetAmount,
            'words' => $words,
            'currency' => $currency,
            'afterDiscount' => $afterDiscount,
            'newVat' => $newVat,
            'newTotal' => $newTotal
        ];
        // Load the view and pass data
        $pdf = PDF::loadView('opening-stocks.estimate_pdf_new', $data);
        $pdf->setPaper('A4', 'portrait');
        return $pdf->download("PO-" . $estimate->estimate_number . '.pdf');
    }

    /**
     * @param  Estimate  $estimate
     * @return JsonResponse
     */
    public function convertToInvoice(Estimate $estimate)
    {
        $invoice = $this->purchaseOrderRepository->convertToInvoice($estimate);

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
        return $this->purchaseOrderRepository->addEstimateAddresses($input, $estimateId);
    }
}
