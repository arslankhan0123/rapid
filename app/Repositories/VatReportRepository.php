<?php

namespace App\Repositories;

use App\Models\VatReport;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use App\Models\SalesItem;
use App\Models\Expense;
use App\Models\Invoice;
use App\Models\CreditNote;
use App\Models\Branch;
use App\Models\PosOrder;
use App\Models\PosOrderRefund;
use App\Models\PurchasedItem;
use Illuminate\Support\Facades\Log;
use App\Models\PurchaseInvoice;
use App\Models\PurchaseReturn;
use Carbon\Carbon;
use App\Models\ManualSale;

/**
 * Class CustomerRepository
 *
 * @version April 3, 2020, 6:37 am UTC
 */
class VatReportRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = ['period', 'input', 'output', 'net', 'paid', 'unpaid', 'year', 'bank_name', 'account_number'];

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
        return VatReport::class;
    }

    public function create($input)
    {
        return VatReport::create(Arr::only($input, $this->getFieldsSearchable()));
    }
    public function getInvoicesData($id)
    {

        $report = VatReport::findOrFail($id);

        // Determine the start and end date based on the quarter (period)
        $dates = $this->getQuarterDateRange($report->period);
        // Fetch invoices related to SalesItem model within the given date range
        $invoices = SalesItem::whereIn('owner_type', ['App\Models\Invoice', 'App\Models\CreditNote'])->whereBetween('created_at', [$dates['start'], $dates['end']])
            ->get();

        $result = $this->calculateVatReport($invoices);
        $report['result'] = $result;
        $report['branch'] = $report->load('branch');
        // Return the invoices data (You can modify the structure as needed)
        //  dd($result);
        return $report;
    }

    public function calculateVatReport($invoices)
    {
        $input = 0;
        $output = 0;

        foreach ($invoices as $invoice) {
            $tmpInput = 0;
            $tmpOutput = 0;
            // For Input: Calculate rate * quantity (without tax) for App\Models\Invoice
            if ($invoice['owner_type'] === 'App\Models\Invoice') {
                $tmpInput = $invoice['rate'] * $invoice['quantity'];  // Calculate base amount without tax
                $input  += ($tmpInput * $invoice['tax']) / 100;
            }
            if ($invoice['owner_type'] === 'App\Models\ManualSale') {
                $tmpInput = $invoice['rate'] * $invoice['quantity'];  // Calculate base amount without tax
                $input  += ($tmpInput * $invoice['tax']) / 100;
            }
            if ($invoice['owner_type'] === 'App\Models\CreditNote') {
                $tmpOutput = $invoice['rate'] * $invoice['quantity'];  // Calculate base amount without tax
                $output  += ($tmpOutput * $invoice['tax']) / 100;
            }
        }

        // Return the result as an array with input and output values
        return [
            'input' => $input,
            'output' => $output,
        ];
    }
    public function calculateVatReportPurchase($invoices)
    {
        $input = 0;
        $output = 0;

        foreach ($invoices as $invoice) {
            $tmpInput = 0;
            $tmpOutput = 0;
            // For Input: Calculate rate * quantity (without tax) for App\Models\Invoice
            if ($invoice['owner_type'] === 'App\Models\PurchaseInvoice') {
                $tmpInput = $invoice['rate'] * $invoice['quantity'];  // Calculate base amount without tax
                $input  += ($tmpInput * $invoice['tax']) / 100;
            }

            if ($invoice['owner_type'] === 'App\Models\PurchaseReturn') {
                $tmpOutput = $invoice['rate'] * $invoice['quantity'];  // Calculate base amount without tax
                $output  += ($tmpOutput * $invoice['tax']) / 100;
            }
        }

        // Return the result as an array with input and output values
        return [
            'output' => $input,
            'input' => $output,
        ];
    }

    private function getQuarterDateRange($period)
    {
        // Define the date ranges for each quarter
        switch ($period) {
            case 'q1':
                // January to March
                $start = now()->year . '-01-01';
                $end = now()->year . '-03-31';
                break;
            case 'q2':
                // April to June
                $start = now()->year . '-04-01';
                $end = now()->year . '-06-30';
                break;
            case 'q3':
                // July to September
                $start = now()->year . '-07-01';
                $end = now()->year . '-09-30';
                break;
            case 'q4':
                // October to December
                $start = now()->year . '-10-01';
                $end = now()->year . '-12-31';
                break;
            default:
                $start = null;
                $end = null;
                break;
        }

        return ['start' => $start, 'end' => $end];
    }

    public function updatePaid($data)
    {

        $vatReport = VatReport::findOrFail($data['id']);
        $vatReport->paid = $vatReport->paid + $data['paid'];
        $vatReport->bank_name = $data['bank_name'];
        $vatReport->account_number = $data['account_number'];
        $vatReport->unpaid = $vatReport->unpaid - $data['paid'];

        $vatReport->save();
    }
    public function makeReport($year, $usersBranches)
    {

        $usersBranches = Branch::pluck('name', 'id');

        // Define the quarters (Q1 to Q4) with their respective months
        $quarters = [
            'q1' => ['start' => "$year-01-01", 'end' => "$year-03-31"],
            'q2' => ['start' => "$year-04-01", 'end' => "$year-06-30"],
            'q3' => ['start' => "$year-07-01", 'end' => "$year-09-30"],
            'q4' => ['start' => "$year-10-01", 'end' => "$year-12-31"],
        ];


        $reportData = [];

        foreach ($usersBranches as $branchId => $branch) {

            // Loop through each quarter and generate the report
            foreach ($quarters as $quarter => $dates) {
                // Fetch sales items for each quarter based on the date range
                $invoices
                    = SalesItem::whereBetween('created_at', [$dates['start'], $dates['end']])
                    ->where(function ($query) use ($branchId) {
                        $query->where(function ($subQuery) use ($branchId) {
                            // Filter for owner_type = Invoice
                            $subQuery->where('owner_type', 'App\Models\Invoice')
                                ->whereHas('invoice', function ($q) use ($branchId) {
                                    $q->where('branch_id', $branchId)
                                    ->where('payment_status', 2);
                                });
                        })
                            ->orWhere(function ($subQuery) use ($branchId) {
                                // Filter for owner_type = CreditNote
                                $subQuery->where('owner_type', 'App\Models\CreditNote')
                                    ->whereHas('creditNote', function ($q) use ($branchId) {
                                        $q->where('branch_id', $branchId);
                                    });
                            })
                            ->orWhere(function ($subQuery) use ($branchId) {
                                // Filter for owner_type = CreditNote
                                $subQuery->where('owner_type', 'App\Models\ManualSale')
                                    ->whereHas('manualSale', function ($q) use ($branchId) {
                                        $q->where('branch_id', $branchId);
                                    });
                            });
                    })
                    ->with(['invoice', 'creditNote', 'manualSale']) // Eager load relationships
                    ->get();

                // Calculate VAT report for this quarter
                $result = $this->calculateVatReport($invoices);


                $totalExpenses = Expense::with('tax1Rate') // Load the related tax1Rate model
                    ->whereBetween('created_at', [$dates['start'], $dates['end']])
                    ->where('branch_id', $branchId)  // Filter by branch ID
                    ->where('isTaxable', 1)
                    ->get()
                    ->map(function ($expense) {

                        // Calculate the tax if tax1Rate is present
                        $taxAmount = 0;
                        if ($expense->tax1Rate) {
                            $taxRate = $expense->tax1Rate->tax_rate; // Assume tax_rate is in percentage
                            $taxAmount = ($expense->amount * $taxRate) / 100; // Calculate the tax amount
                        }

                        // Return the total (amount + tax)
                        return $taxAmount;
                    })
                    ->sum(); // Sum the calculated amounts with tax


                // Update the output value by adding total expenses to it
                $output = $result['output'] + $totalExpenses;

                $totalInput = $result['input'];



                $totalInput += $purchaseResult['input'] ?? 0;
                // Prepare the data for the quarter
                $data = [
                    'year' => $year,  // Include the year
                    'period' => $quarter,  // Include the period (q1, q2, q3, q4)
                    'input' => $totalInput,
                    'output' => $output,  // Update the output value with expenses and tax
                    'net' => $totalInput - $output, // net is input - output
                    'unpaid' => $totalInput - $output, // unpaid = input + output
                    'updated_at' => now(),
                ];


                // Save or update the report using updateOrCreate
                VatReport::updateOrCreate(
                    [
                        'year' => $year,  // Condition for updateOrCreate (match year and period)
                        'period' => $quarter,
                        'branch_id' => $branchId
                    ],
                    $data
                );

                // Collect the report data for debugging or further processing
                $reportData[] = $data;
            }
        }


        return $reportData;
    }

    public function  getViewData($report)
    {

        $year = $report->year;
        $quarters = [
            'q1' => ['start' => "$year-01-01", 'end' => "$year-03-31"],
            'q2' => ['start' => "$year-04-01", 'end' => "$year-06-30"],
            'q3' => ['start' => "$year-07-01", 'end' => "$year-09-30"],
            'q4' => ['start' => "$year-10-01", 'end' => "$year-12-31"],
        ];

        // Get the quarter data for the current report's period
        $reportQuarter = $quarters[$report->period] ?? null;

        $invoices = Invoice::with('salesItems', 'branch') // Assuming your relationship is defined as salesItems
            ->where('branch_id', $report->branch_id)
            ->where('payment_status', 2)
            ->whereBetween('created_at', [$reportQuarter['start'], $reportQuarter['end']])
            ->get()
            ->map(function ($invoice) {
                // Initialize totals for excluding VAT, total VAT, and including VAT
                $excludingVatAmount = 0;
                $totalVatAmount = 0;
                $includingVatAmount = 0;

                // Loop through each sales item in the invoice to calculate amounts
                foreach ($invoice->salesItems as $item) {
                    // Calculate excluding VAT (rate * quantity)
                    $subtotal = $item['rate'] * $item['quantity'];
                    $excludingVatAmount += $subtotal;

                    // Calculate VAT amount ((rate * quantity * tax) / 100)
                    $vatAmount = ($subtotal * $item['tax']) / 100;
                    $totalVatAmount += $vatAmount;

                    // Calculate including VAT (subtotal + VAT)
                    $includingVatAmount += $subtotal + $vatAmount;
                }

                // Add the calculated values to the invoice
                $invoice->excludingVatAmount = $excludingVatAmount;
                $invoice->totalVatAmount = $totalVatAmount;
                $invoice->includingVatAmount = $includingVatAmount;

                // Return the modified invoice object
                return $invoice;
            });
        $manualSales = ManualSale::with('salesItems', 'branch') // Assuming your relationship is defined as salesItems
            ->where('branch_id', $report->branch_id)
            ->whereBetween('created_at', [$reportQuarter['start'], $reportQuarter['end']])
            ->get()
            ->map(function ($invoice) {
                // Initialize totals for excluding VAT, total VAT, and including VAT
                $excludingVatAmount = 0;
                $totalVatAmount = 0;
                $includingVatAmount = 0;

                // Loop through each sales item in the invoice to calculate amounts
                foreach ($invoice->salesItems as $item) {
                    // Calculate excluding VAT (rate * quantity)
                    $subtotal = $item['rate'] * $item['quantity'];
                    $excludingVatAmount += $subtotal;

                    // Calculate VAT amount ((rate * quantity * tax) / 100)
                    $vatAmount = ($subtotal * $item['tax']) / 100;
                    $totalVatAmount += $vatAmount;

                    // Calculate including VAT (subtotal + VAT)
                    $includingVatAmount += $subtotal + $vatAmount;
                }

                // Add the calculated values to the invoice
                $invoice->excludingVatAmount = $excludingVatAmount;
                $invoice->totalVatAmount = $totalVatAmount;
                $invoice->includingVatAmount = $includingVatAmount;

                // Return the modified invoice object
                return $invoice;
            });

        $creditNotes = CreditNote::with('salesItems', 'branch') // Assuming your relationship is defined as salesItems
            ->where('branch_id', $report->branch_id)
            ->whereBetween('created_at', [$reportQuarter['start'], $reportQuarter['end']])
            ->get()
            ->map(function ($invoice) {
                // Initialize totals for excluding VAT, total VAT, and including VAT
                $excludingVatAmount = 0;
                $totalVatAmount = 0;
                $includingVatAmount = 0;

                // Loop through each sales item in the invoice to calculate amounts
                foreach ($invoice->salesItems as $item) {
                    // Calculate excluding VAT (rate * quantity)
                    $subtotal = $item['rate'] * $item['quantity'];
                    $excludingVatAmount += $subtotal;

                    // Calculate VAT amount ((rate * quantity * tax) / 100)
                    $vatAmount = ($subtotal * $item['tax']) / 100;
                    $totalVatAmount += $vatAmount;

                    // Calculate including VAT (subtotal + VAT)
                    $includingVatAmount += $subtotal + $vatAmount;
                }

                // Add the calculated values to the invoice
                $invoice->excludingVatAmount = $excludingVatAmount;
                $invoice->totalVatAmount = $totalVatAmount;
                $invoice->includingVatAmount = $includingVatAmount;

                // Return the modified invoice object
                return $invoice;
            });



        $expenses = Expense::with('tax1Rate', 'branch')->whereBetween('created_at', [$reportQuarter['start'], $reportQuarter['end']])
            ->where('isTaxable', 1)
            ->where('branch_id', $report->branch_id)
            ->get()
            ->map(function ($expense) {

                // Calculate the tax if tax1Rate is present
                $taxAmount = 0;
                if ($expense->tax1Rate) {
                    $taxRate = $expense->tax1Rate->tax_rate; // Assume tax_rate is in percentage
                    $taxAmount = ($expense->amount * $taxRate) / 100; // Calculate the tax amount
                }

                // Add the tax amount as a new key in the expense
                $expense->totalVatAmount = $taxAmount;

                // Return the modified expense object
                return $expense;
            });














        return ['invoices' => $invoices, 'creditNotes' => $creditNotes, 'expenses' => $expenses, 'manualSales' => $manualSales];
    }
}
