<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\User;
use App\Queries\SalesItemReportsDataTable;
use App\Repositories\PurchaseItemRepository;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Yajra\DataTables\DataTables;

class SalesItemReportsController extends AppBaseController
{
    /**
     * @var PurchaseItemRepository
     */
    private $purchaseItemRepository;

    public function __construct(PurchaseItemRepository $purchaseItemRepo)
    {
        $this->purchaseItemRepository = $purchaseItemRepo;
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            return DataTables::of((new SalesItemReportsDataTable())->get($request->all()))
                ->editColumn('rate', function ($item) {
                    return number_format($item->rate, 2);
                })
                ->editColumn('total', function ($item) {
                    return number_format($item->total, 2);
                })
                ->make(true);
        }

        $customers = Customer::select('id', 'company_name')
            ->orderBy('company_name')
            ->pluck('company_name', 'id');

        return view('sales-item-reports.index', compact('customers'));
    }

    public function export(Request $request)
    {
        // Get the query from DataTable
        $query = (new SalesItemReportsDataTable())->get($request->all());
        $salesItems = $query->get();

        $exportData = [];
        foreach ($salesItems as $index => $item) {
            $exportData[] = [
                '#' => $index + 1,
                'Invoice Number' => $item->invoice->invoice_number ?? 'N/A',
                'Customer Name' => $item->invoice->customer->company_name ?? 'N/A',
                'Invoice Date' => $item->invoice_date ? \Carbon\Carbon::parse($item->invoice_date)->format('d-m-Y') : 'N/A',
                'Item' => $item->item ?? 'N/A',
                'Quantity' => number_format($item->quantity, 2),
                'Rate' => number_format($item->rate, 2),
                'Total' => number_format($item->total, 2),
            ];
        }

        $type = $request->get('type');

        if ($type === 'pdf') {
            $pdf = Pdf::setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'defaultFont' => 'dejavu sans',
                'chroot' => realpath(base_path()),
            ])->loadView('sales-item-reports.pdf_list', compact('exportData'));

            return $pdf->download('sales-item-reports.pdf');
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
                    $quantityTotal = $rateTotal = $totalAmount = 0;
                    foreach ($this->data as $row) {
                        $quantityTotal += (float) str_replace(',', '', $row['Quantity']);
                        $rateTotal += (float) str_replace(',', '', $row['Rate']);
                        $totalAmount += (float) str_replace(',', '', $row['Total']);
                    }

                    $exportData = $this->data;
                    $exportData[] = [
                        '#' => 'Total:',
                        'Invoice Number' => '',
                        'Customer Name' => '',
                        'Invoice Date' => '',
                        'Item' => '',
                        'Quantity' => number_format($quantityTotal, 2),
                        'Rate' => number_format($rateTotal, 2),
                        'Total' => number_format($totalAmount, 2),
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
                        'Item',
                        'Quantity',
                        'Rate',
                        'Total',
                    ];
                }

                public function styles(Worksheet $sheet)
                {
                    $sheet->mergeCells('A1:H1');
                    $sheet->setCellValue('A1', config('app.name') . ' - Sales Item Report');
                    $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
                    $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');

                    $sheet->getStyle('A2:H2')->getFont()->setBold(true);

                    $lastRow = count($this->data) + 3;
                    $sheet->getStyle("A{$lastRow}:H{$lastRow}")->getFont()->setBold(true);
                    $sheet->mergeCells("A{$lastRow}:E{$lastRow}");
                    $sheet->setCellValue("A{$lastRow}", 'Total:');
                    $sheet->getStyle("A{$lastRow}")->getAlignment()->setHorizontal('right');

                    foreach (range('A', 'H') as $column) {
                        $sheet->getColumnDimension($column)->setAutoSize(true);
                    }
                }
            }, 'sales_item_reports.xlsx');
        }
        // ... Add CSV and Print if needed
    }

    // public function export(Request $request)
    // {
    //     // Fetch data using the existing DataTable
    //     $salesItemData = DataTables::of((new SalesItemReportsDataTable())->get($request->all()))->make(true);
    //     $salesItems = $salesItemData->original['data'] ?? [];
    //     // dd($salesItems);
    //     $exportData = [];
    //     foreach ($salesItems as $index => $item) {
    //         $exportData[] = [
    //             '#'           => $index + 1,
    //             'Invoice No'  => $item['po_number'] ?? 'N/A',
    //             'Customer'    => $item['customer_name'] ?? 'N/A',
    //             'Sales Date'  => !empty($item['sales_date'])
    //                 ? \Carbon\Carbon::parse($item['sales_date'])->format('d-m-Y')
    //                 : 'N/A',
    //             'Item'        => $item['item'] ?? 'N/A',
    //             'Quantity'    => $item['quantity'] ?? 0,
    //             'Unit Price'  => number_format($item['rate'] ?? 0, 2),
    //             'Total'       => number_format($item['total'] ?? 0, 2),
    //         ];
    //     }

    //     $type = $request->get('type');

    //     if ($type === 'pdf') {
    //         $pdf = Pdf::loadView('reports.sales-item-reports.pdf_list', compact('exportData'));
    //         return $pdf->download('sales-item-reports.pdf');
    //     } elseif ($type === 'xls') {
    //         return Excel::download(new class($exportData) implements FromCollection, WithHeadings, WithStyles, WithCustomStartCell {
    //             protected $data;

    //             public function __construct($data)
    //             {
    //                 $this->data = $data;
    //             }

    //             public function startCell(): string
    //             {
    //                 return 'A2';
    //             }

    //             public function collection()
    //             {
    //                 // Calculate totals for numeric columns
    //                 $quantityTotal = $totalAmount = 0;
    //                 foreach ($this->data as $row) {
    //                     $quantityTotal += (float) $row['Quantity'];
    //                     $totalAmount += (float) str_replace(',', '', $row['Total']);
    //                 }

    //                 // Append total row
    //                 $this->data[] = [
    //                     '#' => 'Total:',
    //                     'Invoice No' => '',
    //                     'Customer' => '',
    //                     'Sales Date' => '',
    //                     'Item' => '',
    //                     'Quantity' => $quantityTotal,
    //                     'Unit Price' => '',
    //                     'Total' => number_format($totalAmount, 2),
    //                 ];

    //                 return collect($this->data);
    //             }

    //             public function headings(): array
    //             {
    //                 return ['#', 'Invoice No', 'Customer', 'Sales Date', 'Item', 'Quantity', 'Unit Price', 'Total'];
    //             }

    //             public function styles(Worksheet $sheet)
    //             {
    //                 // Title row
    //                 $sheet->mergeCells('A1:H1');
    //                 $sheet->setCellValue('A1', config('app.name') . ' - Sales Item Report');
    //                 $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
    //                 $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');

    //                 // Bold headings
    //                 $sheet->getStyle('A2:H2')->getFont()->setBold(true);

    //                 // Bold total row
    //                 $lastRow = count($this->data) + 2; // +2 because title + headings
    //                 $sheet->getStyle("A{$lastRow}:H{$lastRow}")->getFont()->setBold(true);

    //                 // Merge cells for "Total:" label
    //                 $sheet->mergeCells("A{$lastRow}:E{$lastRow}");
    //                 $sheet->getStyle("A{$lastRow}")->getAlignment()->setHorizontal('right');
    //             }
    //         }, 'sales_item_reports.xlsx');
    //     } elseif ($type === 'csv') {
    //         $filename = 'sales_item_reports_export_' . now()->format('Y-m-d_H-i') . '.csv';
    //         $handle = fopen('php://output', 'w');

    //         header('Content-Type: text/csv');
    //         header('Content-Disposition: attachment; filename="' . $filename . '"');
    //         header('Pragma: no-cache');
    //         header('Expires: 0');

    //         // Write CSV header
    //         fputcsv($handle, ['#', 'Invoice No', 'Customer', 'Sales Date', 'Item', 'Quantity', 'Unit Price', 'Total']);

    //         foreach ($exportData as $row) {
    //             fputcsv($handle, $row);
    //         }

    //         fclose($handle);
    //         exit;
    //     } elseif ($type === 'print') {
    //         return view('reports.sales-item-reports.print_list', compact('exportData'));
    //     } else {
    //         return response()->json(['message' => 'Invalid export type'], 400);
    //     }
    // }
}
