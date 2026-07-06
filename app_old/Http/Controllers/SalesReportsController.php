<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\User;
use App\Queries\SalesReportsDataTable;
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

class SalesReportsController extends AppBaseController
{
    /**
     * @var SalesInvoiceRepository
     */
    private $InvoiceRepository;

    public function __construct(InvoiceRepository $salesInvoiceRepo)
    {
        $this->InvoiceRepository = $salesInvoiceRepo;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     *
     * @throws \Exception
     */
    // public function index(Request $request)
    // {
    //     if ($request->ajax()) {
    //         return DataTables::of((new SalesReportsDataTable())->get($request->all()))->make(true);
    //     }

    //     return view('sales-reports.index');
    // }
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return DataTables::of((new SalesReportsDataTable())->get($request->all()))->make(true);
        }

        // Get customers from Customer model
        $customers = Customer::select('id', 'company_name')
            ->orderBy('company_name')
            ->pluck('company_name', 'id');

        return view('sales-reports.index', compact('customers'));
    }


    public function export(Request $request)
    {
        // Get the query from DataTable
        $query = (new SalesReportsDataTable())->get($request->all());
        $invoices = $query->get();

        $exportData = [];
        foreach ($invoices as $index => $invoice) {
            // Calculate amounts
            $taxAmount = $invoice->salesItems->sum('tax');
            $taxableAmount = $invoice->total_amount - $taxAmount;

            $exportData[] = [
                '#' => $index + 1,
                'Invoice Number' => $invoice->invoice_number ?? 'N/A',
                'Customer Name' => $invoice->customer->company_name ?? 'N/A',
                'Invoice Date' => $invoice->invoice_date ? \Carbon\Carbon::parse($invoice->invoice_date)->format('d-m-Y') : 'N/A',
                'Discount' => number_format($invoice->discount, 2),
                'Taxable Amount' => number_format($taxableAmount, 2),
                'Tax Amount' => number_format($taxAmount, 2),
                'Total Amount' => number_format($invoice->total_amount, 2),
            ];
        }

        $type = $request->get('type');

        if ($type === 'pdf') {
            $pdf = Pdf::setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'defaultFont' => 'DejaVu Sans', // Changed from 'dejavu sans'
                'chroot' => realpath(base_path()),
            ])
                ->setPaper('a4', 'landscape') // Set to landscape for better table display
                ->loadView('sales-reports.pdf_list', compact('exportData'));

            return $pdf->download('sales-reports.pdf');
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
                    $discount = $taxable = $tax = $total = 0;
                    foreach ($this->data as $row) {
                        $discount += (float) str_replace(',', '', $row['Discount']);
                        $taxable += (float) str_replace(',', '', $row['Taxable Amount']);
                        $tax += (float) str_replace(',', '', $row['Tax Amount']);
                        $total += (float) str_replace(',', '', $row['Total Amount']);
                    }

                    $exportData = $this->data;
                    $exportData[] = [
                        '#' => 'Total:',
                        'Invoice Number' => '',
                        'Customer Name' => '',
                        'Invoice Date' => '',
                        'Discount' => number_format($discount, 2),
                        'Taxable Amount' => number_format($taxable, 2),
                        'Tax Amount' => number_format($tax, 2),
                        'Total Amount' => number_format($total, 2),
                    ];

                    return collect($exportData);
                }

                public function headings(): array
                {
                    return [
                        '#',
                        'Invoice Number',
                        'Customer Name',
                        'Invoice Date',
                        'Discount',
                        'Taxable Amount',
                        'Tax Amount',
                        'Total Amount',
                    ];
                }

                public function styles(Worksheet $sheet)
                {
                    $sheet->mergeCells('A1:H1');
                    $sheet->setCellValue('A1', config('app.name') . ' - Sales Report');
                    $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
                    $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');

                    $sheet->getStyle('A2:H2')->getFont()->setBold(true);

                    $lastRow = count($this->data) + 3;
                    $sheet->getStyle("A{$lastRow}:H{$lastRow}")->getFont()->setBold(true);
                    $sheet->mergeCells("A{$lastRow}:D{$lastRow}");
                    $sheet->setCellValue("A{$lastRow}", 'Total:');
                    $sheet->getStyle("A{$lastRow}")->getAlignment()->setHorizontal('right');

                    foreach (range('A', 'H') as $column) {
                        $sheet->getColumnDimension($column)->setAutoSize(true);
                    }
                }
            }, 'sales_reports.xlsx');
        }
    }
}
