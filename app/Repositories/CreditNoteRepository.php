<?php

namespace App\Repositories;

use App\Models\CreditNote;
use App\Models\CreditNoteAddress;
use App\Models\Customer;
use App\Models\Item;
use App\Models\TaxRate;
use Exception;
use Illuminate\Container\Container as Application;
use App\Models\Invoice;
use App\Models\Term;
use App\Models\Project;
use App\Models\CreditNoteTerms;
use App\Models\ServiceCategory;
use App\Models\Branch;
use App\Models\Account;

/**
 * Class InvoiceRepository
 *
 * @version April 8, 2020, 11:32 am UTC
 */
class CreditNoteRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'customer_id',
        'title',
        'bill_to',
        'ship_to',
        'credit_note_number',
        'credit_note_date',
        'currency',
        'discount_type',
        'admin_text',
        'percentage_discount',
        'vendor_code',
        'user_id',
        'branch_id'
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
        return CreditNote::class;
    }

    /**
     * @return mixed
     */
    public function getStatusCount()
    {
        return CreditNote::selectRaw('count(case when payment_status = 0 then 1 end) as drafted')
            ->selectRaw('count(case when payment_status = 1 then 1 end) as open')
            ->selectRaw('count(case when payment_status = 2 then 1 end) as void')
            ->selectRaw('count(case when payment_status = 3 then 1 end) as closed')
            ->first();
    }

    /**
     * @return mixed
     */
    public function getSyncList()
    {
        $data['customers'] = Customer::orderBy('company_name', 'asc')->pluck('company_name', 'id')->toArray();
        $data['discountType'] = $this->getDiscountTypes();
        $data['currencies'] = Customer::CURRENCIES;
        $taxRates = TaxRate::orderBy('tax_rate', 'asc')->where('tax_rate', 15)->get();
        $data['taxes'] = $taxRates;
        $data['taxesArr'] = $taxRates->pluck('tax_rate', 'id')->toArray();
        $data['items'] = Item::orderBy('title', 'asc')->get();

        return $data;
    }
    public function getProjects()
    {
        return Project::with(['services', 'terms'])->get();
    }
    public function getServiceCategories()
    {
        return ServiceCategory::pluck('name', 'id');
    }
    function getInvoices($id)
    {

        return Invoice::with(['customer', 'project', 'project.services'])->whereRaw('BINARY invoice_number = ?', [$id])->first();
    }
    public function getCustomersAll()
    {
        return Customer::select(['company_name', 'vendor_code', 'id'])->get();
    }

    public function getTerms()
    {
        return Term::pluck('terms', 'id');
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
     * @param  array  $input
     * @return CreditNote
     */
    public function saveCreditNote($input)
    {
        /** @var CreditNote $creditNote */
        $creditNote = $this->create($this->prepareCreditNoteData($input));


        //updateing accounts

        $totalAmount = $input['total_amount'];
        $branchId = $input['branch_id'];

        $account = Account::where('account_name', 'Main Cash')
            ->where('branch_id', $branchId)
            ->first();

        if ($account) {
            $account->opening_balance -= $totalAmount;
            $account->save();
        }
        ///


        activity()->performedOn($creditNote)->causedBy(getLoggedInUser())
            ->useLog('New Credit Note created.')->log($creditNote->title . ' Credit Note created.');

        // Store Address
        $this->addCreditNoteAddresses($input, $creditNote);
        // Store Items
        $this->invoiceRepository->storeSalesItems($input, $creditNote);
        // Store Applied Taxes with Amount
        $this->invoiceRepository->storeSalesTaxes($input, $creditNote);
        $this->storeTerms($creditNote->id, $input);

        return $creditNote;
    }

    /**
     * @param  array  $input
     * @return array
     */
    public function prepareCreditNoteData($input)
    {
        $creditNoteFields = (new CreditNote())->getFillable();
        $items = [];

        foreach ($input as $key => $value) {
            if (in_array($key, $creditNoteFields)) {
                $items[$key] = $value;
            }
        }

        $items['total_amount'] = formatNumberNew($input['total_amount']);
        $items['discount'] = formatNumberNew($input['final_discount']);
        $items['sub_total'] = formatNumberNew($input['sub_total']);
        $items['payment_status'] = $input['payment_status'];

        return $items;
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
        CreditNoteTerms::where('credit_note_id', $id)->delete();

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
                'credit_note_id' => $id,
                'terms_id' => $term, // Get terms_id from the terms array
                'description' => $description, // Get description, if available
                'created_at' => now(), // Set the created_at timestamp
                'updated_at' => now(), // Set the updated_at timestamp
            ];
        }

        // Insert new terms if there are valid entries
        if (!empty($estimateTerms)) {
            CreditNoteTerms::insert($estimateTerms);
        }
    }

    /**
     * @param  array  $input
     * @param $creditNote
     * @return bool|void
     */
    public function addCreditNoteAddresses($input, $creditNote)
    {
        for ($i = 0; $i <= 2; $i++) {
            if (!isset($input['street'][$i])) {
                return;
            }

            CreditNoteAddress::create([
                'street' => (isset($input['street'][$i])) ? $input['street'][$i] : null,
                'city' => (isset($input['city'][$i])) ? $input['city'][$i] : null,
                'state' => (isset($input['state'][$i])) ? $input['state'][$i] : null,
                'zip_code' => (isset($input['zip_code'][$i])) ? $input['zip_code'][$i] : null,
                'country' => (isset($input['country'][$i])) ? $input['country'][$i] : null,
                'type' => $i + 1,
                'credit_note_id' => $creditNote->id,
            ]);
        }

        return true;
    }

    /**
     * @param  array  $input
     * @param  int  $id
     * @return CreditNote
     */
    public function updateCreditNote($input, $id)
    {


        /** @var CreditNote $creditNote */
        $creditNote = CreditNote::find($id);


        $oldAmount = $creditNote->total_amount;
        $newAmount = $input['total_amount_new'];
        $branchId = $input['branch_id'];

        $creditNote->update($this->prepareCreditNoteData($input));


        //updateing accounts

        $account = Account::where('account_name', 'Main Cash')
            ->where('branch_id', $branchId)
            ->first();

        if ($account) {
            $account->opening_balance = ($account->opening_balance + $oldAmount) - $newAmount;
            $account->save();
        }
        //


        activity()->performedOn($creditNote)->causedBy(getLoggedInUser())
            ->useLog('Credit Note updated.')->log($creditNote->title . ' Credit Note updated.');

        $creditNote->creditNoteAddresses()->delete();
        $this->addCreditNoteAddresses($input, $creditNote);

        // Update Items
        $this->invoiceRepository->storeSalesItems($input, $creditNote);
        // Update Applied Taxes with Amount
        $this->invoiceRepository->storeSalesTaxes($input, $creditNote);
        $this->storeTerms($creditNote->id, $input);

        return $creditNote;
    }

    /**
     * @param  CreditNote  $creditNote
     *
     * @throws Exception
     */
    public function deleteCreditNote($creditNote)
    {


        $totalAmount = $creditNote->total_amount;
        $branchId = $creditNote->branch_id;

        // Step 2: Adjust the account balance
        $account = Account::where('account_name', 'Main Cash')
            ->where('branch_id', $branchId)
            ->first();

        if ($account) {
            $account->opening_balance += $totalAmount;
            $account->save();
        }

        activity()->performedOn($creditNote)->causedBy(getLoggedInUser())
            ->useLog('Credit Note deleted.')->log($creditNote->title . ' Credit Note deleted.');

        $creditNote->creditNoteAddresses()->delete();
        $creditNote->salesItems()->delete();
        $creditNote->salesTaxes()->delete();
        $creditNote->terms()->delete();
        $creditNote->delete();
    }

    /**
     * @param  int  $id
     * @return mixed
     */
    public function getSyncListForCreditNoteDetail($id)
    {
        $creditNote = CreditNote::with([
            'customer',
            'customer.customerCountry',
            'salesItems.taxes',
            'salesItems.service',
            'salesItems.category',
            'creditNoteAddresses',
            'salesTaxes',
            'invoice',
            'invoice.project',
            'invoice.project.terms',
            'invoice.project.services',
            'terms',
            'branch',
            'branch.bank',
            'customer.customerAddress',
            'customer.customerAddress.addressCountry',
            'customer.customerAddress.customerState',
        ])->find($id);

        return $creditNote;
    }

    /**
     * @param  int  $id
     * @param $paymentStatus
     * @return int
     */
    public function changePaymentStatus($id, $paymentStatus)
    {
        return CreditNote::whereId($id)->update(['payment_status' => $paymentStatus]);
    }
    public function getUsersBranches()
    {
        return Branch::with(['UsersBranches', 'UsersBranches.branch'])
            ->whereHas('UsersBranches', function ($query) {
                $query->where('user_id', getLoggedInUserId());
            })
            ->pluck('name', 'id');
    }
}
