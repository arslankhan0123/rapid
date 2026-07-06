<?php
// Map quarters to their respective months
$quarterMonths = [
    'q1' => ['Jan', 'Feb', 'Mar'],
    'q2' => ['Apr', 'May', 'Jun'],
    'q3' => ['Jul', 'Aug', 'Sep'],
    'q4' => ['Oct', 'Nov', 'Dec'],
];

// Get the months for the given period
$months = $quarterMonths[$report->period] ?? [];

// Helper function to format the month name and year
function getMonthNameYear($date)
{
    return \Carbon\Carbon::parse($date)->format('F Y');
}

// Group data by month
$groupedData = [];

foreach ($data['invoices'] as $invoice) {
    $month = getMonthNameYear($invoice->created_at);
    $groupedData[$month]['invoices'][] = $invoice;
}

foreach ($data['creditNotes'] as $creditNote) {
    $month = getMonthNameYear($creditNote->created_at);
    $groupedData[$month]['creditNotes'][] = $creditNote;
}

foreach ($data['expenses'] as $expense) {
    $month = getMonthNameYear($expense->created_at);
    $groupedData[$month]['expenses'][] = $expense;
}

?>
@extends('layouts.app')
@section('title')
{{ __('messages.vat-reports.view') }}
@endsection
@section('page_css')
<link href="{{ asset('assets/css/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="{{ asset('assets/css/bs4-summernote/summernote-bs4.css') }}">
<style>
    input[type="checkbox"] {
        width: 20px;
        /* Adjust size */
        height: 20px;
        /* Adjust size */
        transform: scale(1);
        /* Scale up the checkbox */
        cursor: pointer;
        /* Makes it look clickable */
    }

    input[type="checkbox"]:checked {
        accent-color: rgba(53, 225, 255, 0.849);
        /* Custom color when checked (modern browsers) */
    }
</style>
@endsection
@section('content')
<section class="section">
    <div class="section-header item-align-right">
        <h1>Report : <span class="text-success ">{{ strtoupper($report->period ?? 'N/A') }}</span> <span
                class="text-success ">{{ strtoupper($report->year ?? 'N/A') }}</span>
            <span class="text-primary ">{{ $report->branch?->name ?? '' }}</span>
        </h1>
        <div class="section-header-breadcrumb float-right">
            <div class="card-header-action mr-3 select2-mobile-margin">
            </div>
        </div>
        <div class="float-right">
            <div class="row">
                <div class="col pr-0">
                    <div class="dropdown">
                        <button class="btn btn-primary dropdown-toggle form-btn" type="button" id="dropdownMenuButton"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                            style="line-height: 30px;">
                            Export
                        </button>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            <a class="dropdown-item"
                                href="{{ route('vat-reports.vat-history.download', $report->id) }}">CSV</a>
                            <a class="dropdown-item" id="btnExportPdf" href="#">Export PDF</a>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <a href="{{ route('vat-reports.index') }}"
                        class="btn btn-primary form-btn">{{ __('messages.assets.list') }}</i>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="section-body">
        <div class="card">
            <div class="card-body">

                <div class="alert alert-danger d-none" id="validationErrorsBox"></div>
                <div class="col-md-12">
                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                        <?php
                        $quarterMonths = [
                            'q1' => ['January', 'February', 'March'],
                            'q2' => ['April', 'May', 'June'],
                            'q3' => ['July', 'August', 'September'],
                            'q4' => ['October', 'November', 'December'],
                        ];

                        // Fetch the months for the current quarter dynamically
                        $months = $quarterMonths[$report->period] ?? [];

                        ?>
                        <?php foreach ($months as $index => $month): ?>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link <?= $index === 0 ? 'active' : '' ?>"
                                    id="<?= strtolower($month) ?>-tab" data-toggle="tab"
                                    data-target="#<?= strtolower($month) ?>" type="button" role="tab"
                                    aria-controls="<?= strtolower($month) ?>"
                                    aria-selected="<?= $index === 0 ? 'true' : 'false' ?>">
                                    {{ \Carbon\Carbon::createFromFormat('F', $month)->format('M') }}
                                </button>
                            </li>
                        <?php endforeach; ?>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="all-tab" data-toggle="tab" data-target="#all" type="button"
                                role="tab" aria-controls="all" aria-selected="false">
                                All
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content" id="myTabContent">
                        <?php foreach ($months as $index => $month): ?>
                            <div class="tab-pane fade <?= $index === 0 ? 'show active' : '' ?>"
                                id="<?= strtolower($month) ?>" role="tabpanel"
                                aria-labelledby="<?= strtolower($month) ?>-tab">
                                <?php
                                $monthDataExpense = $data['expenses']->filter(function ($invoice) use ($month) {
                                    return \Carbon\Carbon::parse($invoice->created_at)->format('F') === $month;
                                });
                                // Check if there's data for this month, otherwise display "No data"
                                $monthData = $data['invoices']->filter(function ($invoice) use ($month) {
                                    return \Carbon\Carbon::parse($invoice->created_at)->format('F') === $month;
                                });
                                $monthCreditNote = $data['creditNotes']->filter(function ($invoice) use ($month) {
                                    return \Carbon\Carbon::parse($invoice->created_at)->format('F') === $month;
                                });
                                $monthPurchaseInvoices = $data['purchaseInvoices']->filter(function ($purchase) use ($month) {
                                    return \Carbon\Carbon::parse($purchase->created_at)->format('F') === $month;
                                });
                                $monthPurchaseReturns = $data['purchaseReturns']->filter(function ($return) use ($month) {
                                    return \Carbon\Carbon::parse($return->created_at)->format('F') === $month;
                                });
                                ?>

                                <table class="table table-bordered tableVatReport">
                                    <thead>
                                        <tr>
                                            <th>Doc No</th>
                                            <th>Doc Date</th>
                                            <th>Doc Type</th>
                                            <th>Vat Number</th>
                                            <th class="text-right">Excluding VAT</th>
                                            <th class="text-right">VAT Amount</th>
                                            <th class="text-right">Including VAT</th>
                                            <th class="text-right">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($monthDataExpense as $invoice): ?>
                                            <tr>
                                                <td><?= $invoice->expense_number ?></td>
                                                <td><?= \Carbon\Carbon::parse($invoice->created_at)->format('d-m-Y') ?></td>
                                                <td>Expense</td>
                                                <td>{{ $invoice->supp_vat_number ?? '' }}</td>
                                                <td class="text-right"><?= number_format($invoice->amount, 2) ?>
                                                </td>
                                                <td class="text-right"><?= number_format($invoice->totalVatAmount, 2) ?></td>
                                                <td class="text-right">
                                                    <?= number_format($invoice->amount + $invoice->totalVatAmount, 2) ?>
                                                </td>
                                                <td class="text-right">
                                                    <a href="<?= route('expenses.show', $invoice->id) ?>" target="_blank"
                                                        class="btn btn-info">View</a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                        <?php foreach ($monthData as $invoice): ?>
                                            <tr>
                                                <td><?= $invoice->invoice_number ?></td>
                                                <td><?= \Carbon\Carbon::parse($invoice->created_at)->format('d-m-Y') ?></td>
                                                <td>Sales Invoice</td>
                                                <td></td>
                                                <td class="text-right"><?= number_format($invoice->excludingVatAmount, 2) ?>
                                                </td>
                                                <td class="text-right"><?= number_format($invoice->totalVatAmount, 2) ?></td>
                                                <td class="text-right"><?= number_format($invoice->includingVatAmount, 2) ?>
                                                </td>
                                                <td class="text-right">
                                                    <a href="<?= route('invoices.show', $invoice->id) ?>" target="_blank"
                                                        class="btn btn-info">View</a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>

                                        <?php foreach ($monthCreditNote as $invoice): ?>
                                            <tr>
                                                <td><?= $invoice->invoice_number ?></td>
                                                <td><?= \Carbon\Carbon::parse($invoice->created_at)->format('d-m-Y') ?></td>
                                                <td>Credit Note</td>
                                                <td></td>
                                                <td class="text-right"><?= number_format($invoice->excludingVatAmount, 2) ?>
                                                </td>
                                                <td class="text-right"><?= number_format($invoice->totalVatAmount, 2) ?></td>
                                                <td class="text-right"><?= number_format($invoice->includingVatAmount, 2) ?>
                                                </td>
                                                <td class="text-right">
                                                    <a href="<?= route('invoices.show', $invoice->id) ?>" target="_blank"
                                                        class="btn btn-info">View</a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>

                                        <?php foreach ($monthPurchaseInvoices as $purchase): ?>
                                            <tr>
                                                <td><?= $purchase->estimate_number ?></td>
                                                <td><?= \Carbon\Carbon::parse($purchase->created_at)->format('d-m-Y') ?></td>
                                                <td>Purchase Invoice</td>
                                                <td></td>
                                                <td class="text-right"><?= number_format($purchase->excludingVatAmount, 2) ?>
                                                </td>
                                                <td class="text-right"><?= number_format($purchase->totalVatAmount, 2) ?></td>
                                                <td class="text-right"><?= number_format($purchase->includingVatAmount, 2) ?>
                                                </td>
                                                <td class="text-right">
                                                    <a href="<?= route('purchase-invoices.view', $purchase->id) ?>"
                                                        target="_blank" class="btn btn-info">View</a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>

                                        <?php foreach ($monthPurchaseReturns as $purchaseReturn): ?>
                                            <tr>
                                                <td><?= $purchaseReturn->return_number ?></td>
                                                <td><?= \Carbon\Carbon::parse($purchaseReturn->created_at)->format('d-m-Y') ?>
                                                </td>
                                                <td>Purchase Return</td>
                                                <td></td>
                                                <td class="text-right">
                                                    <?= number_format($purchaseReturn->excludingVatAmount, 2) ?></td>
                                                <td class="text-right"><?= number_format($purchaseReturn->totalVatAmount, 2) ?>
                                                </td>
                                                <td class="text-right">
                                                    <?= number_format($purchaseReturn->includingVatAmount, 2) ?></td>
                                                <td class="text-right">
                                                    <a href="<?= route('purchase-returns.view', $purchaseReturn->id) ?>"
                                                        target="_blank" class="btn btn-info">View</a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th class="text-right">Total</th>
                                            <th class="text-right pr-2"></th>
                                            <th class="text-right pr-2"></th>
                                            <th class="text-right pr-2"></th>
                                            <th></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        <?php endforeach; ?>

                        {{-- tab all below --}}
                        <!-- Tab for displaying all data -->
                        <div class="tab-pane fade" id="all" role="tabpanel" aria-labelledby="all-tab">
                            <table class="table table-bordered " id="allDataTable">
                                <thead>
                                    <tr>
                                        <th>
                                            <input type="checkbox" id="markAllCheckbox">
                                        </th>
                                        <th>Doc No</th>
                                        <th>Doc Date</th>
                                        <th>Doc Type</th>
                                        <th>Vat Number</th>
                                        <th class="text-right">Excluding VAT</th>
                                        <th class="text-right">VAT Amount</th>
                                        <th class="text-right">Including VAT</th>
                                        <th class="text-right">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                    $totalExcludingVat = 0;
                                    $totalVatAmount = 0;
                                    $totalIncludingVat = 0;
                                    @endphp
                                    <?php foreach ($data['invoices'] as $invoice): ?>
                                        @php
                                        $totalExcludingVat += $invoice->excludingVatAmount;
                                        $totalIncludingVat += $invoice->includingVatAmount;
                                        $totalVatAmount += $invoice->totalVatAmount;
                                        @endphp
                                        <tr>
                                            <td>
                                                <input type="checkbox" class="markItemCheckbox"
                                                    name='invoice[{{ $invoice->id }}]' value="{{ $invoice->id }}">
                                            </td>
                                            <td><?= $invoice->invoice_number ?></td>
                                            <td><?= \Carbon\Carbon::parse($invoice->created_at)->format('d-m-Y') ?></td>
                                            <td>Sales Invoice</td>
                                            <td></td>
                                            <td class="text-right"><?= number_format($invoice->excludingVatAmount, 2) ?>
                                            </td>
                                            <td class="text-right"><?= number_format($invoice->totalVatAmount, 2) ?></td>
                                            <td class="text-right"><?= number_format($invoice->includingVatAmount, 2) ?>
                                            </td>
                                            <td class="text-right">
                                                <a href="<?= route('invoices.show', $invoice->id) ?>" target="_blank"
                                                    class="btn btn-info">View</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>

                                    <?php foreach ($data['creditNotes'] as $return): ?>
                                        @php
                                        $totalExcludingVat += $return->excludingVatAmount;
                                        $totalIncludingVat += $return->includingVatAmount;
                                        $totalVatAmount += $return->totalVatAmount;
                                        @endphp
                                        <tr>
                                            <td>
                                                <input type="checkbox" class="markItemCheckbox"
                                                    name='return[{{ $return->id }}]' value="{{ $return->id }}">
                                            </td>
                                            <td><?= $return->credit_note_number ?></td>
                                            <td><?= \Carbon\Carbon::parse($return->created_at)->format('d-m-Y') ?></td>
                                            <td>Return </td>
                                            <td></td>
                                            <td class="text-right"><?= number_format($return->excludingVatAmount, 2) ?>
                                            </td>
                                            <td class="text-right"><?= number_format($return->totalVatAmount, 2) ?></td>
                                            <td class="text-right"><?= number_format($return->includingVatAmount, 2) ?>
                                            </td>
                                            <td class="text-right">
                                                <a href="<?= route('credit-notes.show', $return->id) ?>" target="_blank"
                                                    class="btn btn-info">View</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>

                                    <?php foreach ($data['expenses'] as $expense): ?>
                                        @php
                                        $totalExcludingVat += $expense->amount;
                                        $totalIncludingVat += $expense->amount+$expense->totalVatAmount;
                                        $totalVatAmount += $expense->totalVatAmount;
                                        @endphp
                                        <tr>
                                            <td>
                                                <input type="checkbox" class="markItemCheckbox"
                                                    name='expense[{{ $expense->id }}]' value="{{ $expense->id }}">
                                            </td>
                                            <td><?= $expense->expense_number ?></td>
                                            <td><?= \Carbon\Carbon::parse($expense->created_at)->format('d-m-Y') ?></td>
                                            <td>Expense </td>
                                            <td>{{ $expense->supp_vat_number ?? '' }}</td>
                                            <td class="text-right">{{ number_format($expense->amount, 2) }}
                                            </td>
                                            <td class="text-right"><?= number_format($expense->totalVatAmount, 2) ?></td>
                                            <td class="text-right">
                                                <?= number_format($expense->amount + $expense->totalVatAmount, 2) ?>
                                            </td>
                                            <td class="text-right">
                                                <a href="<?= route('expenses.show', $expense->id) ?>" target="_blank"
                                                    class="btn btn-info">View</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>

                                    <?php foreach ($data['purchaseInvoices'] as $purchase): ?>
                                        @php
                                        $totalExcludingVat += $purchase->excludingVatAmount;
                                        $totalIncludingVat += $purchase->includingVatAmount;
                                        $totalVatAmount += $purchase->totalVatAmount;
                                        @endphp
                                        <tr>
                                            <td>
                                                <input type="checkbox" class="markItemCheckbox"
                                                    name='purchaseInvoice[{{ $purchase->id }}]' value="{{ $purchase->id }}">
                                            </td>
                                            <td><?= $purchase->estimate_number ?></td>
                                            <td><?= \Carbon\Carbon::parse($purchase->created_at)->format('d-m-Y') ?></td>
                                            <td>Purchase Invoice</td>
                                            <td></td>
                                            <td class="text-right"><?= number_format($purchase->excludingVatAmount, 2) ?>
                                            </td>
                                            <td class="text-right"><?= number_format($purchase->totalVatAmount, 2) ?></td>
                                            <td class="text-right"><?= number_format($purchase->includingVatAmount, 2) ?>
                                            </td>
                                            <td class="text-right">
                                                <a href="<?= route('purchase-invoices.view', $purchase->id) ?>"
                                                    target="_blank" class="btn btn-info">View</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>

                                    <?php foreach ($data['purchaseReturns'] as $purchaseReturn): ?>
                                        @php
                                        $totalExcludingVat += $purchaseReturn->excludingVatAmount;
                                        $totalIncludingVat += $purchaseReturn->includingVatAmount;
                                        $totalVatAmount += $purchaseReturn->totalVatAmount;
                                        @endphp
                                        <tr>
                                            <td>
                                                <input type="checkbox" class="markItemCheckbox"
                                                    name='purchaseReturn[{{ $purchaseReturn->id }}]'
                                                    value="{{ $purchaseReturn->id }}">
                                            </td>
                                            <td><?= $purchaseReturn->return_number ?></td>
                                            <td><?= \Carbon\Carbon::parse($purchaseReturn->created_at)->format('d-m-Y') ?>
                                            </td>
                                            <td>Purchase Return</td>
                                            <td></td>
                                            <td class="text-right">
                                                <?= number_format($purchaseReturn->excludingVatAmount, 2) ?></td>
                                            <td class="text-right"><?= number_format($purchaseReturn->totalVatAmount, 2) ?>
                                            </td>
                                            <td class="text-right">
                                                <?= number_format($purchaseReturn->includingVatAmount, 2) ?></td>
                                            <td class="text-right">
                                                <a href="<?= route('purchase-returns.view', $purchaseReturn->id) ?>"
                                                    target="_blank" class="btn btn-info">View</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td class="text-right" colspan="5"><strong>Total</strong></td>
                                        <td class="text-right">
                                            <strong>{{ number_format($totalExcludingVat ?? 0, 2) }}</strong>
                                        </td>
                                        <td class="text-right">
                                            <strong>{{ number_format($totalVatAmount ?? 0, 2) }}</strong>
                                        </td>
                                        <td class="text-right">
                                            <strong>{{ number_format($totalIncludingVat ?? 0, 2) }}</strong>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
@section('page_scripts')
<script src="{{ asset('assets/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ mix('assets/js/custom/custom-datatable.js') }}"></script>
<script src="{{ mix('assets/js/bs4-summernote/summernote-bs4.js') }}"></script>
<script src="{{ mix('assets/js/select2.min.js') }}"></script>
<script>
    $(document).ready(function() {
        $('#invoiceTable').DataTable({
            // Optional: Add options to customize the DataTable
            paging: true, // Enable pagination
            searching: true, // Enable search box
            ordering: false, // Enable column ordering
            info: true // Show information about the number of entries
        });

        // Handle 'Mark All' checkbox change
        $('#markAllCheckbox').on('change', function() {
            // Set all row checkboxes to match the state of the 'Mark All' checkbox
            $('.markItemCheckbox').prop('checked', this.checked);
        });

        // Optional: Update 'Mark All' checkbox state when individual checkboxes are toggled
        $(document).on('change', '.markItemCheckbox', function() {
            const allChecked = $('.markItemCheckbox').length === $('.markItemCheckbox:checked').length;
            $('#markAllCheckbox').prop('checked', allChecked);
        });

        $("#btnExportPdf").click(function() {
            let selectedInvoices = [];
            let selectedReturns = [];
            let selectedExpenses = [];
            let selectedPosOrders = [];
            let selectedPosRefunds = [];
            let selectedPurchaseInvoices = [];
            let selectedPurchaseReturns = [];

            // Iterate through all checked checkboxes
            $(".markItemCheckbox:checked").each(function() {
                let nameAttr = $(this).attr('name'); // Get the 'name' attribute

                // Check if it belongs to 'invoice' or 'return' and collect accordingly
                if (nameAttr.startsWith('invoice[')) {
                    selectedInvoices.push($(this).val()); // Collect invoice IDs
                } else if (nameAttr.startsWith('return[')) {
                    selectedReturns.push($(this).val()); // Collect return IDs
                } else if (nameAttr.startsWith('expense[')) {
                    selectedExpenses.push($(this).val()); // Collect return IDs
                } else if (nameAttr.startsWith('posOrder[')) {
                    selectedPosOrders.push($(this).val()); // Collect return IDs
                } else if (nameAttr.startsWith('posRefund[')) {
                    selectedPosRefunds.push($(this).val()); // Collect return IDs
                } else if (nameAttr.startsWith('purchaseInvoice[')) {
                    selectedPurchaseInvoices.push($(this).val()); // Collect purchase invoice IDs
                } else if (nameAttr.startsWith('purchaseReturn[')) {
                    selectedPurchaseReturns.push($(this).val()); // Collect purchase return IDs
                }
            });

            if (selectedInvoices.length === 0 && selectedReturns.length === 0 && selectedExpenses
                .length === 0 && selectedPosOrders.length === 0 && selectedPosRefunds.length === 0 &&
                selectedPurchaseInvoices.length === 0 && selectedPurchaseReturns.length === 0) {
                displayErrorMessage("Please select at least one item to export.");
                return;
            }
            startLoader();
            displaySuccessMessage("Please Wait preparing files");
            let downloadUrl = route('vat-reports.download-zip');
            $.ajax({
                url: downloadUrl, // Your route for downloading the ZIP
                method: 'POST',
                data: {
                    invoiceIds: selectedInvoices,
                    returnIds: selectedReturns,
                    expenseIds: selectedExpenses,
                    posOrderIds: selectedPosOrders,
                    posRefundIds: selectedPosRefunds,
                    purchaseInvoiceIds: selectedPurchaseInvoices,
                    purchaseReturnIds: selectedPurchaseReturns,
                },
                xhrFields: {
                    responseType: 'blob' // Expect a file in the response
                },
                success: function(response) {
                    stopLoader();
                    // Create a download link for the ZIP file
                    const url = window.URL.createObjectURL(new Blob([response]));
                    const link = document.createElement('a');
                    link.href = url;
                    const downloadFileName =
                        'vat-reports_{{ strtoupper($report->period ?? '
                    N / A ') }}.zip';

                    link.setAttribute('download', downloadFileName); // Set the filename
                    document.body.appendChild(link);
                    link.click();
                },
                error: function(xhr, status, error) {
                    stopLoader();
                    displayErrorMessage(error);
                }
            });

        });
    });

    $('.tableVatReport').DataTable({
        responsive: true,
        paging: true,
        searching: true,
        ordering: true,
        info: true,
        footerCallback: function(row, data, start, end, display) {
            const api = this.api();

            // Helper function to parse float from string
            const parseValue = (i) => {
                return typeof i === 'string' ?
                    parseFloat(i.replace(/[,]/g, '')) || 0 :
                    typeof i === 'number' ?
                    i :
                    0;
            };

            // Indices of columns (0-based): Excluding VAT = 4, VAT Amount = 5, Including VAT = 6
            const columnsToTotal = [4, 5, 6];

            columnsToTotal.forEach(function(colIdx) {
                let total = api
                    .column(colIdx, {
                        page: 'current'
                    })
                    .data()
                    .reduce(function(a, b) {
                        return parseValue(a) + parseValue(b);
                    }, 0);

                // Format as currency with commas
                const formatted = total.toLocaleString(undefined, {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });

                // Update footer
                $(api.column(colIdx).footer()).html(formatted);
            });
        }
    });
</script>
@endsection