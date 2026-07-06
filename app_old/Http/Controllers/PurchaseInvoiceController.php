<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreatePurchaseOrderRequest;
use App\Http\Requests\UpdatePurchaseInvoiceRequest;
use App\Models\PurchaseInvoice;
use App\Queries\PurchaseInvoiceDatatable;
use App\Models\Estimate;
use App\Models\Setting;
use App\Models\Task;
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
use App\Http\Requests\CreatePurchaseInvoiceRequest;
use App\Repositories\PurchaseInvoiceRepository;
use File;

class PurchaseInvoiceController extends AppBaseController
{

    private $purchaseInvoiceRepository;
    private $purchaseItemRepository;

    public function __construct(PurchaseInvoiceRepository $purchaseInvoiceRepo, PurchaseItemRepository $purchaseItemRepo)
    {
        $this->purchaseInvoiceRepository = $purchaseInvoiceRepo;
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
            return DataTables::of((new PurchaseInvoiceDatatable())->get($request->all()))->make(true);
        }
        return view('purchase-invoices.index', compact('statusArr'));
    }

    /**
     * Show the form for creating a new Estimate.
     *
     * @param  null  $customerId
     * @return Factory|View
     */
    public function create($customerId = null)
    {
        $data = $this->purchaseInvoiceRepository->getSyncList();

        $settings = Setting::pluck('value', 'key');
        $services = $data['items'];
        $categories = $this->purchaseInvoiceRepository->getItemCategories();
        $terms = $this->purchaseInvoiceRepository->getTerms();
        $nextNumber = DocumentNextNumber::getNextNumber('purchase_invoices');

        $groups = $this->purchaseItemRepository->getGroups();
        $purchaseCategories = $this->purchaseItemRepository->getCategories();
        $subcategories = $this->purchaseItemRepository->getSubCategories();
        $units = $this->purchaseItemRepository->getUnits();
        $paymentModes = $this->purchaseItemRepository->getPaymentModes();
        $usersBranches = $this->purchaseInvoiceRepository->getUsersBranches();
        return view('purchase-invoices.create', compact('data', 'services', 'customerId', 'settings', 'nextNumber', 'terms', 'categories', 'groups', 'purchaseCategories', 'subcategories', 'units', 'paymentModes', 'usersBranches'));
    }

    public function store(CreatePurchaseInvoiceRequest $request)
    {
        try {
            DB::beginTransaction();
            $input = $request->all();
            // dd($input);
            // if (array_sum($input['quantity']) > 9999999) {
            //     return $this->sendError(__('messages.common.quantity_is_not_greater_than'));
            // }
            $estimate = $this->purchaseInvoiceRepository->saveInvoice($input);
            DB::commit();
            DocumentNextNumber::updateNumber('purchase_invoices');
            Flash::success(__('messages.purchase-orders.saved'));
            return $this->sendSuccess(__('messages.purchase-orders.saved'));
            // Redirect to a route or URL with a success message
            // return redirect()->route('purchase-orders.index')->with('success', __('messages.purchase.estimate_saved_successfully'));
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
    public function show(PurchaseInvoice $invoice)
    {
        $invoice->load(['terms', 'customer', 'customer.supplierCountry']);
        $estimate = $this->purchaseInvoiceRepository->getSyncForEstimateDetail($invoice->id);

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
        $usersBranches = $this->purchaseInvoiceRepository->getUsersBranches();
        // dd($estimate->toArray());
        return view("purchase-invoices.views.$groupName", compact('estimate', 'status', 'priorities', 'groupName', 'totalIncludingVAT', 'totalExcludingVAT', 'totalVATAmount', 'totalDiscount', 'totalNetAmount', 'afterDiscount', 'words', 'newVat', 'newTotal', 'usersBranches'));
    }

    /**
     * Show the form for editing the specified Estimate.
     *
     * @param  Estimate  $estimate
     * @return Application|Factory|\Illuminate\Contracts\View\View|RedirectResponse
     */
    public function edit(PurchaseInvoice $invoice)
    {


        $estimate = $invoice->load(['terms', 'salesItems', 'salesItems.service', 'salesItems.service.unit', 'documents']);


        $customer = $invoice->customer;

        if ($customer) {
            $customer->supplierGroups = '';

            if (!empty($customer->groups) && $customer->groups->isNotEmpty()) {
                $customer->supplierGroups = $customer->groups->pluck('group.name')->implode(', ');
            }
        }

        $data = $this->purchaseInvoiceRepository->getSyncList();



        $addresses = [];
        $services = $data['items'];

        foreach ($estimate->estimateAddresses as $index => $address) {
            $addresses[$index] = $address;
        }
        $terms = $this->purchaseInvoiceRepository->getTerms();
        $categories = $this->purchaseInvoiceRepository->getItemCategories();

        $groups = $this->purchaseItemRepository->getGroups();
        $purchaseCategories = $this->purchaseItemRepository->getCategories();
        $subcategories = $this->purchaseItemRepository->getSubCategories();
        $units = $this->purchaseItemRepository->getUnits();
        $paymentModes = $this->purchaseItemRepository->getPaymentModes();
        $usersBranches = $this->purchaseInvoiceRepository->getUsersBranches();
        return view('purchase-invoices.edit', compact('customer', 'data', 'services', 'estimate', 'addresses', 'terms', 'categories', 'groups', 'purchaseCategories', 'subcategories', 'units', 'paymentModes', 'usersBranches'));
    }


    public function update(PurchaseInvoice $invoice, UpdatePurchaseInvoiceRequest $request)
    {
        // try {
        DB::beginTransaction();
        $input = $request->all();


        $estimate = $this->purchaseInvoiceRepository->updateInvoice($input, $invoice->id);

        DB::commit();

        Flash::success(__('messages.purchase-orders.saved'));

        return $this->sendSuccess(__('messages.purchase-orders.saved'));
        // } catch (Exception $e) {
        //     DB::rollBack();

        //     return redirect()->route('purchase-orders.index')->with('success', __('messages.purchase.estimate_saved_successfully'));
        // }
    }

    /**
     * Remove the specified Estimate from storage.
     *
     * @param  Estimate  $estimate
     * @return JsonResponse
     */
    public function destroy(PurchaseInvoice $invoice)
    {



        try {


            if ($invoice->returns()->exists()) {
                return $this->sendError('Cannot delete: This invoice has associated purchase returns .');
            }

            DB::beginTransaction();

            $documents = DB::table('purchase_invoice_docs')
                ->where('purchase_invoice_id', $invoice->id)
                ->get();

            // Delete related documents and files from public storage
            foreach ($documents as $document) {
                // Construct the full file path
                $filePath = public_path("uploads/public/purchase_invoice_docs/" . $document->file);
                // Delete file from public folder if it exists
                if (File::exists($filePath)) {
                    File::delete($filePath);
                }
                // Delete document record from the database
                DB::table('purchase_invoice_docs')
                    ->where('id', $document->id)
                    ->delete();
            }


            $this->purchaseInvoiceRepository->deleteInvoice($invoice);
            DB::commit();
            return $this->sendSuccess('Purchase Invoice deleted successfully.');
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
        $this->purchaseInvoiceRepository->changeEstimateStatus($estimate->id, $request->get('status'));

        return $this->sendSuccess(__('messages.estimate.estimate_status_updated_successfully'));
    }

    /**
     * @param  Estimate  $estimate
     * @return Factory|View
     */
    public function viewAsCustomer(Estimate $estimate)
    {
        $estimate = $this->purchaseInvoiceRepository->getSyncForEstimateDetail($estimate->id);
        $totalPaid = 0;

        $settings = Setting::pluck('value', 'key')->toArray();

        return view('purchase-invoices.view_as_customer', compact('estimate', 'totalPaid', 'settings'));
    }

    /**
     * @param  Estimate  $estimate
     * @return mixed
     */
    public function convertToPdf(PurchaseInvoice $invoice)
    {

        $estimate = $this->purchaseInvoiceRepository->getSyncForEstimateDetail($invoice->id);



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
            'currency' => '',
            'afterDiscount' => $afterDiscount,
            'newVat' => $newVat,
            'newTotal' => $newTotal
        ];
        // Load the view and pass data
        $pdf = PDF::loadView('purchase-invoices.estimate_pdf_new', $data);
        $pdf->setPaper('A4', 'portrait');
        return $pdf->download("PO-" . $estimate->estimate_number . '.pdf');
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
        return $this->purchaseInvoiceRepository->addEstimateAddresses($input, $estimateId);
    }

    function file_delete($id)
    {

        $employee = $this->purchaseInvoiceRepository->delete_file($id);
        activity()->performedOn($employee)->causedBy(getLoggedInUser())
            ->useLog('Purchase Invoice File Deleted')->log(' Purchase Invoice  File deleted.');
        return $this->sendSuccess(__('messages.employees.delete_file'));
    }
}
