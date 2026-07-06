<?php

namespace App\Repositories;

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
use App\Models\Branch;
use App\Models\Account;

/**
 * Class InvoiceRepository
 *
 * @version April 8, 2020, 11:32 am UTC
 */
class InvoiceRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'customer_id',
        'title',
        'bill_to',
        'ship_to',
        'invoice_number',
        'invoice_date',
        'due_date',
        'sales_agent_id',
        'currency',
        'discount_type',
        'admin_text',
        'total_amount',
        'sub_total',
        'percentage_discount',
        'vendor_code',
        'user_id',
        'branch_id',
        'invoice_month'
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
        return Invoice::class;
    }

    /**
     * @param  null  $customerId
     * @return mixed
     */
    public function getInvoicesStatusCount($customerId = null)
    {
        if (! empty($customerId)) {
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
        $data['customers'] = Customer::orderBy('company_name', 'asc')->pluck('company_name', 'id')->toArray();
        $data['tags'] = Tag::orderBy('name', 'asc')->pluck('name', 'id')->toArray();
        $data['paymentModes'] = PaymentMode::orderBy('name', 'asc')->whereActive(true)->pluck('name', 'id')->toArray();
        $data['saleAgents'] = User::orderBy('first_name', 'asc')->whereIsEnable(true)->user()->get()->pluck(
            'full_name',
            'id'
        )->toArray();
        $data['discountType'] = $this->getDiscountTypes();
        $data['currencies'] = Customer::CURRENCIES;
        $taxRates = TaxRate::where('tax_rate', 15)->orderBy('tax_rate', 'asc')->get();
        $data['taxes'] = $taxRates;
        $data['taxesArr'] = $taxRates->pluck('tax_rate', 'id')->toArray();
        $data['items'] = Item::orderBy('title', 'asc')->get();

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

    public function getProjects()
    {
        return Project::with('services', 'terms')->get();
    }
    public function getCustomersAll()
    {
        return Customer::select(['company_name', 'vendor_code', 'id'])->get();
    }

    /**
     * @param  array  $input
     * @return Invoice
     */
    public function saveInvoice($input)
    {
        // $oldContactIds = Contact::where('customer_id', '=', $input['customer_id'])->where(
        //     'primary_contact',
        //     '=',
        //     '1'
        // )->pluck('user_id')->toArray();
        // $contactIds = Contact::where('customer_id', '=', $input['customer_id'])->where(
        //     'primary_contact',
        //     '=',
        //     '1'
        // )->pluck('user_id')->toArray();
        // $userContacts = User::whereIn('id', $contactIds)->get();
        // /** @var Invoice $invoice */
        $invoice = $this->create($this->prepareInvoiceData($input));


        //updateing accounts

        $totalAmount = $input['total_amount'];
        $branchId = $input['branch_id'];

        $account = Account::where('account_name', 'Main Cash')
            ->where('branch_id', $branchId)
            ->first();

        if ($account) {
            $account->opening_balance += $totalAmount;
            $account->save();
        }
        ///


        $users = User::whereId($invoice->sales_agent_id)->get();

        if ($invoice->payment_status == Invoice::STATUS_UNPAID) {
            if (! empty($input['sales_agent_id'])) {
                foreach ($users as $user) {
                    Notification::create([
                        'title' => 'New Invoice Created',
                        'description' => 'You are assigned to ' . $invoice->title,
                        'type' => Invoice::class,
                        'user_id' => $user->id,
                    ]);
                }
            }
            // if (! empty($input['customer_id'])) {
            //     foreach ($userContacts as $user) {
            //         Notification::create([
            //             'title' => 'New Invoice Created',
            //             'description' => 'You are assigned to ' . $invoice->title,
            //             'type' => Invoice::class,
            //             'user_id' => $user->id,
            //         ]);

            //         foreach ($oldContactIds as $oldUser) {
            //             if ($oldUser == $user->id) {
            //                 continue;
            //             }
            //             Notification::create([
            //                 'title' => 'New User Assigned to Invoice',
            //                 'description' => $user->first_name . ' ' . $user->last_name . ' assigned to ' . $invoice->title,
            //                 'type' => Invoice::class,
            //                 'user_id' => $oldUser,
            //             ]);
            //         }
            //     }
            // }
        }
        activity()->performedOn($invoice)->causedBy(getLoggedInUser())
            ->useLog('New Invoice created.')->log($invoice->title . ' Invoice created.');

        if (isset($input['tags']) && ! empty($input['tags'])) {
            $invoice->tags()->sync($input['tags']);
        }

        $paymentModes = ! empty($input['payment_modes']) ? $input['payment_modes'] : [];
        $invoice->paymentModes()->sync($paymentModes);

        // // Store Address
        // $this->addInvoiceAddresses($input, $invoice);
        // // Store Items
        $this->storeSalesItems($input, $invoice);
        $this->storeTerms($invoice->id, $input);
        // Store Applied Taxes with Amount
        // $this->storeSalesTaxes($input, $invoice);

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


        if (! empty($input['taxes'])) {
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
        InvoiceTerm::where('invoice_id', $id)->delete();

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
                'invoice_id' => $id,
                'terms_id' => $term, // Get terms_id from the terms array
                'description' => $description, // Get description, if available
                'created_at' => now(), // Set the created_at timestamp
                'updated_at' => now(), // Set the updated_at timestamp
            ];
        }

        // Insert new terms if there are valid entries
        if (!empty($estimateTerms)) {
            InvoiceTerm::insert($estimateTerms);
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
            $data['owner_id'] = $owner->getId();
            $data['owner_type'] = $owner->getOwnerType();
            $data['service_id'] = $record['service_id'] ?? null;
            $data['category_id'] = $record['category_id'] ?? null;
            $data['item'] = $record['item'] ?? ' ';
            $data['description'] = $record['description'] ?? ' ';
            $data['headline'] = $record['headline'] ?? ' ';
            $data['subheadline'] = $record['subheadline'] ?? ' ';
            $data['rate'] =  $record['rate'] ?? 0;
            $data['tax'] =  $record['tax'] ?? 0;
            $data['total'] = $record['total'] ?? 0;
            $data = array_merge($record, $data);


            $salesItem = SalesItem::create($data);

            // if (! empty($record['tax'])) {
            //     $taxes = explode(',', $record['tax']);
            //     $taxes = (empty(array_filter($taxes))) ? [] : $taxes;
            //     $salesItem->taxes()->sync($taxes);
            // }

            $data = [];
        }

        return true;
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
    // public function prepareInvoiceData($input)
    // {
    //     $invoiceFields = (new Invoice())->getFillable();
    //     $items = [];

    //     foreach ($input as $key => $value) {
    //         if (in_array($key, $invoiceFields)) {
    //             $items[$key] = $value;
    //         }
    //     }

    //     $items['total_amount'] = formatNumberNew($input['total_amount_new']);
    //     $items['discount'] = formatNumberNew(isset($input['final_discount']) ? $input['final_discount'] : 0);
    //     // $items['sub_total'] = formatNumber($input['sub_total_new']);
    //     $items['payment_status'] = $input['payment_status'];


    //     return $items;
    // }

    public function prepareInvoiceData($input)
    {
        $invoiceFields = (new Invoice())->getFillable();
        $items = [];

        foreach ($input as $key => $value) {
            if (in_array($key, $invoiceFields)) {
                $items[$key] = $value;
            }
        }

        $items['total_amount'] = formatNumberNew($input['total_amount_new']);
        $items['discount'] = formatNumberNew(isset($input['final_discount']) ? $input['final_discount'] : 0);

        // Add deduction fields
        $items['absent_deduction'] = formatNumberNew(isset($input['absent_deduction']) ? $input['absent_deduction'] : 0);
        $items['allowance_deduction'] = formatNumberNew(isset($input['allowance_deduction']) ? $input['allowance_deduction'] : 0);
        $items['damage_deduction'] = formatNumberNew(isset($input['damage_deduction']) ? $input['damage_deduction'] : 0);

        $items['payment_status'] = $input['payment_status'];
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
            if (! isset($input['street'][$i])) {
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
        /** @var Invoice $invoice */
        $invoice = Invoice::find($id);

        $oldAmount = $invoice->total_amount;
        $newAmount = $input['total_amount_new'];
        $branchId = $input['branch_id'];

        $oldUserIds = Invoice::whereId($invoice->id)->get()->pluck('sales_agent_id')->toArray();
        $oldContactIds = Contact::where('customer_id', '=', $invoice->customer_id)->where(
            'primary_contact',
            '=',
            '1'
        )->pluck('user_id')->toArray();

        $userId = implode(' ', $oldUserIds);
        $contactIds = invoice::whereId($id)->pluck('customer_id')->toArray();
        $contactId = implode(' ', $contactIds);

        // $newUserIds = $input['sales_agent_id'];
        $newContactIds = $input['customer_id'];

        // $users = User::whereId($newUserIds)->get();
        $contactUserIds = Contact::where('customer_id', '=', $input['customer_id'])->where(
            'primary_contact',
            '=',
            '1'
        )->pluck('user_id')->toArray();
        $userContacts = User::whereIn('id', $contactUserIds)->get();
        $invoice->update($this->prepareInvoiceData($input));


        //updateing accounts

        $account = Account::where('account_name', 'Main Cash')
            ->where('branch_id', $branchId)
            ->first();

        if ($account) {
            $account->opening_balance = ($account->opening_balance - $oldAmount) + $newAmount;
            $account->save();
        }
        //

        //Contacts Notification
        if (! empty($oldContactIds) && $newContactIds !== $contactId) {
            foreach ($oldContactIds as $removedUser) {
                Notification::create([
                    'title' => 'Removed From Invoice',
                    'description' => 'You removed from ' . $invoice->title,
                    'type' => Invoice::class,
                    'user_id' => $removedUser,
                ]);
            }
        }
        if ($userContacts->count() > 0) {
            foreach ($userContacts as $user) {
                Notification::create([
                    'title' => 'New Invoice Assigned',
                    'description' => 'You are assigned to ' . $invoice->title,
                    'type' => Invoice::class,
                    'user_id' => $user->id,
                ]);
                foreach ($oldContactIds as $oldUser) {
                    if ($oldUser == $user->id) {
                        continue;
                    }
                    Notification::create([
                        'title' => 'New User Assigned to Invoice',
                        'description' => $user->first_name . ' ' . $user->last_name . ' assigned to ' . $invoice->title,
                        'type' => Invoice::class,
                        'user_id' => $oldUser,
                    ]);
                }
            }
        }

        // //Members Notification
        // if (! empty($oldUserIds) && $newUserIds !== $userId) {
        //     foreach ($oldUserIds as $removedUser) {
        //         Notification::create([
        //             'title' => 'Removed From Invoice',
        //             'description' => 'You removed from ' . $invoice->title,
        //             'type' => Invoice::class,
        //             'user_id' => $removedUser,
        //         ]);
        //     }
        // }
        // if ($users->count() > 0) {
        //     foreach ($users as $user) {
        //         Notification::create([
        //             'title' => 'New Invoice Assigned',
        //             'description' => 'You are assigned to ' . $invoice->title,
        //             'type' => Invoice::class,
        //             'user_id' => $user->id,
        //         ]);

        //         foreach ($oldUserIds as $oldUser) {
        //             if ($oldUser == $user->id) {
        //                 continue;
        //             }
        //             Notification::create([
        //                 'title' => 'New User Assigned to Invoice',
        //                 'description' => $user->first_name . ' ' . $user->last_name . ' assigned to ' . $invoice->title,
        //                 'type' => Invoice::class,
        //                 'user_id' => $oldUser,
        //             ]);
        //         }
        //     }
        // }
        activity()->performedOn($invoice)->causedBy(getLoggedInUser())
            ->useLog('Invoice updated.')->log($invoice->title . ' Invoice updated.');

        if (isset($input['tags']) && ! empty($input['tags'])) {
            $invoice->tags()->sync($input['tags']);
        }

        $paymentModes = ! empty($input['payment_modes']) ? $input['payment_modes'] : [];
        $invoice->paymentModes()->sync($paymentModes);

        $invoice->invoiceAddresses()->delete();
        $this->addInvoiceAddresses($input, $invoice);

        // Update Items
        $this->storeSalesItems($input, $invoice);
        // Update Applied Taxes with Amount
        $this->storeSalesTaxes($input, $invoice);
        $this->storeTerms($invoice->id, $input);

        return $invoice;
    }

    /**
     * @param $input
     * @return array
     */
    public function prepareSalesItemData($input)
    {
        $items = [];

        if (isset($input['item']) && ! empty($input['item'])) {
            foreach ($input as $key => $data) {
                foreach ($data as $index => $value) {
                    $items[$index][$key] = $value;
                }
            }

            return $items;
        }
    }

    /**
     * @param  Invoice  $invoice
     *
     * @throws Exception
     */
    public function deleteInvoice($invoice)
    {



        $totalAmount = $invoice->total_amount;
        $branchId = $invoice->branch_id;

        // Step 2: Adjust the account balance
        $account = Account::where('account_name', 'Main Cash')
            ->where('branch_id', $branchId)
            ->first();

        if ($account) {
            $account->opening_balance -= $totalAmount;
            $account->save();
        }


        activity()->performedOn($invoice)->causedBy(getLoggedInUser())
            ->useLog('Invoice deleted.')->log($invoice->title . ' Invoice deleted.');

        $invoice->tags()->detach();
        $invoice->invoiceAddresses()->delete();
        $invoice->paymentModes()->detach();
        $invoice->salesItems()->delete();
        $invoice->salesTaxes()->delete();
        $invoice->delete();
    }

    /**
     * @param  int  $id
     * @return mixed
     */
    public function getSyncListForInvoiceDetail($id)
    {
        $invoice = Invoice::with([
            'customer',
            'user',
            'salesItems.taxes',
            'salesItems.service',
            'salesItems.category',
            'invoiceAddresses',
            'payments.paymentMode',
            'salesTaxes',
            'project',
            'project.terms',
            'project.projectServices.categories',
            'project.projectServices.service',
            'terms',
            'branch',
            'branch.bank',
            'customer.customerAddress',
            'customer.customerAddress.addressCountry',
            'customer.customerAddress.customerState',

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
        return Invoice::whereId($id)->update(['payment_status' => $paymentStatus]);
    }

    /**
     * @param $invoice
     * @return Builder[]|Collection
     */
    public function getNotesData($invoice)
    {
        return Note::with('user.media')->where('owner_id', '=', $invoice->id)
            ->where('owner_type', '=', Invoice::class)->orderByDesc('created_at')->get();
    }
    public function getInvoice($invoiceNumber)
    {
        return Invoice::with([
            'customer',
            'salesItems',
        ])->where('invoice_number', $invoiceNumber)->first();
    }
    public function getCustomers()
    {
        return Customer::whereNotNull('whatsapp')->pluck('company_name', 'id');
    }
    public function getWhatsAppNumber($phones)
    {
        // Fetch the whatsapp number(s) for the given customer IDs
        $whatsappNumbers = Customer::whereIn('id', $phones)->pluck('whatsapp', 'id');

        return $whatsappNumbers;
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
