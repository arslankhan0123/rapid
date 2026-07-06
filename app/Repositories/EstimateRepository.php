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
use App\Models\Branch;
use App\Models\UsersBranch;

/**
 * Class EstimateRepository
 *
 * @version April 27, 2020, 6:16 am UTC
 */
class EstimateRepository extends BaseRepository
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
        'email',
        'other_changes',
        'total_amount',
        'percentage_discount',
        'vendor_code'
    ];

    /**
     * @var InvoiceRepository
     */
    private $invoiceRepository;

    public function __construct(Application $app, InvoiceRepository $invoiceRepository)
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
        return Estimate::class;
    }

    /**
     * @param  null  $customerId
     * @return mixed
     */
    public function getEstimatesStatusCount($customerId = null)
    {
        if (! empty($customerId)) {
            return Estimate::selectRaw('count(case when status = 0 then 1 end) as drafted')
                ->selectRaw('count(case when status = 1 then 1 end) as sent')
                ->selectRaw('count(case when status = 2 then 1 end) as expired')
                ->selectRaw('count(case when status = 3 then 1 end) as declined')
                ->selectRaw('count(case when status = 4 then 1 end) as accepted')
                ->selectRaw('count(case when status != 0 then 1 end) as total_estimates')
                ->where('customer_id', '=', $customerId)->first();
        }

        return Estimate::selectRaw('count(case when status = 0 then 1 end) as drafted')
            ->selectRaw('count(case when status = 1 then 1 end) as sent')
            ->selectRaw('count(case when status = 2 then 1 end) as expired')
            ->selectRaw('count(case when status = 3 then 1 end) as declined')
            ->selectRaw('count(case when status = 4 then 1 end) as accepted')
            ->selectRaw('count(*) as total_estimates')
            ->first();
    }

    /**
     * @return mixed
     */
    public function getSyncList()
    {
        $data['customers'] = Customer::orderBy('company_name', 'asc')->pluck('company_name', 'id')->toArray();
        $data['tags'] = Tag::orderBy('name', 'asc')->pluck('name', 'id')->toArray();
        $data['saleAgents'] = User::orderBy('first_name', 'asc')->whereIsEnable(true)->user()->get()->pluck(
            'full_name',
            'id'
        )->toArray();
        $data['discountType'] = Estimate::DISCOUNT_TYPES;
        $data['status'] = Estimate::STATUS;
        $data['currencies'] = Customer::CURRENCIES;
        $taxRates = TaxRate::where('tax_rate', 15)->orderBy('tax_rate', 'asc')->get();
        $data['taxes'] = $taxRates;
        $data['taxesArr'] = $taxRates->pluck('tax_rate', 'id')->toArray();
        $data['items'] = Item::orderBy('title', 'asc')->get();


        return $data;
    }

    /**
     * @param $input
     * @return Estimate
     */
    public function store($input)
    {

        // dd($input, $input['total_amount'], $this->prepareEstimateData($input));

        // $contactIds = Contact::where('customer_id', '=', $input['customer_id'])->where(
        //     'primary_contact',
        //     '=',
        //     '1'
        // )->pluck('user_id')->toArray();
        // $userContacts = User::whereIn('id', $contactIds)->get();

        /** @var Estimate $estimate */

        $estimate = $this->create($this->prepareEstimateData($input));
        $users = User::whereId($estimate->sales_agent_id)->get();

        if ($estimate->status == Estimate::STATUS_SEND) {
            if (! empty($input['sales_agent_id'])) {
                foreach ($users as $user) {
                    Notification::create([
                        'title' => 'New Estimate Created',
                        'description' => 'You are assigned to ' . $estimate->title,
                        'type' => Estimate::class,
                        'user_id' => $user->id,
                    ]);
                }
            }
            // if (! empty($input['customer_id'])) {
            //     foreach ($userContacts as $user) {
            //         Notification::create([
            //             'title' => 'New Qutation Created',
            //             'description' => 'You are assigned to ' . $estimate->title,
            //             'type' => Estimate::class,
            //             'user_id' => $user->id,
            //         ]);

            //         foreach ($contactIds as $oldUser) {
            //             if ($oldUser == $user->id) {
            //                 continue;
            //             }
            //             Notification::create([
            //                 'title' => 'New User Assigned to Estimate',
            //                 'description' => $user->first_name . ' ' . $user->last_name . ' assigned to ' . $estimate->title,
            //                 'type' => Estimate::class,
            //                 'user_id' => $oldUser,
            //             ]);
            //         }
            //     }
            // }
        }
        activity()->performedOn($estimate)->causedBy(getLoggedInUser())
            ->useLog('New Estimate created.')->log($estimate->title . ' Estimate created.');

        if (isset($input['tags']) && ! empty($input['tags'])) {
            $estimate->tags()->sync($input['tags']);
        }
        // Store Address
        // $this->addEstimateAddresses($input, $estimate);
        // Store Items


        $this->invoiceRepository->storeSalesItems($input, $estimate);
        $this->storeTerms($estimate->id, $input);
        // Store Applied Taxes with Amount
        // $this->invoiceRepository->storeSalesTaxes($input, $estimate);

        return $estimate;
    }
    public function storeTerms($id, $input)
    {
        // Retrieve terms and descriptions from the input
        $terms = $input['terms'] ?? [];
        $descriptions = $input['description'] ?? [];

        // Filter out any null or empty terms and ensure they are properly indexed
        $terms = array_filter($terms, function ($term) {
            return !is_null($term) && $term !== '';
        });

        // If the terms are still empty after filtering, return early
        if (empty($terms)) {
            return;
        }

        // First, delete existing terms with the given estimate_id
        EstimateTerm::where('estimate_id', $id)->delete();

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
            EstimateTerm::insert($estimateTerms);
        }
    }






    /**
     * @param  array  $input
     * @return array
     */
    public function prepareEstimateData($input)
    {

        $estimateFields = (new Estimate())->getFillable();
        $items = [];

        foreach ($input as $key => $value) {
            if (in_array($key, $estimateFields)) {
                $items[$key] = $value;
            }
        }

        $items['total_amount'] = formatNumberNew($input['total_amount']);
        $items['discount'] = formatNumberNew($input['final_discount']);
        $items['sub_total'] = formatNumberNew($input['sub_total']);

        return $items;
    }

    public function getTerms()
    {
        return Term::pluck('terms', 'id');
    }
    public function getCustomers()
    {
        return Customer::select(['company_name', 'vendor_code', 'id'])->get();
    }
    public function getServiceCategories()
    {
        return ServiceCategory::pluck('name', 'id');
    }
    public function addEstimateAddresses($input, $estimateId)
    {

        for ($i = 0; $i <= 2; $i++) {
            if (! isset($input['street'][$i])) {
                return;
            }
            return   EstimateAddress::create([
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

    /**
     * @param  array  $input
     * @param  Estimate  $estimate
     * @return Estimate
     */
    public function update($input, $estimate)
    {
        // $oldUserIds = Estimate::whereId($estimate->id)->get()->pluck('sales_agent_id')->toArray();
        // $oldContactIds = Contact::where('customer_id', '=', $estimate->customer_id)->where(
        //     'primary_contact',
        //     '=',
        //     '1'
        // )->pluck('user_id')->toArray();

        // $userId = implode(' ', $oldUserIds);
        // $contactIds = Estimate::whereId($estimate->id)->pluck('customer_id')->toArray();
        // $contactId = implode(' ', $contactIds);

        // $newUserIds = $input['sales_agent_id'];
        // $newContactIds = $input['customer_id'];

        // $users = User::whereId($newUserIds)->get();
        // $contactUserIds = Contact::where('customer_id', '=', $input['customer_id'])->where(
        //     'primary_contact',
        //     '=',
        //     '1'
        // )->pluck('user_id')->toArray();
        // $userContacts = User::whereIn('id', $contactUserIds)->get();
        $estimate->update($this->prepareEstimateData($input));

        //Contacts Notification
        // if (! empty($oldContactIds) && $newContactIds !== $contactId) {
        //     foreach ($oldContactIds as $removedUser) {
        //         Notification::create([
        //             'title' => 'Removed From Estimate',
        //             'description' => 'You removed from ' . $estimate->title,
        //             'type' => Estimate::class,
        //             'user_id' => $removedUser,
        //         ]);
        //     }
        // }
        // if ($userContacts->count() > 0) {
        //     foreach ($userContacts as $user) {
        //         Notification::create([
        //             'title' => 'New Estimate Assigned',
        //             'description' => 'You are assigned to ' . $estimate->title,
        //             'type' => Estimate::class,
        //             'user_id' => $user->id,
        //         ]);
        //         foreach ($oldContactIds as $oldUser) {
        //             if ($oldUser == $user->id) {
        //                 continue;
        //             }
        //             Notification::create([
        //                 'title' => 'New User Assigned to Estimate',
        //                 'description' => $user->first_name . ' ' . $user->last_name . ' assigned to ' . $estimate->title,
        //                 'type' => Estimate::class,
        //                 'user_id' => $oldUser,
        //             ]);
        //         }
        //     }
        // }

        // if (! empty($oldUserIds) && $newUserIds !== $userId) {
        //     foreach ($oldUserIds as $removedUser) {
        //         Notification::create([
        //             'title' => 'Removed From Estimate',
        //             'description' => 'You removed from ' . $estimate->title,
        //             'type' => Estimate::class,
        //             'user_id' => $removedUser,
        //         ]);
        //     }
        // }
        // if ($users->count() > 0) {
        //     foreach ($users as $user) {
        //         Notification::create([
        //             'title' => 'New Estimate Created',
        //             'description' => 'You are assigned to ' . $estimate->title,
        //             'type' => Estimate::class,
        //             'user_id' => $user->id,
        //         ]);
        //         foreach ($oldUserIds as $oldUser) {
        //             if ($oldUser == $user->id) {
        //                 continue;
        //             }
        //             Notification::create([
        //                 'title' => 'New User Assigned to Estimate',
        //                 'description' => $user->first_name . ' ' . $user->last_name . ' assigned to ' . $estimate->title,
        //                 'type' => Estimate::class,
        //                 'user_id' => $oldUser,
        //             ]);
        //         }
        //     }
        // }

        activity()->performedOn($estimate)->causedBy(getLoggedInUser())
            ->useLog('Estimate updated.')->log($estimate->title . ' Estimate updated.');

        if (isset($input['tags']) && ! empty($input['tags'])) {
            $estimate->tags()->sync($input['tags']);
        }

        //  $estimate->estimateAddresses()->delete();
        //  $this->addEstimateAddresses($input, $estimate);

        // Update Items
        $this->invoiceRepository->storeSalesItems($input, $estimate);
        $this->storeTerms($estimate->id, $input);
        // Update Applied Taxes with Amount
        // $this->invoiceRepository->storeSalesTaxes($input, $estimate);

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
            ->useLog('Estimate deleted.')->log($estimate->title . ' Estimate deleted.');

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
        $estimate = Estimate::with([
            'customer',
            'user',
            'tags',
            'salesItems.taxes',
            'salesItems.service',
            'salesItems.category',
            'salesTaxes',
            'estimateAddresses',
            'branch',
            'branch.bank'
        ])->find($id);

        return $estimate;
    }

    /**
     * @param $estimateId
     * @return Estimate
     */
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

    /**
     * @param  int  $id
     * @param $status
     * @return bool|int
     */
    public function changeStatus($id, $status)
    {
        return Estimate::whereId($id)->update(['status' => $status]);
    }

    /**
     * @param  Estimate  $estimate
     * @return Invoice
     */
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

    // public function getUsersBranches()
    // {
    //     return Branch::with(['UsersBranches', 'UsersBranches.branch'])
    //         ->whereHas('UsersBranches', function ($query) {
    //             $query->where('user_id', getLoggedInUserId());
    //         })
    //         ->pluck('name', 'id');
    // }
    public function getUsersBranches()
    {
        $user = auth()->user();
        $userId = $user->id;

        // Check for admin or superadmin role using your boolean field
        if ($user->is_admin == 1) {
            return Branch::pluck('name', 'id');
        }

        // Normal users: only branches assigned to them
        return Branch::with(['UsersBranches', 'UsersBranches.branch'])
            ->whereHas('UsersBranches', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->pluck('name', 'id');
    }
}
