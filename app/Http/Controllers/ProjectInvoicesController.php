<?php

namespace App\Http\Controllers;

use App\Queries\ProjectInvoicesDataTable;
use Illuminate\Http\Request;
use App\Repositories\ProjectInvoiceRepository;
use Yajra\DataTables\DataTables;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Exception;
use App\Http\Requests\DepartmentNewReqeust;
use App\Models\Department;
use App\Http\Requests\UpdateDepartmentNewRequest;
use Illuminate\Database\QueryException;
use Laracasts\Flash\Flash;
use App\Models\MonthlyAttendanceInvoice;
use Carbon\Carbon;
use App\Models\Customer;
use App\Models\PaymentMode;
use Barryvdh\DomPDF\Facade as PDF;
use App\Models\Setting;

class ProjectInvoicesController extends AppBaseController
{
    /**
     * @var ProjectInvoiceRepository
     */
    private $projectInvoiceRepository;
    private $vat;
    public function __construct(ProjectInvoiceRepository $projectInvoiceRepo)
    {
        $this->projectInvoiceRepository = $projectInvoiceRepo;
        $this->vat = 2.4;
    }
    /**
     * @param  Request  $request
     * @return Application|Factory|View
     *
     * @throws Exception
     */
    public function index(Request $request)
    {




        if ($request->ajax()) {
            return DataTables::of((new ProjectInvoicesDataTable())->get($request->all()))->make(true);
        }
        $customers = $this->projectInvoiceRepository->getCustomers();
        $projects = $this->projectInvoiceRepository->getProjects();
        return view('project_invoices.index', compact(['customers', 'projects']));
    }

    public function employeeList(MonthlyAttendanceInvoice $invoice)
    {

        $vat = $this->vat;
        $currencies = Customer::CURRENCIES;
        $invoice->load(['customer', 'project', 'customer.address', 'customer.customerCountry']);

        $states = $this->projectInvoiceRepository->getStates();
        $summeries =  $this->projectInvoiceRepository->getInvoiceEmployeeList($invoice, $vat);
        $bank = $this->projectInvoiceRepository->getBankDetails();
        $paymentModes = $this->projectInvoiceRepository->getPaymentModes();
        return view('project_invoices.list', compact(['paymentModes', 'invoice', 'currencies', 'vat', 'summeries', 'states', 'bank']));
    }

    public function export(Request $request)
    {

        $bank = $this->projectInvoiceRepository->getBankDetails();
        // Fetch data based on the filters
        $query = MonthlyAttendanceInvoice::with(['customer', 'project', 'postedBy', 'updatedBy']);

        if ($request->filled('month')) {
            $query->whereMonth('posted_at', Carbon::parse($request->month)->month)
                ->whereYear('posted_at', Carbon::parse($request->month)->year);
        }

        if ($request->filled('customer_id')) {
            $query->where('customer_id', $request->customer_id);
        }

        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        $data = $query->get();

        // Prepare CSV data
        $csvData = [];
        $csvData[] = ['Inv. Date', 'Inv. Number', 'Customer', 'Project', 'Employees', 'Amount', 'Status']; // Header row

        foreach ($data as $item) {
            $csvData[] = [
                $item->created_at->format('d-m-Y'), // Inv. Date
                $item->id,                            // Inv. Number
                $item->customer ? $item->customer->company_name : '', // Customer
                $item->project ? $item->project->project_name : '',   // Project
                $item->total_employees,                          // Employees
                $item->total_amount,                              // Amount
                $item->status,                                   // Status
            ];
        }

        // Create CSV output
        $output = fopen('php://output', 'w');
        // Generate a unique filename based on the last item's ID or any other logic you prefer
        // Format the current date
        $currentDate = Carbon::now()->format('Y-m-d'); // or any format you prefer

        // Generate the filename with the new format
        $filename = "Sales_invoice_{$currentDate}.csv";

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        foreach ($csvData as $row) {
            fputcsv($output, $row);
        }
        fclose($output);
        exit();
    }

    public function update(MonthlyAttendanceInvoice $invoice, Request $request)
    {
        // Validate the incoming request
        $validatedData = $request->validate([
            'discount' => 'required|numeric',
            'paid_amount' => 'required|numeric',
            // Add other fields that need validation as necessary
        ]);

        // Update the invoice properties
        $invoice->discount = $validatedData['discount'];
        $invoice->paid_amount = $validatedData['paid_amount']; // Assuming you want to add to the paid amount
        // Update status based on paid amount
        if ($invoice->paid_amount >= $invoice->total_amount) {
            $invoice->status = 'paid';
        } elseif ($invoice->paid_amount > 0) {
            $invoice->status = 'partially';
        } else {
            $invoice->status = 'unpaid';
        }
        $invoice->payment_mode = $request['payment_mode'] ?? null;
        $invoice->vat = $request['vat'] ?? null;

        // Save the changes to the database
        $invoice->save();
        Flash::success(__('Invoice Updates Successfully'));
        // Return a response
        return $this->sendResponse(true, " Invoice Updates Successfully");
    }

    function numberToWords($num)
    {
        $ones = [
            0 => 'zero',
            1 => 'one',
            2 => 'two',
            3 => 'three',
            4 => 'four',
            5 => 'five',
            6 => 'six',
            7 => 'seven',
            8 => 'eight',
            9 => 'nine',
            10 => 'ten',
            11 => 'eleven',
            12 => 'twelve',
            13 => 'thirteen',
            14 => 'fourteen',
            15 => 'fifteen',
            16 => 'sixteen',
            17 => 'seventeen',
            18 => 'eighteen',
            19 => 'nineteen'
        ];

        $tens = [
            2 => 'twenty',
            3 => 'thirty',
            4 => 'forty',
            5 => 'fifty',
            6 => 'sixty',
            7 => 'seventy',
            8 => 'eighty',
            9 => 'ninety'
        ];

        $thousands = [
            0 => '',
            1 => 'thousand',
            2 => 'million',
            3 => 'billion'
        ];

        if ($num == 0) {
            return 'zero';
        }

        $words = '';
        $numStr = (string) $num;
        $numParts = explode('.', $numStr);
        $integerPart = (int) $numParts[0];
        $fractionPart = isset($numParts[1]) ? $numParts[1] : '';

        // Handle the integer part
        $integerPartWords = '';
        $thousandIndex = 0;

        while ($integerPart > 0) {
            $currentChunk = $integerPart % 1000;
            if ($currentChunk > 0) {
                $chunkWords = '';

                if ($currentChunk >= 100) {
                    $chunkWords .= $ones[(int) ($currentChunk / 100)] . ' hundred ';
                    $currentChunk %= 100;
                }

                if ($currentChunk >= 20) {
                    $chunkWords .= $tens[(int) ($currentChunk / 10)] . ' ';
                    $currentChunk %= 10;
                }

                if ($currentChunk > 0) {
                    $chunkWords .= $ones[$currentChunk] . ' ';
                }

                $words = trim($chunkWords) . ' ' . $thousands[$thousandIndex] . ' ' . $words;
            }

            $integerPart = (int) ($integerPart / 1000);
            $thousandIndex++;
        }

        $words = trim($words);

        // Handle the fractional part
        if ($fractionPart) {
            $words .= ' and ' . numberToWords((int)$fractionPart) . ' cents';
        }

        return ucwords($words);
    }
    public function exportInvoiceDetails(MonthlyAttendanceInvoice $invoice)
    {
        $vat = $this->vat;
        $currencies = Customer::CURRENCIES;
        $invoice->load(['customer', 'project', 'customer.address', 'customer.customerCountry']);
        $summeries = $this->projectInvoiceRepository->getInvoiceEmployeeList($invoice, $vat);


        $paymentModes = $this->projectInvoiceRepository->getPaymentModes();

        // Calculate amounts in PHP
        $subTotal = array_sum(array_column($summeries, 'total'));
        $discount = $invoice->discount;
        $totalExcludingVat = $subTotal - $discount;
        $totalVat = array_sum(array_column($summeries, 'vat'));
        $netAmount = $subTotal - $discount + $totalVat;
        $totalPaid = $invoice->paid_amount;
        $dueAmount = $netAmount - $totalPaid;
        $bank = $this->projectInvoiceRepository->getBankDetails();
        $words = $this->numberToWords(500);
        $settings = Setting::pluck('value', 'key')->toArray();



        // $com_logo = base64_encode(file_get_contents('https://placehold.co/200x200/jpg'));
        $com_logo = isset($settings['logo']) && !empty($settings['logo'])
            ? base64_encode(file_get_contents($settings['logo']))
            : ' ';


        // return view('project_invoices.exportDetails', compact([
        //     'paymentModes',
        //     'invoice',
        //     'currencies',
        //     'vat',
        //     'summeries',
        //     'subTotal',
        //     'totalVat',
        //     'netAmount',
        //     'totalPaid',
        //     'dueAmount',
        //     'discount',
        //     'totalExcludingVat',
        //     'words',
        //     'bank',
        //     'settings',
        //     'com_logo'
        // ]));




        $pdf = PDF::loadView('project_invoices.exportDetails', compact([
            'paymentModes',
            'invoice',
            'currencies',
            'vat',
            'summeries',
            'subTotal',
            'totalVat',
            'netAmount',
            'totalPaid',
            'dueAmount',
            'discount',
            'totalExcludingVat',
            'words',
            'bank',
            'settings',
            'com_logo'
        ]));

        // $pdf->setPaper('A4', 'portrait'); // Ensure A4 size
        $pdf->setOptions(['margin-left' => 0, 'margin-right' => 0, 'margin-top' => 0, 'margin-bottom' => 0]); // Remove all margins
        return $pdf->download("Invoice_{$invoice->id}_" . Carbon::now()->format('Y-m-d') . ".pdf");
    }
}
