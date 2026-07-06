<?php
use App\Helpers\VatReportHelper;

// Remove the function declaration from here
// function getMonthNameYear($date)
// {
//     return \Carbon\Carbon::parse($date)->format('F Y');
// }

// Use helper for quarter months
$quarterMonths = VatReportHelper::getQuarterMonths();
$quarterShortMonths = VatReportHelper::getQuarterShortMonths();

// Get the months for the given period
$months = $quarterMonths[$report->period] ?? [];
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
                @if ($selectedBranch)
                    <span class="text-primary ">{{ $currentBranch->name ?? '' }}</span>
                @else
                    <span class="text-primary ">All Branches</span>
                @endif
            </h1>
            <div class="section-header-breadcrumb float-right">
                <div class="card-header-action mr-3 select2-mobile-margin">
                    {{-- Branch Filter Dropdown --}}
                    {{ Form::select('branches', ['' => 'All Branches'] + $branches->toArray(), $selectedBranch, [
                        'id' => 'branch-filter',
                        'class' => 'form-control select2',
                        'style' => 'width: 200px;',
                    ]) }}
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
                                @php
                                    // Check if we have a specific report ID or aggregated view
                                    if ($report->id) {
                                        // Specific report view
                                        $downloadRoute = route('vat-reports.vat-history.download', $report->id);
                                    } else {
                                        // Aggregated quarterly view
                                        $downloadRoute = route('vat-reports.vat-history.download.quarterly', [
                                            'year' => $selectedYear,
                                            'period' => $selectedPeriod,
                                        ]);
                                    }

                                    // Add branch parameter if selected
                                    if ($selectedBranch) {
                                        $downloadRoute .= '?branch=' . $selectedBranch;
                                    }
                                @endphp
                                <a class="dropdown-item" href="{{ $downloadRoute }}">CSV</a>
                                {{-- <a class="dropdown-item" id="btnExportPdf" href="#">Export PDF</a> --}}
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
                                $monthDataExpense = $data['expenses']->filter(function ($expense) use ($month) {
                                    return \Carbon\Carbon::parse($expense->created_at)->format('F') === $month;
                                });
                                
                                $monthData = $data['invoices']->filter(function ($invoice) use ($month) {
                                    return \Carbon\Carbon::parse($invoice->created_at)->format('F') === $month;
                                });
                                
                                $monthCreditNote = $data['creditNotes']->filter(function ($creditNote) use ($month) {
                                    return \Carbon\Carbon::parse($creditNote->created_at)->format('F') === $month;
                                });
                                
                                $monthPurchaseInvoices = $data['purchaseInvoices']->filter(function ($purchase) use ($month) {
                                    return \Carbon\Carbon::parse($purchase->created_at)->format('F') === $month;
                                });
                                
                                $monthPurchaseReturns = $data['purchaseReturns']->filter(function ($return) use ($month) {
                                    return \Carbon\Carbon::parse($return->created_at)->format('F') === $month;
                                });
                                
                                $monthManualSales = $data['manualSales']->filter(function ($manualSale) use ($month) {
                                    return \Carbon\Carbon::parse($manualSale->created_at)->format('F') === $month;
                                });
                                ?>

                                @if (
                                    $monthDataExpense->isEmpty() &&
                                        $monthData->isEmpty() &&
                                        $monthCreditNote->isEmpty() &&
                                        $monthPurchaseInvoices->isEmpty() &&
                                        $monthPurchaseReturns->isEmpty() &&
                                        $monthManualSales->isEmpty())
                                    <div class="alert alert-info">
                                        No data found for {{ $month }}
                                        @if ($selectedBranch)
                                            in {{ $currentBranch->name ?? 'selected branch' }}
                                        @endif
                                    </div>
                                @else
                                    <!-- In the monthly tab tables -->
                                    <table class="table table-bordered tableVatReport">
                                        <thead>
                                            <tr>
                                                <th>Branch</th> <!-- NEW COLUMN -->
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
                                            <?php foreach ($monthDataExpense as $expense): ?>
                                            <tr>
                                                <td><?= $expense->branch ? $expense->branch->name : 'N/A' ?></td>
                                                <!-- BRANCH COLUMN -->
                                                <td><?= $expense->expense_number ?></td>
                                                <td><?= \Carbon\Carbon::parse($expense->created_at)->format('d-m-Y') ?></td>
                                                <td>Expense</td>
                                                <td>{{ $expense->supp_vat_number ?? '' }}</td>
                                                <td class="text-right"><?= number_format($expense->amount, 2) ?></td>
                                                <td class="text-right"><?= number_format($expense->totalVatAmount, 2) ?>
                                                </td>
                                                <td class="text-right">
                                                    <?= number_format($expense->amount + $expense->totalVatAmount, 2) ?>
                                                </td>
                                                <td class="text-right">
                                                    <a href="<?= route('expenses.show', $expense->id) ?>" target="_blank"
                                                        class="btn btn-info">View</a>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>

                                            <?php foreach ($monthManualSales as $manualSale): ?>
                                            <tr>
                                                <td><?= $manualSale->branch ? $manualSale->branch->name : 'N/A' ?></td>
                                                <!-- BRANCH COLUMN -->
                                                <td><?= $manualSale->manual_sale_number ?? 'MS-' . $manualSale->id ?></td>
                                                <td><?= \Carbon\Carbon::parse($manualSale->created_at)->format('d-m-Y') ?>
                                                </td>
                                                <td>Manual Sale</td>
                                                <td></td>
                                                <td class="text-right">
                                                    <?= number_format($manualSale->excludingVatAmount, 2) ?></td>
                                                <td class="text-right"><?= number_format($manualSale->totalVatAmount, 2) ?>
                                                </td>
                                                <td class="text-right">
                                                    <?= number_format($manualSale->includingVatAmount, 2) ?></td>
                                                <td class="text-right">
                                                    @if ($manualSale->id)
                                                        <a href="<?= route('manual-sales.show', $manualSale->id) ?>"
                                                            target="_blank" class="btn btn-info">View</a>
                                                    @endif
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>

                                            <?php foreach ($monthData as $invoice): ?>
                                            <tr>
                                                <td><?= $invoice->branch ? $invoice->branch->name : 'N/A' ?></td>
                                                <!-- BRANCH COLUMN -->
                                                <td><?= $invoice->invoice_number ?></td>
                                                <td><?= \Carbon\Carbon::parse($invoice->created_at)->format('d-m-Y') ?></td>
                                                <td>Sales Invoice</td>
                                                <td></td>
                                                <td class="text-right"><?= number_format($invoice->excludingVatAmount, 2) ?>
                                                </td>
                                                <td class="text-right"><?= number_format($invoice->totalVatAmount, 2) ?>
                                                </td>
                                                <td class="text-right"><?= number_format($invoice->includingVatAmount, 2) ?>
                                                </td>
                                                <td class="text-right">
                                                    <a href="<?= route('invoices.show', $invoice->id) ?>" target="_blank"
                                                        class="btn btn-info">View</a>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>

                                            <!-- Add similar branch column for all other record types... -->
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th></th> <!-- Empty for branch column -->
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
                                    {{-- <table class="table table-bordered tableVatReport">
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
                                            <?php foreach ($monthDataExpense as $expense): ?>
                                            <tr>
                                                <td><?= $expense->expense_number ?></td>
                                                <td><?= \Carbon\Carbon::parse($expense->created_at)->format('d-m-Y') ?></td>
                                                <td>Expense</td>
                                                <td>{{ $expense->supp_vat_number ?? '' }}</td>
                                                <td class="text-right"><?= number_format($expense->amount, 2) ?></td>
                                                <td class="text-right"><?= number_format($expense->totalVatAmount, 2) ?>
                                                </td>
                                                <td class="text-right">
                                                    <?= number_format($expense->amount + $expense->totalVatAmount, 2) ?>
                                                </td>
                                                <td class="text-right">
                                                    <a href="<?= route('expenses.show', $expense->id) ?>" target="_blank"
                                                        class="btn btn-info">View</a>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>

                                            <?php foreach ($monthManualSales as $manualSale): ?>
                                            <tr>
                                                <td><?= $manualSale->manual_sale_number ?? 'MS-' . $manualSale->id ?></td>
                                                <td><?= \Carbon\Carbon::parse($manualSale->created_at)->format('d-m-Y') ?>
                                                </td>
                                                <td>Manual Sale</td>
                                                <td></td>
                                                <td class="text-right">
                                                    <?= number_format($manualSale->excludingVatAmount, 2) ?></td>
                                                <td class="text-right"><?= number_format($manualSale->totalVatAmount, 2) ?>
                                                </td>
                                                <td class="text-right">
                                                    <?= number_format($manualSale->includingVatAmount, 2) ?></td>
                                                <td class="text-right">
                                                    @if ($manualSale->id)
                                                        <a href="<?= route('manual-sales.show', $manualSale->id) ?>"
                                                            target="_blank" class="btn btn-info">View</a>
                                                    @endif
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
                                                <td class="text-right"><?= number_format($invoice->totalVatAmount, 2) ?>
                                                </td>
                                                <td class="text-right"><?= number_format($invoice->includingVatAmount, 2) ?>
                                                </td>
                                                <td class="text-right">
                                                    <a href="<?= route('invoices.show', $invoice->id) ?>" target="_blank"
                                                        class="btn btn-info">View</a>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>

                                            <?php foreach ($monthCreditNote as $creditNote): ?>
                                            <tr>
                                                <td><?= $creditNote->invoice_number ?></td>
                                                <td><?= \Carbon\Carbon::parse($creditNote->created_at)->format('d-m-Y') ?>
                                                </td>
                                                <td>Credit Note</td>
                                                <td></td>
                                                <td class="text-right">
                                                    <?= number_format($creditNote->excludingVatAmount, 2) ?></td>
                                                <td class="text-right"><?= number_format($creditNote->totalVatAmount, 2) ?>
                                                </td>
                                                <td class="text-right">
                                                    <?= number_format($creditNote->includingVatAmount, 2) ?></td>
                                                <td class="text-right">
                                                    <a href="<?= route('invoices.show', $creditNote->id) ?>" target="_blank"
                                                        class="btn btn-info">View</a>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>

                                            <?php foreach ($monthPurchaseInvoices as $purchase): ?>
                                            <tr>
                                                <td><?= $purchase->estimate_number ?></td>
                                                <td><?= \Carbon\Carbon::parse($purchase->created_at)->format('d-m-Y') ?>
                                                </td>
                                                <td>Purchase Invoice</td>
                                                <td></td>
                                                <td class="text-right">
                                                    <?= number_format($purchase->excludingVatAmount, 2) ?></td>
                                                <td class="text-right"><?= number_format($purchase->totalVatAmount, 2) ?>
                                                </td>
                                                <td class="text-right">
                                                    <?= number_format($purchase->includingVatAmount, 2) ?></td>
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
                                                <td class="text-right">
                                                    <?= number_format($purchaseReturn->totalVatAmount, 2) ?></td>
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
                                    </table> --}}
                                @endif
                            </div>
                            <?php endforeach; ?>

                            {{-- tab all below --}}
                            <!-- Tab for displaying all data -->
                            <div class="tab-pane fade" id="all" role="tabpanel" aria-labelledby="all-tab">
                                @if (
                                    $data['invoices']->isEmpty() &&
                                        $data['creditNotes']->isEmpty() &&
                                        $data['expenses']->isEmpty() &&
                                        $data['purchaseInvoices']->isEmpty() &&
                                        $data['purchaseReturns']->isEmpty() &&
                                        $data['manualSales']->isEmpty())
                                    <div class="alert alert-info">
                                        No data found for {{ strtoupper($report->period) }} {{ $report->year }}
                                        @if ($selectedBranch)
                                            in {{ $currentBranch->name ?? 'selected branch' }}
                                        @endif
                                    </div>
                                @else
                                    <!-- In the "All" tab table -->
                                    <table class="table table-bordered " id="allDataTable">
                                        <thead>
                                            <tr>
                                                <th>
                                                    <input type="checkbox" id="markAllCheckbox">
                                                </th>
                                                <th>Branch</th> <!-- NEW COLUMN -->
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

                                            <?php foreach ($data['manualSales'] as $manualSale): ?>
                                            @php
                                                $totalExcludingVat += $manualSale->excludingVatAmount;
                                                $totalIncludingVat += $manualSale->includingVatAmount;
                                                $totalVatAmount += $manualSale->totalVatAmount;
                                            @endphp
                                            <tr>
                                                <td>
                                                    <input type="checkbox" class="markItemCheckbox"
                                                        name='manualSale[{{ $manualSale->id }}]'
                                                        value="{{ $manualSale->id }}">
                                                </td>
                                                <td><?= $manualSale->branch ? $manualSale->branch->name : 'N/A' ?></td>
                                                <!-- BRANCH COLUMN -->
                                                <td><?= $manualSale->manual_sale_number ?? 'MS-' . $manualSale->id ?></td>
                                                <td><?= \Carbon\Carbon::parse($manualSale->created_at)->format('d-m-Y') ?>
                                                </td>
                                                <td>Manual Sale</td>
                                                <td></td>
                                                <td class="text-right">
                                                    <?= number_format($manualSale->excludingVatAmount, 2) ?></td>
                                                <td class="text-right"><?= number_format($manualSale->totalVatAmount, 2) ?>
                                                </td>
                                                <td class="text-right">
                                                    <?= number_format($manualSale->includingVatAmount, 2) ?></td>
                                                <td class="text-right">
                                                    @if ($manualSale->id)
                                                        <a href="<?= route('manual-sales.show', $manualSale->id) ?>"
                                                            target="_blank" class="btn btn-info">View</a>
                                                    @endif
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>

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
                                                <td><?= $invoice->branch ? $invoice->branch->name : 'N/A' ?></td>
                                                <!-- BRANCH COLUMN -->
                                                <td><?= $invoice->invoice_number ?></td>
                                                <td><?= \Carbon\Carbon::parse($invoice->created_at)->format('d-m-Y') ?></td>
                                                <td>Sales Invoice</td>
                                                <td></td>
                                                <td class="text-right">
                                                    <?= number_format($invoice->excludingVatAmount, 2) ?></td>
                                                <td class="text-right"><?= number_format($invoice->totalVatAmount, 2) ?>
                                                </td>
                                                <td class="text-right">
                                                    <?= number_format($invoice->includingVatAmount, 2) ?></td>
                                                <td class="text-right">
                                                    <a href="<?= route('invoices.show', $invoice->id) ?>" target="_blank"
                                                        class="btn btn-info">View</a>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>

                                            <!-- Add similar for credit notes, expenses, purchase invoices, purchase returns... -->
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td class="text-right" colspan="6"><strong>Total</strong></td>
                                                <!-- Changed from colspan="5" to "6" -->
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
                                    {{-- <table class="table table-bordered " id="allDataTable">
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

                                            <?php foreach ($data['manualSales'] as $manualSale): ?>
                                            @php
                                                $totalExcludingVat += $manualSale->excludingVatAmount;
                                                $totalIncludingVat += $manualSale->includingVatAmount;
                                                $totalVatAmount += $manualSale->totalVatAmount;
                                            @endphp
                                            <tr>
                                                <td>
                                                    <input type="checkbox" class="markItemCheckbox"
                                                        name='manualSale[{{ $manualSale->id }}]'
                                                        value="{{ $manualSale->id }}">
                                                </td>
                                                <td><?= $manualSale->manual_sale_number ?? 'MS-' . $manualSale->id ?></td>
                                                <td><?= \Carbon\Carbon::parse($manualSale->created_at)->format('d-m-Y') ?>
                                                </td>
                                                <td>Manual Sale</td>
                                                <td></td>
                                                <td class="text-right">
                                                    <?= number_format($manualSale->excludingVatAmount, 2) ?></td>
                                                <td class="text-right"><?= number_format($manualSale->totalVatAmount, 2) ?>
                                                </td>
                                                <td class="text-right">
                                                    <?= number_format($manualSale->includingVatAmount, 2) ?></td>
                                                <td class="text-right">
                                                    @if ($manualSale->id)
                                                        <a href="<?= route('manual-sales.show', $manualSale->id) ?>"
                                                            target="_blank" class="btn btn-info">View</a>
                                                    @endif
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>

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
                                                <td class="text-right">
                                                    <?= number_format($invoice->excludingVatAmount, 2) ?></td>
                                                <td class="text-right"><?= number_format($invoice->totalVatAmount, 2) ?>
                                                </td>
                                                <td class="text-right">
                                                    <?= number_format($invoice->includingVatAmount, 2) ?></td>
                                                <td class="text-right">
                                                    <a href="<?= route('invoices.show', $invoice->id) ?>" target="_blank"
                                                        class="btn btn-info">View</a>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>

                                            <?php foreach ($data['creditNotes'] as $creditNote): ?>
                                            @php
                                                $totalExcludingVat += $creditNote->excludingVatAmount;
                                                $totalIncludingVat += $creditNote->includingVatAmount;
                                                $totalVatAmount += $creditNote->totalVatAmount;
                                            @endphp
                                            <tr>
                                                <td>
                                                    <input type="checkbox" class="markItemCheckbox"
                                                        name='creditNote[{{ $creditNote->id }}]'
                                                        value="{{ $creditNote->id }}">
                                                </td>
                                                <td><?= $creditNote->invoice_number ?></td>
                                                <td><?= \Carbon\Carbon::parse($creditNote->created_at)->format('d-m-Y') ?>
                                                </td>
                                                <td>Credit Note</td>
                                                <td></td>
                                                <td class="text-right">
                                                    <?= number_format($creditNote->excludingVatAmount, 2) ?></td>
                                                <td class="text-right"><?= number_format($creditNote->totalVatAmount, 2) ?>
                                                </td>
                                                <td class="text-right">
                                                    <?= number_format($creditNote->includingVatAmount, 2) ?></td>
                                                <td class="text-right">
                                                    <a href="<?= route('invoices.show', $creditNote->id) ?>"
                                                        target="_blank" class="btn btn-info">View</a>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>

                                            <?php foreach ($data['expenses'] as $expense): ?>
                                            @php
                                                $totalExcludingVat += $expense->amount;
                                                $totalIncludingVat += $expense->amount + $expense->totalVatAmount;
                                                $totalVatAmount += $expense->totalVatAmount;
                                            @endphp
                                            <tr>
                                                <td>
                                                    <input type="checkbox" class="markItemCheckbox"
                                                        name='expense[{{ $expense->id }}]' value="{{ $expense->id }}">
                                                </td>
                                                <td><?= $expense->expense_number ?></td>
                                                <td><?= \Carbon\Carbon::parse($expense->created_at)->format('d-m-Y') ?></td>
                                                <td>Expense</td>
                                                <td>{{ $expense->supp_vat_number ?? '' }}</td>
                                                <td class="text-right">{{ number_format($expense->amount, 2) }}</td>
                                                <td class="text-right"><?= number_format($expense->totalVatAmount, 2) ?>
                                                </td>
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
                                                        name='purchaseInvoice[{{ $purchase->id }}]'
                                                        value="{{ $purchase->id }}">
                                                </td>
                                                <td><?= $purchase->estimate_number ?></td>
                                                <td><?= \Carbon\Carbon::parse($purchase->created_at)->format('d-m-Y') ?>
                                                </td>
                                                <td>Purchase Invoice</td>
                                                <td></td>
                                                <td class="text-right">
                                                    <?= number_format($purchase->excludingVatAmount, 2) ?></td>
                                                <td class="text-right"><?= number_format($purchase->totalVatAmount, 2) ?>
                                                </td>
                                                <td class="text-right">
                                                    <?= number_format($purchase->includingVatAmount, 2) ?></td>
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
                                                <td class="text-right">
                                                    <?= number_format($purchaseReturn->totalVatAmount, 2) ?></td>
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
                                    </table> --}}
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="alert alert-info">
            <strong>Showing data for:</strong>
            @if ($selectedBranch)
                {{ $currentBranch->name ?? 'Selected Branch' }}
            @else
                All Branches
                @php
                    // Get unique branches from the data
                    $allBranches = collect();
                    foreach ($data['invoices'] as $invoice) {
                        if ($invoice->branch) {
                            $allBranches->push($invoice->branch);
                        }
                    }
                    foreach ($data['creditNotes'] as $creditNote) {
                        if ($creditNote->branch) {
                            $allBranches->push($creditNote->branch);
                        }
                    }
                    foreach ($data['expenses'] as $expense) {
                        if ($expense->branch) {
                            $allBranches->push($expense->branch);
                        }
                    }
                    foreach ($data['manualSales'] as $manualSale) {
                        if ($manualSale->branch) {
                            $allBranches->push($manualSale->branch);
                        }
                    }
                    foreach ($data['purchaseInvoices'] as $purchase) {
                        if ($purchase->branch) {
                            $allBranches->push($purchase->branch);
                        }
                    }
                    foreach ($data['purchaseReturns'] as $purchaseReturn) {
                        if ($purchaseReturn->branch) {
                            $allBranches->push($purchaseReturn->branch);
                        }
                    }

                    $uniqueBranches = $allBranches->unique('id');
                @endphp
                ({{ $uniqueBranches->count() }} branch{{ $uniqueBranches->count() !== 1 ? 'es' : '' }})
            @endif
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
            // Initialize branch filter
            $('#branch-filter').select2();

            // Branch filter change handler
            $('#branch-filter').on('change', function() {
                const branchId = $(this).val();
                const currentUrl = new URL(window.location.href);

                if (branchId) {
                    currentUrl.searchParams.set('branch', branchId);
                } else {
                    currentUrl.searchParams.delete('branch');
                }

                window.location.href = currentUrl.toString();
            });

            // Initialize DataTable for monthly tables - ONLY ONCE
            $('.tableVatReport').DataTable({
                responsive: true,
                paging: true,
                searching: true,
                ordering: true,
                info: true,
                pageLength: 10,
                footerCallback: function(row, data, start, end, display) {
                    const api = this.api();

                    const parseValue = (i) => {
                        return typeof i === 'string' ?
                            parseFloat(i.replace(/[,]/g, '')) || 0 :
                            typeof i === 'number' ?
                            i :
                            0;
                    };

                    // Adjust column indices for the new branch column
                    // Branch column is index 0, so Excluding VAT is now 5, VAT Amount is 6, Including VAT is 7
                    const columnsToTotal = [5, 6, 7];

                    columnsToTotal.forEach(function(colIdx) {
                        let total = api
                            .column(colIdx, {
                                page: 'current'
                            })
                            .data()
                            .reduce(function(a, b) {
                                return parseValue(a) + parseValue(b);
                            }, 0);

                        const formatted = total.toLocaleString(undefined, {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        });

                        $(api.column(colIdx).footer()).html(formatted);
                    });
                }
            });

            // Initialize DataTable for "All" tab table
            $('#allDataTable').DataTable({
                paging: true,
                searching: true,
                ordering: true,
                info: true,
                pageLength: 25,
                columnDefs: [{
                        targets: [0], // Checkbox column
                        orderable: false,
                        searchable: false
                    },
                    {
                        targets: [1], // Branch column
                        orderable: true,
                        searchable: true
                    },
                    {
                        targets: [9], // Actions column
                        orderable: false,
                        searchable: false
                    }
                ]
            });

            // Handle 'Mark All' checkbox change
            $('#markAllCheckbox').on('change', function() {
                $('.markItemCheckbox').prop('checked', this.checked);
            });

            // Update 'Mark All' checkbox state when individual checkboxes are toggled
            $(document).on('change', '.markItemCheckbox', function() {
                const allChecked = $('.markItemCheckbox').length === $('.markItemCheckbox:checked').length;
                $('#markAllCheckbox').prop('checked', allChecked);
            });

            $("#btnExportPdf").click(function() {
                let selectedInvoices = [];
                let selectedCreditNotes = [];
                let selectedExpenses = [];
                let selectedManualSales = [];
                let selectedPurchaseInvoices = [];
                let selectedPurchaseReturns = [];

                // Iterate through all checked checkboxes
                $(".markItemCheckbox:checked").each(function() {
                    let nameAttr = $(this).attr('name');

                    if (nameAttr.startsWith('invoice[')) {
                        selectedInvoices.push($(this).val());
                    } else if (nameAttr.startsWith('creditNote[')) {
                        selectedCreditNotes.push($(this).val());
                    } else if (nameAttr.startsWith('expense[')) {
                        selectedExpenses.push($(this).val());
                    } else if (nameAttr.startsWith('manualSale[')) {
                        selectedManualSales.push($(this).val());
                    } else if (nameAttr.startsWith('purchaseInvoice[')) {
                        selectedPurchaseInvoices.push($(this).val());
                    } else if (nameAttr.startsWith('purchaseReturn[')) {
                        selectedPurchaseReturns.push($(this).val());
                    }
                });

                if (selectedInvoices.length === 0 && selectedCreditNotes.length === 0 && selectedExpenses
                    .length === 0 &&
                    selectedManualSales.length === 0 && selectedPurchaseInvoices.length === 0 &&
                    selectedPurchaseReturns.length === 0) {
                    displayErrorMessage("Please select at least one item to export.");
                    return;
                }

                startLoader();
                displaySuccessMessage("Please Wait preparing files");

                let downloadUrl = route('vat-reports.download-zip');

                // Add branch parameter to the request if selected
                let requestData = {
                    invoiceIds: selectedInvoices,
                    creditNoteIds: selectedCreditNotes,
                    expenseIds: selectedExpenses,
                    manualSaleIds: selectedManualSales,
                    purchaseInvoiceIds: selectedPurchaseInvoices,
                    purchaseReturnIds: selectedPurchaseReturns,
                };

                @if ($selectedBranch)
                    requestData.branchId = {{ $selectedBranch }};
                @endif

                $.ajax({
                    url: downloadUrl,
                    method: 'POST',
                    data: requestData,
                    xhrFields: {
                        responseType: 'blob'
                    },
                    success: function(response) {
                        stopLoader();
                        const url = window.URL.createObjectURL(new Blob([response]));
                        const link = document.createElement('a');
                        link.href = url;

                        let fileName =
                            'vat-reports_{{ strtoupper($report->period ?? 'N/A') }}_{{ $report->year }}';
                        @if ($selectedBranch)
                            fileName += '_branch{{ $selectedBranch }}';
                        @endif
                        fileName += '.zip';

                        link.setAttribute('download', fileName);
                        document.body.appendChild(link);
                        link.click();
                        link.remove();
                    },
                    error: function(xhr, status, error) {
                        stopLoader();
                        displayErrorMessage("Error exporting files: " + error);
                    }
                });
            });
        });
    </script>
    {{-- <script>
        $(document).ready(function() {
            // Initialize branch filter
            $('#branch-filter').select2();

            // Branch filter change handler
            $('#branch-filter').on('change', function() {
                const branchId = $(this).val();
                const currentUrl = new URL(window.location.href);

                if (branchId) {
                    currentUrl.searchParams.set('branch', branchId);
                } else {
                    currentUrl.searchParams.delete('branch');
                }

                window.location.href = currentUrl.toString();
            });

            // Initialize DataTable for "All" tab
            // For monthly tables
            $('.tableVatReport').DataTable({
                responsive: true,
                paging: true,
                searching: true,
                ordering: true,
                info: true,
                pageLength: 10,
                footerCallback: function(row, data, start, end, display) {
                    const api = this.api();

                    const parseValue = (i) => {
                        return typeof i === 'string' ?
                            parseFloat(i.replace(/[,]/g, '')) || 0 :
                            typeof i === 'number' ?
                            i :
                            0;
                    };

                    // Adjust column indices for the new branch column
                    // Branch column is index 0, so Excluding VAT is now 5, VAT Amount is 6, Including VAT is 7
                    const columnsToTotal = [5, 6, 7];

                    columnsToTotal.forEach(function(colIdx) {
                        let total = api
                            .column(colIdx, {
                                page: 'current'
                            })
                            .data()
                            .reduce(function(a, b) {
                                return parseValue(a) + parseValue(b);
                            }, 0);

                        const formatted = total.toLocaleString(undefined, {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        });

                        $(api.column(colIdx).footer()).html(formatted);
                    });
                }
            });

            // For "All" tab table
            $('#allDataTable').DataTable({
                paging: true,
                searching: true,
                ordering: true,
                info: true,
                pageLength: 25,
                columnDefs: [{
                        targets: [0], // Checkbox column
                        orderable: false,
                        searchable: false
                    },
                    {
                        targets: [1], // Branch column
                        orderable: true,
                        searchable: true
                    },
                    {
                        targets: [9], // Actions column
                        orderable: false,
                        searchable: false
                    }
                ]
            });

            // Handle 'Mark All' checkbox change
            $('#markAllCheckbox').on('change', function() {
                $('.markItemCheckbox').prop('checked', this.checked);
            });

            // Update 'Mark All' checkbox state when individual checkboxes are toggled
            $(document).on('change', '.markItemCheckbox', function() {
                const allChecked = $('.markItemCheckbox').length === $('.markItemCheckbox:checked').length;
                $('#markAllCheckbox').prop('checked', allChecked);
            });

            $("#btnExportPdf").click(function() {
                let selectedInvoices = [];
                let selectedCreditNotes = [];
                let selectedExpenses = [];
                let selectedManualSales = [];
                let selectedPurchaseInvoices = [];
                let selectedPurchaseReturns = [];

                // Iterate through all checked checkboxes
                $(".markItemCheckbox:checked").each(function() {
                    let nameAttr = $(this).attr('name');

                    if (nameAttr.startsWith('invoice[')) {
                        selectedInvoices.push($(this).val());
                    } else if (nameAttr.startsWith('creditNote[')) {
                        selectedCreditNotes.push($(this).val());
                    } else if (nameAttr.startsWith('expense[')) {
                        selectedExpenses.push($(this).val());
                    } else if (nameAttr.startsWith('manualSale[')) {
                        selectedManualSales.push($(this).val());
                    } else if (nameAttr.startsWith('purchaseInvoice[')) {
                        selectedPurchaseInvoices.push($(this).val());
                    } else if (nameAttr.startsWith('purchaseReturn[')) {
                        selectedPurchaseReturns.push($(this).val());
                    }
                });

                if (selectedInvoices.length === 0 && selectedCreditNotes.length === 0 && selectedExpenses
                    .length === 0 &&
                    selectedManualSales.length === 0 && selectedPurchaseInvoices.length === 0 &&
                    selectedPurchaseReturns.length === 0) {
                    displayErrorMessage("Please select at least one item to export.");
                    return;
                }

                startLoader();
                displaySuccessMessage("Please Wait preparing files");

                let downloadUrl = route('vat-reports.download-zip');

                // Add branch parameter to the request if selected
                let requestData = {
                    invoiceIds: selectedInvoices,
                    creditNoteIds: selectedCreditNotes,
                    expenseIds: selectedExpenses,
                    manualSaleIds: selectedManualSales,
                    purchaseInvoiceIds: selectedPurchaseInvoices,
                    purchaseReturnIds: selectedPurchaseReturns,
                };

                @if ($selectedBranch)
                    requestData.branchId = {{ $selectedBranch }};
                @endif

                $.ajax({
                    url: downloadUrl,
                    method: 'POST',
                    data: requestData,
                    xhrFields: {
                        responseType: 'blob'
                    },
                    success: function(response) {
                        stopLoader();
                        const url = window.URL.createObjectURL(new Blob([response]));
                        const link = document.createElement('a');
                        link.href = url;

                        let fileName =
                            'vat-reports_{{ strtoupper($report->period ?? 'N/A') }}_{{ $report->year }}';
                        @if ($selectedBranch)
                            fileName += '_branch{{ $selectedBranch }}';
                        @endif
                        fileName += '.zip';

                        link.setAttribute('download', fileName);
                        document.body.appendChild(link);
                        link.click();
                        link.remove();
                    },
                    error: function(xhr, status, error) {
                        stopLoader();
                        displayErrorMessage("Error exporting files: " + error);
                    }
                });
            });

            // Initialize DataTables for monthly tables
            $('.tableVatReport').DataTable({
                responsive: true,
                paging: true,
                searching: true,
                ordering: true,
                info: true,
                pageLength: 10,
                footerCallback: function(row, data, start, end, display) {
                    const api = this.api();

                    const parseValue = (i) => {
                        return typeof i === 'string' ?
                            parseFloat(i.replace(/[,]/g, '')) || 0 :
                            typeof i === 'number' ?
                            i :
                            0;
                    };

                    const columnsToTotal = [5, 6, 7];

                    columnsToTotal.forEach(function(colIdx) {
                        let total = api
                            .column(colIdx, {
                                page: 'current'
                            })
                            .data()
                            .reduce(function(a, b) {
                                return parseValue(a) + parseValue(b);
                            }, 0);

                        const formatted = total.toLocaleString(undefined, {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        });

                        $(api.column(colIdx).footer()).html(formatted);
                    });
                }
            });
        });
    </script> --}}
@endsection
