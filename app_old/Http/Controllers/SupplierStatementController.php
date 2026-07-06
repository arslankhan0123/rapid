<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Queries\CustomerStatementDataTableNew;
use App\Queries\SuppliertatementDataTable;
use App\Repositories\SupplierRepository;
use App\Repositories\SupplierStatementRepository;
use Illuminate\Http\Request;
use App\Repositories\PayslipRepository;
use Yajra\DataTables\DataTables;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Exception;
use App\Http\Requests\SalaryGenerateRequest;
use App\Http\Requests\UpdateSalaryGenerateRequest;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use Throwable;
use App\Models\SalaryGenerate;
use App\Models\SalarySheet;
use App\Models\Salary;
use App\Models\Bonus;
use App\Models\Loan;
use App\Models\Commission;
use App\Models\Insurance;
use Barryvdh\DomPDF\Facade as PDF;
use App\Models\Setting;
use App\Models\Customer;
use Illuminate\Support\Facades\Response;
use App\Models\Branch;

use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SupplierStatementController extends AppBaseController
{


    private $supplierRepository;
    public function __construct(SupplierStatementRepository $supplierRepo)
    {
        $this->supplierRepository = $supplierRepo;
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            return DataTables::of((new SuppliertatementDataTable())->get($request->all()))->make(true);
        }

        $suppliers = $this->supplierRepository->getSuppliers();
        $projects = [];
        $usersBranches = $this->getUsersBranches();

        return view('supplier_statements.index', compact(['suppliers', 'projects', 'usersBranches']));
    }

    public function export(Request $request)
    {


        $branch_name = '';



        if (!$request->has('customer_select') || !$request['customer_select']) {
            return redirect()->back()->with('error', 'Customer ID is required.'); // Redirect back with an error message
        }

        $customer = Supplier::with(['supplierState'])->find($request['customer_select']);
        $companyName = str_replace(' ', '-', $customer->company_name);

        // Retrieve data from DataTables
        $data = DataTables::of((new SuppliertatementDataTable())->get($request->all()))->make(true);


        $data = $data->getData()->data;



        if ($request->type === 'xls') {

            $excelData = [];
            $totalDebit = 0;
            $totalCredit = 0;

            // Build data rows
            foreach ($data as $statement) {
                $debit = $statement->debit ?? 0.00;
                $credit = $statement->credit ?? 0.00;
                $balance = $statement->balance ?? 0.00;

                $totalDebit += $debit;
                $totalCredit += $credit;

                $excelData[] = [
                    $statement->invoice_number ?? '',
                    $statement->type ?? '',
                    $statement->invoice_date ?? '',
                    $statement->receipt_date ?? '',
                    $statement->month ?? '',
                    number_format($debit, 2),
                    number_format($credit, 2),
                    number_format($balance, 2),
                ];
            }

            // Add totals row
            $excelData[] = [
                '',
                '',
                '',
                '',
                'Total',
                number_format($totalDebit, 2),
                number_format($totalCredit, 2),
                number_format($totalCredit - $totalDebit, 2),
            ];


            $filename = 'Supplier_statements_' . $companyName . '.xlsx';


            // Use inline export via anonymous class
            return Excel::download(new class ($excelData) implements FromArray, WithHeadings {
                protected $data;

                public function __construct(array $data)
                {
                    $this->data = $data;
                }

                public function array(): array
                {
                    return $this->data;
                }

                public function headings(): array
                {
                    return [
                        'Invoice No',
                        'Invoice Type',
                        'Invoice Date',
                        'Receipt Date',
                        'Month',
                        'Debit',
                        'Credit',
                        'Balance',
                    ];
                }
            }, $filename);
        } else if ($request->type === 'pdf') {
            $settings = Setting::all()->pluck('value', 'key')->toArray();
            $customer = Supplier::with(['supplierCountry'])->where('id', $request['customer_select'])->first();
            // Data to pass to the view
            $data = [
                'settings' => $settings,
                'statement' => $data,
                'customer' => $customer,
                'start_date' => $request['from_date'] ?? '',
                'end_date' => $request['to_date'],
                'branch_name' => $branch?->name ?? '',
            ];

            // dd($customer->toArray());
            $pdf = PDF::loadView('supplier_statements.pdf_v3', $data);
            $pdf->setPaper('A4', 'portrait');
            $pdf->setOptions(["isPhpEnabled" => true, 'isHtml5ParserEnabled' => true]);
            $filename = 'Supplier_statements_' . $companyName . '.pdf';
            return $pdf->download($filename);
        }
    }
}
