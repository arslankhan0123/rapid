<?php

namespace App\Http\Controllers;

use App\Http\Requests\VatReportRequest;
use App\Queries\VatreportDataTable;
use App\Repositories\VatReportRepository;

use Illuminate\Database\QueryException;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Models\AssetCategory;
use App\Http\Requests\UpdateServiceCategoryRequest;
use Laracasts\Flash\Flash;
use Throwable;
use App\Models\VatReport;
use App\Models\Expense;
use App\Repositories\InvoiceRepository;
use Barryvdh\DomPDF\Facade as PDF;

use Illuminate\Support\Facades\Storage;
use ZipArchive;
use App\Models\Setting;
use App\Models\Bank;
use App\Repositories\CreditNoteRepository;
use App\Models\Customer;
use App\Repositories\ExpenseRepository;
use Carbon\Carbon;

use Salla\ZATCA\GenerateQrCode;
use Salla\ZATCA\Tags\Seller;
use Salla\ZATCA\Tags\TaxNumber;
use Salla\ZATCA\Tags\InvoiceDate;
use Salla\ZATCA\Tags\InvoiceTotalAmount;
use Salla\ZATCA\Tags\InvoiceTaxAmount;


class VatReportController extends AppBaseController
{

    private $invoiceRepository;
    private $vatReportRepository;
    private $creditNoteRepository;
    private $expenseRepository;
    private $purchaseInvoiceRepository;
    private $purchaseReturnRepository;
    public function __construct(VatReportRepository $vatReportRepoy, InvoiceRepository $invoiceRepo, CreditNoteRepository $creditNoteRepo, ExpenseRepository $expenseRepo)
    {
        $this->vatReportRepository = $vatReportRepoy;
        $this->invoiceRepository = $invoiceRepo;
        $this->creditNoteRepository = $creditNoteRepo;
        $this->expenseRepository = $expenseRepo;
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

            if (isset($request->year) && $request->year != null) {
                $this->makeReport($request->year);
            }

            return DataTables::of((new VatreportDataTable())->get($request->all()))->addColumn('period_description', function ($row) {
                return $row->period_description;  // Use the custom accessor
            })->make(true);
        }
        $usersBranches = $this->getUsersBranches();
        return view('vat-reports.index', compact('usersBranches'));
    }

    public function create()
    {
        return view('vat-reports.create');
    }

    public function makeReport($year)
    {

        $usersBranches = $this->getUsersBranches();
        $result = $this->vatReportRepository->makeReport($year, $usersBranches);

        return $this->sendResponse($result, $year . " " . __('messages.vat-reports.saved'));
    }
    public function store(VatReportRequest $request)
    {

        $input = $request->all();
        try {
            $assetCategory = $this->vatReportRepository->create($input);
            activity()->causedBy(getLoggedInUser())
                ->performedOn($assetCategory)
                ->useLog('VAT Report created.')
                ->log(' VAT Report Created.');
            Flash::success(__('messages.vat-reports.saved'));
            return $this->sendResponse($assetCategory, __('messages.vat-reports.saved'));
        } catch (Throwable $e) {
            throw $e;
        }
    }
    public function getModalData($report)
    {

        $result = $this->vatReportRepository->getInvoicesData($report);
        return $this->sendResponse($result, true);
        //   return $result;
    }

    public function updatePaid(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|exists:vat_reports,id', // Ensure the record exists
            'paid' => 'required|numeric|min:0', // Ensure paid is numeric and >= 0
            'bank_name' => "nullable",
            'account_number' => 'nullable'
        ]);

        $this->vatReportRepository->updatePaid($validated);
        return response()->json(['success' => true, 'message' => 'Paid status updated successfully']);
    }
    public function destroy(VatReport $report)
    {
        // $report->load('services');
        // if ($report->services->isNotEmpty()) {
        //     return $this->sendError('Already In Use');
        // }
        // try {
        //     $report->delete();
        //     activity()->performedOn($report)->causedBy(getLoggedInUser())
        //         ->useLog('Service Category deleted.')->log($report->title . ' Asset Category deleted.');
        //     return $this->sendSuccess('Service Category deleted successfully.');
        // } catch (QueryException $e) {
        //     return $this->sendError('Failed to delete! Already in use.');
        // }
    }

    public function download(VatReport $report) {}
    public function update(VatReport $report, UpdateServiceCategoryRequest $updateServiceCategoryRequest)
    {

        $input = $updateServiceCategoryRequest->all();
        $assetCategory = $this->vatReportRepository->update($input, $updateServiceCategoryRequest->id);
        activity()->performedOn($assetCategory)->causedBy(getLoggedInUser())
            ->useLog('Service Category Updated')->log($assetCategory->title . 'Service Category updated.');
        Flash::success(__('messages.vat-reports.saved'));
        return $this->sendSuccess(__('messages.vat-reports.saved'));
    }
    public function view(VatReport $report)
    {



        $report->load('branch');
        $data = $this->vatReportRepository->getViewData($report);

        return view('vat-reports.view', ['report' => $report, 'data' => $data]);
    }



    public function downloadVatHistoryReport(VatReport $report)
    {
        // Fetch data using the vatReportRepository
        $data = $this->vatReportRepository->getViewData($report);

        // Prepare CSV content from the data
        $csvData = $this->prepareVatHistoryCsvData($data);

        // Set a filename for the CSV
        $filename = "VAT_history_report_{$report->period}_{$report->year}.csv";

        // Stream the CSV file for download
        return response()->streamDownload(function () use ($csvData) {
            echo $csvData;
        }, $filename, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    protected function prepareVatHistoryCsvData($data)
    {


        ob_start();
        $csv = fopen('php://output', 'w');
        // Init totals
        $totalExVat = 0;
        $totalVat = 0;
        $totalIncVat = 0;

        // Add header row to CSV
        fputcsv($csv, ['Doc No', 'Doc Date', 'Doc Type', 'Excluding VAT', 'VAT Amount', 'Including VAT', 'Branch']);

        // Process invoices
        foreach ($data['invoices'] as $invoice) {

            $excluding = floatval($invoice->excludingVatAmount);
            $vat = floatval($invoice->totalVatAmount);
            $including = floatval($invoice->includingVatAmount);

            $totalExVat += $excluding;
            $totalVat += $vat;
            $totalIncVat += $including;

            fputcsv($csv, [
                $invoice->invoice_number,
                Carbon::parse($invoice->created_at)->format('d-m-Y'),
                'Sales Invoice',
                number_format($invoice->excludingVatAmount, 2),
                number_format($invoice->totalVatAmount, 2),
                number_format($invoice->includingVatAmount, 2),
                $invoice->branch?->name ?? '',
            ]);
        }

        // Process credit notes
        foreach ($data['creditNotes'] as $return) {

            $excluding = floatval($return->excludingVatAmount);
            $vat = floatval($return->totalVatAmount);
            $including = floatval($return->includingVatAmount);

            $totalExVat += $excluding;
            $totalVat += $vat;
            $totalIncVat += $including;
            fputcsv($csv, [
                $return->credit_note_number ?? '',
                Carbon::parse($return->created_at)->format('d-m-Y'),
                'Sales Return',
                number_format($return->excludingVatAmount, 2),
                number_format($return->totalVatAmount, 2),
                number_format($return->includingVatAmount, 2),
                $return->branch?->name ?? '',
            ]);
        }

        // Process expenses
        foreach ($data['expenses'] as $expense) {

            $excluding = floatval($expense->amount);
            $vat = floatval($expense->totalVatAmount);
            $including = $excluding + $vat;

            $totalExVat += $excluding;
            $totalVat += $vat;
            $totalIncVat += $including;

            fputcsv($csv, [
                $expense->expense_number ?? '',
                Carbon::parse($expense->created_at)->format('d-m-Y'),
                'Expense',
                number_format($expense->amount, 2),
                number_format($expense->totalVatAmount, 2),
                number_format($expense->amount + $expense->totalVatAmount, 2),
                $expense->branch?->name ?? '',
            ]);
        }




        // Add footer row with totals
        fputcsv($csv, [
            '',
            '',
            'Total',
            number_format($totalExVat, 2),
            number_format($totalVat, 2),
            number_format($totalIncVat, 2),
            ''
        ]);

        fclose($csv);
        $csvOutput = ob_get_clean();

        return $csvOutput;
    }


    public function downloadAsZip(Request $request)
    {




        $invoiceIds = $request['invoiceIds'];
        $returnIds = $request['returnIds'];
        $expenseIds = $request['expenseIds'];



        $settings = Setting::all()->pluck('value', 'key')->toArray();
        $bankDetails = Bank::first();

        $zipFileName = 'VAT_reports_' . time() . '.zip';
        $tempDirectory = storage_path('app/tmp_vat_reports');
        $zipFilePath = storage_path("app/{$zipFileName}");
        // Create a temporary directory for the PDFs
        if (!file_exists($tempDirectory)) {
            mkdir($tempDirectory, 0777, true);
        }



        if (!empty($invoiceIds)) {
            foreach ($invoiceIds as $invoiceId) {
                $invoice = $this->invoiceRepository->getSyncListForInvoiceDetail($invoiceId);

                $totalPaid = 0;
                foreach ($invoice->payments as $payment) {
                    $totalPaid += $payment->amount_received;
                }


                $subtotal = $totalTaxable = $totalVat = 0;

                foreach ($invoice->salesItems as $item) {
                    $subtotal += ($item->quantity * $item->rate);
                    $totalTaxable += ($item->quantity * $item->rate) - $item->discount;
                    $totalVat += (($item->quantity * $item->rate) - $item->discount) * .15;
                }

                $words = $this->amountToWords($invoice->total_amount);

                // Data for the view
                $data = [
                    'invoice' => $invoice,
                    'settings' => $settings,
                    'subtotal' => $subtotal,
                    'totalTaxable' => $totalTaxable,
                    'totalVat' => $totalVat,
                    'words' => $words,
                    'bank' => $bankDetails,
                ];

                // Generate PDF
                $pdf = PDF::loadView('invoices.invoice_pdf_v3', $data);
                $pdf->setPaper('A4', 'portrait');


                $date = Carbon::parse($invoice->invoice_date)->format('d-m-Y'); // Format the invoice date
                $pdfFilePath = "{$tempDirectory}/{$date}_invoice_{$invoice->invoice_number}.pdf";

                $pdf->save($pdfFilePath);
            }
        }


        if (!empty($returnIds)) {
            foreach ($returnIds as $return_id) {
                $creditNote = $this->creditNoteRepository->getSyncListForCreditNoteDetail($return_id);
                $currency = Customer::CURRENCIES[$creditNote->currency];
                $creditNote->load(['invoice', 'invoice.project', 'invoice.payments.paymentMode']);

                $subtotal = 0;
                $totalTaxable = 0;
                $totalVat = 0;
                // Loop through the sales items to calculate totals
                foreach ($creditNote->salesItems as $item) {
                    //$itemVATAmount = $itemSubtotalExcludingVAT * ($item->tax / 100);
                    $subtotal += ($item->quantity * $item->rate);
                    $totalTaxable += ($item->quantity * $item->rate) - $item->discount;
                    $totalVat += (($item->quantity * $item->rate) - $item->discount) * .15;
                }
                $words = $this->amountToWords($creditNote->total_amount);
                $data = [

                    'creditNote' => $creditNote,
                    'settings' => $settings,
                    'subtotal' => $subtotal,
                    'totalTaxable' => $totalTaxable,
                    'totalVat' => $totalVat,
                    'words' => $words,
                    'bank' => $bankDetails
                ];

                $pdf = PDF::loadView('credit_notes.credit_note_pdf_v3', $data);
                $pdf->setPaper('A4', 'portrait');
                $pdf->setOptions(["isPhpEnabled" => true, 'isHtml5ParserEnabled' => true]);
                $date = Carbon::parse($creditNote->credit_note_date)->format('d-m-Y'); // Format the credit note date
                $pdfFilePath = "{$tempDirectory}/{$date}_CreditNote_{$creditNote->credit_note_number}.pdf";
                $pdf->save($pdfFilePath);
            }
        }




        if (!empty($expenseIds)) {

            foreach ($expenseIds as $expense_id) {


                $expense = Expense::where('id', $expense_id)->first();
                $expense->load(['paymentMode', 'currencyNew']);


                $data = $this->expenseRepository->getReminderData($expense->id, Expense::class);
                $comments = $this->expenseRepository->getCommentData($expense);
                $notes = $this->expenseRepository->getNotesData($expense);
                $groupName = (request('group') == null) ? 'expense_details' : request('group');

                // Load the view with the data
                $pdf = Pdf::loadView('expenses.views.pdf', compact('expense', 'data', 'comments', 'notes', 'groupName'));

                $pdf->setPaper('A4', 'portrait');
                $pdf->setOptions(["isPhpEnabled" => true, 'isHtml5ParserEnabled' => true]);

                $date = Carbon::parse($expense->expense_date)->format('d-m-Y'); // Format the expense date
                $pdfFilePath = "{$tempDirectory}/{$date}_Expense_{$expense->expense_number}.pdf";
                $pdf->save($pdfFilePath);
            }
        }



        // Create a ZIP file
        $zip = new ZipArchive();
        if ($zip->open($zipFilePath, ZipArchive::CREATE) === true) {
            $pdfFiles = glob("{$tempDirectory}/*.pdf"); // Get all PDF files
            foreach ($pdfFiles as $file) {
                $zip->addFile($file, basename($file));
            }
            $zip->close();
        }

        // Clean up temporary PDF files
        foreach ($pdfFiles as $file) {
            unlink($file);
        }
        rmdir($tempDirectory); // Remove the temporary directory

        // Return the ZIP file as a download response
        return response()->download($zipFilePath)->deleteFileAfterSend(true);
    }
}
