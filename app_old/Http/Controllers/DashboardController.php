<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Contract;
use App\Models\Expense;
use App\Models\Invoice;
use App\Models\LeadStatus;
use App\Models\Project;
use App\Models\TicketStatus;
use App\Repositories\CustomerRepository;
use App\Repositories\EstimateRepository;
use App\Repositories\InvoiceRepository;
use App\Repositories\MemberRepository;
use App\Repositories\ProjectRepository;
use App\Repositories\ProposalRepository;
use Carbon\Carbon;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Notice;
use App\Models\Appointment;
use App\Repositories\EmployeeRepository;
use App\Queries\EmployeeIDExpireDataTable;
use Yajra\DataTables\DataTables;
use App\Models\Employee;
use App\Models\PaymentMode;
use App\Repositories\AccountRepository;
use App\Models\SalaryGenerate;


/**
 * Class DashboardController
 */
class DashboardController extends AppBaseController
{
    /** @var InvoiceRepository */
    private $invoiceRepository;

    /** @var ProposalRepository */
    private $proposalRepository;

    /** @var EstimateRepository */
    private $estimateRepository;

    /** @var CustomerRepository */
    private $customerRepository;

    /** @var ProjectRepository */
    private $projectRepository;

    /** @var MemberRepository */
    private $memberRepository;
    private $employeeRepository;
    private $accountRepository;

    public function __construct(
        InvoiceRepository $invoiceRepository,
        ProposalRepository $proposalRepository,
        EstimateRepository $estimateRepository,
        CustomerRepository $customerRepository,
        ProjectRepository $projectRepository,
        MemberRepository $memberRepository,
        EmployeeRepository $employeeRepo,
        AccountRepository $accountRepo
    ) {
        $this->invoiceRepository = $invoiceRepository;
        $this->proposalRepository = $proposalRepository;
        $this->estimateRepository = $estimateRepository;
        $this->customerRepository = $customerRepository;
        $this->projectRepository = $projectRepository;
        $this->memberRepository = $memberRepository;
        $this->employeeRepository = $employeeRepo;
        $this->accountRepository = $accountRepo;
    }

    /**
     * @return Factory|View
     */
    public function index()
    {




        $accounts = $this->accountRepository->getAccountsByBranch();

        $salaryGenerate = SalaryGenerate::with(['salarySheets.employee', 'branch', 'salarySheets'])
            ->where('status', 1)
            ->orderByDesc('salary_month') // Order by salary_month in descending order
            ->first(); // Get the first result

        // Check if salaryGenerate is not empty
        if ($salaryGenerate) {
            $sheets = $salaryGenerate->salarySheets;
        } else {
            $sheets = [];
        }






        $notices = Notice::where('show', 1)->latest()->take(3)->get();

        $data['invoiceStatusCount'] = $this->invoiceRepository->getInvoicesStatusCount();
        $data['proposalStatusCount'] = $this->proposalRepository->getProposalsStatusCount();
        $data['estimateStatusCount'] = $this->estimateRepository->getEstimatesStatusCount();
        $data['projectStatusCount'] = $this->projectRepository->getProjectsStatusCount();
        $data['customerCount'] = $this->customerRepository->customerCount();
        $data['memberCount'] = $this->memberRepository->memberCount();
        $leadStatuses = LeadStatus::withCount('leads')->get();
        $ticketStatus = TicketStatus::withCount('tickets')->get();
        $projectStatus = Project::STATUS;

        $data['contractsCurrentMonths'] = Contract::with('customer')->whereMonth(
            'end_date',
            Carbon::now()->month
        )->get();

        $data['currentMonth'] = Carbon::now()->month;

        $weekNames = [
            1 => 'Monday',
            2 => 'Tuesday',
            3 => 'Wednesday',
            4 => 'Thursday',
            5 => 'Friday',
            6 => 'Saturday',
            7 => 'Sunday',
        ];

        $currentWeekInvoicePayments = Invoice::query()
            ->where('payment_status', Invoice::STATUS_PAID)
            ->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
            ->select(['created_at', 'total_amount'])->get()->groupBy(function ($data) {
                return Carbon::parse($data->created_at)->dayOfWeek;
            });

        $lastWeekInvoicePayments = Invoice::query()
            ->where('payment_status', Invoice::STATUS_PAID)
            ->whereBetween('created_at', [Carbon::now()->subWeek()->startOfWeek(), Carbon::now()->subWeek()->endOfWeek()])
            ->select(['created_at', 'total_amount'])->get()->groupBy(function ($data) {
                return Carbon::parse($data->created_at)->dayOfWeek;
            });

        $data['currentWeekInvoices'] = [];
        $data['lastWeekInvoices'] = [];

        foreach ($weekNames as $dayOfWeek => $dayName) {
            $currentWeekInvoicePayment = $currentWeekInvoicePayments->get($dayOfWeek);
            $data['currentWeekInvoices'][$dayName] = $currentWeekInvoicePayment ? $currentWeekInvoicePayment->sum('total_amount') : 0;
            $lastWeekInvoicePayment = $lastWeekInvoicePayments->get($dayOfWeek);
            $data['lastWeekInvoices'][$dayName] = $lastWeekInvoicePayment ? $lastWeekInvoicePayment->sum('total_amount') : 0;
        }

        $invoices = Invoice::whereYear('created_at', Carbon::now()->year)
            ->select(DB::raw('MONTH(created_at) as month,invoices.*'))->get();
        $expenses = Expense::whereYear('created_at', Carbon::now()->year)
            ->select(DB::raw('MONTH(created_at) as month,expenses.*'))->get();
        $months = [
            1 => 'January',
            2 => 'February',
            3 => 'March',
            4 => 'April',
            5 => 'May',
            6 => 'June',
            7 => 'July',
            8 => 'August',
            9 => 'September',
            10 => 'October',
            11 => 'November',
            12 => 'December',
        ];

        $monthWiseRecords = [];

        $employees = Employee::all();
        $employeeStatus = [
            'active' => $employees->filter(fn($employee) => $employee->status == 1)->count(),
            'inactive' => $employees->filter(fn($employee) => $employee->status == 0)->count(),
        ];


        foreach ($months as $month => $monthName) {
            $monthWiseRecords['income'][$monthName] = $invoices->where('month', $month)
                ->where('payment_status', Invoice::STATUS_PAID)->sum('total_amount');
            $monthWiseRecords['expenses'][$monthName] = $expenses->where('month', $month)
                ->whereNotNull('payment_mode_id')->sum('amount');
        }

        // Assuming you have paymentMode IDs for each
        $paymentModes = PaymentMode::whereIn('name', ['Bank Transfer', 'Main Cash', 'Petty Cash'])->get();

        $paymentModeResult = [];
        foreach ($paymentModes as $paymentMode) {
            // Get the total amount for each payment mode
            $totalAmount = Expense::select(['amount', 'payment_mode_id'])
                ->where('payment_mode_id', $paymentMode->id)
                ->sum('amount'); // Assuming the "amount" column exists on the Expense model

            $paymentModeResult[] = [
                'payment_mode' => $paymentMode->name,
                'total_amount' => $totalAmount
            ];
        }


        $accountSums = Account::select('account_name')
            ->selectRaw('SUM(opening_balance) as total_balance')
            ->groupBy('account_name')
            ->get();

        $totalExpenseAmount = Expense::sum('amount');
        
        $today = Carbon::today()->toDateString();

        $appointment_list = Appointment::orderByRaw("
            CASE 
                WHEN appointment_date = ? THEN 0
                WHEN appointment_date > ? THEN 1
                ELSE 2
            END ASC,
            appointment_date ASC,
            appointment_time ASC
        ", [$today, $today])->get();
        

        
        return view(
            'dashboard.dashboard',
            compact('leadStatuses', 'totalExpenseAmount', 'accounts', 'accountSums', 'salaryGenerate', 'sheets', 'projectStatus', 'ticketStatus', 'monthWiseRecords', 'months', 'notices', 'employeeStatus', 'paymentModeResult', 'appointment_list')
        )->with($data);
    }
    public function getExpireIdentications(Request $request)
    {
        if ($request->ajax()) {
            return DataTables::of((new EmployeeIDExpireDataTable())->get($request->all()))->make(true);
        }
    }

    /**
     * @param  Request  $request
     * @return mixed
     */
    public function contractMonthFilter(Request $request)
    {
        $filterMonth = $request->get('month');

        $contractsCurrentMonths = Contract::with('customer')->whereMonth(
            'end_date',
            $filterMonth
        )->get();

        return $this->sendResponse($contractsCurrentMonths, 'Contract Month Filter retrieved successfully.');
    }

    public function expireList()
    {
        $months = [
            1 => 'January',
            2 => 'February',
            3 => 'March',
            4 => 'April',
            5 => 'May',
            6 => 'June',
            7 => 'July',
            8 => 'August',
            9 => 'September',
            10 => 'October',
            11 => 'November',
            12 => 'December',
        ];
        // Get the current month in numeric format (e.g., '09' for September)
        $currentMonth = Carbon::now()->format('n');

        return view('dashboard.expiry', compact(['months', 'currentMonth']));
    }
}
