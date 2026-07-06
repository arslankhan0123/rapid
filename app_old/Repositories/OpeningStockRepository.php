<?php

namespace App\Repositories;

use App\Models\Contact;
use App\Models\Customer;
use App\Models\Estimate;
use App\Models\EstimateAddress;
use App\Models\Invoice;
use App\Models\Item;
use App\Models\Notification;
use App\Models\PaymentMode;
use App\Models\PurchasedItem;
use App\Models\Tag;
use App\Models\TaxRate;
use App\Models\User;
use Auth;
use Exception;
use Illuminate\Container\Container as Application;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use App\Models\Term;
use App\Models\EstimateTerm;
use App\Models\ServiceCategory;
use App\Models\Supplier;
use App\Models\PurchaseCategory;
use App\Models\PurchaseItem;
use App\Models\OpeningStock;
use App\Models\PurchaseOrderTerm;
use App\Models\OpeningStockTerm;


class OpeningStockRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'title',
        'status',
        'currency',
        'estimate_number',
        'reference',
        'sales_agent_id',
        'discount_type',
        'estimate_date',
        'estimate_expiry_date',
        'admin_note',
        'discount',
        'po_number',
        'payment_mode_id',
        'due_days',
    ];

    /**
     * @var PurchaseInvoiceRepository
     */
    private $invoiceRepository;

    public function __construct(Application $app, PurchaseInvoiceRepository $invoiceRepository)
    {
        parent::__construct($app);
        $this->invoiceRepository = $invoiceRepository;
    }

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
        return OpeningStock::class;
    }


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
        $data['items'] = PurchaseItem::with(['unit'])->orderBy('name', 'asc')->limit(5)->get();

        return $data;
    }


    public function store($input)
    {

        $estimate = OpeningStock::create($this->prepareEstimateData($input));

        activity()->performedOn($estimate)->causedBy(getLoggedInUser())
            ->useLog('Purchase Order created.')->log($estimate->title . ' Order created.');

        if (isset($input['tags']) && !empty($input['tags'])) {
            $estimate->tags()->sync($input['tags']);
        }


        $this->storeSalesItems($input, $estimate);
        $this->storeTerms($estimate->id, $input);


        return $estimate;
    }
    public function storeTerms($id, $input)
    {

        // dd($input);
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
        OpeningStockTerm::where('estimate_id', $id)->delete();

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
                'estimate_id' => $id,
                'terms_id' => $term, // Get terms_id from the terms array
                'description' => $description, // Get description, if available
                'created_at' => now(), // Set the created_at timestamp
                'updated_at' => now(), // Set the updated_at timestamp
            ];
        }

        // Insert new terms if there are valid entries
        if (!empty($estimateTerms)) {
            OpeningStockTerm::insert($estimateTerms);
        }
    }


    public function prepareEstimateData($input)
    {

        $estimateFields = (new OpeningStock())->getFillable();
        $items = [];

        foreach ($input as $key => $value) {
            if (in_array($key, $estimateFields)) {
                $items[$key] = $value;
            }
        }

        $items['total_amount'] = formatNumber($input['total_amount']);
        $items['discount'] = formatNumber($input['final_discount']);
        $items['sub_total'] = formatNumber($input['total_amount'] - $input['final_discount']);

        return $items;
    }

    public function getTerms()
    {
        return Term::pluck('terms', 'id');
    }
    public function getItemCategories()
    {
        return PurchaseCategory::pluck('name', 'id');
    }
    public function addEstimateAddresses($input, $estimateId)
    {

        for ($i = 0; $i <= 2; $i++) {
            if (!isset($input['street'][$i])) {
                return;
            }
            return EstimateAddress::create([
                'street' => (isset($input['street'][$i])) ? $input['street'][$i] : null,
                'city' => (isset($input['city'][$i])) ? $input['city'][$i] : null,
                'state' => (isset($input['state'][$i])) ? $input['state'][$i] : null,
                'zip_code' => (isset($input['zip_code'][$i])) ? $input['zip_code'][$i] : null,
                'country' => (isset($input['country'][$i])) ? $input['country'][$i] : null,
                'type' => $i + 1,
                'estimate_number' => $estimateId,
            ]);
        }
        return false;
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

    public function storeSalesItems($input, $owner, $ifUpdate = false)
    {
        $oldQTY = 0;
        $oldSalesItem = null;
        if ($ifUpdate) {
            $oldSalesItem = $owner->salesItems;
        }


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
            $data['unit_id'] = $record['unit_id'] ?? 0;
            $data = array_merge($record, $data);


            $oldSalesItem = $owner->salesItems->first(function ($item) use ($owner, $record) {
                return $item->owner_id == $owner->id && $item->service_id == $record['service_id'];
            });

            $oldQTY = $oldSalesItem?->quantity ?? 0;



            $salesItem = PurchasedItem::create($data);



            if (isset($record['service_id']) && isset($record['quantity'])) {
                $itemId = $record['service_id'];
                $qty = $record['quantity'];

                if ($ifUpdate) {


                    $this->updatePurchaseItemInventory($itemId, $qty, true, $ifUpdate, $oldQTY);
                } else {
                    $this->updatePurchaseItemInventory($itemId, $qty, true);

                }


            }


            $data = [];
        }

        return true;
    }


    // public function updatePurchaseItemInventory($item_id, $qty, $status = true, $ifUpdate = false, $oldQTY = 0)
    // {
    //     $purchaseItem = PurchaseItem::findOrFail($item_id);

    //     if ($status) {
    //         // Adding inventory
    //         $purchaseItem->qty_in += $qty;
    //         $purchaseItem->qty_current += $qty;
    //     } else {
    //         // Subtracting from current stock only
    //         $purchaseItem->qty_current -= $qty;
    //         $purchaseItem->qty_out += $qty;
    //     }

    //     $purchaseItem->save();

    //     return $purchaseItem;
    // }


    public function updatePurchaseItemInventory($item_id, $qty, $status = true, $ifUpdate = false, $oldQTY = 0)
    {
        $purchaseItem = PurchaseItem::find($item_id);
        // Only continue if the item exists
        if (!$purchaseItem) {
            return null; // Or handle gracefully, log it, throw custom exception, etc.
        }


        if ($ifUpdate) {
            if ($status) {
                // Revert previously added qty
                $purchaseItem->qty_in -= $oldQTY;
                $purchaseItem->qty_current -= $oldQTY;

                // Apply new qty
                $purchaseItem->qty_in += $qty;
                $purchaseItem->qty_current += $qty;
            } else {
                // Revert previously subtracted qty
                $purchaseItem->qty_out -= $oldQTY;
                $purchaseItem->qty_current += $oldQTY;

                // Apply new qty
                $purchaseItem->qty_out += $qty;
                $purchaseItem->qty_current -= $qty;
            }
        } else {
            if ($status) {
                // Just add normally
                $purchaseItem->qty_in += $qty;
                $purchaseItem->qty_current += $qty;
            } else {
                // Just subtract normally
                $purchaseItem->qty_out += $qty;
                $purchaseItem->qty_current -= $qty;
            }
        }

        // dd($oldQTY,$ifUpdate,$purchaseItem->toArray());

        $purchaseItem->save();

        return $purchaseItem;
    }


    public function update($input, $estimate)
    {
        $estimate->update($this->prepareEstimateData($input));
        activity()->performedOn($estimate)->causedBy(getLoggedInUser())
            ->useLog(' updated.')->log($estimate->title . ' Order updated.');


        $this->storeSalesItems($input, $estimate, true);
        $this->storeTerms($estimate->id, $input);
        return $estimate;
    }

    /**
     * @param  Estimate  $estimate
     *
     * @throws Exception
     */
    public function deleteEstimate($estimate)
    {
        activity()->performedOn($estimate)->causedBy(getLoggedInUser())
            ->useLog('Stock deleted.')->log(' Stock deleted.');



        // Revert inventory for each sales item before deletion
        foreach ($estimate->salesItems as $item) {

            $this->updatePurchaseItemInventory(
                $item->service_id,
                $item->quantity,
                false
            );
        }

        $estimate->tags()->detach();
        $estimate->estimateAddresses()->delete();
        $estimate->salesItems()->delete();
        $estimate->salesTaxes()->delete();
        $estimate->delete();
        $estimate->terms()->delete();
    }

    /**
     * @param  int  $id
     * @param $status
     * @return bool|int
     */
    public function changeEstimateStatus($id, $status)
    {
        return Estimate::whereId($id)->update(['status' => $status]);
    }

    /**
     * @param  int  $id
     * @return mixed
     */
    public function getSyncForEstimateDetail($id)
    {
        $estimate = OpeningStock::with([
            'customer',
            'salesItems',
            'salesItems.unit',
            'salesItems.service',
        ])->find($id);

        return $estimate;
    }


    public function getEstimateDetailClient($estimateId)
    {
        $customerId = Auth::user()->contact->customer_id;

        /** @var Estimate $estimate */
        $estimate = Estimate::with([
            'customer',
            'user',
            'tags',
            'salesItems.taxes',
            'salesTaxes',
            'estimateAddresses',
        ])->whereCustomerId($customerId)->findOrFail($estimateId);

        return $estimate;
    }


    public function changeStatus($id, $status)
    {
        return Estimate::whereId($id)->update(['status' => $status]);
    }


    public function convertToInvoice($estimate)
    {
        try {
            $data['title'] = $estimate->title;
            $data['customer_id'] = $estimate->customer_id;
            $data['sales_agent_id'] = $estimate->sales_agent_id;
            $data['discount_type'] = $estimate->discount_type;
            $data['invoice_number'] = Invoice::generateUniqueInvoiceId();
            $data['invoice_date'] = $estimate->estimate_date;
            $data['due_date'] = $estimate->estimate_expiry_date;
            $data['currency'] = $estimate->currency;
            $data['unit'] = $estimate->unit;
            $data['adjustment'] = $estimate->adjustment;
            $data['final_discount'] = $estimate->discount;
            $data['sub_total'] = $estimate->sub_total;
            $data['total_amount'] = $estimate->total_amount;
            $data['payment_status'] = Invoice::STATUS_UNPAID;
            $data['payment_modes'] = PaymentMode::whereActive(true)->pluck('id')->toArray();
            $data['tags'] = $estimate->tags->pluck('id')->toArray();
            $data['taxes'] = [];

            foreach ($estimate->salesItems as $key => $record) {
                $itemArr['item'] = $record['item'];
                $itemArr['description'] = $record['description'];
                $itemArr['quantity'] = $record['quantity'];
                $itemArr['rate'] = formatNumber($record['rate']);
                $itemArr['total'] = formatNumber($record['total']);
                $data['itemsArr'][] = $itemArr;
            }

            $invoice = $this->invoiceRepository->saveInvoice($data);

            return $invoice;
        } catch (Exception $e) {
            throw new UnprocessableEntityHttpException($e->getMessage());
        }
    }


}
