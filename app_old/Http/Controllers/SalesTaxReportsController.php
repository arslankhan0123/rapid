<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\User;
use App\Queries\SalesTaxReportsDataTable;
use App\Repositories\InvoiceRepository;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Yajra\DataTables\DataTables;

class SalesTaxReportsController extends AppBaseController
{
    /**
     * @var SalesInvoiceRepository
     */
    private $InvoiceRepository;

    public function __construct(InvoiceRepository $salesInvoiceRepo)
    {
        $this->InvoiceRepository = $salesInvoiceRepo;
    }

    // public function index(Request $request)
    // {
    //     if ($request->ajax()) {
    //         return DataTables::of((new SalesTaxReportsDataTable())->get($request->all()))
    //             ->editColumn('tax_amount', function ($invoice) {
    //                 return number_format($invoice->tax_amount, 2);
    //             })
    //             ->editColumn('total_amount', function ($invoice) {
    //                 return number_format($invoice->total_amount, 2);
    //             })
    //             ->make(true);
    //     }

    //     $customers = Invoice::select('customer_id')
    //         ->distinct()
    //         ->orderBy('customer_id')
    //         ->pluck('customer_id', 'customer_id');

    //     return view('sales-tax-reports.index', compact('customers'));
    // }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            return DataTables::of((new SalesTaxReportsDataTable())->get($request->all()))
                ->addColumn('tax_amount', function ($invoice) {
                    return number_format(SalesTaxReportsDataTable::calculateTaxAmount($invoice), 2);
                })
                ->addColumn('taxable_amount', function ($invoice) {
                    return number_format(SalesTaxReportsDataTable::calculateTaxableAmount($invoice), 2);
                })
                ->addColumn('customer_name', function ($invoice) {
                    return $invoice->customer ? $invoice->customer->company_name : 'N/A';
                })
                ->editColumn('total_amount', function ($invoice) {
                    return number_format($invoice->total_amount, 2);
                })
                ->editColumn('invoice_date', function ($invoice) {
                    return $invoice->invoice_date ? \Carbon\Carbon::parse($invoice->invoice_date)->format('d M Y') : 'N/A';
                })
                ->make(true);
        }

        $customers = Customer::select('id', 'company_name')
            ->orderBy('company_name')
            ->pluck('company_name', 'id');

        return view('sales-tax-reports.index', compact('customers'));
    }


    public function export(Request $request)
    {
        // Get the query from DataTable
        $query = (new SalesTaxReportsDataTable())->get($request->all());
        $invoices = $query->get();

        $exportData = [];
        foreach ($invoices as $index => $invoice) {
            // Calculate amounts directly for each invoice
            $taxAmount = $invoice->salesItems->sum('tax');
            $taxableAmount = $invoice->salesItems->sum('total') - $taxAmount;

            $exportData[] = [
                '#'              => $index + 1,
                'Invoice Number' => $invoice->invoice_number ?? 'N/A',
                'Customer Name'  => $invoice->customer->company_name ?? 'N/A',
                'Date'           => !empty($invoice->invoice_date)
                    ? \Carbon\Carbon::parse($invoice->invoice_date)->format('d-m-Y')
                    : 'N/A',
                'Taxable Amount' => number_format($taxableAmount, 2),
                'Tax Amount'     => number_format($taxAmount, 2),
                'Total Amount'   => number_format($invoice->total_amount, 2),
            ];
        }

        $type = $request->get('type');

        if ($type === 'pdf') {
            // Configure DomPDF for Arabic support
            $pdf = Pdf::setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'defaultFont' => 'cairo'
            ])->loadView('sales-tax-reports.pdf_list', compact('exportData'));


            return $pdf->download('sales-tax-reports.pdf');
        } elseif ($type === 'xls') {
            return Excel::download(new class($exportData) implements FromCollection, WithHeadings, WithStyles, WithCustomStartCell {
                protected $data;

                public function __construct($data)
                {
                    $this->data = $data;
                }

                public function startCell(): string
                {
                    return 'A2';
                }

                public function collection()
                {
                    // Calculate totals
                    $taxableTotal = $taxTotal = $totalAmount = 0;
                    foreach ($this->data as $row) {
                        $taxableTotal += (float) str_replace(',', '', $row['Taxable Amount']);
                        $taxTotal += (float) str_replace(',', '', $row['Tax Amount']);
                        $totalAmount += (float) str_replace(',', '', $row['Total Amount']);
                    }

                    // Create a copy of data to avoid modifying original
                    $exportData = $this->data;

                    // Append total row
                    $exportData[] = [
                        '#' => 'Total:',
                        'Invoice Number' => '',
                        'Customer Name' => '',
                        'Date' => '',
                        'Taxable Amount' => number_format($taxableTotal, 2),
                        'Tax Amount' => number_format($taxTotal, 2),
                        'Total Amount' => number_format($totalAmount, 2),
                    ];

                    return collect($exportData);
                }

                public function headings(): array
                {
                    return [
                        '#',
                        'Invoice Number',
                        'Customer Name',
                        'Date',
                        'Taxable Amount',
                        'Tax Amount',
                        'Total Amount',
                    ];
                }

                public function styles(Worksheet $sheet)
                {
                    // Page title in row 1
                    $sheet->mergeCells('A1:G1');
                    $sheet->setCellValue('A1', config('app.name') . ' - Sales Tax Report');
                    $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
                    $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');

                    // Bold header row
                    $sheet->getStyle('A2:G2')->getFont()->setBold(true);

                    // Bold total row
                    $lastRow = count($this->data) + 3; // title + header + data rows
                    $sheet->getStyle("A{$lastRow}:G{$lastRow}")->getFont()->setBold(true);
                    $sheet->mergeCells("A{$lastRow}:D{$lastRow}");
                    $sheet->setCellValue("A{$lastRow}", 'Total:');
                    $sheet->getStyle("A{$lastRow}")->getAlignment()->setHorizontal('right');

                    // Set number formatting for amount columns
                    $dataRowCount = count($this->data) + 2;
                    $sheet->getStyle("E3:G{$dataRowCount}")->getNumberFormat()->setFormatCode('#,##0.00');

                    // Auto-size columns for better display
                    foreach (range('A', 'G') as $column) {
                        $sheet->getColumnDimension($column)->setAutoSize(true);
                    }
                }
            }, 'sales_tax_reports.xlsx');
        } elseif ($type === 'csv') {
            $filename = 'sales_tax_reports_export_' . now()->format('Y-m-d_H-i') . '.csv';
            $handle = fopen('php://output', 'w');

            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header('Pragma: no-cache');
            header('Expires: 0');

            // Write CSV header
            fputcsv($handle, ['#', 'Invoice Number', 'Customer Name', 'Date', 'Taxable Amount', 'Tax Amount', 'Total Amount']);

            // Calculate totals for CSV
            $taxableTotal = $taxTotal = $totalAmount = 0;

            foreach ($exportData as $index => $row) {
                fputcsv($handle, $row);

                // Calculate totals
                $taxableTotal += (float) str_replace(',', '', $row['Taxable Amount']);
                $taxTotal += (float) str_replace(',', '', $row['Tax Amount']);
                $totalAmount += (float) str_replace(',', '', $row['Total Amount']);
            }

            // Add total row to CSV
            fputcsv($handle, [
                'Total:',
                '',
                '',
                '',
                number_format($taxableTotal, 2),
                number_format($taxTotal, 2),
                number_format($totalAmount, 2)
            ]);

            fclose($handle);
            exit;
        } elseif ($type === 'print') {
            return view('sales-tax-reports.print_list', compact('exportData'));
        } else {
            return response()->json(['message' => 'Invalid export type'], 400);
        }
    }
}