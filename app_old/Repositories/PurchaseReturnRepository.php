<?php

namespace App\Repositories;

use App\Models\Branch;
use App\Models\Contact;
use App\Models\CreditNote;
use App\Models\Customer;
use App\Models\Estimate;
use App\Models\Invoice;
use App\Models\InvoiceAddress;
use App\Models\Item;
use App\Models\Note;
use App\Models\Notification;
use App\Models\PaymentMode;
use App\Models\PurchaseReturnDoc;
use App\Models\SalesItem;
use App\Models\SalesTax;
use App\Models\Tag;
use App\Models\TaxRate;
use App\Models\User;
use Auth;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use App\Models\Project;
use App\Models\Term;
use App\Models\InvoiceTerm;
use App\Models\ServiceCategory;
use App\Models\ProjectTerm;
use App\Models\PurchasedItem;
use App\Models\PurchaseOrderTerm;
use App\Models\PurchaseCategory;
use App\Models\Supplier;
use App\Models\PurchaseItem;
use App\Models\PurchaseReturn;
use App\Models\PurchaseReturnTerm;
use Illuminate\Support\Facades\DB;



/**
 * Class InvoiceRepository
 *
 * @version April 8, 2020, 11:32 am UTC
 */
class PurchaseReturnRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'customer_id',
        'title',
        'bill_to',
        'ship_to',
        'return_number',
        'estimate_date',
        'due_date',
        'sales_agent_id',
        'currency',
        'discount_type',
        'admin_text',
        'po_number',
        'payment_type',
        'due_days',
        'branch_id',
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
        return PurchaseReturn::class;
    }

    /**
     * @param  null  $customerId
     * @return mixed
     */
    public function getInvoicesStatusCount($customerId = null)
    {
        if (!empty($customerId)) {
            return Invoice::selectRaw('count(case when payment_status = 0 then 1 end) as drafted')
                ->selectRaw('count(case when payment_status = 1 then 1 end) as unpaid')
                ->selectRaw('count(case when payment_status = 2 then 1 end) as paid')
                ->selectRaw('count(case when payment_status = 3 then 1 end) as partially_paid')
                ->selectRaw('count(case when payment_status = 4 then 1 end) as cancelled')
                ->selectRaw('count(case when payment_status != 0 then 1 end) as total_invoices')
                ->where('customer_id', '=', $customerId)->first();
        }

        return Invoice::selectRaw('count(case when payment_status = 0 then 1 end) as drafted')
            ->selectRaw('count(case when payment_status = 1 then 1 end) as unpaid')
            ->selectRaw('count(case when payment_status = 2 then 1 end) as paid')
            ->selectRaw('count(case when payment_status = 3 then 1 end) as partially_paid')
            ->selectRaw('count(case when payment_status = 4 then 1 end) as cancelled')
            ->selectRaw('count(*) as total_invoices')
            ->first();
    }

    /**
     * @return array
     */
    public function getDiscountTypes()
    {
        return $discountType = [
            '0' => 'No Discount',
            '1' => 'Before Tax',
            '2' => 'After Tax',
        ];
    }

    /**
     * @return mixed
     */
    public function getSyncList()
    {
        $data['customers'] = Supplier::with(['supplierCountry', 'supplierState', 'groups', 'groups.group'])->orderBy('updated_at', 'desc')->orderBy('updated_at', 'desc')->get()->toArray();
        $data['tags'] = Tag::orderBy('name', 'asc')->pluck('name', 'id')->toArray();
        $data['saleAgents'] = User::orderBy('first_name', 'asc')->whereIsEnable(true)->user()->get()->pluck(
            'full_name',
            'id'
        )->toArray();
        $data['discountType'] = Estimate::DISCOUNT_TYPES;
        $data['status'] = Estimate::STATUS;
        $data['currencies'] = Supplier::CURRENCIES;
        $taxRates = TaxRate::where('tax_rate', 15)->orderBy('tax_rate', 'asc')->get();
        $data['taxes'] = $taxRates;
        $data['taxesArr'] = $taxRates->pluck('tax_rate', 'id')->toArray();
        $data['items'] = PurchaseItem::with(['unit'])->orderBy('name', 'asc')->get();

        return $data;
    }

    public function getTerms()
    {
        return Term::pluck('terms', 'id');
    }

    public function getServiceCategories()
    {
        return ServiceCategory::pluck('name', 'id');
    }

    public function getUsersBranches()
    {
        return Branch::with(['UsersBranches', 'UsersBranches.branch'])
            ->whereHas('UsersBranches', function ($query) {
                $query->where('user_id', getLoggedInUserId());
            })
            ->pluck('name', 'id');
    }

    public function getProjects()
    {
        return Project::with('services', 'terms')->get();
    }
    public function getItemCategories()
    {
        return PurchaseCategory::pluck('name', 'id');
    }

    /**
     * @param  array  $input
     * @return Invoice
     */
    public function saveInvoice($input)
    {


        $invoice = $this->create($this->prepareInvoiceData($input));
        $users = User::whereId($invoice->sales_agent_id)->get();

        activity()->performedOn($invoice)->causedBy(getLoggedInUser())
            ->useLog('New Invoice created.')->log($invoice->title . ' Invoice created.');

        if (isset($input['tags']) && !empty($input['tags'])) {
            $invoice->tags()->sync($input['tags']);
        }

        $this->storeSalesItems($input, $invoice);
        $this->uploadFiles($input, $invoice->id);

        $this->storeTerms($invoice->id, $input);
        return $invoice;
    }

    /**
     * @param  array  $input
     * @param  null|Invoice|CreditNote|Estimate  $owner
     * @return bool
     */
    public function storeSalesTaxes($input, $owner = null)
    {
        $owner->salesTaxes()->delete();


        if (!empty($input['taxes'])) {
            foreach ($input['taxes'] as $tax => $amount) {
                SalesTax::create([
                    'owner_id' => $owner->getId(),
                    'owner_type' => $owner->getOwnerType(),
                    'tax' => $tax,
                    'amount' => formatNumber($amount),
                ]);
            }
        }

        return true;
    }
    public function storeTerms($id, $input)
    {



        // Retrieve terms and descriptions from the input
        $terms = $input['terms'] ?? [];
        $descriptions = $input['terms_description'] ?? [];

        // Filter out any null or empty terms and ensure they are properly indexed
        $terms = array_filter($terms, function ($term) {
            return !is_null($term) && $term !== '';
        });

        // If the terms are still empty after filtering, return early
        if (empty($terms)) {
            return;
        }


        // First, delete existing terms with the given estimate_id
        PurchaseReturnTerm::where('purchase_return_id', $id)->delete();

        // Prepare data for insertion
        $estimateTerms = [];
        $count = count($terms);  // Use the filtered count of terms

        // Loop through the terms and descriptions
        for ($i = 0; $i < $count; $i++) {
            $term = $terms[$i] ?? null;
            $description = $descriptions[$i] ?? null;

            // Skip if both term and description are empty or null
            if (empty($term) && empty($description)) {
                continue;
            }

            $estimateTerms[] = [
                'purchase_return_id' => $id,
                'terms_id' => $term, // Get terms_id from the terms array
                'description' => $description, // Get description, if available
                'created_at' => now(), // Set the created_at timestamp
                'updated_at' => now(), // Set the updated_at timestamp
            ];
        }

        // Insert new terms if there are valid entries
        if (!empty($estimateTerms)) {
            PurchaseReturnTerm::insert($estimateTerms);
        }
    }

    /**
     * @param  array  $input
     * @param  Invoice|CreditNote|Estimate  $owner
     * @return bool
     */
    public function storeSalesItems($input, $owner)
    {



        $input = $this->sanitizeInput($input);
        $owner->salesItems()->delete();


        foreach ($input['itemsArr'] as $record) {
            if (empty($record['service_id'])) {
                continue;
            }
            $data['owner_id'] = $owner->getId();
            $data['owner_type'] = $owner->getOwnerType();
            $data['service_id'] = $record['service_id'] ?? null;
            $data['category_id'] = $record['category_id'] ?? null;
            $data['item'] = $record['item'] ?? ' ';
            $data['description'] = $record['description'] ?? ' ';
            $data['rate'] = $record['rate'] ?? 0;
            $data['tax'] = $record['tax'] ?? 0;
            $data['total'] = $record['total'] ?? 0;
            $data = array_merge($record, $data);


            $salesItem = PurchasedItem::create($data);



            if (isset($record['service_id']) && isset($record['quantity'])) {
                $itemId = $record['service_id'];
                $qty = $record['quantity'];

                if ($data['owner_type'] == "App\Models\PurchaseInvoice") {

                    $this->updatePurchaseItemInventory($itemId, $qty, true);
                } else if ($data['owner_type'] == "App\Models\PurchaseReturn") {
                    $this->updatePurchaseItemInventory($itemId, $qty, false);
                }
            }

            $data = [];
        }

        return true;
    }


    public function getSyncForEstimateDetail($id)
    {
        $estimate = PurchaseReturn::with([
            'customer',
            'salesItems',
            'salesItems.service',
            'customer.supplierCountry',
        ])->find($id);

        return $estimate;
    }
    private function sanitizeInput($data)
    {
        if (is_array($data)) {
            $sanitized = [];
            foreach ($data as $key => $value) {
                // Remove extra quotes from keys
                $cleanKey = trim($key, "'");
                $sanitized[$cleanKey] = $this->sanitizeInput($value);
            }
            return $sanitized;
        }
        return $data;
    }

    /**
     * @param  array  $input
     * @return array
     */
    public function prepareInvoiceData($input)
    {
        $invoiceFields = (new PurchaseReturn())->getFillable();
        $items = [];

        foreach ($input as $key => $value) {
            if (in_array($key, $invoiceFields)) {
                $items[$key] = $value;
            }
        }

        $items['total_amount'] = formatNumber($input['total_amount']);
        $items['discount'] = formatNumber(isset($input['final_discount']) ? $input['final_discount'] : 0);
        $items['sub_total'] = formatNumber($input['sub_total']);
        // $items['payment_status'] = $input['payment_status'];

        return $items;
    }

    /**
     * @param  array  $input
     * @param  Invoice  $invoice
     * @return bool|void
     */
    public function addInvoiceAddresses($input, $invoice)
    {
        for ($i = 0; $i <= 2; $i++) {
            if (!isset($input['street'][$i])) {
                return;
            }

            InvoiceAddress::create([
                'street' => (isset($input['street'][$i])) ? $input['street'][$i] : null,
                'city' => (isset($input['city'][$i])) ? $input['city'][$i] : null,
                'state' => (isset($input['state'][$i])) ? $input['state'][$i] : null,
                'zip_code' => (isset($input['zip_code'][$i])) ? $input['zip_code'][$i] : null,
                'country' => (isset($input['country'][$i])) ? $input['country'][$i] : null,
                'type' => $i + 1,
                'invoice_id' => $invoice->id,
            ]);
        }

        return true;
    }

    /**
     * @param $invoiceId
     * @return Invoice
     */
    public function getInvoiceDetailClient($invoiceId)
    {
        $customerId = Auth::user()->contact->customer_id;

        /** @var Invoice $invoice */
        $invoice = Invoice::with([
            'customer',
            'user',
            'salesItems.taxes',
            'invoiceAddresses',
            'payments.paymentMode',
            'salesTaxes',
        ])->whereCustomerId($customerId)->findOrFail($invoiceId);

        return $invoice;
    }

    /**
     * @param  int  $id
     * @return Builder[]|Collection
     */
    public function getInvoiceItems($id)
    {
        $invoice = Invoice::find($id);

        $invoiceItems = SalesItem::where('owner_id', '=', $invoice->getId())->where(
            'owner_type',
            '=',
            $invoice->getOwnerType()
        )->get();

        return $invoiceItems;
    }


    /**
     * @param $input
     * @param $id
     * @return Invoice
     */
    public function updateInvoice($input, $id)
    {
        $invoice = PurchaseReturn::find($id);


        $oldUserIds = PurchaseReturn::whereId($invoice->id)->get()->pluck('sales_agent_id')->toArray();


        $userId = implode(' ', $oldUserIds);
        $contactIds = PurchaseReturn::whereId($id)->pluck('customer_id')->toArray();
        $contactId = implode(' ', $contactIds);

        $invoice->update($this->prepareInvoiceData($input));

        activity()->performedOn($invoice)->causedBy(getLoggedInUser())
            ->useLog('Return updated.')->log($invoice->title . ' Return updated.');
        $this->storeSalesItems($input, $invoice);
        $this->storeSalesTaxes($input, $invoice);
        $this->uploadFiles($input, $invoice->id);

        $this->storeTerms($invoice->id, $input);
        return $invoice;
    }

    public function deleteInvoice($invoice)
    {


        activity()->performedOn($invoice)->causedBy(getLoggedInUser())
            ->useLog('Purhcase Return deleted.')->log($invoice->title . 'Purchase Return deleted.');
        $salesItemsDeleted = $invoice->salesItems()->delete();

        // Delete related terms and get deleted count
        $termsDeleted = $invoice->terms()->delete();

        // Delete the invoice itself and get deleted count
        $invoiceDeleted = $invoice->delete() ? 1 : 0;

        return [
            'sales_items_deleted' => $salesItemsDeleted,
            'terms_deleted' => $termsDeleted,
            'invoice_deleted' => $invoiceDeleted,
        ];
    }

    /**
     * @param  int  $id
     * @return mixed
     */
    public function getSyncListForInvoiceDetail($id)
    {
        $invoice = PurchaseReturn::with([
            'customer',
            'user',
            'salesItems.taxes',
            'salesItems.service',
            'salesItems.category',
            'invoiceAddresses',
            'payments.paymentMode',
            'salesTaxes',
            'project',
            'project.projectServices.categories',
            'project.projectServices.service',
            'terms'

        ])->find($id);

        return $invoice;
    }

    /**
     * @param  int  $id
     * @param $paymentStatus
     * @return int
     */
    public function changePaymentStatus($id, $paymentStatus)
    {
        return PurchaseReturn::whereId($id)->update(['payment_status' => $paymentStatus]);
    }

    /**
     * @param $invoice
     * @return Builder[]|Collection
     */
    public function getNotesData($invoice)
    {
        return Note::with('user.media')->where('owner_id', '=', $invoice->id)
            ->where('owner_type', '=', PurchaseReturn::class)->orderByDesc('created_at')->get();
    }


    public function updatePurchaseItemInventory($item_id, $qty, $status = true)
    {
        $purchaseItem = PurchaseItem::findOrFail($item_id);

        if ($status) {
            // Adding inventory
            $purchaseItem->qty_in += $qty;
            $purchaseItem->qty_current += $qty;
        } else {
            // Subtracting from current stock only
            $purchaseItem->qty_current -= $qty;
            $purchaseItem->qty_out += $qty;
        }

        $purchaseItem->save();

        return $purchaseItem;
    }


    private function uploadFiles($input, $employeeId)
    {
        // Check if the input contains files
        if (isset($input['file']) && is_array($input['file'])) {
            foreach ($input['file'] as $index => $file) {
                // Check if a file has been uploaded
                if (is_uploaded_file($file->getPathname())) {
                    $name = $input['doc_name'][$index] ?? ''; // Default to 'unknown' if no name is provided
                    $expiryDate = $input['expiry_date'][$index] ?? null; // Default to null if no expiry date is provided
                    $this->uploadAndStoreFile($file, $name, $employeeId, $expiryDate);
                }
            }
        }
    }
    private function uploadAndStoreFile($file, $name, $employeeId, $expiryDate)
    {
        // Generate a unique filename
        $filename = time() . '_' . $file->getClientOriginalName();

        // Store the file in the 'public/employee_docs' directory
        $file->storeAs('public/purchase_return_docs', $filename);

        // Store only the filename in the database
        DB::table('purchase_return_docs')->insert([
            'name' => $name,
            'purchase_return_id' => $employeeId,
            'file' => $filename, // Save only the filename
            'expiry_date' => $expiryDate,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }


    public function delete_file($id)
    {
        // Find the document by ID using Eloquent
        $document = PurchaseReturnDoc::find($id);

        // Check if the document exists
        if (!$document) {
            return false; // Document not found
        }

        // Get the file path
        $filePath = public_path('uploads/public/purchase_return_docs/' . basename($document->file));

        DB::beginTransaction();
        try {
            // Delete the file from the folder if it exists
            if (file_exists($filePath)) {
                unlink($filePath);
            }

            // Delete the document record from the database
            $document->delete();

            DB::commit();
            return $document; // Return the deleted model instance
        } catch (Exception $e) {
            DB::rollBack();
            return false; // Deletion failed
        }
    }
}
