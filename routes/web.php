<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\ArticleGroupController;
use App\Http\Controllers\AssetCategoryController;
use App\Http\Controllers\Auth as AuthController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\CertificateController;
use App\Http\Controllers\Clients;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\ContractTypeController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\CreditNoteController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\CustomerGroupController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\DepartmentNewController;
use App\Http\Controllers\EstimateController;
use App\Http\Controllers\ExpenseCategoryController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\GoalController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\LeadSourceController;
use App\Http\Controllers\LeadStatusController;
use App\Http\Controllers\Listing;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\OpeningStockController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PaymentModeController;
use App\Http\Controllers\PredefinedReplyController;
use App\Http\Controllers\PrintLabelController;
use App\Http\Controllers\ProductBrandController;
use App\Http\Controllers\ProductColorController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductGroupController;
use App\Http\Controllers\ProductSizeController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProposalController;
use App\Http\Controllers\PurchaseCategoryController;
use App\Http\Controllers\PurchaseGroupController;
use App\Http\Controllers\PurchaseInvoiceController;
use App\Http\Controllers\PurchaseItemController;
use App\Http\Controllers\PurchaseReturnController;
use App\Http\Controllers\PurchaseSubCategoryController;
use App\Http\Controllers\ReminderController;
use App\Http\Controllers\SampleReceivingController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\StockReportsController;
use App\Http\Controllers\SupplierStatementController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TaxRateController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\TicketPriorityController;
use App\Http\Controllers\TicketReplyController;
use App\Http\Controllers\TicketStatusController;
use App\Http\Controllers\TranslationManagerController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\Web;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DesignationController;
use App\Http\Controllers\ProductUnitController;
use App\Http\Controllers\SubDepartmentController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\NoticeController;
use App\Http\Controllers\SupplierGroupController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\BonusController;
use App\Http\Controllers\OverTimeController;
use App\Http\Controllers\LeaveController;
use App\Http\Controllers\IncrementController;
use App\Http\Controllers\AllowanceController;
use App\Http\Controllers\DeductionController;
use App\Http\Controllers\RetirementController;
use App\Http\Controllers\RentalController;
use App\Http\Controllers\SalaryController;
use App\Http\Controllers\AwardController;
use App\Http\Controllers\HolidayController;
use App\Http\Controllers\CommissionController;
use App\Http\Controllers\InsuranceController;
use App\Http\Controllers\LeaveApplicationController;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\SalaryGenerateController;
use App\Http\Controllers\EmployeeSalaryController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\StateController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\JobSourceController;
use App\Http\Controllers\JobRecruiterController;


use App\Http\Controllers\CityController;
use App\Http\Controllers\AreaController;
use App\Http\Controllers\SalaryAdvanceController;
use App\Http\Controllers\ManageAttendanceController;
use App\Http\Controllers\TerminationController;
use App\Http\Controllers\CurrencyController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\FinancialYearController;
use App\Http\Controllers\RestoreController;
use App\Http\Controllers\ProjectInvoicesController;
use App\Http\Controllers\CustomerStatementController;
use App\Http\Controllers\TaskStatusController;
use App\Http\Controllers\TermController;
use App\Http\Controllers\ExpenseSubCategoryController;
use App\Http\Controllers\ServiceCategoryController;
use App\Http\Controllers\SampleCategoryController;
use App\Http\Controllers\TaskAssignController;
use App\Http\Controllers\VatReportController;
use App\Http\Controllers\BankController;
use App\Http\Controllers\EmployeeStatementController;
use App\Http\Controllers\ProfitLossStatementController;
use App\Http\Controllers\TransferController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\CashTransferController;
use App\Http\Controllers\CasualEmployeeTimesheetController;
use App\Http\Controllers\JournalVoucherController;
use App\Http\Controllers\AccountStatementController;
use App\Http\Controllers\VehicleRentalController;
use App\Http\Controllers\OtpController;
use App\Http\Controllers\ApprovalLeaveController;
use App\Http\Controllers\BalanceSheetController;
use App\Http\Controllers\CasualEmployeeController;
use App\Http\Controllers\CompanyLoanController;
use App\Http\Controllers\CreditNoteReportController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\LoggedUserController;
use App\Http\Controllers\PrintCheckController;
use App\Http\Controllers\ManualSaleController;
use App\Http\Controllers\MasterAccountController;
use App\Http\Controllers\ProfitLossController;
use App\Http\Controllers\ProjectCalculationController;
use App\Http\Controllers\RevokeController;
use App\Http\Controllers\SafetyMaterialController;
use App\Http\Controllers\SalesItemReportsController;
use App\Http\Controllers\SalesReportsController;
use App\Http\Controllers\SalesTaxReportsController;
use App\Http\Controllers\TradingAccountController;
use Illuminate\Support\Facades\Artisan;
use Spatie\Permission\Models\Permission;
use Stevebauman\Location\Facades\Location;
use Carbon\Carbon;

Route::get('/otp-verify', [OtpController::class, 'showVerifyOtp'])->name('otp.verify');
Route::post('/otp-verify', [OtpController::class, 'verifyOtp'])->name('otp.verify.post');

Route::get('/', function () {
    $today = Carbon::now();
    if ($today->toDateString() > '2027-06-18') {
        return view('errors.expired');
    } else {
        return Redirect::to('/login');
    }
    
})->name('redirect.login');

Route::get('download/invoice/{number}', [InvoiceController::class, 'downloadPDF']);

Route::get('/add-specs-columns', function () {
    if (!Illuminate\Support\Facades\Schema::hasColumn('purchase_items', 'specifications')) {
        Illuminate\Support\Facades\Schema::table('purchase_items', function ($table) {
            $table->longText('specifications')->nullable();
            $table->longText('processor')->nullable();
            $table->string('ram')->nullable();
            $table->string('storage')->nullable();
            $table->string('casing')->nullable();
        });
        return 'Columns added successfully!';
    }
    return 'Columns already exist.';
});

Auth::routes(['verify' => true]);

// Route::get('/debug-salary-hours', [SalaryGenerateController::class, 'quickDebugHours']);
// Route::get('/debug-salary-branch-4-jan25', [SalaryGenerateController::class, 'debugSalaryBranch9January2025']);


Route::get('/clear', function () {

    Artisan::call('optimize:clear');

    return 'Application all kind of cache has been cleared';
});


Route::get('/setup-permissions', function () {
    $user = auth()->user(); // or use User::find(ID) if not logged in
    if (!$user) {
        return 'Not logged in.';
    }

    //Permission::findOrCreate('manage_dashboard_expire_list');
    //$user->givePermissionTo('manage_dashboard_expire_list');

    // $permissions = [
    //     'manage_dashboard_incomes_vs_expenses',
    //     'manage_dashboard_salary_sheet',
    //     'manage_dashboard_cash_flow',
    // ];

    // foreach ($permissions as $permission) {
    //     Permission::findOrCreate($permission);
    //     $user->givePermissionTo($permission);
    // }

    return 'Permission added to user: ' . $user->name;
});


/** account verification route */
Route::get('activate', [AuthController\RegisterController::class, 'verifyAccount'])->name('activate');

Route::get('/home', [HomeController::class, 'index'])->name('home');

Route::get('articles', [Web\ArticleController::class, 'index']);
Route::get('search-article', [Web\ArticleController::class, 'searchArticle'])->name('article.search');
Route::get('articles/{article}', [Web\ArticleController::class, 'show']);

// Impersonate admin routes
Route::get('/impersonate/{userId}', [MemberController::class, 'impersonate'])->name('impersonate');
Route::get('/impersonate-leave', [MemberController::class, 'impersonateLeave'])->name('impersonate.leave');

// Impersonate customer routes
Route::get('/contacts-impersonate/{userId}', [ContactController::class, 'impersonate'])->name('contacts.impersonate');
Route::get(
    '/contacts-impersonate-leave',
    [ContactController::class, 'impersonateLeave']
)->name('contacts.impersonate.leave');

//Header Notification
Route::get('/get-notifications', [NotificationController::class, 'index']);
Route::post(
    '/notification/{notification}/read',
    [NotificationController::class, 'readNotification']
)->name('notifications.read');
Route::post(
    '/read-all-notification',
    [NotificationController::class, 'readAllNotification']
)->name('notifications.read.all');

Route::middleware(['auth', 'xss', 'checkUserStatus', 'checkRoleUrl', 'super_admin_timeout'])->prefix('admin')->group(function () {
    // Dashboard route

    Route::get('/clear-raw-data', function () {
        // Array of configuration/system tables to preserve
        $excludedTables = [
            'users',
            'settings',
            'migrations',
            'failed_jobs',
            'password_resets',
            'permissions',
            'roles',
            'model_has_permissions',
            'model_has_roles',
            'role_has_permissions',
            'countries',
            'currencies',
            'languages',
            'states',
            'cities',
            'areas',
            'branches',
            'branch_docs',
            'contract_types',
            'certificate_types',
            'bonus_types',
            'allowance_types',
            'deduction_types',
            'expense_categories',
            'expense_sub_categories',
            'goal_types',
            'payment_modes',
            'product_brands',
            'product_colors',
            'product_sizes',
            'product_units',
            'project_calculation_partners',
            'service_categories',
            'tax_rates',
            'document_next_number',
            'users_branches',
            'user_departments',
            'statuses',
            'ticket_priorities',
            'ticket_statuses',
            'task_status',
            'lead_sources',
            'lead_statuses',
            'item_groups',
            'customer_groups',
            'supplier_groups',
            'contact_email_notifications',
            'email_templates',
            'email_notifications',
            'predefined_replies'
        ];

        $tables = Illuminate\Support\Facades\DB::select('SHOW TABLES');
        
        $truncated = [];
        $skipped = [];

        Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        foreach ($tables as $table) {
            $tableArray = (array)$table;
            $tableName = array_values($tableArray)[0];

            if (in_array($tableName, $excludedTables)) {
                $skipped[] = $tableName;
            } else {
                Illuminate\Support\Facades\DB::table($tableName)->truncate();
                $truncated[] = $tableName;
            }
        }

        Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $html = '<html><head><title>Database Cleanup Result</title><style>body { font-family: Arial, sans-serif; margin: 20px; } .container { display: flex; } .box { flex: 1; padding: 20px; margin: 10px; border: 1px solid #ddd; border-radius: 5px; } h3 { color: #333; } ul { list-style-type: none; padding: 0; } li { padding: 5px; border-bottom: 1px solid #eee; } .truncated { color: #d9534f; } .skipped { color: #5cb85c; }</style></head><body>';
        $html .= '<h2>Database Cleanup Result</h2>';
        $html .= '<p>Raw data has been deleted, but system and configuration tables were preserved.</p>';
        $html .= '<div class="container">';
        
        $html .= '<div class="box"><h3 class="truncated">Deleted Tables Data (' . count($truncated) . ')</h3><ul>';
        foreach ($truncated as $t) {
            $html .= "<li>{$t}</li>";
        }
        $html .= '</ul></div>';
        
        $html .= '<div class="box"><h3 class="skipped">Skipped Tables (' . count($skipped) . ')</h3><ul>';
        foreach ($skipped as $s) {
            $html .= "<li>{$s}</li>";
        }
        $html .= '</ul></div>';
        
        $html .= '</div></body></html>';

        return $html;
    });

    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('dashboard/employees', [DashboardController::class, 'getExpireIdentications'])->name('dashboard.employees');
    Route::get('dashboard/expiry/', [DashboardController::class, 'expireList'])->name('dashboard.expire.list');
    // Customer groups routes
    Route::middleware('permission:manage_customer_groups')->group(function () {
        Route::get('customer-groups', [CustomerGroupController::class, 'index'])->name('customer-groups.index');
        Route::post('customer-groups', [CustomerGroupController::class, 'store'])->name('customer-groups.store');
        Route::get('customer-groups/create', [CustomerGroupController::class, 'create'])->name('customer-groups.create');
        Route::put(
            'customer-groups/{customerGroup}',
            [CustomerGroupController::class, 'update']
        )->name('customer-groups.update');
        Route::get('customer-groups/{customerGroup}', [CustomerGroupController::class, 'show'])->name('customer-groups.show');
        Route::delete(
            'customer-groups/{customerGroup}',
            [CustomerGroupController::class, 'destroy']
        )->name('customer-groups.destroy');
        Route::get(
            'customer-groups/{customerGroup}/edit',
            [CustomerGroupController::class, 'edit']
        )->name('customer-groups.edit');
    });

    Route::middleware(['permission:view_supplier_groups'])->group(function () {
        Route::get('supplier-groups', [SupplierGroupController::class, 'index'])->name('supplier-groups.index');
        Route::get('supplier-groups/{supplier_group}/view', [SupplierGroupController::class, 'view'])->name('supplier-groups.view');
    });
    Route::middleware(['permission:create_supplier_groups'])->group(function () {
        Route::get('supplier-groups/create', [SupplierGroupController::class, 'create'])->name('supplier-groups.create');
        Route::post('supplier-groups', [SupplierGroupController::class, 'store'])->name('supplier-groups.store');
    });
    Route::middleware(['permission:update_supplier_groups'])->group(function () {
        Route::get('supplier-groups/{supplier_group}/edit', [SupplierGroupController::class, 'edit'])->name('supplier-groups.edit');
        Route::put('supplier-groups/{supplier_group}', [SupplierGroupController::class, 'update'])->name('supplier-groups.update');
    });
    Route::middleware(['permission:delete_supplier_groups'])->group(function () {
        Route::delete('supplier-groups/{supplier_group}', [SupplierGroupController::class, 'destroy'])->name('supplier-groups.destroy');
    });

    Route::group(['middleware' => ['permission:view_suppliers|create_suppliers|update_suppliers|delete_suppliers']], function () {
        Route::get('suppliers', [SupplierController::class, 'index'])->name('suppliers.index');
        Route::middleware(['permission:view_suppliers'])->group(function () {
            Route::get('suppliers/{supplier}/view', [SupplierController::class, 'view'])->name('suppliers.view');
        });
        Route::middleware(['permission:create_suppliers'])->group(function () {
            Route::get('suppliers/create', [SupplierController::class, 'create'])->name('suppliers.create');
            Route::post('suppliers', [SupplierController::class, 'store'])->name('suppliers.store');
        });
        Route::middleware(['permission:update_suppliers'])->group(function () {
            Route::get('suppliers/{supplier}/edit', [SupplierController::class, 'edit'])->name('suppliers.edit');
            Route::post('suppliers/{supplier}', [SupplierController::class, 'update'])->name('suppliers.update');
        });
        Route::middleware(['permission:delete_suppliers'])->group(function () {
            Route::delete('suppliers/{supplier}', [SupplierController::class, 'destroy'])->name('suppliers.destroy');
        });

        Route::delete('suppliers/file/{id}', [SupplierController::class, 'file_delete'])->middleware('permission:delete_suppliers')->name('suppliers.file.delete');
    });





    Route::group(['middleware' => ['permission:view_branches|create_branches|update_branches|delete_branches']], function () {
        Route::get('branches', [BranchController::class, 'index'])->name('branches.index');
        Route::get('branches/create', [BranchController::class, 'create'])->middleware('permission:create_branches')->name('branches.create');
        Route::post(
            'branches',
            [BranchController::class, 'store']
        )->middleware('permission:create_branches')->name('branches.store');
        Route::get('branches/{branch}/view', [BranchController::class, 'view'])->middleware('permission:view_branches')->name('branches.view');
        Route::get('branches/{branch}/edit', [BranchController::class, 'edit'])->middleware('permission:update_branches')->name('branches.edit');
        Route::post('branches/{branch}', [BranchController::class, 'update'])->middleware('permission:update_branches')->name('branches.update');
        Route::delete('branches/{branch}', [BranchController::class, 'destroy'])->middleware('permission:delete_branches')->name('branches.destroy');
        Route::delete('branches/file/{id}', [BranchController::class, 'file_delete'])->middleware('permission:delete_employees')->name('branches.file.delete');
    });


    Route::get('/backup', [BackupController::class, 'index'])->middleware('permission:create_backup')->name('backup.index');
    Route::get('/backup/create', [BackupController::class, 'backup'])->middleware('permission:create_backup')->name('backup.create');
    Route::delete('/backups/{backup}', [BackupController::class, 'delete'])->middleware('permission:delete_backup')->name('backup.delete');

    Route::get('/backup/download/{file}', [BackupController::class, 'download'])->middleware('permission:download_backup')->name('backup.download');
    Route::post('/backup/schedule', [BackupController::class, 'setBackupSchedule'])->name('backup.schedule');


    Route::get('/restore', [RestoreController::class, 'index'])->middleware('permission:restore')->name('restore.index');
    Route::post('/restore/upload', [RestoreController::class, 'upload'])->middleware('permission:restore')->name('restore.upload');
    Route::post('/restore/from-file', [RestoreController::class, 'fromFile'])->middleware('permission:restore')->name('restore.from-file');



    Route::get('/financial-year', [FinancialYearController::class, 'index'])->name('financial-year.index');
    Route::get('financial-year-ending', [FinancialYearController::class, 'ending'])->name('financial-year.ending');


    // Tags module routes
    Route::middleware('permission:manage_tags')->group(function () {
        Route::get('tags', [TagController::class, 'index'])->name('tags.index');
        Route::post('tags', [TagController::class, 'store'])->name('tags.store');
        Route::get('tags/{tag}/edit', [TagController::class, 'edit'])->name('tags.edit');
        Route::put('tags/{tag}', [TagController::class, 'update'])->name('tags.update');
        Route::delete('tags/{tag}', [TagController::class, 'destroy'])->name('tags.destroy');
        Route::get('tags/{tag}', [TagController::class, 'show'])->name('tags.show');
    });

    // Customer routes
    Route::middleware(['permission:view_customers|create_customers|update_customers|delete_customers'])->group(function () {
        Route::get('customers', [CustomerController::class, 'index'])->name('customers.index');
        Route::get('customers/create', [CustomerController::class, 'create'])->middleware('permission:create_customers')->name('customers.create');
        Route::post(
            'customers',
            [CustomerController::class, 'store']
        )->middleware('permission:create_customers')->name('customers.store');
        Route::get('customers/{customer}', [CustomerController::class, 'show'])->middleware('permission:view_customers')->name('customers.show');
        Route::get('customers/{customer}/edit', [CustomerController::class, 'edit'])->middleware('permission:update_customers')->name('customers.edit');
        Route::post('customers/{customer}', [CustomerController::class, 'update'])->middleware('permission:update_customers')->name('customers.update');
        Route::delete('customers/{customer}', [CustomerController::class, 'destroy'])->middleware('permission:delete_customers')->name('customers.destroy');
        Route::get('customers/{customer}/{group}', [CustomerController::class, 'show'])->middleware('permission:view_customers');
        Route::post('customers/{customer}/{group}/notes-count', [CustomerController::class, 'getNotesCount'])->middleware('permission:view_customers');
        Route::get('search-customers', [CustomerController::class, 'searchCustomer'])->middleware('permission:view_customers')->name('customers.search.customer');
        Route::post('add-customer-address', [CustomerController::class, 'addCustomerAddress'])->middleware('permission:update_customers')->name('add.customer.address');
        Route::delete('customers/file/{id}', [CustomerController::class, 'file_delete'])->middleware('permission:delete_customers')->name('customers.file.delete');
    });


    // Contacts routes
    Route::get('contacts', [ContactController::class, 'index'])->name('contacts.index');
    Route::get('contacts/create/{customerId?}', [ContactController::class, 'create'])->name('contacts.create');
    Route::post('contacts', [ContactController::class, 'store'])->name('contacts.store');
    Route::get('contacts/{contact}', [ContactController::class, 'show'])->name('contacts.show');
    Route::get('contacts/{contact}/edit', [ContactController::class, 'edit'])->name('contacts.edit');
    Route::post('contacts/{contact}', [ContactController::class, 'update'])->name('contacts.update');
    Route::delete('contacts/{contact}', [ContactController::class, 'destroy'])->name('contacts.destroy');
    Route::post(
        'contacts/{contact}/active-deactive',
        [ContactController::class, 'activeDeActiveContact']
    )->name('contacts.activeDeActiveContact');

    // Notes routes
    Route::get('notes', [NoteController::class, 'index'])->name('notes.index');
    Route::post('notes', [NoteController::class, 'store'])->name('notes.store');
    Route::get('notes/{note}/edit', [NoteController::class, 'edit'])->name('notes.edit');
    Route::put('notes/{note}', [NoteController::class, 'update'])->name('notes.update');
    Route::delete('notes/{note}', [NoteController::class, 'destroy'])->name('notes.destroy');

    // Reminders routes
    Route::get('reminder', [ReminderController::class, 'index'])->name('reminder.index');
    Route::post('reminder', [ReminderController::class, 'store'])->name('reminder.store');
    Route::get('reminder/{reminder}/edit', [ReminderController::class, 'edit'])->name('reminder.edit');
    Route::put('reminder/{reminder}', [ReminderController::class, 'update'])->name('reminder.update');
    Route::delete('reminder/{reminder}', [ReminderController::class, 'destroy'])->name('reminder.destroy');

    // Comments routes
    Route::get('comments', [CommentController::class, 'index'])->name('comments.index');
    Route::post('comments', [CommentController::class, 'store'])->name('comments.store');
    Route::put('comments/{comment}', [CommentController::class, 'update'])->name('comments.update');
    Route::delete('comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');
    Route::get('comments/{comment}/edit', [CommentController::class, 'edit'])->name('comments.edit');

    // Departments routes
    Route::group(['middleware' => ['permission:view_departments|create_departments|update_departments|delete_departments']], function () {
        Route::get('departments', [DepartmentNewController::class, 'index'])->name('departments.index');
        Route::get('departments/create', [DepartmentNewController::class, 'create'])->middleware('permission:create_departments')->name('departments.create');
        Route::post('departments', [DepartmentNewController::class, 'store'])->middleware('permission:create_departments')->name('departments.store');
        Route::get('departments/{department}/view', [DepartmentNewController::class, 'view'])->middleware('permission:view_departments')->name('departments.view');
        Route::get('departments/{department}/edit', [DepartmentNewController::class, 'edit'])->middleware('permission:update_departments')->name('departments.edit');
        Route::put('departments/{department}', [DepartmentNewController::class, 'update'])->middleware('permission:update_departments')->name('departments.update');
        Route::delete('departments/{department}', [DepartmentNewController::class, 'destroy'])->middleware('permission:delete_departments')->name('departments.destroy');
    });




    Route::group(['middleware' => ['permission:view_purchase_orders|create_purchase_orders|update_purchase_orders|delete_purchase_orders']], function () {
        Route::get('purchase-orders', [OrderController::class, 'index'])->name('purchase-orders.index');
        Route::get('purchase-orders/create/{customerId?}', [OrderController::class, 'create'])->name('purchase-orders.create');
        Route::post('purchase-orders', [OrderController::class, 'store'])->name('purchase-orders.store');
        Route::get('purchase-orders/{order}/edit', [OrderController::class, 'edit'])->name('purchase-orders.edit');
        Route::post('purchase-orders/{order}', [OrderController::class, 'update'])->name('purchase-orders.update');
        Route::delete('purchase-orders/{order}', [OrderController::class, 'destroy'])->name('purchase-orders.destroy');
        Route::get('purchase-orders/{order}', [OrderController::class, 'show'])->name('purchase-orders.view');
        Route::get('purchase-orders/{order}/pdf', [OrderController::class, 'convertToPdf'])->name('purchase-orders.pdf');
    });

    Route::group(['middleware' => ['permission:view_purchase_orders|create_purchase_orders|update_purchase_orders|delete_purchase_orders']], function () {
        Route::get('purchase-invoices', [PurchaseInvoiceController::class, 'index'])->name('purchase-invoices.index');
        Route::get('purchase-invoices/create/{customerId?}', [PurchaseInvoiceController::class, 'create'])->name('purchase-invoices.create');
        Route::post('purchase-invoices', [PurchaseInvoiceController::class, 'store'])->name('purchase-invoices.store');
        Route::get('purchase-invoices/{invoice}/edit', [PurchaseInvoiceController::class, 'edit'])->name('purchase-invoices.edit');
        Route::post('purchase-invoices/{invoice}', [PurchaseInvoiceController::class, 'update'])->name('purchase-invoices.update');
        Route::delete('purchase-invoices/{invoice}', [PurchaseInvoiceController::class, 'destroy'])->name('purchase-invoices.destroy');
        Route::get('purchase-invoices/{invoice}', [PurchaseInvoiceController::class, 'show'])->name('purchase-invoices.view');
        Route::get('purchase-invoices/{invoice}/pdf', [PurchaseInvoiceController::class, 'convertToPdf'])->name('purchase-invoices.pdf');

        Route::delete('purchase-invoices/file/{id}', [PurchaseInvoiceController::class, 'file_delete'])->middleware('permission:delete_purchase_orders')->name('purchase-invoices.file.delete');
    });
    Route::group(['middleware' => ['permission:view_purchase_returns|create_purchase_returns|update_purchase_returns|delete_purchase_returns']], function () {
        Route::get('purchase-returns', [PurchaseReturnController::class, 'index'])->name('purchase-returns.index');
        Route::get('purchase-returns/invoice/{invoice}', [PurchaseReturnController::class, 'getInvoice'])->name('purchase-returns.invoice.view');
        Route::get('purchase-returns/create/{customerId?}', [PurchaseReturnController::class, 'create'])->name('purchase-returns.create');
        Route::post('purchase-returns', [PurchaseReturnController::class, 'store'])->name('purchase-returns.store');
        Route::get('purchase-returns/{return}/edit', [PurchaseReturnController::class, 'edit'])->name('purchase-returns.edit');
        Route::post('purchase-returns/{return}', [PurchaseReturnController::class, 'update'])->name('purchase-returns.update');
        Route::delete('purchase-returns/{return}', [PurchaseReturnController::class, 'destroy'])->name('purchase-returns.destroy');
        Route::get('purchase-returns/{return}', [PurchaseReturnController::class, 'show'])->name('purchase-returns.view');
        Route::get('purchase-returns/{return}/pdf', [PurchaseReturnController::class, 'convertToPdf'])->name('purchase-returns.pdf');
        Route::delete('purchase-returns/file/{id}', [PurchaseReturnController::class, 'file_delete'])->middleware('permission:delete_purchase_returns')->name('purchase-returns.file.delete');
    });



    Route::group(['middleware' => ['permission:view_purchase_groups|create_purchase_groups|update_purchase_groups|delete_purchase_groups']], function () {
        Route::prefix('purchase-groups')->name('purchase-groups.')->group(function () {
            Route::get('/', [PurchaseGroupController::class, 'index'])->name('index');
            Route::get('/{group}/view', [PurchaseGroupController::class, 'view'])->middleware('permission:view_purchase_groups')->name('view');
            Route::get('/create', [PurchaseGroupController::class, 'create'])->middleware('permission:create_purchase_groups')->name('create');
            Route::post(
                '/',
                [PurchaseGroupController::class, 'store']
            )->middleware('permission:create_purchase_groups')->name('store');
            Route::get('/{group}/edit', [PurchaseGroupController::class, 'edit'])->middleware('permission:update_purchase_groups')->name('edit');
            Route::put('/{group}', [PurchaseGroupController::class, 'update'])->middleware('permission:update_purchase_groups')->name('update');
            Route::delete('/{group}', [PurchaseGroupController::class, 'destroy'])->middleware('permission:delete_purchase_groups')->name('destroy');
        });
    });


    Route::group(['middleware' => ['permission:view_purchase_categories|create_purchase_categories|update_purchase_categories|delete_purchase_categories']], function () {
        Route::prefix('purchase-categories')->name('purchase-categories.')->group(function () {
            Route::get('/', [PurchaseCategoryController::class, 'index'])->name('index');
            Route::get('/{category}/view', [PurchaseCategoryController::class, 'view'])->middleware('permission:view_purchase_categories')->name('view');
            Route::get('/create', [PurchaseCategoryController::class, 'create'])->middleware('permission:create_purchase_categories')->name('create');
            Route::post(
                '/',
                [PurchaseCategoryController::class, 'store']
            )->middleware('permission:create_purchase_categories')->name('store');
            Route::get('/{category}/edit', [PurchaseCategoryController::class, 'edit'])->middleware('permission:update_purchase_categories')->name('edit');
            Route::put('/{category}', [PurchaseCategoryController::class, 'update'])->middleware('permission:update_purchase_categories')->name('update');
            Route::delete('/{category}', [PurchaseCategoryController::class, 'destroy'])->middleware('permission:delete_purchase_categories')->name('destroy');
        });
    });

    Route::group(['middleware' => ['permission:view_purchase_sub_categories|create_purchase_sub_categories|update_purchase_sub_categories|delete_purchase_sub_categories']], function () {
        Route::prefix('purchase-sub-categories')->name('purchase-sub-categories.')->group(function () {
            Route::get(
                '/',
                [PurchaseSubCategoryController::class, 'index']
            )->name('index');
            Route::get('/{subcategory}/view', [PurchaseSubCategoryController::class, 'view'])->middleware('permission:view_purchase_sub_categories')->name('view');
            Route::get('/subcategory', [PurchaseSubCategoryController::class, 'create'])->middleware('permission:create_purchase_sub_categories')->name('create');
            Route::post(
                '/',
                [PurchaseSubCategoryController::class, 'store']
            )->middleware('permission:create_purchase_sub_categories')->name('store');
            Route::get('/{subcategory}/edit', [PurchaseSubCategoryController::class, 'edit'])->middleware('permission:update_purchase_sub_categories')->name('edit');
            Route::put('/{subcategory}', [PurchaseSubCategoryController::class, 'update'])->middleware('permission:update_purchase_sub_categories')->name('update');
            Route::delete('/{subcategory}', [PurchaseSubCategoryController::class, 'destroy'])->middleware('permission:delete_purchase_sub_categories')->name('destroy');
        });
    });

    Route::group(['middleware' => ['permission:view_purchase_items|create_purchase_items|update_purchase_items|delete_purchase_items']], function () {
        Route::prefix('purchase-items')->name('purchase-items.')->group(function () {
            Route::get(
                '/',
                [PurchaseItemController::class, 'index']
            )->name('index');
            Route::get('/{item}/view', [PurchaseItemController::class, 'view'])->middleware('permission:view_purchase_items')->name('view');
            // Route::get('/services/all', [PurchaseItemController::class, 'getServices'])->middleware('permission:view_purchase_items')->name('services-all');
            Route::get('/item', [PurchaseItemController::class, 'create'])->middleware('permission:create_purchase_items')->name('create');
            Route::post(
                '/',
                [PurchaseItemController::class, 'store']
            )->middleware('permission:create_purchase_items')->name('store');
            Route::get('/{item}/edit', [PurchaseItemController::class, 'edit'])->middleware('permission:update_purchase_items')->name('edit');
            Route::put('/{item}', [PurchaseItemController::class, 'update'])->middleware('permission:update_purchase_items')->name('update');
            Route::delete('/{item}', [PurchaseItemController::class, 'destroy'])->middleware('permission:delete_purchase_items')->name('destroy');
        });
    });

    Route::group(['middleware' => ['permission:view_purchase_items|create_purchase_items|update_purchase_items|delete_purchase_items']], function () {
        Route::prefix('item-listing')->name('item-listing.')->group(function () {
            Route::get(
                '/',
                [PurchaseItemController::class, 'itemListing']
            )->name('index');
        });
    });


    Route::group(['middleware' => ['permission:manage_stock_reports']], function () {
        Route::prefix('stock-reports')->name('stock-reports.')->group(function () {
            Route::get(
                '/',
                [StockReportsController::class, 'index']
            )->name('index');
        });
    });
    Route::group(['middleware' => ['permission:manage_print_labels']], function () {
        Route::prefix('print-labels')->name('print-labels.')->group(function () {
            Route::get(
                '/',
                [PrintLabelController::class, 'index']
            )->name('index');
        });
        Route::get('print-labels/items', [PrintLabelController::class, 'getAllItems'])->name('print-labels.getItems');
        Route::post('print-labels/preview', [PrintLabelController::class, 'previewLabel'])->name('print-labels.preview');
        Route::get('print-labels/all-items', [PrintLabelController::class, 'getAllItemsNew'])->name('print-labels.all-items');
    });


    Route::get('opening-stocks/items', [OpeningStockController::class, 'getAllItems'])->name('opening-stocks.item');


    Route::group(['middleware' => ['permission:view_opening_stocks|create_opening_stocks|update_opening_stocks|delete_opening_stocks']], function () {
        Route::get('opening-stocks', [OpeningStockController::class, 'index'])->name('opening-stocks.index');
        Route::get('opening-stocks/create/{customerId?}', [OpeningStockController::class, 'create'])->name('opening-stocks.create');
        Route::post('opening-stocks', [OpeningStockController::class, 'store'])->name('opening-stocks.store');
        Route::get('opening-stocks/{stock}/edit', [OpeningStockController::class, 'edit'])->name('opening-stocks.edit');
        Route::post('opening-stocks/{stock}', [OpeningStockController::class, 'update'])->name('opening-stocks.update');
        Route::delete('opening-stocks/{stock}', [OpeningStockController::class, 'destroy'])->name('opening-stocks.destroy');
        Route::get('opening-stocks/{stock}', [OpeningStockController::class, 'show'])->name('opening-stocks.view');
        Route::get('opening-stocks/{stock}/pdf', [OpeningStockController::class, 'convertToPdf'])->name('opening-stocks.pdf');
    });



    Route::group(['middleware' => ['permission:view_product_brands|create_product_brands|update_product_brands|delete_product_brands']], function () {
        Route::get('product-brand', [ProductBrandController::class, 'index'])->name('products.brand.index');
        Route::get('product-brand/{brand}/view', [ProductBrandController::class, 'view'])->middleware('permission:view_product_brands')->name('products.brand.view');
        Route::get('product-brand/create', [ProductBrandController::class, 'create'])->middleware('permission:create_product_brands')->name('products.brand.create');
        Route::post('product-brand', [ProductBrandController::class, 'store'])->middleware('permission:create_product_brands')->name('products.brand.store');
        Route::get('product-brand/{brand}/edit', [ProductBrandController::class, 'edit'])->middleware('permission:update_product_brands')->name('products.brand.edit');
        Route::put('product-brand/{brand}', [ProductBrandController::class, 'update'])->middleware('permission:update_product_brands')->name('products.brand.update');
        Route::delete('product-brand/{brand}', [ProductBrandController::class, 'destroy'])->middleware('permission:delete_product_brands')->name('products.brand.destroy');
    });

    //for size
    Route::group(['middleware' => ['permission:view_product_sizes|create_product_sizes|update_product_sizes|delete_product_sizes']], function () {
        Route::get('product-size', [ProductSizeController::class, 'index'])->name('products.size.index');
        Route::get('product-size/{size}/view', [ProductSizeController::class, 'view'])->name('products.size.view');
        Route::get('product-size/create', [ProductSizeController::class, 'create'])->name('products.size.create');
        Route::post('product-size', [ProductSizeController::class, 'store'])->name('products.size.store');
        Route::get('product-size/{size}/edit', [ProductSizeController::class, 'edit'])->name('products.size.edit');
        Route::put('product-size/{size}', [ProductSizeController::class, 'update'])->name('products.size.update');
        Route::delete('product-size/{size}', [ProductSizeController::class, 'destroy'])->name('products.size.destroy');
    });

    //for colors
    Route::group(['middleware' => ['permission:view_product_colors|create_product_colors|update_product_colors|delete_product_colors']], function () {
        Route::get('product-color', [ProductColorController::class, 'index'])->name('products.color.index');
        Route::get('product-color/{color}/view', [ProductColorController::class, 'view'])->name('products.color.view');
        Route::get('product-color/create', [ProductColorController::class, 'create'])->name('products.color.create');
        Route::post('product-color', [ProductColorController::class, 'store'])->name('products.color.store');
        Route::get('product-color/{color}/edit', [ProductColorController::class, 'edit'])->name('products.color.edit');
        Route::put('product-color/{color}', [ProductColorController::class, 'update'])->name('products.color.update');
        Route::delete('product-color/{color}', [ProductColorController::class, 'destroy'])->name('products.color.destroy');
    });





    Route::group(['middleware' => ['permission:view_sub_departments|create_sub_departments|update_sub_departments|delete_sub_departments']], function () {
        Route::get('sub_departments', [SubDepartmentController::class, 'index'])->name('sub_departments.index');
        Route::get('sub_departments/create', [SubDepartmentController::class, 'create'])->middleware('permission:create_sub_departments')->name('sub_departments.create');
        Route::post('sub_departments', [SubDepartmentController::class, 'store'])->middleware('permission:create_sub_departments')->name('sub_departments.store');
        Route::get('sub_departments/{subDepartment}/view', [SubDepartmentController::class, 'view'])->middleware('permission:view_sub_departments')->name('sub_departments.view');
        Route::get('sub_departments/{subDepartment}/edit', [SubDepartmentController::class, 'edit'])->middleware('permission:update_sub_departments')->name('sub_departments.edit');
        Route::put('sub_departments/{subDepartment}', [SubDepartmentController::class, 'update'])->middleware('permission:update_sub_departments')->name('sub_departments.update');
        Route::delete('sub_departments/{subDepartment}', [SubDepartmentController::class, 'destroy'])->middleware('permission:delete_sub_departments')->name('sub_departments.destroy');
    });

    Route::group(['middleware' => ['permission:view_expense_sub_categories|create_expense_sub_categories|update_expense_sub_categories|delete_expense_sub_categories']], function () {
        Route::get('expense_sub_categories', [ExpenseSubCategoryController::class, 'index'])->name('expense_sub_categories.index');
        Route::get('expense_sub_categories/create', [ExpenseSubCategoryController::class, 'create'])->middleware('permission:create_expense_sub_categories')->name('expense_sub_categories.create');
        Route::post('expense_sub_categories', [ExpenseSubCategoryController::class, 'store'])->middleware('permission:create_expense_sub_categories')->name('expense_sub_categories.store');
        Route::get('expense_sub_categories/{category}/view', [ExpenseSubCategoryController::class, 'view'])->middleware('permission:view_expense_sub_categories')->name('expense_sub_categories.view');
        Route::get('expense_sub_categories/{category}/edit', [ExpenseSubCategoryController::class, 'edit'])->middleware('permission:update_expense_sub_categories')->name('expense_sub_categories.edit');
        Route::put('expense_sub_categories/{category}', [ExpenseSubCategoryController::class, 'update'])->middleware('permission:update_expense_sub_categories')->name('expense_sub_categories.update');
        Route::delete('expense_sub_categories/{category}', [ExpenseSubCategoryController::class, 'destroy'])->middleware('permission:delete_expense_sub_categories')->name('expense_sub_categories.destroy');
    });


    Route::group(['middleware' => ['permission:view_designations|create_designations|update_designations|delete_designations']], function () {
        Route::get('designations', [DesignationController::class, 'index'])->name('designations.index');
        Route::get('designations/{designation}/view', [DesignationController::class, 'view'])->middleware(['permission:view_designations'])->name('designations.view');
        Route::get('designations/create', [DesignationController::class, 'create'])->middleware(['permission:create_designations'])->name('designations.create');
        Route::post('designations', [DesignationController::class, 'store'])->middleware(['permission:create_designations'])->name('designations.store');
        Route::get('designations/{designation}/edit', [DesignationController::class, 'edit'])->middleware(['permission:update_designations'])->name('designations.edit');
        Route::put('designations/{designation}', [DesignationController::class, 'update'])->middleware(['permission:update_designations'])->name('designations.update');
        Route::delete('designations/{designation}', [DesignationController::class, 'destroy'])->middleware(['permission:delete_designations'])->name('designations.destroy');
    });


    Route::group(['middleware' => ['permission:view_employees|create_employees|update_employees|delete_employees']], function () {
        Route::get('employees', [EmployeeController::class, 'index'])->name('employees.index');
        Route::get('employees/create', [EmployeeController::class, 'create'])->middleware('permission:create_employees')->name('employees.create');
        Route::post('employees', [EmployeeController::class, 'store'])->middleware('permission:create_employees')->name('employees.store');
        Route::get('employees/{employee}/edit', [EmployeeController::class, 'edit'])->middleware('permission:update_employees')->name('employees.edit');
        Route::get('employees/{employee}/view', [EmployeeController::class, 'view'])->middleware('permission:view_employees')->name('employees.view');
        Route::post('employees/{employee}', [EmployeeController::class, 'update'])->middleware('permission:update_employees')->name('employees.update');
        Route::delete('employees/{employee}', [EmployeeController::class, 'destroy'])->middleware('permission:delete_employees')->name('employees.destroy');
        Route::delete('employees/file/{id}', [EmployeeController::class, 'file_delete'])->middleware('permission:delete_employees')->name('employees.file.delete');
        Route::get('employees/export/{status}/{branch?}', [EmployeeController::class, 'export'])
            ->where(['status' => '0|1|all', 'branch' => '[0-9]+']) // Ensure branch is a numeric value if provided
            ->middleware('permission:view_employees')
            ->name('employees.export');

        Route::get('employees/card/{employee}', [EmployeeController::class, 'cardView'])->middleware('permission:view_employees')->name('employees.card.view');
    });

    Route::group(['middleware' => ['permission:view_transfer|create_transfer|update_transfer|delete_transfer']], function () {
        Route::get('transfers', [TransferController::class, 'index'])->name('transfers.index');
        Route::get('transfers/{transfer}/view', [TransferController::class, 'view'])->middleware('permission:view_transfer')->name('transfers.view');
        Route::get('transfers/create', [TransferController::class, 'create'])->middleware('permission:create_transfer')->name('transfers.create');
        Route::post('transfers', [TransferController::class, 'store'])->middleware('permission:create_transfer')->name('transfers.store');
        Route::get('transfers/{transfer}/edit', [TransferController::class, 'edit'])->middleware('permission:update_transfer')->name('transfers.edit');
        Route::put('transfers/{transfer}', [TransferController::class, 'update'])->middleware('permission:update_transfer')->name('transfers.update');
        Route::delete('transfers/{transfer}', [TransferController::class, 'destroy'])->middleware('permission:delete_transfer')->name('transfers.destroy');
    });

    Route::group(['middleware' => ['permission:create_attendances|update_attendances|delete_attendances|import_attendances']], function () {
        Route::get('attendances', [AttendanceController::class, 'index'])->name('attendances.index');
        Route::get('attendances/{attendance}/view', [AttendanceController::class, 'view'])->name('attendances.view');
        Route::post('attendances/daily', [AttendanceController::class, 'daily_store'])->middleware('permission:create_attendances')->name('attendances.store.daily');
        Route::post('attendances/import', [AttendanceController::class, 'import_attendances'])->middleware('permission:import_attendances')->name('attendances.import');
        Route::get('attendances/{attendance}/edit', [AttendanceController::class, 'edit'])->middleware('permission:update_attendances')->name('attendances.edit');
        Route::put('attendances/{attendance}', [AttendanceController::class, 'update'])->middleware('permission:update_attendances')->name('attendances.update');
        Route::delete('attendances/{attendance}', [AttendanceController::class, 'destroy'])->middleware('permission:delete_attendances')->name('attendances.destroy');
        Route::post('attendances/import', [AttendanceController::class, 'import_attendances'])->middleware('permission:import_attendances')->name('attendances.import');
    });

    Route::group(['middleware' => ['permission:view_project_invoices|update_project_invoices|export_project_invoices']], function () {
        Route::get('project-invoices', [ProjectInvoicesController::class, 'index'])->name('project-invoices.index');
        Route::get('project-invoices/{invoice}', [ProjectInvoicesController::class, 'employeeList'])->name('project-invoices.invoice');
        Route::post('project-invoices/export', [ProjectInvoicesController::class, 'export'])->middleware('permission:export_project_invoices')->name('project-invoices.export');
        Route::get('project-invoices/details/{invoice}', [ProjectInvoicesController::class, 'exportInvoiceDetails'])->middleware('permission:export_project_invoices')->name('project-invoices.export-details');
        Route::post('project-invoices/{invoice}', [ProjectInvoicesController::class, 'update'])->middleware('permission:update_project_invoices')->name('project-invoices.update');
    });

    Route::group(['middleware' => ['permission:view_shifts|create_shifts|update_shifts|delete_shifts']], function () {
        Route::get('shifts', [ShiftController::class, 'index'])->name('shifts.index');
        Route::get('shifts/create', [ShiftController::class, 'create'])->middleware('permission:create_shifts')->name('shifts.create');
        Route::post('shifts', [ShiftController::class, 'store'])->middleware('permission:create_shifts')->name('shifts.store');
        Route::get('shifts/{shift}/view', [ShiftController::class, 'view'])->middleware('permission:view_shifts')->name('shifts.view');
        Route::get('shifts/{shift}/edit', [ShiftController::class, 'edit'])->middleware('permission:update_shifts')->name('shifts.edit');
        Route::put('shifts/{shift}', [ShiftController::class, 'update'])->middleware('permission:update_shifts')->name('shifts.update');
        Route::delete('shifts/{shift}', [ShiftController::class, 'destroy'])->middleware('permission:delete_shifts')->name('shifts.destroy');
    });

    Route::group(['middleware' => ['permission:view_salary_advances|create_salary_advances|update_salary_advances|delete_salary_advances']], function () {
        Route::get('salary_advances', [SalaryAdvanceController::class, 'index'])->name('salary_advances.index');
        Route::get('salary_advances/{salaryAdvance}/view', [SalaryAdvanceController::class, 'view'])->middleware('permission:view_salary_advances')->name('salary_advances.view');
        Route::get('salary_advances/create', [SalaryAdvanceController::class, 'create'])->middleware('permission:create_salary_advances')->name('salary_advances.create');
        Route::post('salary_advances', [SalaryAdvanceController::class, 'store'])->middleware('permission:create_salary_advances')->name('salary_advances.store');
        Route::get('salary_advances/{salaryAdvance}/edit', [SalaryAdvanceController::class, 'edit'])->middleware('permission:update_salary_advances')->name('salary_advances.edit');
        Route::put('salary_advances/{salaryAdvance}', [SalaryAdvanceController::class, 'update'])->middleware('permission:update_salary_advances')->name('salary_advances.update');
        Route::delete('salary_advances/{salaryAdvance}', [SalaryAdvanceController::class, 'destroy'])->middleware('permission:delete_salary_advances')->name('salary_advances.destroy');
    });

    Route::group(['middleware' => ['permission:view_salary_sheet|create_salary_sheet|export_salary_sheet']], function () {
        Route::get('salary_generates', [SalaryGenerateController::class, 'index'])->name('salary_generates.index');
        Route::get('salary_generates/create', [SalaryGenerateController::class, 'create'])->middleware('permission:create_salary_sheet')->name('salary_generates.create');
        Route::post('salary_generates', [SalaryGenerateController::class, 'store'])->middleware('permission:create_salary_sheet')->name('salary_generates.store');
        Route::get('salary_generates/{salaryGenerate}/verify', [SalaryGenerateController::class, 'verify'])->middleware('permission:create_salary_sheet')->name('salary_generates.verify');
        Route::get('salary_generates/{salaryGenerate}/sheet', [SalaryGenerateController::class, 'sheet'])->middleware('permission:view_salary_sheet')->name('salary_generates.sheets');
        Route::get('salary_generates/export/', [SalaryGenerateController::class, 'export'])->middleware('permission:export_salary_sheet')->name('salary_generates.sheets.export');
    });
    Route::group(['middleware' => ['permission:view_employee_salaries|export_employee_salaries']], function () {
        Route::get('employee-salaries', [EmployeeSalaryController::class, 'index'])->name('employee-salaries.index');
        Route::get('employee-salaries/{salarySheet}/payslip/view', [EmployeeSalaryController::class, 'payslip'])->name('employee-salaries.payslip.view');
        Route::post('employee-salaries/pay-salary', [EmployeeSalaryController::class, 'paySalary'])->name('employee-salaries.pay-salary');
        Route::get('employee-salaries/{salarySheet}/payslip/download/{overtime_status?}', [EmployeeSalaryController::class, 'payslipPDF'])->name('employee-salaries.payslip.download');
        Route::get('employee-salaries/{salarySheet}/payslip/download-view/{overtime_status?}', [EmployeeSalaryController::class, 'pdfView'])->name('employee-salaries.payslip.download-view');
        Route::get('employee-salaries/{salarySheet}/payslip/export', [EmployeeSalaryController::class, 'export'])->name('employee-salaries.payslip.export');
        Route::delete('employee-salaries/{id}', [EmployeeSalaryController::class, 'destroy'])->name('employee-salaries.destroy');
        Route::get('employee-salaries/bulk/export', [EmployeeSalaryController::class, 'bulkExport'])->name('employee-salaries.bulk.export');
    });
    Route::group(['middleware' => ['permission:view_statement|export_statement']], function () {
        Route::get('customer-statement', [CustomerStatementController::class, 'index'])->middleware('permission:view_statement')->name('customer-statement.index');
        Route::get('customer-statements/export', [CustomerStatementController::class, 'export'])
            ->middleware('permission:export_statement')
            ->name('customer-statement.export');
    });

    Route::group(['middleware' => ['permission:view_statement_supplier|export_statement_supplier']], function () {
        Route::get('supplier-statement', [SupplierStatementController::class, 'index'])->middleware('permission:view_statement_supplier')->name('supplier-statement.index');
        Route::get('supplier-statements/export', [SupplierStatementController::class, 'export'])
            ->middleware('permission:export_statement_supplier')
            ->name('supplier-statement.export');
    });

    Route::group(['middleware' => ['permission:view_statement_employee|export_statement_employee']], function () {
        Route::get('employee-statements', [EmployeeStatementController::class, 'index'])->middleware('permission:view_statement_employee')->name('employee-statements.index');
        Route::get('employee-statements/export', [EmployeeStatementController::class, 'export'])
            ->middleware('permission:export_statement_employee')
            ->name('employee-statements.export');
        Route::get('employee-statements/pdf/{id}', [EmployeeStatementController::class, 'downloadPDF'])
            ->middleware('permission:export_statement_employee')
            ->name('employee-statements.pdf');
    });


    Route::group(['middleware' => ['permission:view_account_statements|export_account_statements']], function () {
        Route::get('account-statements', [AccountStatementController::class, 'index'])->middleware('permission:view_account_statements')->name('account-statements.index');
        Route::get('account-statements/export', [AccountStatementController::class, 'export'])
            ->middleware('permission:export_account_statements')
            ->name('account-statements.export');
        Route::get('account-statements/pdf/{id}', [AccountStatementController::class, 'downloadPDF'])
            ->middleware(middleware: 'permission:export_account_statements')
            ->name('account-statements.pdf');
    });

    Route::group(['middleware' => ['permission:view_profit_and_loss|export_profit_and_loss']], function () {
        Route::get('profit-loss', [ProfitLossStatementController::class, 'index'])->middleware('permission:view_profit_and_loss')->name('profit-loss.index');
        Route::get('profit-loss/export', [ProfitLossStatementController::class, 'export'])->middleware('permission:export_profit_and_loss')->name('profit-loss.export');
    });
    Route::group(['middleware' => ['permission:create_manage_attendances|import_manage_attendances|export_manage_attendances']], function () {
        Route::get('manage-attendances', [ManageAttendanceController::class, 'index'])->name('manage-attendances.index');
        Route::post('manage-attendances', [ManageAttendanceController::class, 'store'])->middleware('permission:create_manage_attendances')->name('manage-attendances.store');
        Route::get('manage-attendances/create', [ManageAttendanceController::class, 'create'])->middleware('permission:create_manage_attendances')->name('manage-attendances.create');
        Route::post('manage-attendances/import', [ManageAttendanceController::class, 'import'])->middleware('permission:import_manage_attendances')->name('manage-attendances.import');
    });

    Route::group(['middleware' => ['permission:view_loan|create_loan|update_loan|delete_loan']], function () {
        Route::get('loans', [LoanController::class, 'index'])->name('loans.index');
        Route::get('loans/{loan}/view', [LoanController::class, 'view'])->middleware('permission:view_loan')->name('loans.view');
        Route::get('loans/create', [LoanController::class, 'create'])->middleware('permission:create_loan')->name('loans.create');
        Route::post('loans', [LoanController::class, 'store'])->middleware('permission:create_loan')->name('loans.store');
        Route::get('loans/{loan}/edit', [LoanController::class, 'edit'])->middleware('permission:update_loan')->name('loans.edit');
        Route::put('loans/{loan}', [LoanController::class, 'update'])->middleware('permission:update_loan')->name('loans.update');
        Route::delete('loans/{loan}', [LoanController::class, 'destroy'])->middleware('permission:delete_loan')->name('loans.destroy');
    });

    Route::group(['middleware' => ['permission:view_bonuses|create_bonuses|update_bonuses|delete_bonuses']], function () {
        Route::get('bonuses', [BonusController::class, 'index'])->name('bonuses.index');
        Route::get('bonuses/{bonus}/view', [BonusController::class, 'view'])->middleware('permission:view_bonuses')->name('bonuses.view');
        Route::get('bonuses/create', [BonusController::class, 'create'])->middleware('permission:create_bonuses')->name('bonuses.create');
        Route::post('bonuses', [BonusController::class, 'store'])->middleware('permission:create_bonuses')->name('bonuses.store');
        Route::get('bonuses/{bonus}/edit', [BonusController::class, 'edit'])->middleware('permission:update_bonuses')->name('bonuses.edit');
        Route::put('bonuses/{bonus}', [BonusController::class, 'update'])->middleware('permission:update_bonuses')->name('bonuses.update');
        Route::delete('bonuses/{bonus}', [BonusController::class, 'destroy'])->middleware('permission:delete_bonuses')->name('bonuses.destroy');
    });
    Route::group(['middleware' => ['permission:view_overtimes|create_overtimes|update_overtimes|delete_overtimes']], function () {
        Route::get('overtimes', [OverTimeController::class, 'index'])->name('overtimes.index');
        Route::get('overtimes/{overtime}/view', [OverTimeController::class, 'view'])->middleware('permission:view_overtimes')->name('overtimes.view');
        Route::get('overtimes/create', [OverTimeController::class, 'create'])->middleware('permission:create_overtimes')->name('overtimes.create');
        Route::post('overtimes', [OverTimeController::class, 'store'])->middleware('permission:create_overtimes')->name('overtimes.store');
        Route::get('overtimes/{overtime}/edit', [OverTimeController::class, 'edit'])->middleware('permission:update_overtimes')->name('overtimes.edit');
        Route::put('overtimes/{overtime}', [OverTimeController::class, 'update'])->middleware('permission:update_overtimes')->name('overtimes.update');
        Route::delete('overtimes/{overtime}', [OverTimeController::class, 'destroy'])->middleware('permission:delete_overtimes')->name('overtimes.destroy');
    });

    Route::group(['middleware' => ['permission:view_banks|create_banks|edit_bank|delete_banks']], function () {
        Route::get('banks', [BankController::class, 'index'])->name('banks.index');
        Route::get('banks/{bank}/view', [BankController::class, 'view'])->middleware('permission:view_banks')->name('banks.view');
        Route::get('banks/create', [BankController::class, 'create'])->middleware('permission:create_banks')->name('banks.create');
        Route::post('banks', [BankController::class, 'store'])->middleware('permission:create_banks')->name('banks.store');
        Route::get('banks/{bank}/edit', [BankController::class, 'edit'])->middleware('permission:update_leave_groups')->name('banks.edit');
        Route::put('banks/{bank}', [BankController::class, 'update'])->middleware('permission:update_leave_groups')->name('banks.update');
        Route::delete('banks/{bank}', [BankController::class, 'destroy'])->middleware('permission:delete_leave_groups')->name('banks.destroy');
    });
    Route::group(['middleware' => ['permission:view_leave_groups|create_leave_groups|update_leave_groups|delete_leave_groups']], function () {
        Route::get('leaves', [LeaveController::class, 'index'])->name('leaves.index');
        Route::get('leaves/{leave}/view', [LeaveController::class, 'view'])->middleware('permission:view_leave_groups')->name('leaves.view');
        Route::get('leaves/create', [LeaveController::class, 'create'])->middleware('permission:create_leave_groups')->name('leaves.create');
        Route::post('leaves', [LeaveController::class, 'store'])->middleware('permission:create_leave_groups')->name('leaves.store');
        Route::get('leaves/{leave}/edit', [LeaveController::class, 'edit'])->middleware('permission:update_leave_groups')->name('leaves.edit');
        Route::put('leaves/{leave}', [LeaveController::class, 'update'])->middleware('permission:update_leave_groups')->name('leaves.update');
        Route::delete('leaves/{leave}', [LeaveController::class, 'destroy'])->middleware('permission:delete_leave_groups')->name('leaves.destroy');
    });




    Route::group(['middleware' => ['permission:view_leave_applications|create_leave_applications|update_leave_applications|delete_leave_applications']], function () {
        Route::get('leave-applications', [LeaveApplicationController::class, 'index'])->name('leave-applications.index');
        Route::get('leave-applications/{leaveApplication}/view', [LeaveApplicationController::class, 'view'])->middleware('permission:view_leave_applications')->name('leave-applications.view');
        Route::get('leave-applications/create', [LeaveApplicationController::class, 'create'])->middleware('permission:create_leave_applications')->name('leave-applications.create');
        Route::post('leave-applications', [LeaveApplicationController::class, 'store'])->middleware('permission:create_leave_applications')->name('leave-applications.store');
        Route::get('leave-applications/{leaveApplication}/edit', [LeaveApplicationController::class, 'edit'])->middleware('permission:update_leave_applications')->name('leave-applications.edit');
        Route::post('leave-applications/{leaveApplication}', [LeaveApplicationController::class, 'update'])->middleware('permission:update_leave_applications')->name('leave-applications.update');

        Route::delete('leave-applications/{leaveApplication}', [LeaveApplicationController::class, 'destroy'])->middleware('permission:delete_leave_applications')->name('leave-applications.destroy');
        Route::get('leave-applications/{leaveApplication}/pdf', [LeaveApplicationController::class, 'downloadPDF'])->name('leave-applications.pdf');
    });
    Route::group(['middleware' => ['permission:view_approval_leaves|approve_approval_leaves']], function () {
        Route::get('approval-leaves', [ApprovalLeaveController::class, 'index'])->name('approval-leaves.index');
        Route::get('approval-leaves/{application}', [ApprovalLeaveController::class, 'update'])->middleware('permission:approve_approval_leaves')->name('approval-leaves.update');
        Route::get('approval-leaves/{application}/view', [ApprovalLeaveController::class, 'view'])->middleware('permission:view_approval_leaves')->name('approval-leaves.view');
        Route::delete('approval-leaves/{approvalLeave}', [ApprovalLeaveController::class, 'destroy'])->middleware('permission:delete_approval_leaves')->name('approval-leaves.destroy');

        Route::post('leave-applications/annual/{leaveApplication}', [LeaveApplicationController::class, 'updateAnnual'])->name('leave-applications.annual.update');
    });


    Route::group(['middleware' => ['permission:view_accounts|create_accounts|update_accounts|delete_accounts|export_accounts']], function () {
        Route::get(
            'accounts',
            [AccountController::class, 'index']
        )->name('accounts.index');
        Route::get('accounts/{account}/view', [AccountController::class, 'view'])->middleware('permission:view_accounts')->name('accounts.view');
        Route::get('accounts/card/{id?}', [AccountController::class, 'getCardsInfo'])->name('accounts.card');
        Route::get('accounts/create', [AccountController::class, 'create'])->middleware('permission:create_accounts')->name('accounts.create');
        Route::post('accounts', [AccountController::class, 'store'])->middleware('permission:create_accounts')->name('accounts.store');
        Route::get('accounts/{account}/edit', [AccountController::class, 'edit'])->middleware('permission:update_accounts')->name('accounts.edit');
        Route::put('accounts/{account}', [AccountController::class, 'update'])->middleware('permission:update_accounts')->name('accounts.update');
        Route::delete('accounts/{account}', [AccountController::class, 'destroy'])->middleware('permission:delete_accounts')->name('accounts.destroy');
        Route::post('accounts/cash-transfer', [AccountController::class, 'transferCash'])->middleware('permission:update_accounts')->name('accounts.cash-transfer');
        Route::post('accounts/pay', [AccountController::class, 'payCash'])->middleware('permission:update_accounts')->name('accounts.pay-cash');
        Route::post('accounts/update', [AccountController::class, 'updateCash'])->middleware('permission:update_accounts')->name('accounts.update-cash');
    });

    Route::group(['middleware' => ['permission:view_cash_transfers|create_cash_transfers|update_cash_transfers|delete_cash_transfers|export_cash_transfers']], function () {
        Route::get(
            'cash-transfers',
            [CashTransferController::class, 'index']
        )->name('cash-transfers.index');
        Route::get('cash-transfers/{transfer}/view', [CashTransferController::class, 'view'])->middleware('permission:view_cash_transfers')->name('cash-transfers.view');
        Route::get('cash-transfers/create', [CashTransferController::class, 'create'])->middleware('permission:create_cash_transfers')->name('cash-transfers.create');
        Route::post('cash-transfers', [CashTransferController::class, 'store'])->middleware('permission:create_cash_transfers')->name('cash-transfers.store');
        Route::get('cash-transfers/{transfer}/edit', [CashTransferController::class, 'edit'])->middleware('permission:update_cash_transfers')->name('cash-transfers.edit');
        Route::put('cash-transfers/{transfer}', [CashTransferController::class, 'update'])->middleware('permission:update_cash_transfers')->name('cash-transfers.update');
        Route::delete('cash-transfers/{transfer}', [CashTransferController::class, 'destroy'])->middleware('permission:delete_cash_transfers')->name('cash-transfers.destroy');
    });


    Route::group(['middleware' => ['permission:view_journal_vouchers|create_journal_vouchers|update_journal_vouchers|delete_journal_vouchers|export_journal_vouchers']], function () {
        Route::get(
            'journal-vouchers',
            [JournalVoucherController::class, 'index']
        )->name('journal-vouchers.index');
        Route::get('journal-vouchers/export', [JournalVoucherController::class, 'export'])->middleware('permission:export_journal_vouchers')->name('journal-vouchers.export');

        Route::get('journal-vouchers/{voucher}/view', [JournalVoucherController::class, 'view'])->middleware('permission:view_journal_vouchers')->name('journal-vouchers.view');
        Route::get('journal-vouchers/create', [JournalVoucherController::class, 'create'])->middleware('permission:create_journal_vouchers')->name('journal-vouchers.create');
        Route::post('journal-vouchers', [JournalVoucherController::class, 'store'])->middleware('permission:create_journal_vouchers')->name('journal-vouchers.store');
        Route::get('journal-vouchers/{voucher}/edit', [JournalVoucherController::class, 'edit'])->middleware('permission:update_journal_vouchers')->name('journal-vouchers.edit');
        Route::put('journal-vouchers/{voucher}', [JournalVoucherController::class, 'update'])->middleware('permission:update_journal_vouchers')->name('journal-vouchers.update');
        Route::delete('journal-vouchers/{voucher}', [JournalVoucherController::class, 'destroy'])->middleware('permission:delete_journal_vouchers')->name('journal-vouchers.destroy');
    });
    Route::group(['middleware' => ['permission:view_holidays|create_holidays|update_holidays|delete_holidays']], function () {
        Route::get('holidays', [HolidayController::class, 'index'])->name('holidays.index');
        Route::get('holidays/{holiday}/view', [HolidayController::class, 'view'])->middleware('permission:view_holidays')->name('holidays.view');
        Route::get('holidays/create', [HolidayController::class, 'create'])->middleware('permission:create_holidays')->name('holidays.create');
        Route::post('holidays', [HolidayController::class, 'store'])->middleware('permission:create_holidays')->name('holidays.store');
        Route::get('holidays/{holiday}/edit', [HolidayController::class, 'edit'])->middleware('permission:update_holidays')->name('holidays.edit');
        Route::put('holidays/{holiday}', [HolidayController::class, 'update'])->middleware('permission:update_holidays')->name('holidays.update');
        Route::delete('holidays/{holiday}', [HolidayController::class, 'destroy'])->middleware('permission:delete_holidays')->name('holidays.destroy');
    });

    Route::group(['middleware' => ['permission:view_appointments|create_appointments|update_appointments|delete_appointments']], function () {
        Route::get('appointments', [AppointmentController::class, 'index'])->name('appointments.index');
        Route::get('appointments/{holiday}/view', [AppointmentController::class, 'view'])->middleware('permission:view_appointments')->name('appointments.view');
        Route::get('appointments/create', [AppointmentController::class, 'create'])->middleware('permission:create_appointments')->name('appointments.create');
        Route::post('appointments', [AppointmentController::class, 'store'])->middleware('permission:create_appointments')->name('appointments.store');
        Route::get('appointments/{holiday}/edit', [AppointmentController::class, 'edit'])->middleware('permission:update_appointments')->name('appointments.edit');
        Route::put('appointments/{holiday}', [AppointmentController::class, 'update'])->middleware('permission:update_appointments')->name('appointments.update');
        Route::delete('appointments/{holiday}', [AppointmentController::class, 'destroy'])->middleware('permission:delete_appointments')->name('appointments.destroy');
    });



    Route::group(['middleware' => ['permission:view_increments|create_increments|update_increments|delete_increments']], function () {
        Route::get('increments', [IncrementController::class, 'index'])->name('increments.index');
        Route::get('increments/{increment}/view', [IncrementController::class, 'view'])->middleware('permission:view_increments')->name('increments.view');
        Route::get('increments/create', [IncrementController::class, 'create'])->middleware('permission:update_increments')->name('increments.create');
        Route::post('increments', [IncrementController::class, 'store'])->middleware('permission:update_increments')->name('increments.store');
        Route::get('increments/{increment}/edit', [IncrementController::class, 'edit'])->middleware('permission:update_increments')->name('increments.edit');
        Route::put('increments/{increment}', [IncrementController::class, 'update'])->middleware('permission:update_increments')->name('increments.update');
        Route::delete('increments/{increment}', [IncrementController::class, 'destroy'])->middleware('permission:delete_increments')->name('increments.destroy');
    });
    Route::group(['middleware' => ['permission:view_allowances|create_allowances|update_allowances|delete_allowances']], function () {
        Route::get('allowances', [AllowanceController::class, 'index'])->name('allowances.index');
        Route::get('allowances/get-employee-working-days/{employee_id}/{issue_date}', [AllowanceController::class, 'get_employee_working_days'])->name('allowances.employee_worked');
        Route::get('allowances/{allowance}/view', [AllowanceController::class, 'view'])->middleware('permission:view_allowances')->name('allowances.view');
        Route::get('allowances/create', [AllowanceController::class, 'create'])->middleware('permission:create_allowances')->name('allowances.create');
        Route::post('allowances', [AllowanceController::class, 'store'])->middleware('permission:create_allowances')->name('allowances.store');
        Route::get('allowances/{allowance}/edit', [AllowanceController::class, 'edit'])->middleware('permission:update_allowances')->name('allowances.edit');
        Route::put('allowances/{allowance}', [AllowanceController::class, 'update'])->middleware('permission:update_allowances')->name('allowances.update');
        Route::delete('allowances/{allowance}', [AllowanceController::class, 'destroy'])->middleware('permission:delete_allowances')->name('allowances.destroy');
    });
    Route::group(['middleware' => ['permission:view_deductions|create_deductions|update_deductions|delete_deductions']], function () {
        Route::get('deductions', [DeductionController::class, 'index'])->name('deductions.index');
        Route::get('deductions/{deduction}/view', [DeductionController::class, 'view'])->middleware('permission:view_deductions')->name('deductions.view');
        Route::get('deductions/create', [DeductionController::class, 'create'])->middleware('permission:create_deductions')->name('deductions.create');
        Route::post('deductions', [DeductionController::class, 'store'])->middleware('permission:create_deductions')->name('deductions.store');
        Route::get('deductions/{deduction}/edit', [DeductionController::class, 'edit'])->middleware('permission:update_deductions')->name('deductions.edit');
        Route::put('deductions/{deduction}', [DeductionController::class, 'update'])->middleware('permission:update_deductions')->name('deductions.update');
        Route::delete('deductions/{deduction}', [DeductionController::class, 'destroy'])->middleware('permission:delete_deductions')->name('deductions.destroy');
    });

    Route::group(['middleware' => ['permission:view_retirements|create_retirements|update_retirements|delete_retirements']], function () {
        Route::get('retirements', [RetirementController::class, 'index'])->name('retirements.index');
        Route::get('retirements/{retirement}/view', [RetirementController::class, 'view'])->middleware('permission:view_retirements')->name('retirements.view');
        Route::get('retirements/create', [RetirementController::class, 'create'])->middleware('permission:create_retirements')->name('retirements.create');
        Route::post('retirements', [RetirementController::class, 'store'])->middleware('permission:create_retirements')->name('retirements.store');
        Route::get('retirements/{retirement}/edit', [RetirementController::class, 'edit'])->middleware('permission:update_retirements')->name('retirements.edit');
        Route::put('retirements/{retirement}', [RetirementController::class, 'update'])->middleware('permission:update_retirements')->name('retirements.update');
        Route::delete('retirements/{retirement}', [RetirementController::class, 'destroy'])->middleware('permission:delete_retirements')->name('retirements.destroy');
    });

    Route::group(['middleware' => ['permission:view_terminations|create_terminations|update_terminations|delete_terminations']], function () {
        Route::get('terminations', [TerminationController::class, 'index'])->name('terminations.index');
        Route::get('terminations/{termination}/view', [TerminationController::class, 'view'])->middleware('permission:view_terminations')->name('terminations.view');
        Route::get('terminations/create', [TerminationController::class, 'create'])->middleware('permission:create_terminations')->name('terminations.create');
        Route::post('terminations', [TerminationController::class, 'store'])->middleware('permission:create_terminations')->name('terminations.store');
        Route::get('terminations/{termination}/edit', [TerminationController::class, 'edit'])->middleware('permission:update_terminations')->name('terminations.edit');
        Route::put('terminations/{termination}', [TerminationController::class, 'update'])->middleware('permission:update_terminations')->name('terminations.update');
        Route::delete('terminations/{termination}', [TerminationController::class, 'destroy'])->middleware('permission:delete_terminations')->name('terminations.destroy');
    });

    Route::group(['middleware' => ['permission:manage_terms']], function () {
        Route::get('terms', [TermController::class, 'index'])->name('terms.index');
        Route::get('terms/{term}/view', [TermController::class, 'view'])->middleware('permission:manage_terms')->name('terms.view');
        Route::get('terms/create', [TermController::class, 'create'])->middleware('permission:manage_terms')->name('terms.create');
        Route::post('terms', [TermController::class, 'store'])->middleware('permission:manage_terms')->name('terms.store');
        Route::get('terms/{term}/edit', [TermController::class, 'edit'])->middleware('permission:manage_terms')->name('terms.edit');
        Route::put('terms/{term}', [TermController::class, 'update'])->middleware('permission:manage_terms')->name('terms.update');
        Route::delete('terms/{term}', [TermController::class, 'destroy'])->middleware('permission:manage_terms')->name('terms.destroy');
    });

    Route::group(['middleware' => ['permission:view_rentals|create_rentals|update_rentals|delete_rentals']], function () {
        Route::get('rentals', [RentalController::class, 'index'])->name('rentals.index');
        Route::get('rentals/{rental}/view', [RentalController::class, 'view'])->middleware('permission:view_rentals')->name('rentals.view');
        Route::get('rentals/create', [RentalController::class, 'create'])->middleware('permission:create_rentals')->name('rentals.create');
        Route::post('rentals', [RentalController::class, 'store'])->middleware('permission:create_rentals')->name('rentals.store');
        Route::get('rentals/{rental}/edit', [RentalController::class, 'edit'])->middleware('permission:update_rentals')->name('rentals.edit');
        Route::put('rentals/{rental}', [RentalController::class, 'update'])->middleware('permission:update_rentals')->name('rentals.update');
        Route::delete('rentals/{rental}', [RentalController::class, 'destroy'])->middleware('permission:delete_rentals')->name('rentals.destroy');
    });
    Route::group(['middleware' => ['permission:view_awards|create_awards|update_awards|delete_awards']], function () {
        Route::get('awards', [AwardController::class, 'index'])->name('awards.index');
        Route::get('awards/{award}/view', [AwardController::class, 'view'])->middleware('permission:view_awards')->name('awards.view');
        Route::get('awards/create', [AwardController::class, 'create'])->middleware('permission:create_awards')->name('awards.create');
        Route::post('awards', [AwardController::class, 'store'])->middleware('permission:create_awards')->name('awards.store');
        Route::get('awards/{award}/edit', [AwardController::class, 'edit'])->middleware('permission:update_awards')->name('awards.edit');
        Route::put('awards/{award}', [AwardController::class, 'update'])->middleware('permission:update_awards')->name('awards.update');
        Route::delete('awards/{award}', [AwardController::class, 'destroy'])->middleware('permission:delete_awards')->name('awards.destroy');
    });

    Route::group(['middleware' => ['permission:view_commissions|create_commissions|update_commissions|delete_commissions']], function () {
        Route::get('commissions', [CommissionController::class, 'index'])->name('commissions.index');
        Route::get('commissions/{commission}/view', [CommissionController::class, 'view'])->middleware('permission:view_commissions')->name('commissions.view');
        Route::get('commissions/create', [CommissionController::class, 'create'])->middleware('permission:create_commissions')->name('commissions.create');
        Route::post('commissions', [CommissionController::class, 'store'])->middleware('permission:create_commissions')->name('commissions.store');
        Route::get('commissions/{commission}/edit', [CommissionController::class, 'edit'])->middleware('permission:update_commissions')->name('commissions.edit');
        Route::put('commissions/{commission}', [CommissionController::class, 'update'])->middleware('permission:update_commissions')->name('commissions.update');
        Route::delete('commissions/{commission}', [CommissionController::class, 'destroy'])->middleware('permission:delete_commissions')->name('commissions.destroy');
        Route::get('commissions/get-info/{employee}', [CommissionController::class, 'get_info'])->name('commissions.employee.info');
    });

    Route::group(['middleware' => ['permission:view_insurances|create_insurances|update_insurances|delete_insurances']], function () {
        Route::get('insurances', [InsuranceController::class, 'index'])->name('insurances.index');
        Route::get('insurances/{insurance}/view', [InsuranceController::class, 'view'])->middleware('permission:view_insurances')->name('insurances.view');
        Route::get('insurances/create', [InsuranceController::class, 'create'])->middleware('permission:create_insurances')->name('insurances.create');
        Route::post('insurances', [InsuranceController::class, 'store'])->middleware('permission:create_insurances')->name('insurances.store');
        Route::get('insurances/{insurance}/edit', [InsuranceController::class, 'edit'])->middleware('permission:update_insurances')->name('insurances.edit');
        Route::put('insurances/{insurance}', [InsuranceController::class, 'update'])->middleware('permission:update_insurances')->name('insurances.update');
        Route::delete('insurances/{insurance}', [InsuranceController::class, 'destroy'])->middleware('permission:delete_insurances')->name('insurances.destroy');
        Route::get('insurances/get-info/{employee}', [InsuranceController::class, 'get_info'])->name('insurances.employee.info');
    });
    // Article Groups module routes
    Route::middleware('permission:manage_article_groups')->group(function () {
        Route::get('article-groups', [ArticleGroupController::class, 'index'])->name('article-groups.index');
        Route::post('article-groups', [ArticleGroupController::class, 'store'])->name('article-groups.store');
        Route::get(
            'article-groups/{articleGroup}/edit',
            [ArticleGroupController::class, 'edit']
        )->name('article-groups.edit');
        Route::put('article-groups/{articleGroup}', [ArticleGroupController::class, 'update'])->name('article-groups.update');
        Route::delete(
            'article-groups/{articleGroup}',
            [ArticleGroupController::class, 'destroy']
        )->name('article-groups.destroy');
    });

    Route::middleware(['permission:view_expense_categories|create_expense_categories|update_expense_categories|delete_expense_categories'])->group(function () {
        Route::get('expense-categories', [ExpenseCategoryController::class, 'index'])->name('expense-categories.index');
        Route::post('expense-categories', [ExpenseCategoryController::class, 'store'])->middleware('permission:create_expense_categories')->name('expense-categories.store');
        Route::get('expense-categories/{expenseCategory}/edit', [ExpenseCategoryController::class, 'edit'])->name('expense-categories.edit');
        Route::put('expense-categories/{expenseCategory}', [ExpenseCategoryController::class, 'update'])->name('expense-categories.update');
        Route::delete('expense-categories/{expenseCategory}', [ExpenseCategoryController::class, 'destroy'])->name('expense-categories.destroy');
    });


    // Predefined Replies routes
    Route::middleware('permission:manage_predefined_replies')->group(function () {
        Route::get('predefined-replies', [PredefinedReplyController::class, 'index'])->name('predefinedReplies.index');
        Route::post('predefined-replies', [PredefinedReplyController::class, 'store'])->name('predefinedReplies.store');
        Route::get(
            'predefined-replies/{predefinedReply}/edit',
            [PredefinedReplyController::class, 'edit']
        )->name('predefinedReplies.edit');
        Route::put(
            'predefined-replies/{predefinedReply}',
            [PredefinedReplyController::class, 'update']
        )->name('predefinedReplies.update');
        Route::delete(
            'predefined-replies/{predefinedReply}',
            [PredefinedReplyController::class, 'destroy']
        )->name('predefinedReplies.destroy');
        Route::get(
            'predefined-replies/{predefinedReply}',
            [PredefinedReplyController::class, 'show']
        )->name('predefinedReplies.show');
    });

    // Services routes
    Route::middleware('permission:manage_services')->group(function () {
        Route::get('services', [ServiceController::class, 'index'])->name('services.index');
        Route::post('services', [ServiceController::class, 'store'])->name('services.store');
        Route::get('services/{service}/edit', [ServiceController::class, 'edit'])->name('services.edit');
        Route::put('services/{service}', [ServiceController::class, 'update'])->name('services.update');
        Route::delete('services/{service}', [ServiceController::class, 'destroy'])->name('services.destroy');
    });

    // Products routes
    // Route::middleware('permission:manage_items')->group(function () {
    //     Route::get('products', [ProductController::class, 'index'])->name('products.index');
    //     Route::post('products', [ProductController::class, 'store'])->name('products.store');
    //     Route::get('products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
    //     Route::put('products/{product}', [ProductController::class, 'update'])->name('products.update');
    //     Route::delete('products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');
    // });

    Route::middleware('permission:view_services')->group(function () {
        Route::get('products', action: [ProductController::class, 'index'])->name('products.index');
        Route::get('products/{product}', action: [ProductController::class, 'view'])->name('products.view');
    });
    Route::middleware('permission:create_services')->group(function () {
        Route::post(
            'products',
            [ProductController::class, 'store']
        )->name('products.store');
    });
    Route::middleware('permission:update_services')->group(function () {
        Route::get('products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
        Route::put('products/{product}', [ProductController::class, 'update'])->name('products.update');
    });
    Route::middleware('permission:delete_services')->group(function () {
        Route::delete('products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');
    });

    Route::group(['middleware' => ['permission:view_product_units|create_product_units|update_product_units|delete_product_units']], function () {
        Route::get('product-unit', [ProductUnitController::class, 'index'])->name('products.unit.index');
        Route::get('product-unit/{unit}/view', [ProductUnitController::class, 'view'])->middleware('permission:view_product_units')->name('products.unit.view');
        Route::get('product-unit/create', [ProductUnitController::class, 'create'])->middleware('permission:create_product_units')->name('products.unit.create');
        Route::post('product-unit', [ProductUnitController::class, 'store'])->middleware('permission:create_product_units')->name('products.unit.store');
        Route::get('product-unit/{unit}/edit', [ProductUnitController::class, 'edit'])->middleware('permission:update_product_units')->name('products.unit.edit');
        Route::put('product-unit/{unit}', [ProductUnitController::class, 'update'])->middleware('permission:update_product_units')->name('products.unit.update');
        Route::delete('product-unit/{unit}', [ProductUnitController::class, 'destroy'])->middleware('permission:delete_product_units')->name('products.unit.destroy');
    });


    Route::group(['middleware' => ['permission:view_vat_reports|pay_vat_reports|update_vat_reports|export_vat_reports']], function () {
        Route::prefix('vat-reports')->name('vat-reports.')->group(function () {
            Route::get(
                '/',
                [VatReportController::class, 'index']
            )->name('index');
            // Route::get('/{report}/view', [VatReportController::class, 'view'])->middleware('permission:view_vat_reports')->name('view');
            Route::get('/view/aggregated', [VatReportController::class, 'view'])
                ->middleware('permission:view_vat_reports')
                ->name('view.aggregated');
            // Route::get('/vat-reports/{report}/modal', [VatReportController::class, 'getModalData'])->middleware('permission:view_vat_reports')->name('modal');
            Route::get('/vat-reports/{report}/modal', [VatReportController::class, 'getModalData'])
                ->middleware('permission:view_vat_reports')
                ->name('modal');
            Route::get('period/{period}', [VatReportController::class, 'getInput'])->middleware('permission:pay_vat_reports')->name('input');
            Route::get('/create', [VatReportController::class, 'create'])->middleware('permission:pay_vat_reports')->name('create');
            Route::post('/', [VatReportController::class, 'store'])->middleware('permission:pay_vat_reports')->name('store');
            Route::post('pay/', [VatReportController::class, 'updatePaid'])->middleware('permission:pay_vat_reports')->name('pay');

            Route::get('/{report}/download', [VatReportController::class, 'download'])->middleware('permission:export_vat_reports')->name('download');
            Route::put('/{report}', [VatReportController::class, 'update'])->middleware('permission:update_vat_reports')->name('update');
            Route::get('/{report}/download-vat-history', [VatReportController::class, 'downloadVatHistoryReport'])
                ->middleware('permission:export_vat_reports')
                ->name('vat-history.download');
            Route::get('/download/quarterly/{year}/{period}', [VatReportController::class, 'downloadQuarterlyVatHistory'])
                ->middleware('permission:export_vat_reports')
                ->name('vat-history.download.quarterly');

            Route::post('/download-zip', [VatReportController::class, 'downloadAsZip'])
                ->middleware('permission:export_vat_reports')
                ->name('download-zip');
        });
    });

    Route::get('make/{year}', [VatReportController::class, 'makeReport'])->name('vat-reports.make');


    Route::group(['middleware' => ['permission:view_service_categories|create_service_categories|update_service_categories|delete_service_categories']], function () {
        Route::prefix('service_categories')->name('service_categories.')->group(function () {
            Route::get(
                '/',
                [ServiceCategoryController::class, 'index']
            )->name('index');
            Route::get('/{category}/view', [ServiceCategoryController::class, 'view'])->middleware('permission:view_service_categories')->name('view');
            Route::get('/create', [ServiceCategoryController::class, 'create'])->middleware('permission:create_service_categories')->name('create');
            Route::post('/', [ServiceCategoryController::class, 'store'])->middleware('permission:create_service_categories')->name('store');
            Route::get('/{category}/edit', [ServiceCategoryController::class, 'edit'])->middleware('permission:update_service_categories')->name('edit');
            Route::put('/{category}', [ServiceCategoryController::class, 'update'])->middleware('permission:update_service_categories')->name('update');
            Route::delete('/{category}', [ServiceCategoryController::class, 'destroy'])->middleware('permission:delete_service_categories')->name('destroy');
        });
    });


    Route::group(['middleware' => ['permission:view_asset_categories|create_asset_categories|update_asset_categories|delete_asset_categories']], function () {
        Route::prefix('asset_category')->name('asset.category.')->group(function () {
            Route::get('/', [AssetCategoryController::class, 'index'])->name('index');
            Route::get('/{category}/view', [AssetCategoryController::class, 'view'])->middleware('permission:view_asset_categories')->name('view');
            Route::get('/create', [AssetCategoryController::class, 'create'])->middleware('permission:create_asset_categories')->name('create');
            Route::post('/', [AssetCategoryController::class, 'store'])->middleware('permission:create_asset_categories')->name('store');
            Route::get('/{category}/edit', [AssetCategoryController::class, 'edit'])->middleware('permission:update_asset_categories')->name('edit');
            Route::put('/{category}', [AssetCategoryController::class, 'update'])->middleware('permission:update_asset_categories')->name('update');
            Route::delete('/{category}', [AssetCategoryController::class, 'destroy'])->middleware('permission:delete_asset_categories')->name('destroy');
        });
    });

    Route::group(['middleware' => ['permission:view_assets|create_assets|update_assets|delete_assets']], function () {
        Route::prefix('assets')->name('assets.')->group(function () {
            Route::get('/', [AssetController::class, 'index'])->name('index');
            Route::get('/{asset}/view', [AssetController::class, 'view'])->middleware('permission:view_assets')->name('view');
            Route::get('/create', [AssetController::class, 'create'])->middleware('permission:create_assets')->name('create');
            Route::post('/', [AssetController::class, 'store'])->middleware('permission:create_assets')->name('store');
            Route::delete('/{asset}', [AssetController::class, 'destroy'])->middleware('permission:update_assets')->name('destroy');
            Route::get('/{asset}/edit', [AssetController::class, 'edit'])->middleware('permission:update_assets')->name('edit');
            Route::post('/{asset}', [AssetController::class, 'update'])->middleware('permission:delete_assets')->name('update');
        });
    });
    Route::group(['middleware' => ['permission:view_vehicle_rental|create_vehicle_rental|update_vehicle_rental|delete_vehicle_rental']], function () {
        Route::prefix('vehicle-rentals')->name('vehicle-rentals.')->group(function () {
            Route::get('/', [VehicleRentalController::class, 'index'])->name('index');
            Route::get('/{rental}/view', [VehicleRentalController::class, 'view'])->middleware('permission:view_vehicle_rental')->name('view');
            Route::get('/create', [VehicleRentalController::class, 'create'])->middleware('permission:create_vehicle_rental')->name('create');
            Route::post('/', [VehicleRentalController::class, 'store'])->middleware('permission:create_vehicle_rental')->name('store');
            Route::delete('/{rental}', [VehicleRentalController::class, 'destroy'])->middleware('permission:delete_vehicle_rental')->name('destroy');
            Route::get('/{rental}/edit', [VehicleRentalController::class, 'edit'])->middleware('permission:update_vehicle_rental')->name('edit');
            Route::post('/{rental}', [VehicleRentalController::class, 'update'])->middleware('permission:update_vehicle_rental')->name('update');
            Route::post('/payment/{rental}', [VehicleRentalController::class, 'updatePayment'])
                ->middleware('permission:update_vehicle_rental')
                ->name('update-payment');
        });
    });


    // Tax Rates routes
    Route::middleware('permission:manage_tax_rates')->group(function () {
        Route::get('tax-rates', [TaxRateController::class, 'index'])->name('tax-rates.index');
        Route::post('tax-rates', [TaxRateController::class, 'store'])->name('tax-rates.store');
        Route::get('tax-rates/{taxRate}/edit', [TaxRateController::class, 'edit'])->name('tax-rates.edit');
        Route::put('tax-rates/{taxRate}', [TaxRateController::class, 'update'])->name('tax-rates.update');
        Route::delete('tax-rates/{taxRate}', [TaxRateController::class, 'destroy'])->name('tax-rates.destroy');
    });

    Route::group(['middleware' => ['permission:view_print_checks|create_print_checks|update_print_checks|delete_print_checks']], function () {
        Route::get('print-checks', [PrintCheckController::class, 'index'])->name('print-checks.index');
        Route::get('print-checks/{check}/view', [PrintCheckController::class, 'view'])->middleware('permission:view_print_checks')->name('print-checks.view');
        Route::get('print-checks/create', [PrintCheckController::class, 'create'])->middleware('permission:create_print_checks')->name('print-checks.create');
        Route::post('print-checks', [PrintCheckController::class, 'store'])->middleware('permission:create_print_checks')->name('print-checks.store');
        Route::delete('print-checks/{check}', [PrintCheckController::class, 'destroy'])->middleware('permission:update_print_checks')->name('print-checks.destroy');
        Route::get('print-checks/{check}/edit', [PrintCheckController::class, 'edit'])->middleware('permission:update_print_checks')->name('print-checks.edit');
        Route::post('print-checks/{check}', [PrintCheckController::class, 'update'])->middleware('permission:delete_print_checks')->name('print-checks.update');
        Route::get('print-checks/{check}/export/print', [PrintCheckController::class, 'printPDF'])->middleware('permission:view_print_checks')->name('print-checks.export.print');
    });

    // Articles routes
    Route::middleware('permission:manage_articles')->group(function () {
        Route::get('articles', [ArticleController::class, 'index'])->name('articles.index');
        Route::get('articles/create', [ArticleController::class, 'create'])->name('articles.create');
        Route::post('articles', [ArticleController::class, 'store'])->name('articles.store');
        Route::get('articles/{article}', [ArticleController::class, 'show'])->name('articles.show');
        Route::get('articles/{article}/edit', [ArticleController::class, 'edit'])->name('articles.edit');
        Route::post('articles/{article}', [ArticleController::class, 'update'])->name('articles.update');
        Route::delete('articles/{article}', [ArticleController::class, 'destroy'])->name('articles.destroy');
        Route::post(
            'articles/{article}/active-deactive-article',
            [ArticleController::class, 'activeDeActiveInternalArticle']
        )->name('active.deactive.article');
        Route::post(
            'articles/{article}/active-deactive-disabled',
            [ArticleController::class, 'activeDeActiveDisabled']
        )->name('active.deactive.disabled');
        Route::get('attachment-download/{article}', [ArticleController::class, 'downloadMedia']);
    });

    // Product Groups routes
    Route::middleware('permission:manage_items_groups')->group(function () {
        Route::get('product-groups', [ProductGroupController::class, 'index'])->name('product-groups.index');
        Route::post('product-groups', [ProductGroupController::class, 'store'])->name('product-groups.store');
        Route::get(
            'product-groups/{productGroup}/edit',
            [ProductGroupController::class, 'edit']
        )->name('product-groups.edit');
        Route::put(
            'product-groups/{productGroup}',
            [ProductGroupController::class, 'update']
        )->name('product-groups.update');
        Route::delete(
            'product-groups/{productGroup}',
            [ProductGroupController::class, 'destroy']
        )->name('product-groups.destroy');
    });

    // Announcements routes
    Route::middleware('permission:manage_announcements')->group(function () {
        Route::get('announcements', [AnnouncementController::class, 'index'])->name('announcements.index');
        Route::post('announcements', [AnnouncementController::class, 'store'])->name('announcements.store');
        Route::get('announcements/{announcement}', [AnnouncementController::class, 'show'])->name('announcements.show');
        Route::get('announcements/{announcement}/edit', [AnnouncementController::class, 'edit'])->name('announcements.edit');
        Route::put('announcements/{announcement}', [AnnouncementController::class, 'update'])->name('announcements.update');
        Route::delete(
            'announcements/{announcement}',
            [AnnouncementController::class, 'destroy']
        )->name('announcements.destroy');
        Route::post(
            'announcements/{announcement}/active-deactive-client',
            [AnnouncementController::class, 'activeDeActiveClient']
        )->name('announcement.active.deactive.client');
        Route::get(
            'announcement-detail/{announcement}',
            [AnnouncementController::class, 'getAnnouncementDetails']
        )->name('announcement.details');
        Route::post(
            'announcements/{announcement}/change-status',
            [AnnouncementController::class, 'statusChange']
        )->name('announcements.status.change');
    });

    Route::get('all_notices', [NoticeController::class, 'all_notices'])->name('notices.all_notices');
    Route::get('notices', [NoticeController::class, 'index'])->name('notices.index');
    Route::get('notices/create', [NoticeController::class, 'create'])->name('notices.create');
    Route::post('notices', [NoticeController::class, 'store'])->name('notices.store');
    Route::get('notices/{notice}/edit', [NoticeController::class, 'edit'])->name('notices.edit');
    Route::put('notices/{notice}', [NoticeController::class, 'update'])->name('notices.update');
    Route::delete('notices/{notice}', [NoticeController::class, 'destroy'])->name('notices.destroy');
    // Calendar routes
    Route::middleware('permission:manage_calenders')->group(function () {
        Route::get('calendars', [CalendarController::class, 'index'])->name('calendars.index');
        Route::get('calendar-list', [CalendarController::class, 'calendarList']);
    });

    // Contracts type routes
    Route::middleware('permission:manage_contracts_types')->group(function () {
        Route::get('contract-types', [ContractTypeController::class, 'index'])->name('contract-types.index');
        Route::post('contract-types', [ContractTypeController::class, 'store'])->name('contract-types.store');
        Route::get(
            'contract-types/{contractType}/edit',
            [ContractTypeController::class, 'edit']
        )->name('contract-types.edit');
        Route::put('contract-types/{contractType}', [ContractTypeController::class, 'update'])->name('contract-types.update');
        Route::delete(
            'contract-types/{contractType}',
            [ContractTypeController::class, 'destroy']
        )->name('contract-types.destroy');
    });

    // Member routes
    // Route::middleware('permission:manage_staff_member')->group(function () {
    //     Route::get('members', [MemberController::class, 'index'])->name('members.index');
    //     Route::post('members', [MemberController::class, 'store'])->name('members.store');
    //     Route::get('members/create', [MemberController::class, 'create'])->name('members.create');
    //     Route::get('members/{member}/edit', [MemberController::class, 'edit'])->name('members.edit');
    //     Route::put('members/{member}', [MemberController::class, 'update'])->name('members.update');
    //     Route::get('members/{member}', [MemberController::class, 'show'])->name('members.show');
    //     Route::get('members/{member}/{group}', [MemberController::class, 'show']);
    //     Route::delete('members/{member}', [MemberController::class, 'destroy'])->name('members.destroy');
    //     Route::post(
    //         'members/{member}/active-deactive-administrator',
    //         [MemberController::class, 'activeDeActiveAdministrator']
    //     )->name('members.active.deactive');
    //     Route::post(
    //         'members/{member}/email-send',
    //         [MemberController::class, 'resendEmailVerification']
    //     )->name('email-send');
    //     Route::post(
    //         'members/{member}/email-verify',
    //         [MemberController::class, 'emailVerified']
    //     )->name('email-verify');
    // });
    Route::middleware(['permission:view_users|create_users|update_users|delete_users'])->group(function () {
        Route::get('members', [MemberController::class, 'index'])->middleware('permission:view_users')->name('members.index');
        Route::post('members', [MemberController::class, 'store'])->middleware('permission:create_users')->name('members.store');
        Route::get('members/create', [MemberController::class, 'create'])->middleware('permission:create_users')->name('members.create');
        Route::get('members/{member}/edit', [MemberController::class, 'edit'])->middleware('permission:update_users')->name('members.edit');
        Route::put('members/{member}', [MemberController::class, 'update'])->middleware('permission:update_users')->name('members.update');
        Route::get('members/{member}', [MemberController::class, 'show'])->middleware('permission:view_users')->name('members.show');
        Route::get('members/{member}/{group}', [MemberController::class, 'show'])->middleware('permission:view_users');
        Route::delete('members/{member}', [MemberController::class, 'destroy'])->middleware('permission:delete_users')->name('members.destroy');
        Route::post('members/{member}/active-deactive-administrator', [
            MemberController::class,
            'activeDeActiveAdministrator'
        ])->middleware('permission:update_users')->name('members.active.deactive');
        Route::post('members/{member}/email-send', [MemberController::class, 'resendEmailVerification'])->middleware('permission:update_users')->name('email-send');
        Route::post('members/{member}/email-verify', [MemberController::class, 'emailVerified'])->middleware('permission:update_users')->name('email-verify');
    });
    // Expenses routes
    Route::middleware(['permission:view_expenses|create_expenses|update_expenses|delete_expenses|export_expenses'])->group(function () {
        Route::get('expenses', [ExpenseController::class, 'index'])->name('expenses.index');
        Route::get('expenses/export', [ExpenseController::class, 'export'])->name('expenses.export');
        Route::get('expenses/create/{customerId?}', [ExpenseController::class, 'create'])->middleware('permission:create_expenses')->name('expenses.create');
        Route::post('expenses', [ExpenseController::class, 'store'])->middleware('permission:create_expenses')->name('expenses.store');
        Route::get('expenses/{expense}', [ExpenseController::class, 'show'])->middleware('permission:view_expenses')->name('expenses.show');
        Route::get('expenses/{expense}/pdf', [ExpenseController::class, 'downloadPDF'])->middleware('permission:export_expenses')->name('expenses.pdf');
        Route::get('expenses/{expense}/edit', [ExpenseController::class, 'edit'])->middleware('permission:update_expenses')->name('expenses.edit');
        Route::put('expenses/{expense}', [ExpenseController::class, 'update'])->middleware('permission:update_expenses')->name('expenses.update');
        Route::delete('expenses/{expense}', [ExpenseController::class, 'destroy'])->middleware('permission:delete_expenses')->name('expenses.destroy');
        Route::get('expense-attachment-download/{expense}', [ExpenseController::class, 'downloadMedia'])->middleware('permission:view_expenses');
        Route::get('expenses/{expense}/comments-count', [ExpenseController::class, 'getCommentsCount'])->middleware('permission:view_expenses')->name('expense.comments.count');
        Route::get('expenses/{expense}/{group}', [ExpenseController::class, 'show'])->middleware('permission:view_expenses');
        Route::post('expenses/{expense}/{group}/notes-count', [ExpenseController::class, 'getNotesCount'])->middleware('permission:view_expenses');
        Route::get('expense-download-media/{mediaItem}', [ExpenseController::class, 'download'])->middleware('permission:export_expenses')->name('expense.download.media');
        Route::get('expenses-category-chart', [ExpenseController::class, 'expenseCategoryByChart'])->middleware('permission:view_expenses')->name('expenses.expenseCategoryChart');

        Route::delete('expenses/file/{id}', [ExpenseController::class, 'file_delete'])->middleware('permission:delete_expenses')->name('expenses.file.delete');
    });


    // Leads routes
    Route::group(['middleware' => ['permission:view_leads|create_leads|update_leads|delete_leads']], function () {

        Route::get('leads', [LeadController::class, 'index'])->name('leads.index');
        Route::get('leads/create/{customerId?}', [LeadController::class, 'create'])->middleware('permission:create_leads')->name('leads.create');
        Route::post('leads', [LeadController::class, 'store'])->middleware('permission:create_leads')->name('leads.store');
        // Route::get('leads/{lead}', [LeadController::class, 'show'])->name('leads.show');
        Route::get('leads/{lead}', [LeadController::class, 'view'])->middleware('permission:view_leads')->name('leads.show');
        Route::get('leads/{lead}/edit', [LeadController::class, 'edit'])->middleware('permission:update_leads')->name('leads.edit');
        Route::put('leads/{lead}', [LeadController::class, 'update'])->middleware('permission:update_leads')->name('leads.update');
        Route::delete('leads/{lead}', [LeadController::class, 'destroy'])->middleware('permission:delete_leads')->name('leads.destroy');


        Route::put(
            'leads/{lead}/status/{status}',
            [LeadController::class, 'changeStatus']
        )->name('leads.changeStatus');
        Route::get('leads-kanban-list', [LeadController::class, 'kanbanList'])->name('leads.kanbanList');
        Route::post(
            'contact-as-per-customer',
            [LeadController::class, 'contactAsPerCustomer']
        )->name('leads.contactAsPerCustomer');
        Route::get('leads/{lead}/{group}', [LeadController::class, 'show']);
        Route::post(
            'leads/{lead}/{group}/notes-count',
            [LeadController::class, 'getNotesCount']
        );
        Route::post(
            'lead-convert-customer',
            [CustomerController::class, 'leadConvertToCustomer']
        )->name('lead.convert.customer');
        Route::get(
            'leads-convert-chart',
            [LeadController::class, 'leadConvertChart']
        )->name('leads.leadConvertChart');
    });

    // Invoices routes
    // Route::middleware('permission:manage_invoices')->group(function () {
    //     Route::get('invoices', [InvoiceController::class, 'index'])->name('invoices.index');
    //     Route::post('invoices', [InvoiceController::class, 'store'])->name('invoices.store');
    //     Route::get('invoices/create/{customerId?}', [InvoiceController::class, 'create'])->name('invoices.create');
    //     Route::get('invoices/{invoice}/edit', [InvoiceController::class, 'edit'])->name('invoices.edit');
    //     Route::post('invoices/{invoice}', [InvoiceController::class, 'update'])->name('invoices.update');
    //     Route::delete('invoices/{invoice}', [InvoiceController::class, 'destroy'])->name('invoices.destroy');
    //     Route::get('invoices/{invoice}', [InvoiceController::class, 'show'])->name('invoices.show');
    //     Route::get(
    //         'invoices/{invoice}/view-as-customer',
    //         [InvoiceController::class, 'viewAsCustomer']
    //     )->name('invoice.view-as-customer');

    //     Route::put(
    //         'invoices/{invoice}/change-status',
    //         [InvoiceController::class, 'changeStatus']
    //     )->name('invoice.change-status');
    //     Route::get('invoices/{invoice}/{group}', [InvoiceController::class, 'show']);
    //     Route::post(
    //         'invoices/{invoice}/{group}/notes-count',
    //         [InvoiceController::class, 'getNotesCount']
    //     );
    // });

    // Goal routes
    Route::middleware('permission:manage_goals')->group(function () {
        Route::get('goals', [GoalController::class, 'index'])->name('goals.index');
        Route::post('goals', [GoalController::class, 'store'])->name('goals.store');
        Route::get('goals/create', [GoalController::class, 'create'])->name('goals.create');
        Route::put('goals/{goal}', [GoalController::class, 'update'])->name('goals.update');
        Route::get('goals/{goal}', [GoalController::class, 'show'])->name('goals.show');
        Route::delete('goals/{goal}', [GoalController::class, 'destroy'])->name('goals.destroy');
        Route::get('goals/{goal}/edit', [GoalController::class, 'edit'])->name('goals.edit');
    });

    // Contracts routes
    Route::middleware('permission:manage_contracts')->group(function () {
        Route::get('contracts', [ContractController::class, 'index'])->name('contracts.index');
        Route::post('contracts', [ContractController::class, 'store'])->name('contracts.store');
        Route::get('contracts/create/{customerId?}', [ContractController::class, 'create'])->name('contracts.create');
        Route::put('contracts/{contract}', [ContractController::class, 'update'])->name('contracts.update');
        Route::get('contracts/{contract}', [ContractController::class, 'show'])->name('contracts.show');
        Route::delete('contracts/{contract}', [ContractController::class, 'destroy'])->name('contracts.destroy');
        Route::get('contracts/{contract}/edit', [ContractController::class, 'edit'])->name('contracts.edit');
        Route::get('contracts/{contract}/{group}', [ContractController::class, 'show']);
        Route::get(
            'contracts-summary',
            [ContractController::class, 'contractSummary']
        )->name('contracts.contractSummary');
    });

    // Proposals routes
    Route::middleware('permission:manage_proposals')->group(function () {
        Route::get('proposals', [ProposalController::class, 'index'])->name('proposals.index');
        Route::post('proposals', [ProposalController::class, 'store'])->name('proposals.store');
        Route::get('proposals/create/{relatedTo?}', [ProposalController::class, 'create'])->name('proposals.create');
        Route::get('proposals/{proposal}/edit', [ProposalController::class, 'edit'])->name('proposals.edit');
        Route::post('proposals/{proposal}', [ProposalController::class, 'update'])->name('proposals.update');
        Route::delete('proposals/{proposal}', [ProposalController::class, 'destroy'])->name('proposals.destroy');
        Route::get('proposals/{proposal}', [ProposalController::class, 'show'])->name('proposals.show');
        Route::put(
            'proposals/{proposal}/change-status',
            [ProposalController::class, 'changeStatus']
        )->name('proposal.change-status');
        Route::get(
            'proposals/{proposal}/view-as-customer',
            [ProposalController::class, 'viewAsCustomer']
        )->name('proposal.view-as-customer');
        Route::get('proposals/{proposal}/pdf', [ProposalController::class, 'convertToPdf'])->name('proposal.pdf');
        Route::post(
            'proposals/{proposal}/convert-to-invoice',
            [ProposalController::class, 'convertToInvoice']
        )->name('proposal.convert-to-invoice');
        Route::post(
            'proposals/{proposal}/convert-to-estimate',
            [ProposalController::class, 'convertToEstimate']
        )->name('proposal.convert-to-estimate');
        Route::get('proposals/{proposal}/{group}', [ProposalController::class, 'show']);
    });

    // Credit Notes routes
    Route::middleware(['permission:view_credit_notes|create_credit_notes|update_credit_notes|delete_credit_notes|export_credit_notes'])->group(function () {
        Route::get('credit-notes', [CreditNoteController::class, 'index'])->name('credit-notes.index');
        Route::get('credit-notes/invoice', [CreditNoteController::class, 'getInvoice'])->name('credit-notes.invoice');
        Route::post('credit-notes', [CreditNoteController::class, 'store'])->middleware('permission:create_credit_notes')->name('credit-notes.store');
        Route::get('credit-notes/create/{customerId?}', [CreditNoteController::class, 'create'])->middleware('permission:create_credit_notes')->name('credit-notes.create');
        Route::get('credit-notes/{creditNote}/edit', [CreditNoteController::class, 'edit'])->middleware('permission:update_credit_notes')->name('credit-notes.edit');
        Route::post('credit-notes/{creditNote}', [CreditNoteController::class, 'update'])->middleware('permission:update_credit_notes')->name('credit-notes.update');
        Route::delete('credit-notes/{creditNote}', [CreditNoteController::class, 'destroy'])->middleware('permission:delete_credit_notes')->name('credit-notes.destroy');
        Route::get('credit-notes/{creditNote}', [CreditNoteController::class, 'show'])->middleware('permission:view_credit_notes')->name('credit-notes.show');
        Route::put('credit-notes/{creditNote}/change-payment-status', [CreditNoteController::class, 'changePaymentStatus'])->middleware('permission:update_credit_notes')->name('credit-note.change-payment-status');
        Route::get('credit-notes/{creditNote}/view-as-customer', [CreditNoteController::class, 'viewAsCustomer'])->middleware('permission:view_credit_notes')->name('credit-note.view-as-customer');
        Route::get('credit-notes/{creditNote}/pdf', [CreditNoteController::class, 'convertToPdf'])->middleware('permission:export_credit_notes')->name('credit-note.pdf');
    });


    // setting routes
    Route::middleware('permission:manage_settings')->group(function () {
        Route::get('settings', [SettingController::class, 'show'])->name('settings.show');
        Route::post('settings', [SettingController::class, 'update'])->name('settings.update');
    });

    // Activity Log
    Route::get('activity-logs', [ActivityLogController::class, 'index'])->name('activity.logs.index');
    Route::post('change-filter', [ActivityLogController::class, 'index'])->name('change.filter');

    Route::get(
        'translation-manager',
        [TranslationManagerController::class, 'index']
    )->name('translation-manager.index');
    Route::get(
        'translation-manager/{language}/edit',
        [TranslationManagerController::class, 'edit']
    )->name('translation.manager.edit');
    Route::get(
        'language/translation/{language}',
        [TranslationManagerController::class, 'showTranslation']
    )->name('language.translation');
    Route::post(
        'translation-manager',
        [TranslationManagerController::class, 'store']
    )->name('translation-manager.store');
    Route::put(
        'translation-manager/{language}',
        [TranslationManagerController::class, 'update']
    )->name('translation-manager.update');
    Route::delete(
        'translation-manager/{language}',
        [TranslationManagerController::class, 'destroy']
    )->name('translation.manager.destroy');
    Route::post(
        'language/translation/{language}/update',
        [TranslationManagerController::class, 'updateTranslation']
    )->name('language.translation.update');

    // Task routes
    Route::middleware('permission:manage_tasks')->group(function () {
        Route::get('tasks', [TaskController::class, 'index'])->name('tasks.index');
        Route::get(
            'tasks/create/{relatedTo?}/{customerId?}',
            [TaskController::class, 'create']
        )->name('tasks.create');
        Route::post('tasks', [TaskController::class, 'store'])->name('tasks.store');
        Route::get('tasks/{task}', [TaskController::class, 'show'])->name('tasks.show');
        Route::get('tasks/{task}/edit', [TaskController::class, 'edit'])->name('tasks.edit');
        Route::put('tasks/{task}', [TaskController::class, 'update'])->name('tasks.update');
        Route::delete('tasks/{task}', [TaskController::class, 'destroy'])->name('tasks.destroy');
        Route::get('change-owner', [TaskController::class, 'changeOwner'])->name('change-owner');
        Route::put('tasks/{task}/status/{status}', [TaskController::class, 'changeStatus'])->name('tasks.changeStatus');
        Route::get('tasks-kanban-list', [TaskController::class, 'kanbanList'])->name('tasks.kanbanList');
        Route::get(
            'tasks/{task}/comments-count',
            [TaskController::class, 'getCommentsCount']
        )->name('task.comments-count');
        Route::get('tasks/{task}/{group}', [TaskController::class, 'show']);
    });

    // Projects routes
    Route::middleware(['permission:view_projects|create_projects|update_projects|delete_projects'])->group(function () {
        Route::get('projects', [ProjectController::class, 'index'])->middleware('permission:view_projects')->name('projects.index');
        Route::get('projects/customers', [ProjectController::class, 'customerList'])->middleware('permission:view_projects')->name('projects.customers');
        Route::post('projects', [ProjectController::class, 'store'])->middleware('permission:create_projects')->name('projects.store');
        Route::get('projects/create/{customerId?}', [ProjectController::class, 'create'])->middleware('permission:create_projects')->name('projects.create');
        Route::put('projects/{project}', [ProjectController::class, 'update'])->middleware('permission:update_projects')->name('projects.update');
        Route::get('projects/{project}', [ProjectController::class, 'show'])->middleware('permission:view_projects')->name('projects.show');
        Route::delete('projects/{project}', [ProjectController::class, 'destroy'])->middleware('permission:delete_projects')->name('projects.destroy');
        Route::get('projects/{project}/edit', [ProjectController::class, 'edit'])->middleware('permission:update_projects')->name('projects.edit');
        Route::post('member-as-per-customer', [ProjectController::class, 'memberAsPerCustomer'])->middleware('permission:view_projects')->name('projects.memberAsPerCustomer');
        Route::get('projects/{project}/{group}', [ProjectController::class, 'show'])->middleware('permission:view_projects');
    });


    // Tickets routes
    Route::middleware('permission:manage_tickets')->group(function () {
        Route::get('tickets', [TicketController::class, 'index'])->name('ticket.index');
        Route::get('tickets/create', [TicketController::class, 'create'])->name('ticket.create');
        Route::post('tickets', [TicketController::class, 'store'])->name('ticket.store');
        Route::get('tickets/{ticket}', [TicketController::class, 'show'])->name('ticket.show');
        Route::get('tickets/{ticket}/edit', [TicketController::class, 'edit'])->name('ticket.edit');
        Route::put('tickets/{ticket}', [TicketController::class, 'update'])->name('ticket.update');
        Route::delete('tickets/{ticket}', [TicketController::class, 'destroy'])->name('ticket.destroy');
        Route::get(
            'tickets/predefinedReplyBody/{predefinedReplyId?}',
            [TicketController::class, 'getPredefinedReplyBody']
        )->name('ticket.reply.body');
        Route::get('tickets-attachment-download/{ticket}', [TicketController::class, 'downloadMedia']);
        Route::get('tickets/{ticket}/{group}', [TicketController::class, 'show'])->name('tickets.show');
        Route::post(
            'tickets/{ticket}/{group}/notes-count',
            [TicketController::class, 'getNotesCount']
        );
        Route::get('tickets-kanban-list', [TicketController::class, 'kanbanList'])->name('tickets.kanbanList');
        Route::put(
            'tickets/{ticket}/status/{statusId}',
            [TicketController::class, 'changeStatus']
        )->name('tickets.changeStatus');
        Route::delete(
            'ticket-attachment-delete',
            [TicketController::class, 'attachmentDelete']
        )->name('ticket.attachment');
        Route::get(
            'download-media/{mediaItem}',
            [TicketController::class, 'download']
        )->name('ticket.download.media');
    });

    // Ticket Priorities routes
    Route::middleware('permission:manage_ticket_priority')->group(function () {
        Route::get('ticket-priorities', [TicketPriorityController::class, 'index'])->name('ticketPriorities.index');
        Route::post('ticket-priorities', [TicketPriorityController::class, 'store'])->name('ticketPriorities.store');
        Route::get(
            'ticket-priorities/{ticketPriority}/edit',
            [TicketPriorityController::class, 'edit']
        )->name('ticketPriorities.edit');
        Route::put(
            'ticket-priorities/{ticketPriority}',
            [TicketPriorityController::class, 'update']
        )->name('ticketPriorities.update');
        Route::delete(
            'ticket-priorities/{ticketPriority}',
            [TicketPriorityController::class, 'destroy']
        )->name('ticketPriorities.destroy');
        Route::post(
            'ticket-priorities/{ticket_priority_id}/active-deactive',
            [TicketPriorityController::class, 'activeDeActiveCategory']
        )->name('active.deactive');
    });

    // Ticket Status routes
    Route::middleware('permission:manage_ticket_statuses')->group(function () {
        Route::get('ticket-statuses', [TicketStatusController::class, 'index'])->name('ticket.status.index');
        Route::post('ticket-statuses', [TicketStatusController::class, 'store'])->name('ticket.status.store');
        Route::get(
            'ticket-statuses/{ticketStatus}/edit',
            [TicketStatusController::class, 'edit']
        )->name('ticket.status.edit');
        Route::put(
            'ticket-statuses/{ticketStatus}',
            [TicketStatusController::class, 'update']
        )->name('ticket.status.update');
        Route::delete(
            'ticket-statuses/{ticketStatus}',
            [TicketStatusController::class, 'destroy']
        )->name('ticket.status.destroy');
    });

    // Payment Modes routes
    Route::middleware('permission:manage_payment_mode')->group(function () {
        Route::get('payment-modes', [PaymentModeController::class, 'index'])->name('payment-modes.index');
        Route::post('payment-modes', [PaymentModeController::class, 'store'])->name('payment-modes.store');
        Route::get('payment-modes/{paymentMode}/edit', [PaymentModeController::class, 'edit'])->name('payment-modes.edit');
        Route::put('payment-modes/{paymentMode}', [PaymentModeController::class, 'update'])->name('payment-modes.update');
        Route::delete(
            'payment-modes/{paymentMode}',
            [PaymentModeController::class, 'destroy']
        )->name('payment-modes.destroy');
        Route::post(
            'payment-modes/{paymentMode}/active-deactive',
            [PaymentModeController::class, 'activeDeActivePaymentMode']
        )->name('payment-modes.active.deactive');
        Route::get(
            'payment-modes/{paymentMode}',
            [PaymentModeController::class, 'show']
        )->name('payment-modes.show');
    });

    // Lead Sources route
    Route::middleware('permission:manage_lead_sources')->group(function () {
        Route::get('lead-sources', [LeadSourceController::class, 'index'])->name('lead.source.index');
        Route::post('lead-sources', [LeadSourceController::class, 'store'])->name('lead.source.store');
        Route::get('lead-sources/{leadSource}/edit', [LeadSourceController::class, 'edit'])->name('lead.source.edit');
        Route::put('lead-sources/{leadSource}', [LeadSourceController::class, 'update'])->name('lead.source.update');
        Route::delete('lead-sources/{leadSource}', [LeadSourceController::class, 'destroy'])->name('lead.source.destroy');
    });

    // Lead Status routes
    Route::middleware('permission:manage_lead_status')->group(function () {
        Route::get('lead-status', [LeadStatusController::class, 'index'])->name('lead.status.index');
        Route::post('lead-status', [LeadStatusController::class, 'store'])->name('lead.status.store');
        Route::get('lead-status/{leadStatus}/edit', [LeadStatusController::class, 'edit'])->name('lead.status.edit');
        Route::put('lead-status/{leadStatus}', [LeadStatusController::class, 'update'])->name('lead.status.update');
        Route::delete('lead-status/{leadStatus}', [LeadStatusController::class, 'destroy'])->name('lead.status.destroy');
    });

    Route::prefix('sales-reports')->name('sales-reports.')->group(function () {
        // ->middleware('permission:view_sales_reports') // Commented out permission
        Route::get('/', [SalesReportsController::class, 'index'])->name('index');
        Route::get('/exports', [SalesReportsController::class, 'export'])->name('export');
    });
    Route::prefix('sales-tax-reports')->name('sales-tax-reports.')->group(function () {
        // ->middleware('permission:view_sales_reports') // Commented out permission
        Route::get('/', [SalesTaxReportsController::class, 'index'])->name('index');
        Route::get('/exports', [SalesTaxReportsController::class, 'export'])->name('export');
    });
    Route::get('sales-item-reports', [SalesItemReportsController::class, 'index'])
        ->name('sales-item-reports.index');
    Route::get('sales-item-reports/exports', [SalesItemReportsController::class, 'export'])
        ->name('sales-item-reports.export');

    Route::get('credit-note-reports', [CreditNoteReportController::class, 'index'])->name('credit-note-reports.index');
    Route::get('credit-note-reports/export', [CreditNoteReportController::class, 'export'])->name('credit-note-reports.export');

    // Invoices routes
    Route::middleware(['permission:view_invoices|create_invoices|update_invoices|delete_invoices|export_invoices'])->group(function () {
        Route::get('invoices/{invoice}/pdf', [InvoiceController::class, 'covertToPdf'])->name('invoice.pdf');
        Route::get('invoices/dop', [InvoiceController::class, 'downloadPDF'])->name('invoices.dop');
        Route::get('invoices', [InvoiceController::class, 'index'])->name('invoices.index');
        Route::post('invoices', [InvoiceController::class, 'store'])->middleware('permission:create_invoices')->name('invoices.store');
        Route::get('invoices/create/{customerId?}', [InvoiceController::class, 'create'])->middleware('permission:create_invoices')->name('invoices.create');
        Route::get('invoices/{invoice}/edit', [InvoiceController::class, 'edit'])->middleware('permission:update_invoices')->name('invoices.edit');
        Route::post('invoices/{invoice}', [InvoiceController::class, 'update'])->middleware('permission:update_invoices')->name('invoices.update');
        Route::delete('invoices/{invoice}', [InvoiceController::class, 'destroy'])->middleware('permission:delete_invoices')->name('invoices.destroy');
        Route::get('invoices/{invoice}', [InvoiceController::class, 'show'])->middleware('permission:view_invoices')->name('invoices.show');
        Route::get('invoices/view/{invoice}', [InvoiceController::class, 'show'])->middleware('permission:view_invoices')->name('invoices.view');
        Route::post('invoices/send/email/', [InvoiceController::class, 'sendEmail'])->middleware('permission:create_invoices')->name('invoices.send.email');
        Route::post('invoices/send/sms/', [InvoiceController::class, 'sendSMS'])->middleware('permission:create_invoices')->name('invoices.send.sms');
        Route::post('invoices/send/whatsapp/', [InvoiceController::class, 'sendWhatsApp'])->middleware('permission:create_invoices')->name('invoices.send.whatsapp');
        Route::get('invoices/{invoice}/view-as-customer', [InvoiceController::class, 'viewAsCustomer'])->middleware('permission:view_invoices')->name('invoice.view-as-customer');
        Route::put('invoices/{invoice}/change-status', [InvoiceController::class, 'changeStatus'])->middleware('permission:update_invoices')->name('invoice.change-status');
        Route::get('invoices/{invoice}/{group}', [InvoiceController::class, 'show'])->middleware('permission:view_invoices');
        Route::post('invoices/{invoice}/{group}/notes-count', [InvoiceController::class, 'getNotesCount'])->middleware('permission:view_invoices');
    });

    // Invoices routes
    Route::middleware(['permission:view_manual_sales|create_manual_sales|update_manual_sales|delete_manual_sales|export_manual_sales'])->group(function () {
        Route::get('manual-sales/{invoice}/pdf', [ManualSaleController::class, 'covertToPdf'])->name('manual-sales.pdf');
        Route::get('manual-sales/dop', [ManualSaleController::class, 'downloadPDF'])->name('manual-sales.dop');
        Route::get('manual-sales', [ManualSaleController::class, 'index'])->name('manual-sales.index');
        Route::post('manual-sales', [ManualSaleController::class, 'store'])->middleware('permission:create_manual_sales')->name('manual-sales.store');
        Route::get('manual-sales/create/{customerId?}', [ManualSaleController::class, 'create'])->middleware('permission:create_manual_sales')->name('manual-sales.create');
        Route::get('manual-sales/{invoice}/edit', [ManualSaleController::class, 'edit'])->middleware('permission:update_manual_sales')->name('manual-sales.edit');
        Route::post('manual-sales/{invoice}', [ManualSaleController::class, 'update'])->middleware('permission:update_manual_sales')->name('manual-sales.update');
        Route::delete('manual-sales/{invoice}', [ManualSaleController::class, 'destroy'])->middleware('permission:delete_manual_sales')->name('manual-sales.destroy');
        Route::get('manual-sales/{invoice}', [ManualSaleController::class, 'show'])->middleware('permission:view_manual_sales')->name('manual-sales.show');
        Route::get('manual-sales/accept-invoice/{invoice}', [ManualSaleController::class, 'accept_invoice'])->middleware('permission:view_manual_sales')->name('manual-sales.accept.invoice');
        Route::get('item-services/services', [ManualSaleController::class, 'getServices'])->name('item-services.services');
        Route::post(
            'item-services/services',
            [ProductController::class, 'store']
        )->name('item-services.services.store');

        Route::get('manual-sales/{id}/approve', [ManualSaleController::class, 'approve'])->name('manual-sales.approve');
    });

    Route::get('sample-qr-code', [ManualSaleController::class, 'tstqrCode']);


    Route::get('customer-address', [InvoiceController::class, 'getCustomerAddress'])->name('get.customer.address');
    Route::get(
        'credit-note-customer-address',
        [CreditNoteController::class, 'getCustomerAddress']
    )->name('get.creditnote.customer.address');
    Route::get(
        'estimates-customer-address',
        [EstimateController::class, 'getCustomerAddress']
    )->name('get.estimate.customer.address');
    Route::get(
        'proposal-customer-address',
        [ProposalController::class, 'getCustomerAddress']
    )->name('get.proposal.customer.address');

    // Payments routes
    Route::middleware('permission:manage_payment_mode')->group(function () {
        Route::get('payments', [PaymentController::class, 'index'])->name('payments.index');
        Route::post('payments', [PaymentController::class, 'store'])->name('payments.store');
        Route::delete('payments/{payment}', [PaymentController::class, 'destroy'])->name('payments.destroy');
        Route::get('payments/edit', [PaymentController::class, 'addPayment'])->name('payments.create');
    });

    // Payment for Invoices routes
    Route::get('payments-list', [Listing\PaymentListing::class, 'index'])->name('payments.list.index');
    Route::get('payment-details/{payment?}', [Listing\PaymentListing::class, 'show'])->name('payments.list.show');
    Route::get('payments-list/create', [Listing\PaymentListing::class, 'create'])->name('payments.list.create');
    Route::post('payments-list/store', [Listing\PaymentListing::class, 'store'])->name('payments.list.store');
    Route::put('payments-list/{payment}', [Listing\PaymentListing::class, 'update'])->name('payments.list.update');
    Route::get('payments-list/{payment}/edit', [Listing\PaymentListing::class, 'edit'])->name('payments.list.edit');
    Route::get('payments-list/{payment}/show', [Listing\PaymentListing::class, 'show'])->name('payments.list.show');
    Route::delete('payments-list/{payment}', [Listing\PaymentListing::class, 'destroy'])->name('payments.list.destroy');
    Route::get('payments-list/export-csv', [Listing\PaymentListing::class, 'exportCsv'])->name('payments.list.exportCsv');


    Route::get('purchase-invoice-payments-list', [Listing\PurchaseInvoicePaymentListing::class, 'index'])->name('purchase-invoice-payments.list.index');
    Route::get('purchase-invoice-payment-details/{payment?}', [Listing\PurchaseInvoicePaymentListing::class, 'show'])->name('purchase-invoice-payments.list.show');
    Route::get('purchase-invoice-payments-list/create', [Listing\PurchaseInvoicePaymentListing::class, 'create'])->name('purchase-invoice-payments.list.create');
    Route::post('purchase-invoice-payments-list/store', [Listing\PurchaseInvoicePaymentListing::class, 'store'])->name('purchase-invoice-payments.list.store');
    Route::put('purchase-invoice-payments-list/{payment}', [Listing\PurchaseInvoicePaymentListing::class, 'update'])->name('purchase-invoice-payments.list.update');
    Route::get('purchase-invoice-payments-list/{payment}/edit', [Listing\PurchaseInvoicePaymentListing::class, 'edit'])->name('purchase-invoice-payments.list.edit');
    Route::get('purchase-invoice-payments-list/{payment}/show', [Listing\PurchaseInvoicePaymentListing::class, 'show'])->name('purchase-invoice-payments.list.show');
    Route::delete('purchase-invoice-payments-list/{payment}', [Listing\PurchaseInvoicePaymentListing::class, 'destroy'])->name('purchase-invoice-payments.list.destroy');
    Route::get('purchase-invoice-payments-list/export-csv', [Listing\PurchaseInvoicePaymentListing::class, 'exportCsv'])->name('purchase-invoice-payments.list.exportCsv');

    // Estimates routes
    // Route::middleware('permission:manage_estimates')->group(function () {
    //     Route::get('estimates', [EstimateController::class, 'index'])->name('estimates.index');
    //     Route::get('estimates/create/{customerId?}', [EstimateController::class, 'create'])->name('estimates.create');
    //     Route::post('estimates', [EstimateController::class, 'store'])->name('estimates.store');
    //     Route::get('estimates/{estimate}/edit', [EstimateController::class, 'edit'])->name('estimates.edit');
    //     Route::post('estimates/{estimate}', [EstimateController::class, 'update'])->name('estimates.update');
    //     Route::delete('estimates/{estimate}', [EstimateController::class, 'destroy'])->name('estimates.destroy');
    //     Route::get('estimates/{estimate}', [EstimateController::class, 'show'])->name('estimates.show');
    //     Route::post('address/estimates', [EstimateController::class, 'saveAddress'])->name(name: 'estimates.address');
    //     Route::put(
    //         'estimates/{estimate}/change-status',
    //         [EstimateController::class, 'changeStatus']
    //     )->name('estimate.change-status');
    //     Route::get(
    //         'estimates/{estimate}/view-as-customer',
    //         [EstimateController::class, 'viewAsCustomer']
    //     )->name('estimate.view-as-customer');
    //     Route::get('estimates/{estimate}/pdf', [EstimateController::class, 'convertToPdf'])->name('estimate.pdf');
    //     Route::post(
    //         'estimates/{estimate}/convert-to-invoice',
    //         [EstimateController::class, 'convertToInvoice']
    //     )->name('estimate.convert-to-invoice');
    //     Route::get('estimates/{estimate}/{group}', [EstimateController::class, 'show']);
    // });
    Route::middleware(['permission:view_quotations|create_quotations|update_quotations|delete_quotations|export_quotations'])->group(function () {
        Route::get('estimates', [EstimateController::class, 'index'])->name('estimates.index');
        Route::get('estimates/create/{customerId?}', [EstimateController::class, 'create'])->name('estimates.create');
        Route::post(
            'estimates',
            [EstimateController::class, 'store']
        )->name('estimates.store');
        Route::get('estimates/{estimate}/edit', [EstimateController::class, 'edit'])->name('estimates.edit');
        Route::post('estimates/{estimate}', [EstimateController::class, 'update'])->name('estimates.update');
        Route::delete('estimates/{estimate}', [EstimateController::class, 'destroy'])->name('estimates.destroy');
        Route::get('estimates/{estimate}', [EstimateController::class, 'show'])->name('estimates.show');
        Route::post('address/estimates', [EstimateController::class, 'saveAddress'])->name('estimates.address');
        Route::put('estimates/{estimate}/change-status', [EstimateController::class, 'changeStatus'])->name('estimate.change-status');
        Route::get('estimates/{estimate}/view-as-customer', [EstimateController::class, 'viewAsCustomer'])->name('estimate.view-as-customer');
        Route::get('estimates/{estimate}/pdf', [EstimateController::class, 'convertToPdf'])->name('estimate.pdf');
        Route::post('estimates/{estimate}/convert-to-invoice', [EstimateController::class, 'convertToInvoice'])->name('estimate.convert-to-invoice');
        Route::get('estimates/{estimate}/{group}', [EstimateController::class, 'show']);
    });






    // Profile routes
    Route::post('change-password', [UserController::class, 'changePassword'])->name('change.password');
    Route::get('profile', [UserController::class, 'editProfile'])->name('profile');
    Route::post('update-profile', [UserController::class, 'updateProfile'])->name('update.profile');
    Route::post('change-language', [UserController::class, 'changeLanguage'])->name('change.language');

    Route::post('contract-month-filter', [DashboardController::class, 'contractMonthFilter'])->name('contract.month.filter');




    Route::group(['middleware' => ['permission:view_currencies|create_currencies|update_currencies|delete_currencies']], function () {
        Route::get('currencies', [CurrencyController::class, 'index'])->name('currencies.index');
        Route::get('currencies/create', [CurrencyController::class, 'create'])->middleware('permission:create_currencies')->name('currencies.create');
        Route::post('currencies', [CurrencyController::class, 'store'])->middleware('permission:create_currencies')->name('currencies.store');
        Route::get('currencies/{currency}/view', [CurrencyController::class, 'view'])->middleware('permission:view_currencies')->name('currencies.view');
        Route::get('currencies/{currency}/edit', [CurrencyController::class, 'edit'])->middleware('permission:update_currencies')->name('currencies.edit');
        Route::put('currencies/{currency}', [CurrencyController::class, 'update'])->middleware('permission:update_currencies')->name('currencies.update');
        Route::delete('currencies/{currency}', [CurrencyController::class, 'destroy'])->middleware('permission:delete_currencies')->name('currencies.destroy');
    });

    Route::group(['middleware' => ['permission:view_task_assign|create_task_assign|update_task_assign|delete_task_assign']], function () {
        Route::get('task-assign', [TaskAssignController::class, 'index'])->name('task-assign.index');
        Route::get('task-assign/create', [TaskAssignController::class, 'create'])->middleware('permission:create_task_assign')->name('task-assign.create');
        Route::post('task-assign', [TaskAssignController::class, 'store'])->middleware('permission:create_task_assign')->name('task-assign.store');
        Route::get('task-assign/{task}/view', [TaskAssignController::class, 'view'])->middleware('permission:view_task_assign')->name('task-assign.view');
        Route::get('task-assign/{task}/edit', [TaskAssignController::class, 'edit'])->middleware('permission:update_task_assign')->name('task-assign.edit');
        Route::put('task-assign/{task}', [TaskAssignController::class, 'update'])->middleware('permission:update_task_assign')->name('task-assign.update');
        Route::delete('task-assign/{task}', [TaskAssignController::class, 'destroy'])->middleware('permission:delete_task_assign')->name('task-assign.destroy');
    });

    Route::get('task-status', [TaskStatusController::class, 'index'])->name('task-status.index');
    Route::get('task-status/create', [TaskStatusController::class, 'create'])->name('task-status.create');
    Route::post('task-status', [TaskStatusController::class, 'store'])->name('task-status.store');
    Route::get('task-status/{task}/view', [TaskStatusController::class, 'view'])->name('task-status.view');
    Route::get('task-status/{task}/edit', [TaskStatusController::class, 'edit'])->name('task-status.edit');
    Route::put('task-status/{task}', [TaskStatusController::class, 'update'])->name('task-status.update');
    Route::delete('task-status/{task}', [TaskStatusController::class, 'destroy'])->name('task-status.destroy');

    // Country module routes
    Route::get('countries', [CountryController::class, 'index'])->name('countries.index');
    Route::post('countries', [CountryController::class, 'store'])->name('countries.store');
    Route::get('countries/{country}/edit', [CountryController::class, 'edit'])->name('countries.edit');
    Route::put('countries/{country}', [CountryController::class, 'update'])->name('countries.update');
    Route::delete('countries/{country}', [CountryController::class, 'destroy'])->name('countries.destroy');
    Route::get('countries/{country}', [CountryController::class, 'show'])->name('countries.show');





    // states routes
    Route::group(['middleware' => ['permission:view_states|create_states|update_states|delete_states']], function () {
        Route::get('states', [StateController::class, 'index'])->name('states.index');
        Route::get('states/create', [StateController::class, 'create'])->middleware('permission:create_states')->name('states.create');
        Route::post('states', [StateController::class, 'store'])->middleware('permission:create_states')->name('states.store');
        Route::get('states/{state}/view', [StateController::class, 'view'])->middleware('permission:view_states')->name('states.view');
        Route::get('states/{state}/edit', [StateController::class, 'edit'])->middleware('permission:update_states')->name('states.edit');
        Route::put('states/{state}', [StateController::class, 'update'])->middleware('permission:update_states')->name('states.update');
        Route::delete('states/{state}', [StateController::class, 'destroy'])->middleware('permission:delete_states')->name('states.destroy');
    });

    // locations routes
    Route::group(['middleware' => ['permission:view_locations|create_locations|update_locations|delete_locations']], function () {
        Route::get('locations', [LocationController::class, 'index'])->name('locations.index');
        Route::get('locations/create', [LocationController::class, 'create'])->middleware('permission:create_locations')->name('locations.create');
        Route::post('locations', [LocationController::class, 'store'])->middleware('permission:create_locations')->name('locations.store');
        Route::get('locations/{location}/view', [LocationController::class, 'view'])->middleware('permission:view_locations')->name('locations.view');
        Route::get('locations/{location}/edit', [LocationController::class, 'edit'])->middleware('permission:update_locations')->name('locations.edit');
        Route::put('locations/{location}', [LocationController::class, 'update'])->middleware('permission:update_locations')->name('locations.update');
        Route::delete('locations/{location}', [LocationController::class, 'destroy'])->middleware('permission:delete_locations')->name('locations.destroy');
    });

    // Job Sources routes
    Route::group(['middleware' => ['permission:view_job_sources|create_job_sources|update_job_sources|delete_job_sources']], function () {
        Route::get('job-sources', [JobSourceController::class, 'index'])->name('job-sources.index');
        Route::get('job-sources/create', [JobSourceController::class, 'create'])->middleware('permission:create_job_sources')->name('job-sources.create');
        Route::post('job-sources', [JobSourceController::class, 'store'])->middleware('permission:create_job_sources')->name('job-sources.store');
        Route::get('job-sources/{jobSource}/view', [JobSourceController::class, 'view'])->middleware('permission:view_job_sources')->name('job-sources.view');
        Route::get('job-sources/{jobSource}/edit', [JobSourceController::class, 'edit'])->middleware('permission:update_job_sources')->name('job-sources.edit');
        Route::put('job-sources/{jobSource}', [JobSourceController::class, 'update'])->middleware('permission:update_job_sources')->name('job-sources.update');
        Route::delete('job-sources/{jobSource}', [JobSourceController::class, 'destroy'])->middleware('permission:delete_job_sources')->name('job-sources.destroy');
    });

    // Job Recruiters routes
    Route::group(['middleware' => ['permission:view_job_recruiters|create_job_recruiters|update_job_recruiters|delete_job_recruiters']], function () {
        Route::get('job-recruiters', [JobRecruiterController::class, 'index'])->name('job-recruiters.index');
        Route::get('job-recruiters/create', [JobRecruiterController::class, 'create'])->middleware('permission:create_job_recruiters')->name('job-recruiters.create');
        Route::post('job-recruiters', [JobRecruiterController::class, 'store'])->middleware('permission:create_job_recruiters')->name('job-recruiters.store');
        Route::get('job-recruiters/{jobRecruiter}/view', [JobRecruiterController::class, 'view'])->middleware('permission:view_job_recruiters')->name('job-recruiters.view');
        Route::get('job-recruiters/{jobRecruiter}/edit', [JobRecruiterController::class, 'edit'])->middleware('permission:update_job_recruiters')->name('job-recruiters.edit');
        Route::put('job-recruiters/{jobRecruiter}', [JobRecruiterController::class, 'update'])->middleware('permission:update_job_recruiters')->name('job-recruiters.update');
        Route::delete('job-recruiters/{jobRecruiter}', [JobRecruiterController::class, 'destroy'])->middleware('permission:delete_job_recruiters')->name('job-recruiters.destroy');
    });


    // cities routes
    Route::group(['middleware' => ['permission:view_cities|create_cities|update_cities|delete_cities']], function () {
        Route::get('cities', [CityController::class, 'index'])->name('cities.index');
        Route::get('cities/create', [CityController::class, 'create'])->middleware('permission:create_cities')->name('cities.create');
        Route::post('cities', [CityController::class, 'store'])->middleware('permission:create_cities')->name('cities.store');
        Route::get('cities/{city}/view', [CityController::class, 'view'])->middleware('permission:view_cities')->name('cities.view');
        Route::get('cities/{city}/edit', [CityController::class, 'edit'])->middleware('permission:update_cities')->name('cities.edit');
        Route::put('cities/{city}', [CityController::class, 'update'])->middleware('permission:update_cities')->name('cities.update');
        Route::delete('cities/{city}', [CityController::class, 'destroy'])->middleware('permission:delete_cities')->name('cities.destroy');
    });

    // areas routes
    Route::group(['middleware' => ['permission:view_areas|create_areas|update_areas|delete_areas']], function () {
        Route::get('areas', [AreaController::class, 'index'])->name('areas.index');
        Route::get('areas/create', [AreaController::class, 'create'])->middleware('permission:create_areas')->name('areas.create');
        Route::post('areas', [AreaController::class, 'store'])->middleware('permission:create_areas')->name('areas.store');
        Route::get('areas/{area}/view', [AreaController::class, 'view'])->middleware('permission:view_areas')->name('areas.view');
        Route::get('areas/{area}/edit', [AreaController::class, 'edit'])->middleware('permission:update_areas')->name('areas.edit');
        Route::put('areas/{area}', [AreaController::class, 'update'])->middleware('permission:update_areas')->name('areas.update');
        Route::delete('areas/{area}', [AreaController::class, 'destroy'])->middleware('permission:delete_areas')->name('areas.destroy');
    });

    // sample categories routes
    Route::group(['middleware' => ['permission:view_sample_categories|create_sample_categories|update_sample_categories|delete_sample_categories']], function () {
        Route::prefix('sample_categories')->name('sample_categories.')->group(function () {
            Route::get(
                '/',
                [SampleCategoryController::class, 'index']
            )->name('index');
            Route::get('/{category}/view', [SampleCategoryController::class, 'view'])->middleware('permission:view_sample_categories')->name('view');
            Route::get('/create', [SampleCategoryController::class, 'create'])->middleware('permission:create_sample_categories')->name('create');
            Route::post('/', [SampleCategoryController::class, 'store'])->middleware('permission:create_sample_categories')->name('store');
            Route::get('/{category}/edit', [SampleCategoryController::class, 'edit'])->middleware('permission:update_sample_categories')->name('edit');
            Route::put('/{category}', [SampleCategoryController::class, 'update'])
                ->middleware('permission:update_sample_categories')
                ->name('update');
            Route::delete('/{category}', [SampleCategoryController::class, 'destroy'])->middleware('permission:delete_sample_categories')->name('destroy');
            Route::get('/export', [SampleCategoryController::class, 'export'])
                ->middleware('permission:view_sample_categories')
                ->name('export');
        });
    });

    // sample receiving routes
    Route::group(['middleware' => ['permission:view_sample_receiving|create_sample_receiving|update_sample_receiving|delete_sample_receiving']], function () {
        Route::prefix('sample_receiving')->name('sample_receiving.')->group(function () {
            Route::get(
                '/',
                [SampleReceivingController::class, 'index']
            )->name('index');
            Route::get('/{category}/view', [SampleReceivingController::class, 'view'])->middleware('permission:view_sample_receiving')->name('view');
            Route::get('/{category}/pdf', [SampleReceivingController::class, 'pdf'])->middleware('permission:view_sample_receiving')->name('pdf');
            Route::get('/create', [SampleReceivingController::class, 'create'])->middleware('permission:create_sample_receiving')->name('create');
            Route::post('/', [SampleReceivingController::class, 'store'])->middleware('permission:create_sample_receiving')->name('store');
            Route::get('/{category}/edit', [SampleReceivingController::class, 'edit'])->middleware('permission:update_sample_receiving')->name('edit');
            Route::put('/{category}', [SampleReceivingController::class, 'update'])
                ->middleware('permission:update_sample_receiving')
                ->name('update');
            Route::delete('/{category}', [SampleReceivingController::class, 'destroy'])->middleware('permission:delete_sample_receiving')->name('destroy');
            Route::get('/export', [SampleReceivingController::class, 'export'])
                ->middleware('permission:view_sample_receiving')
                ->name('export');
        });
    });

    // certificate routes
    Route::group(['middleware' => ['permission:view_certificate|create_certificate|update_certificate|delete_certificate']], function () {
        Route::prefix('certificate')->name('certificate.')->group(function () {
            Route::get(
                '/',
                [CertificateController::class, 'index']
            )->name('index');
            Route::get('/{category}/view', [CertificateController::class, 'view'])->middleware('permission:view_certificate')->name('view');
            Route::get('/{category}/pdf', [CertificateController::class, 'pdf'])->middleware('permission:view_certificate')->name('pdf');

            Route::get('/create', [CertificateController::class, 'create'])->middleware('permission:create_certificate')->name('create');
            Route::post('/', [CertificateController::class, 'store'])->middleware('permission:create_certificate')->name('store');
            Route::get('/{category}/edit', [CertificateController::class, 'edit'])->middleware('permission:update_certificate')->name('edit');
            Route::put('/{category}', [CertificateController::class, 'update'])
                ->middleware('permission:update_certificate')
                ->name('update');
            Route::delete('/{category}', [CertificateController::class, 'destroy'])->middleware('permission:delete_certificate')->name('destroy');
            Route::get('/export', [CertificateController::class, 'export'])
                ->middleware('permission:view_certificate')
                ->name('export');
        });
    });

    // Route::resource('company-loans', CompanyLoanController::class);
    // Route::get('company-loans/get-customers-by-type', [CompanyLoanController::class, 'getCustomersByType'])->name('company-loans.get-customers-by-type');

    // Route::resource('documents', DocumentController::class);
    // Route::get('documents/{document}/download', [DocumentController::class, 'download'])->name('documents.download');

    Route::group(['middleware' => ['permission:view_company_loans|create_company_loans|update_company_loans|delete_company_loans']], function () {
        Route::get('company-loans', [CompanyLoanController::class, 'index'])->name('company-loans.index');
        Route::get('company-loans/create', [CompanyLoanController::class, 'create'])->middleware('permission:create_company_loans')->name('company-loans.create');
        Route::post('company-loans', [CompanyLoanController::class, 'store'])->middleware('permission:create_company_loans')->name('company-loans.store');
        Route::get('company-loans/{company_loan}', [CompanyLoanController::class, 'show'])->middleware('permission:view_company_loans')->name('company-loans.show');
        Route::get('company-loans/{company_loan}/edit', [CompanyLoanController::class, 'edit'])->middleware('permission:update_company_loans')->name('company-loans.edit');
        Route::put('company-loans/{company_loan}', [CompanyLoanController::class, 'update'])->middleware('permission:update_company_loans')->name('company-loans.update');
        Route::delete('company-loans/{company_loan}', [CompanyLoanController::class, 'destroy'])->middleware('permission:delete_company_loans')->name('company-loans.destroy');
        Route::get('company-loans/get-customers-by-type', [CompanyLoanController::class, 'getCustomersByType'])->name('company-loans.get-customers-by-type');
    });

    // Documents routes with permissions
    Route::group(['middleware' => ['permission:view_documents|create_documents|update_documents|delete_documents']], function () {
        Route::get('documents', [DocumentController::class, 'index'])->name('documents.index');
        Route::get('documents/create', [DocumentController::class, 'create'])->middleware('permission:create_documents')->name('documents.create');
        Route::post('documents', [DocumentController::class, 'store'])->middleware('permission:create_documents')->name('documents.store');
        Route::get('documents/{document}', [DocumentController::class, 'show'])->middleware('permission:view_documents')->name('documents.show');
        Route::get('documents/{document}/edit', [DocumentController::class, 'edit'])->middleware('permission:update_documents')->name('documents.edit');
        Route::put('documents/{document}', [DocumentController::class, 'update'])->middleware('permission:update_documents')->name('documents.update');
        Route::delete('documents/{document}', [DocumentController::class, 'destroy'])->middleware('permission:delete_documents')->name('documents.destroy');
        Route::get('documents/{document}/download', [DocumentController::class, 'download'])->name('documents.download');
    });

    // Route::resource('logged-users', LoggedUserController::class);
    // Route::get('logged-users/online', [LoggedUserController::class, 'getOnlineUsers'])->name('logged-users.online');
    // Route::post('logged-users/{id}/force-logout', [LoggedUserController::class, 'forceLogout'])->name('logged-users.force-logout');

    // Route::post('/logged-users/geolocate', [LoggedUserController::class, 'storeBrowserLocation'])
    //     ->name('logged-users.geolocate')
    //     ->middleware(['web', 'auth']);

    Route::group(['middleware' => ['permission:view_logged_users|force_logout_logged_users']], function () {

        Route::resource('logged-users', LoggedUserController::class);

        Route::get('logged-users/online', [LoggedUserController::class, 'getOnlineUsers'])
            ->name('logged-users.online')
            ->middleware('permission:view_logged_users');

        Route::post('logged-users/{id}/force-logout', [LoggedUserController::class, 'forceLogout'])
            ->name('logged-users.force-logout')
            ->middleware('permission:force_logout_logged_users');

        Route::post('logged-users/geolocate', [LoggedUserController::class, 'storeBrowserLocation'])
            ->name('logged-users.geolocate')
            ->middleware(['web', 'auth']); // Keep web+auth if needed
    });

    // Route::resource('revokes', RevokeController::class);
    // // Route::get('revokes/create/{termination}', [RevokeController::class, 'create'])->name('revokes.create');
    // Route::get('revokes/create', [RevokeController::class, 'create'])->name('revokes.create');


    // Revoke Routes with permissions
    Route::group(['middleware' => ['permission:view_revokes|create_revokes|update_revokes|delete_revokes']], function () {
        Route::get('revokes', [RevokeController::class, 'index'])->name('revokes.index');
        Route::get('revokes/{revoke}', [RevokeController::class, 'show'])->middleware('permission:view_revokes')->name('revokes.show');
        // Route::get('revokes/create', [RevokeController::class, 'create'])->middleware('permission:create_revokes')->name('revokes.create');
        Route::post('revokes', [RevokeController::class, 'store'])->middleware('permission:create_revokes')->name('revokes.store');
        Route::get('revokes/{revoke}/edit', [RevokeController::class, 'edit'])->middleware('permission:update_revokes')->name('revokes.edit');
        Route::put('revokes/{revoke}', [RevokeController::class, 'update'])->middleware('permission:update_revokes')->name('revokes.update');
        Route::delete('revokes/{revoke}', [RevokeController::class, 'destroy'])->middleware('permission:delete_revokes')->name('revokes.destroy');
    });

    // Route::resource('safety-materials', SafetyMaterialController::class);
    // Route::post('safety-materials/calculate-next-date', [SafetyMaterialController::class, 'calculateNextDate'])->name('safety-materials.calculate-next-date');

    Route::group(['middleware' => ['permission:view_safety_materials|create_safety_materials|update_safety_materials|delete_safety_materials']], function () {
        Route::resource('safety-materials', SafetyMaterialController::class);
        Route::post('safety-materials/calculate-next-date', [SafetyMaterialController::class, 'calculateNextDate'])
            ->name('safety-materials.calculate-next-date')
            ->middleware('permission:create_safety_materials'); // or whichever permission you want
    });

    Route::resource('project-calculations', ProjectCalculationController::class);

    Route::resource('casual-employee-timesheets', CasualEmployeeTimesheetController::class);
    Route::get('get-employee-details/{id}', [CasualEmployeeTimesheetController::class, 'getEmployeeDetails'])
        ->name('get-employee-details');

    Route::group(['middleware' => ['permission:view_master_accounts|create_master_accounts|update_master_accounts|delete_master_accounts']], function () {
        Route::get('master-accounts', [MasterAccountController::class, 'index'])->name('master-accounts.index');
        Route::get('master-accounts/create', [MasterAccountController::class, 'create'])->middleware('permission:create_master_accounts')->name('master-accounts.create');
        Route::post('master-accounts', [MasterAccountController::class, 'store'])->middleware('permission:create_master_accounts')->name('master-accounts.store');
        Route::get('master-accounts/{masterAccount}', [MasterAccountController::class, 'show'])->middleware('permission:view_master_accounts')->name('master-accounts.show');
        Route::get('master-accounts/{masterAccount}/edit', [MasterAccountController::class, 'edit'])->middleware('permission:update_master_accounts')->name('master-accounts.edit');
        Route::put('master-accounts/{masterAccount}', [MasterAccountController::class, 'update'])->middleware('permission:update_master_accounts')->name('master-accounts.update');
        Route::delete('master-accounts/{masterAccount}', [MasterAccountController::class, 'destroy'])->middleware('permission:delete_master_accounts')->name('master-accounts.destroy');
    });
    // Casual Employee
    Route::group([], function () {
        Route::get('casuel-employees', [CasualEmployeeController::class, 'index'])->name('casual.employees.index');
        Route::get('casuel-employees/create', [CasualEmployeeController::class, 'create'])->name('casual.employees.create');
        Route::post('casuel-employees', [CasualEmployeeController::class, 'store'])->name('casual.employees.store');
        Route::get('casuel-employees/{employee}/edit', [CasualEmployeeController::class, 'edit'])->name('casual.employees.edit');
        Route::get('casuel-employees/{employee}/view', [CasualEmployeeController::class, 'view'])->name('casual.employees.view');
        Route::post('casuel-employees/{employee}', [CasualEmployeeController::class, 'update'])->name('casual.employees.update');
        Route::delete('casuel-employees/{employee}', [CasualEmployeeController::class, 'destroy'])->name('casual.employees.destroy');
        Route::delete('casuel-employees/file/{id}', [CasualEmployeeController::class, 'file_delete'])->name('casual.employees.file.delete');
        Route::get('casuel-employees/export/{branch?}', [CasualEmployeeController::class, 'export'])
            ->name('casual.employees.export');
        Route::get('casuel-employees/card/{employee}', [CasualEmployeeController::class, 'cardView'])->name('casual.employees.card.view');
    });

    //  Route::group(['middleware' => ['permission:view_balance_sheets']], function () {
    //     Route::get('balance-sheets', [BalanceSheetController::class, 'index'])->name('balance-sheets.index');
    // });
    Route::get('balance-sheets', [BalanceSheetController::class, 'index'])->name('balance-sheets.index');
    Route::get('trading-accounts', [TradingAccountController::class, 'index'])->name('trading-accounts.index');
    Route::get('profit-and-loss', [ProfitLossController::class, 'index'])->name('profit-and-loss.index');
});
Route::get('revokes/create', [RevokeController::class, 'create'])
    // ->middleware('permission:create_revokes')
    ->name('revokes.create');

Route::middleware(['auth', 'xss', 'checkUserStatus', 'checkRoleAdmin', 'role:client'])->prefix('client')->group(function () {
    Route::get('dashboard', [Clients\DashboardController::class, 'index'])->name('clients.dashboard');


    // Projects routes
    Route::middleware('permission:contact_projects')->group(function () {
        Route::get('projects', [Clients\ProjectController::class, 'index'])->name('clients.projects.index');
        Route::get('projects/{project}', [Clients\ProjectController::class, 'show'])->name('clients.projects.show');
        Route::get(
            'projects/{project}/{group}',
            [Clients\ProjectController::class, 'show']
        );
    });

    // Tasks routes
    Route::get('tasks', [Clients\TaskController::class, 'index'])->name('clients.tasks.index');
    Route::get('tasks/{task}', [Clients\TaskController::class, 'show'])->name('clients.tasks.show');
    Route::get('tasks/{task}/{group}', [Clients\TaskController::class, 'show']);

    // Reminder routes
    Route::get('reminder', [Clients\ReminderController::class, 'index'])->name('clients.reminder.index');

    // Invoices routes
    Route::middleware('permission:contact_invoices')->group(function () {
        Route::get('invoices', [Clients\InvoiceController::class, 'index'])->name('clients.invoices.index');
        Route::get(
            'invoices/{invoice}/view-as-customer',
            [Clients\InvoiceController::class, 'viewAsCustomer']
        )->name('clients.invoices.view-as-customer');
        Route::get(
            'invoices/{invoice}/pdf',
            [Clients\InvoiceController::class, 'covertToPdf']
        )->name('clients.invoice.pdf');
        Route::post('invoice-stripe-payment', [PaymentController::class, 'createSession']);
        Route::get(
            'invoice-payment-success',
            [PaymentController::class, 'paymentSuccess']
        )->name('clients.invoice-payment-success');
        Route::get(
            'invoice-failed-payment',
            [PaymentController::class, 'handleFailedPayment']
        )->name('clients.invoice-failed-payment');
    });

    // Proposals routes
    Route::middleware('permission:contact_proposals')->group(function () {
        Route::get('proposals', [Clients\ProposalController::class, 'index'])->name('clients.proposals.index');
        Route::get(
            'proposals/{proposal}/view-as-customer',
            [Clients\ProposalController::class, 'viewAsCustomer']
        )->name('clients.proposals.view-as-customer');
        Route::post(
            'proposals/{proposal}/change-status',
            [Clients\ProposalController::class, 'changeStatus']
        )->name('clients.proposals.change-status');
        Route::get('proposals/{proposal}/pdf', [Clients\ProposalController::class, 'covertToPdf'])->name('clients.proposal.pdf');
    });

    // Contracts routes
    Route::middleware('permission:contact_contracts')->group(function () {
        Route::get('contracts', [Clients\ContractController::class, 'index'])->name('clients.contracts.index');
        Route::get('contracts/{contract}/view-as-customer', [Clients\ContractController::class, 'viewAsCustomer'])
            ->name('clients.contracts.view-as-customer');
        Route::get(
            'contracts/{contract}/pdf',
            [Clients\ContractController::class, 'convertToPdf']
        )->name('clients.contracts.pdf');
        Route::get(
            'contracts-summary',
            [Clients\ContractController::class, 'contractSummary']
        )->name('contracts.contract-summary');
    });

    // Estimates routes
    Route::middleware('permission:contact_estimates')->group(function () {
        Route::get('estimates', [Clients\EstimateController::class, 'index'])->name('clients.estimates.index');
        Route::get('estimates/{estimate}/view-as-customer', [Clients\EstimateController::class, 'viewAsCustomer'])
            ->name('clients.estimates.view-as-customer');
        Route::get('estimates/{estimate}/pdf', [Clients\EstimateController::class, 'convertToPDF'])->name('clients.estimate.pdf');
        Route::post('estimates/{estimate}/change-status', [Clients\EstimateController::class, 'changeStatus'])
            ->name('clients.estimates.change-status');
    });

    // Announcements routes
    Route::get('announcements', [Clients\AnnouncementController::class, 'index'])->name('clients.announcements.index');
    Route::get(
        'announcements/{announcement}',
        [Clients\AnnouncementController::class, 'show']
    )->name('clients.announcements.show');

    // Company Details Routes
    Route::get(
        'company-details',
        [Clients\CompanyController::class, 'companyDetails']
    )->name('clients.company-details');
    Route::put('company-details/{customer}', [Clients\CompanyController::class, 'update'])->name('clients.update');

    // Profile routes
    Route::post('change-password', [Clients\UserController::class, 'changePassword'])->name('clients.change.password');
    Route::get('profile', [Clients\UserController::class, 'editProfile'])->name('clients.profile');
    Route::post('update-profile', [Clients\UserController::class, 'updateProfile'])->name('clients.update.profile');
    Route::post('change-language', [Clients\UserController::class, 'changeLanguage'])->name('clients.change.language');

    //Header Client Notification
    Route::get(
        'get-notifications',
        [Clients\DashboardController::class, 'getNotifications']
    )->name('client.notifications.index');
    Route::post(
        'notification/{notification}/read',
        [Clients\DashboardController::class, 'readNotification']
    )->name('client.notifications.read');
    Route::post(
        'read-all-notification',
        [Clients\DashboardController::class, 'readAllNotification']
    )->name('client.notifications.read.all');

    // Ticket routes
    Route::get('tickets', [Clients\TicketController::class, 'index'])->name('client.tickets.index');
    Route::get('tickets/create', [Clients\TicketController::class, 'create'])->name('client.tickets.create');
    Route::post('tickets', [Clients\TicketController::class, 'store'])->name('client.tickets.store');
    Route::get('tickets/{ticket}', [Clients\TicketController::class, 'show'])->name('client.tickets.show');
    Route::delete('tickets/{ticket}', [Clients\TicketController::class, 'destroy'])->name('client.tickets.destroy');
});

Route::middleware(['auth', 'xss', 'checkUserStatus'])->group(function () {
    // ticket reply routes
    Route::post('ticket-reply', [TicketReplyController::class, 'store'])->name('ticket.reply.store');
    Route::get('ticket-reply/{ticket}/edit', [TicketReplyController::class, 'edit'])->name('ticket.reply.edit');
    Route::put('ticket-reply/{ticket}', [TicketReplyController::class, 'update'])->name('ticket.reply.update');
    Route::delete('ticket-reply/{ticket}', [TicketReplyController::class, 'destroy'])->name('ticket.reply.destroy');
});

Route::get('article-search', function () {
    return view('articles.search');
});

require __DIR__ . '/upgrade.php';
