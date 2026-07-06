<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdatePurchaseReturnRequest;
use App\Models\PurchaseInvoice;
use App\Queries\PurchaseReturnDatatable;
use App\Models\Estimate;
use App\Models\Setting;
use App\Models\Task;
use App\Repositories\PurchaseItemRepository;
use Barryvdh\DomPDF\Facade as PDF;
use Exception;
use File;
use Flash;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use App\Models\DocumentNextNumber;
use Yajra\DataTables\DataTables;
use App\Http\Requests\CreatePurchaseReturnRequest;
use App\Repositories\PurchaseReturnRepository;
use App\Models\PurchaseReturn;

class PurchaseReturnController extends AppBaseController
{

    private $purchaseReturnRepository;
    private $purchaseItemRepository;

    public function __construct(PurchaseReturnRepository $purchaseReturnRepo, PurchaseItemRepository $purchaseItemRepo)
    {
        $this->purchaseReturnRepository = $purchaseReturnRepo;
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
            return DataTables::of((new PurchaseReturnDatatable())->get($request->all()))->make(true);
        }
        return view('purchase-returns.index', compact('statusArr'));
    }

    /**
     * Show the form for creating a new Estimate.
     *
     * @param  null  $customerId
     * @return Factory|View
     */
    public function create($customerId = null)
    {
        $data = $this->purchaseReturnRepository->getSyncList();

        $settings = Setting::pluck('value', 'key');
        $services = $data['items'];
        $categories = $this->purchaseReturnRepository->getItemCategories();
        $terms = $this->purchaseReturnRepository->getTerms();
        $nextNumber = DocumentNextNumber::getNextNumber('purchase_returns');

        $groups = $this->purchaseItemRepository->getGroups();
        $purchaseCategories = $this->purchaseItemRepository->getCategories();
        $subcategories = $this->purchaseItemRepository->getSubCategories();
        $units = $this->purchaseItemRepository->getUnits();
        $userBranches = $this->purchaseReturnRepository->getUsersBranches();
        return view('purchase-returns.create', compact('data', 'services', 'customerId', 'settings', 'nextNumber', 'terms', 'categories', 'groups', 'purchaseCategories', 'subcategories', 'units', 'userBranches'));
    }

    public function store(CreatePurchaseReturnRequest $request)
    {
        try {
            DB::beginTransaction();
            $input = $request->all();

            // if (array_sum($input['quantity']) > 9999999) {
            //     return $this->sendError(__('messages.common.quantity_is_not_greater_than'));
            // }
            $estimate = $this->purchaseReturnRepository->saveInvoice($input);
            DB::commit();
            DocumentNextNumber::updateNumber('purchase_returns');
            Flash::success(__('messages.purchase-returns.saved'));
            return $this->sendSuccess(__('messages.purchase-returns.saved'));
            // Redirect to a route or URL with a success message
            // return redirect()->route('purchase-orders.index')->with('success', __('messages.purchase.estimate_saved_successfully'));
        } catch (Exception $e) {
            DB::rollBack();
            return $this->sendError($e->getMessage());
        }
    }

    public function getInvoice($invoice)
    {

        $invoice = PurchaseInvoice::with((['customer', 'customer.supplierCountry', 'salesItems', 'terms']))->where('estimate_number', $invoice)->first();
        return $this->sendResponse($invoice, 'Invoice retrieved successfully');
    }


    /**
     * Display the specified Estimate.
     *
     * @param  Estimate  $estimate
     * @return Factory|View
     */
    public function show(PurchaseReturn $return)
    {

        $return->load(['terms', 'customer', 'customer.supplierCountry']);
        $estimate = $this->purchaseReturnRepository->getSyncForEstimateDetail($return->id);

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
        $userBranches = $this->purchaseReturnRepository->getUsersBranches();
        // dd($estimate->toArray());
        return view("purchase-returns.views.$groupName", compact('estimate', 'status', 'priorities', 'groupName', 'totalIncludingVAT', 'totalExcludingVAT', 'totalVATAmount', 'totalDiscount', 'totalNetAmount', 'afterDiscount', 'words', 'newVat', 'newTotal', 'userBranches'));
    }

    /**
     * Show the form for editing the specified Estimate.
     *
     * @param  Estimate  $estimate
     * @return Application|Factory|\Illuminate\Contracts\View\View|RedirectResponse
     */
    public function edit(PurchaseReturn $return)
    {


        $estimate = $return->load(['terms', 'salesItems', 'salesItems.service', 'salesItems.service.unit']);

        $customer = $estimate->customer;
        // $customer->supplierGroups = '';


        // if ($customer->groups && $customer->groups->isNotEmpty()) {
        //     $customer->supplierGroups = $customer->groups->pluck('group.name')->implode(', ');
        // }
        //    dd($estimate->toArray());
        $customer = $estimate->customer;

        $data = $this->purchaseReturnRepository->getSyncList();



        $addresses = [];
        $services = $data['items'];

        foreach ($estimate->estimateAddresses as $index => $address) {
            $addresses[$index] = $address;
        }
        $terms = $this->purchaseReturnRepository->getTerms();

        $categories = $this->purchaseReturnRepository->getItemCategories();

        $groups = $this->purchaseItemRepository->getGroups();
        $purchaseCategories = $this->purchaseItemRepository->getCategories();
        $subcategories = $this->purchaseItemRepository->getSubCategories();
        $units = $this->purchaseItemRepository->getUnits();
        $userBranches = $this->purchaseReturnRepository->getUsersBranches();

        $estimate = $return;
        return view('purchase-returns.edit', compact('customer', 'estimate', 'data', 'services', 'estimate', 'addresses', 'terms', 'categories', 'groups', 'purchaseCategories', 'subcategories', 'units', 'userBranches'));
    }


    public function update(PurchaseReturn $return, UpdatePurchaseReturnRequest $request)
    {
        try {
            DB::beginTransaction();
            $input = $request->all();
            $estimate = $this->purchaseReturnRepository->updateInvoice($input, $return->id);
            DB::commit();
            Flash::success(__('messages.purchase-returns.saved'));
            return $this->sendSuccess(__('messages.purchase-returns.saved'));
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->route('purchase-orders.index')->with('success', __('messages.purchase.estimate_saved_successfully'));
        }
    }

    /**
     * Remove the specified Estimate from storage.
     *
     * @param  Estimate  $estimate
     * @return JsonResponse
     */
    public function destroy(PurchaseReturn $return)
    {
        try {
            DB::beginTransaction();
            $status = $this->purchaseReturnRepository->deleteInvoice($return);


            $documents = DB::table('purchase_return_docs')
                ->where('purchase_return_id', $return->id)
                ->get();

            // Delete related documents and files from public storage
            foreach ($documents as $document) {
                // Construct the full file path
                $filePath = public_path("uploads/public/purchase_return_docs/" . $document->file);
                // Delete file from public folder if it exists
                if (File::exists($filePath)) {
                    File::delete($filePath);
                }
                // Delete document record from the database
                DB::table('purchase_return_docs')
                    ->where('id', $document->id)
                    ->delete();
            }

            DB::commit();
            return $this->sendSuccess('Purchase Return deleted successfully.');
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
        $this->purchaseReturnRepository->changeEstimateStatus($estimate->id, $request->get('status'));

        return $this->sendSuccess(__('messages.estimate.estimate_status_updated_successfully'));
    }

    /**
     * @param  Estimate  $estimate
     * @return Factory|View
     */
    public function viewAsCustomer(Estimate $estimate)
    {
        $estimate = $this->purchaseReturnRepository->getSyncForEstimateDetail($estimate->id);
        $totalPaid = 0;

        $settings = Setting::pluck('value', 'key')->toArray();

        return view('purchase-returns.view_as_customer', compact('estimate', 'totalPaid', 'settings'));
    }

    /**
     * @param  Estimate  $estimate
     * @return mixed
     */
    public function convertToPdf(PurchaseReturn $return)
    {

        $estimate = $this->purchaseReturnRepository->getSyncForEstimateDetail($return->id);



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
        $pdf = PDF::loadView('purchase-returns.estimate_pdf_new', $data);
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
        return $this->purchaseReturnRepository->addEstimateAddresses($input, $estimateId);
    }

    function file_delete($id)
    {

        $employee = $this->purchaseReturnRepository->delete_file($id);
        activity()->performedOn($employee)->causedBy(getLoggedInUser())
            ->useLog('Purchase Return File Deleted')->log(' Purchase Return File deleted.');
        return $this->sendSuccess(__('messages.employees.delete_file'));
    }
}
