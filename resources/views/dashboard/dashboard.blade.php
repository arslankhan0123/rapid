@extends('layouts.app')
@section('title')
    {{ __('messages.dashboard') }}
@endsection

@section('page_css')
    <link href="{{ asset('assets/css/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ asset('assets/css/bs4-summernote/summernote-bs4.css') }}">
    <style>
        .edit-icon {
            cursor: pointer;
            margin-left: 5px;
            color: #28ace2 !important;
            /* Change to your desired color */
        }

        .date-value {
            cursor: pointer;
        }

        .contract-table-bg-color,
        .progress-bar {
            background: #28ace2 !important;
        }

        .btnColor {
            background: #28ace2 !important;
        }
    </style>
@endsection
@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{ __('messages.dashboard') }}</h1>
        </div>
        @include('flash::message')


        
            <div class="row">
                @can('manage_dashboard_customer')
                <div class="col-md-4 col-sm-12 dashboard-card-css">
                    <div class="card card-statistic-2 d-total-six-border">
                        <div class="card-stats">
                            <div class="card-stats-title">
                                <a href="{{ route('customers.index') }}"
                                    class="font-weight-bold anchor-underline">{{ __('messages.customers') }}</a>
                            </div>
                            <div class="card-stats-items d-stat-items-flex">
                                <div class="card-stats-item d-stat-item-flex">
                                    <div class="card-stats-item-count">{{ $customerCount['total_customers'] }}</div>
                                    <span class="text-success font-weight-bold">{{ __('messages.common.active') }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="card-icon shadow-primary d-total-six-bg d-border-radius">
                            <i class="fas fa-street-view"></i>
                        </div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>{{ __('messages.customer.total_customers') }}</h4>
                            </div>
                            <div class="card-body">
                                {{ $customerCount['total_customers'] }}
                            </div>
                        </div>
                    </div>
                </div>
                @endcan
                @can('manage_dashboard_project')
                <div class="col-md-4 col-sm-12 dashboard-card-css">
                    <div class="card card-statistic-2 d-total-four-border">
                        <div class="card-stats">
                            <div class="card-stats-title">
                                <a href="{{ route('projects.index') }}"
                                    class="font-weight-bold anchor-underline">{{ __('messages.projects') }}</a>
                            </div>
                            <div class="card-stats-items d-stat-items-flex">
                                <div class="card-stats-item d-stat-item-flex">
                                    <div class="card-stats-item-count">{{ $projectStatusCount['not_started'] }}</div>
                                    <span class="text-danger font-weight-bold">{{ __('messages.project.not_started') }}</span>
                                </div>
                                <div class="card-stats-item d-stat-item-flex">
                                    <div class="card-stats-item-count">{{ $projectStatusCount['in_progress'] }}</div>
                                    <span class="text-primary font-weight-bold">{{ __('messages.project.in_progress') }}</span>
                                </div>
                                <div class="card-stats-item d-stat-item-flex">
                                    <div class="card-stats-item-count">{{ $projectStatusCount['on_hold'] }}</div>
                                    <span class="text-warning font-weight-bold">{{ __('messages.project.on_hold') }}</span>
                                </div>
                                <div class="card-stats-item d-stat-item-flex">
                                    <div class="card-stats-item-count">{{ $projectStatusCount['cancelled'] }}</div>
                                    <span class="text-info font-weight-bold">{{ __('messages.project.cancelled') }}</span>
                                </div>
                                <div class="card-stats-item d-stat-item-flex">
                                    <div class="card-stats-item-count">{{ $projectStatusCount['finished'] }}</div>
                                    <span class="text-success font-weight-bold">{{ __('messages.project.finished') }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="card-icon shadow-primary d-total-four-bg d-border-radius">
                            <i class="fas fa-layer-group"></i>
                        </div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>{{ __('messages.project.total_projects') }}</h4>
                            </div>
                            <div class="card-body">
                                {{ $projectStatusCount['total_projects'] }}
                            </div>
                        </div>
                    </div>
                </div>
                @endcan
                @can('manage_dashboard_invoice')
                <div class="col-md-4 col-sm-12 dashboard-card-css">
                    <div class="card card-statistic-2 d-total-one-border">
                        <div class="card-stats">
                            <div class="card-stats-title">
                                <a href="{{ route('invoices.index') }}"
                                    class="font-weight-bold anchor-underline">{{ __('messages.invoices') }}</a>
                            </div>
                            {{-- {{dd($invoiceStatusCount->toArray())}} --}}
                            <div class="card-stats-items d-stat-items-flex">
                                <div class="card-stats-item d-stat-item-flex">
                                    <div class="card-stats-item-count">{{ $invoiceStatusCount['drafted'] }}</div>
                                    <span class="text-warning font-weight-bold">{{ __('messages.common.drafted') }}</span>
                                </div>
                                <div class="card-stats-item d-stat-item-flex">
                                    <div class="card-stats-item-count">{{ $invoiceStatusCount['unpaid'] }}</div>
                                    <span class="text-primary font-weight-bold">{{ __('messages.invoice.unpaid') }}</span>
                                </div>
                                <div class="card-stats-item d-stat-item-flex">
                                    <div class="card-stats-item-count">{{ $invoiceStatusCount['partially_paid'] }}</div>
                                    <span class="text-info font-weight-bold">{{ __('messages.invoice.partially_paid') }}</span>
                                </div>
                                <div class="card-stats-item d-stat-item-flex">
                                    <div class="card-stats-item-count">{{ $invoiceStatusCount['paid'] }}</div>
                                    <span class="text-success font-weight-bold">{{ __('messages.invoice.paid') }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="card-icon shadow-primary d-total-one-bg d-border-radius">
                            <i class="fas fa-file-invoice"></i>
                        </div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>{{ __('messages.invoice.total_invoices') }}</h4>
                            </div>
                            <div class="card-body">
                                {{ $invoiceStatusCount['total_invoices'] }}
                            </div>
                        </div>
                    </div>
                </div>
                @endcan
            </div>

            @php

                // Calculate the total expense
                $totalExpense = collect($paymentModeResult)->sum('total_amount');
            @endphp
            <div class="row">
                @foreach ($paymentModeResult as $result)
                    <!-- Payment Mode Card -->
                    <div class="col-md-3 mb-2">
                        <div class="card"
                            style="box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); transition: transform 0.2s ease-in-out;"
                            onmouseover="this.style.transform='translateY(-10px)';this.style.boxShadow='0 10px 20px rgba(0, 0, 0, 0.1)';"
                            onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='0 4px 8px rgba(0, 0, 0, 0.1)';">

                            <!-- Card Header with Dynamic Payment Mode Name -->
                            <div class="card-header"
                                style="background-color:
                    @if ($result['payment_mode'] == 'Bank Transfer') #28ace2;
                    @elseif($result['payment_mode'] == 'Petty Cash') #f5365c;
                    @else orange; @endif
                    color: white; font-size: 1.25rem;">

                                {{ $result['payment_mode'] }}
                            </div>
                            <!-- Card Body with Dynamic Total Amount -->
                            <div class="card-body text-center" style="font-size: 1.5rem; font-weight: bold;">
                                <h3>{{ number_format($result['total_amount'], 2) }}</h3>
                            </div>
                        </div>
                    </div>
                @endforeach

                <!-- Total Expense Card -->

            </div>


            <div class="row">
                <div class="col-lg-12">

                    <div class="row ">
                        @can('manage_dashboard_invoice_overview')
                        <div class="col-md-4 col-lg-4 col-sm-12 ">
                            <div class="card card-statistic-2 d-total-four-border">
                                <div class="col-sm-12">
                                    <p class="text-dark mt-1">
                                        <a href="{{ route('invoices.index') }}"
                                            class="inline-block font-weight-bold anchor-underline">{{ __('messages.invoice.invoice_overview') }}</a>
                                    </p>
                                    <hr>
                                </div>
                                @php
                                    $style = 'style';
                                    $width = 'width';
                                @endphp
                                <div class="col-md-12 d-flex">
                                    <span class="inline-block font-weight-bold text-warning">
                                        {{ __('messages.common.drafted') }}</span>
                                </div>
                                <div class="col-md-12 progress-finance-status">
                                    <div class="progress progress-bar-mini height-25 mt-1">
                                        <div class="progress-bar" role="progressbar"
                                            aria-valuenow="{{ ($invoiceStatusCount['drafted'] * 100) / totalCountForDashboard($invoiceStatusCount['total_invoices']) }}"
                                            aria-valuemin="0" aria-valuemax="100"
                                            {{ $style }}="{{ $width }} :{{ ($invoiceStatusCount['drafted'] * 100) / totalCountForDashboard($invoiceStatusCount['total_invoices']) }}%">
                                            {{ number_format(($invoiceStatusCount['drafted'] * 100) / totalCountForDashboard($invoiceStatusCount['total_invoices']), 2) }}%
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-12 mt-1 d-flex">
                                    <span
                                        class="inline-block font-weight-bold text-primary">{{ __('messages.invoice.unpaid_cap') }}</span>
                                </div>
                                <div class="col-md-12 progress-finance-status">
                                    <div class="progress progress-bar-mini height-25 mt-1">
                                        <div class="progress-bar" role="progressbar"
                                            aria-valuenow="{{ ($invoiceStatusCount['unpaid'] * 100) / totalCountForDashboard($invoiceStatusCount['total_invoices']) }}"
                                            aria-valuemin="0" aria-valuemax="100"
                                            {{ $style }}="{{ $width }} :{{ ($invoiceStatusCount['unpaid'] * 100) / totalCountForDashboard($invoiceStatusCount['total_invoices']) }}%">
                                            {{ number_format(($invoiceStatusCount['unpaid'] * 100) / totalCountForDashboard($invoiceStatusCount['total_invoices']), 2) }}%
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-12 mt-1 d-flex">
                                    <span class="inline-block font-weight-bold text-success">
                                        {{ __('messages.invoice.paid_cap') }}</span>
                                </div>
                                <div class="col-md-12 progress-finance-status">
                                    <div class="progress progress-bar-mini height-25 mt-1">
                                        <div class="progress-bar" role="progressbar"
                                            aria-valuenow="{{ ($invoiceStatusCount['paid'] * 100) / totalCountForDashboard($invoiceStatusCount['total_invoices']) }}"
                                            aria-valuemin="0" aria-valuemax="100"
                                            {{ $style }}="{{ $width }}: {{ ($invoiceStatusCount['paid'] * 100) / totalCountForDashboard($invoiceStatusCount['total_invoices']) }}%">
                                            {{ number_format(($invoiceStatusCount['paid'] * 100) / totalCountForDashboard($invoiceStatusCount['total_invoices']), 2) }}%
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12 mt-1 d-flex">
                                    <span class="inline-block font-weight-bold text-success">
                                        {{ __('messages.invoice.partials') }}</span>
                                </div>

                                <div class="col-md-12 progress-finance-status pb-2">
                                    <div class="progress progress-bar-mini height-25 mt-1">
                                        <div class="progress-bar" role="progressbar"
                                            aria-valuenow="{{ ($invoiceStatusCount['partially_paid'] * 100) / totalCountForDashboard($invoiceStatusCount['total_invoices']) }}"
                                            aria-valuemin="0" aria-valuemax="100"
                                            {{ $style }}="{{ $width }}: {{ ($invoiceStatusCount['partially_paid'] * 100) / totalCountForDashboard($invoiceStatusCount['total_invoices']) }}%">
                                            {{ number_format(($invoiceStatusCount['partially_paid'] * 100) / totalCountForDashboard($invoiceStatusCount['total_invoices']), 2) }}%
                                        </div>
                                    </div>
                                </div>
                            </div>


                        </div>
                        @endcan
                        @can('manage_dashboard_online_user')
                        <div class="col-md-4 col-lg-4 col-sm-12  ">
                            <div class="card card-statistic-2 d-total-six-border " style="padding-bottom: 0px;">
                                <div class="card-stats">
                                    <div class="card-stats-title">
                                        <a href="" class="font-weight-bold anchor-underline">Online Users</a>
                                    </div><br>
                                    <div class="card-stats-items d-stat-items-flex">
                                        <div class="card-stats-item d-stat-item-flex">
                                            <div class="card-stats-item-count">{{ $customerCount['total_customers'] }}
                                            </div>
                                            <span
                                                class="text-success font-weight-bold">{{ __('messages.common.active') }}</span>
                                        </div>
                                    </div>
                                </div><br><br><br>
                                <div class="card-icon shadow-primary d-total-six-bg d-border-radius">
                                    <i class="fas fa-street-view"></i>
                                </div>
                                <div class="card-wrap">
                                    <div class="card-header">
                                        <h4>Offline Users</h4>
                                    </div>
                                    <div class="card-body">
                                        {{ $customerCount['total_customers'] }}
                                    </div>
                                </div>

                            </div>
                        </div>
                        @endcan
                        @can('manage_dashboard_active_employee')
                        <div class="col-md-4 col-lg-4 col-sm-12  ">
                            <div class="card card-statistic-2  d-total-one-border " style="padding-bottom: 0px;">
                                <div class="card-stats">
                                    <div class="card-stats-title">
                                        <a href="" class="font-weight-bold anchor-underline">Active Employees</a>
                                    </div><br>
                                    <div class="card-stats-items d-stat-items-flex">
                                        <div class="card-stats-item d-stat-item-flex">
                                            <div class="card-stats-item-count">{{ $employeeStatus['active'] ?? 0 }}
                                            </div>
                                            <span
                                                class="text-success font-weight-bold">{{ __('messages.common.active') }}</span>
                                        </div>
                                    </div>
                                </div><br><br><br>
                                <div class="card-icon shadow-primary d-total-six-bg d-border-radius">
                                    <i class="fas fa-street-view"></i>
                                </div>
                                <div class="card-wrap">
                                    <div class="card-header">
                                        <h4>Inactive Employees</h4>
                                    </div>
                                    <div class="card-body">
                                        {{ $employeeStatus['inactive'] ?? 0 }}
                                    </div>
                                </div>

                            </div>
                        </div>
                        @endcan
                    </div>


                </div>
            </div>
            @can('manage_dashboard_incomes_vs_expenses')
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="text-dark">{{ __('messages.common.incomes_vs_expenses') }}</h6>
                        </div>
                        <div class="card-body">
                            <canvas id="incomeVsExpenseChart" width="400" height="150"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            @endcan

            @php
                if ($salaryGenerate) {
                    $report_date =
                        \Carbon\Carbon::createFromFormat('Y-m', $salaryGenerate->salary_month)->format('F Y') .
                            ' | ' .
                            $salaryGenerate->branch?->name ??
                        '';
                }

            @endphp
            
            @can('manage_dashboard_salary_sheet')
            @if ($salaryGenerate)
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="text-dark">
                                    Employee Salary Sheet for {{ $report_date }}
                                </h6>
                                <div class="float-right ml-auto">
                                    <a href="{{ route('salary_generates.index') }}" class="btn btn-primary">
                                        More
                                    </a>
                                </div>
                            </div>

                            <div class="card-body">
                                <div class="table-responsive">


                                    <table id="salaryTable" class="table-sm table-bordered" style="width: 100%;">
                                        <thead class="text-center">
                                            <tr>
                                                <th>#</th>
                                                <th style="text-align: center;">{{ __('messages.employee_salaries.id') }}</th>
                                                <th>
                                                    <div style="width: 130px;text-align:center">
                                                        {{ __('messages.employee_salaries.employee_name') }}</div>
                                                </th>
                                                <th>{{ __('messages.employee_salaries.basic_salary') }}</th>
                                                <th>{{ __('messages.employee_salaries.bonuses') }}</th>
                                                <th>{{ __('messages.employee_salaries.overtime') }}</th>
                                                <th>{{ __('messages.employee_salaries.total_allowances') }}</th>
                                                <th>{{ __('messages.employee_salaries.gross_salary') }}</th>
                                                <th>{{ __('messages.employee_salaries.salary_advance') }}</th>
                                                <th>{{ __('messages.employee_salaries.loan') }}</th>
                                                <th>{{ __('messages.employee_salaries.hourly_deduction') }}</th>
                                                <th>{{ __('messages.employee_salaries.total_deduction') }}</th>
                                                <th>{{ __('messages.employee_salaries.net_salary') }}</th>
                                                <th>Action</th>

                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($sheets as $sheet)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>
                                                        <div style="width: 100px;text-align:center">
                                                            {{ $sheet->employee->iqama_no }}
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div style="width: 130px;text-align:left;">
                                                            {{ $sheet->employee->name }}
                                                        </div>
                                                    </td>
                                                    <td class="text-right">{{ number_format($sheet->basic_salary, 2) }}</td>
                                                    <td class="text-right">{{ number_format($sheet->total_bonus, 2) }}</td>
                                                    <td class="text-right">{{ number_format($sheet->total_overtimes, 2) }}
                                                    </td>
                                                    <td class="text-right">{{ number_format($sheet->total_allowances, 2) }}
                                                    </td>
                                                    <td class="text-right">{{ number_format($sheet->gross_salary, 2) }}</td>
                                                    <td class="text-right">{{ number_format($sheet->salary_advance, 2) }}</td>
                                                    <td class="text-right">{{ number_format($sheet->loan, 2) }}</td>
                                                    <td class="text-right">{{ number_format($sheet->hourly_deduction, 2) }}
                                                    </td>
                                                    <td class="text-right">{{ number_format($sheet->total_deduction, 2) }}
                                                    </td>
                                                    <td class="text-right">{{ number_format($sheet->net_salary, 2) }}</td>
                                                    <td style="white-space: nowrap;" class="text-right">
                                                        <button onclick="printItem({{ $sheet->id }})"
                                                            class="btn btn-info btn-sm">
                                                            <i class="fas fa-print"></i>
                                                        </button>
                                                        <button onclick="downloadItem({{ $sheet->id }})"
                                                            class="btn btn-success btn-sm ms-2">
                                                            <i class="fas fa-download"></i>
                                                        </button>
                                                    </td>


                                                    {{-- <td>{{ number_format($sheet->state_income_tax, 2) }}</td>
                                        <td>{{ number_format($sheet->total_commission, 2) }}</td> --}}
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
            @endcan
        @can('manage_dashboard_cash_flow')
            <div class="card-body pt-2 pb-2">

                @php
                    $bgColors = [
                        'bg-success',
                        'bg-primary text-white',
                        'bg-success text-white',
                        'bg-warning',
                        'bg-info text-white',
                        'bg-secondary text-white',
                    ];
                @endphp

                <div class="row">


                    <div class="col-md-3 p-2"> {{-- Controls padding between cards --}}
                        <div class="card shadow-sm border-0 h-100 bg-info">
                            <div class="card-body text-center py-3"> {{-- Reduced vertical padding --}}
                                <h5 class="card-title font-weight-bold mb-2">
                                   Total Expense
                                </h5>
                                <p class="card-text display-4 mb-0">
                                    {{ number_format($totalExpenseAmount??0, 2) }}
                                </p>
                            </div>
                            <div class="card-footer bg-transparent border-0 text-center py-2">
                                <small>Amount</small>
                            </div>
                        </div>
                    </div>
                    @foreach ($accountSums as $account)
                        @php
                            $bgClass = $bgColors[$loop->index % count($bgColors)];
                        @endphp

                        <div class="col-md-3 p-2"> {{-- Controls padding between cards --}}
                            <div class="card shadow-sm border-0 h-100 {{ $bgClass }}">
                                <div class="card-body text-center py-3"> {{-- Reduced vertical padding --}}
                                    <h5 class="card-title font-weight-bold mb-2">
                                        {{ $account['account_name'] }}
                                    </h5>
                                    <p class="card-text display-4 mb-0">
                                        {{ number_format($account['total_balance'], 2) }}
                                    </p>
                                </div>
                                <div class="card-footer bg-transparent border-0 text-center py-2">
                                    <small>Balance</small>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>


            </div>
        @endcan
        @can('manage_dashboard')
            {{-- this parts remains --}}
            <div class="card-body">
                <div class="row">
                    @foreach ($accounts as $branch)
                        @foreach ($branch['accounts'] as $account)
                            <div class="col-md-4 mb-2">
                                <div class="text-center p-2" style="border:1px solid #ddd; font-size:14px; line-height:1.2;">
                                    <h5 class="text-dark font-weight-bold mt-3 mb-2">
                                        {{ $branch['name'] }} {{-- Branch Name --}}
                                    </h5>
                                    <hr style="margin: 5px 0;">
                                    <p style="margin: 0; padding: 2px 0;font-weight:bold;">{{ $account['account_name'] }}</p>
                                    {{-- Account Name --}}
                                    <hr style="margin: 5px 0;">
                                    <p style="margin: 0; padding: 2px 0;font-weight:bold;">
                                        {{ number_format($account['opening_balance'], 2) }}
                                    </p> {{-- Opening Balance --}}
                                    <hr style="margin: 5px 0;">
                                    <div class="d-flex">
                                        <button title="Pay" data-id="{{ $account['id'] }}"
                                            data-branch-id="{{ $account['branch_id'] }}"
                                            class="btn btn-info action-btn has-icon pay-btn w-50 mr-1">
                                            Pay
                                        </button>
                                        <button title="Edit" data-id="{{ $account['id'] }}"
                                            data-branch-id="{{ $account['branch_id'] }}"
                                            data-balance="{{ $account['opening_balance'] ?? 0 }}"
                                            data-date="{{ $account['date'] ?? '' }}"
                                            class="btn btn-success action-btn has-icon edit-btn-cash w-50">
                                            Edit
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endforeach
                </div>
            </div>
        @endcan
        @can('manage_dashboard_expire_list')
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h6 class="text-dark w-100">{{ __('messages.employees.id_expire') }}</h6>

                        <div>
                            {!! Form::select('idType', ['iqama_no' => 'Iqama No', 'passport' => 'Passport'], null, [
                                'class' => 'form-control',
                                'id' => 'idTypeSelect',
                                'style' => 'border-radius:0px;width:200px;',
                            ]) !!}
                        </div>
                        <div style="width: 50px;"> </div>
                        <div>
                            <input type="date" class="form-control " id="monthId"
                                style = 'border-radius:0px;width:200px;height:43px;',>
                            {{-- {!! Form::select('month', $months, $currentMonth, ['class' => 'form-control', 'id' => 'monthId']) !!} --}}
                        </div>

                    </div>
                    <div class="card-body">
                        <table class="table table-striped table-bordered table-responsive-sm" id="employeeIDExpireTable">
                            <thead class="text-white contract-table-bg-color">
                                <tr>
                                    <td id="idType">{{ __('messages.employees.id') }}</td>
                                    <td>{{ __('messages.employees.name') }}</td>
                                    <td>{{ __('messages.designations.name') }}</td>
                                    <td>{{ __('messages.employees.expire_date') }}</td>
                                </tr>
                            </thead>
                            <tbody class="expiring-contracts">
                            </tbody>
                        </table>
                        <div class="row justify-content-end mt-2">
                            <div class="col-md-2 col-lg-1"> <a href="{{ route('dashboard.expire.list') }}"
                                    class="btn text-primary">
                                    See More <i class="fa fa-arrow-right"></i>
                                </a></div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        @endcan
        @can('manage_dashboard_apointments')
        @if ($appointment_list)
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h6 class="text-dark w-100">{{ __('messages.appointments.appointments') }}</h6>
                    </div>
                    <div class="card-body">
                        @include('appointments.table')
                        <div class="row justify-content-end mt-2">
                            <div class="col-md-2 col-lg-1"> <a href="{{ route('appointments.index') }}"
                                    class="btn text-primary">
                                    See More <i class="fa fa-arrow-right"></i>
                                </a></div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        @endif
        @endcan


    </section>
    @include('dashboard.templates.templates')
    @include('dashboard.cashTransferModal')
@endsection
@section('page_scripts')
    <script>
        let leadData = JSON.parse('@json($leadStatuses)');
        let projectStatus = JSON.parse('@json($projectStatus)');
        let projectStatusCounts = JSON.parse('@json($projectStatusCount)');
        let ticketStatusData = JSON.parse('@json($ticketStatus)');
        let currentWeekInvoices = JSON.parse('@json($currentWeekInvoices)');
        let lastWeekInvoices = JSON.parse('@json($lastWeekInvoices)');
        let incomeAndExpenseData = JSON.parse('@json($monthWiseRecords)');
        let expiringContractLists = JSON.parse('@json($contractsCurrentMonths)');
    </script>
    <script src="{{ asset('assets/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ mix('assets/js/custom/custom-datatable.js') }}"></script>
    <script src="{{ mix('assets/js/bs4-summernote/summernote-bs4.js') }}"></script>
    <script src="{{ mix('assets/js/chart/Chart.min.js') }}"></script>
    <script src="{{ mix('assets/js/dashboard/dashboard.js') }}"></script>
    <script>
        document.getElementById('monthId').value = new Date().toISOString().split('T')[0];
        const ctx = document.getElementById('incomeVsExpenseChart').getContext('2d');
        const incomeVsExpenseChart = new Chart(ctx, {
            type: 'bar', // You can change this to 'line', 'pie', etc.
            data: {
                // Use short month names for 12 months
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov',
                    'Dec'
                ], // 12 months
                datasets: [{
                        label: 'Incomes',
                        data: generateRandomData(12, 1000,
                            5000), // Generate 12 random income values between 1000 and 5000
                        backgroundColor: 'rgba(54, 162, 235, 0.2)', // Customize color
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'Expenses',
                        data: generateRandomData(12, 500,
                            3000), // Generate 12 random expense values between 500 and 3000
                        backgroundColor: 'rgba(255, 99, 132, 0.2)', // Customize color
                        borderColor: 'rgba(255, 99, 132, 1)',
                        borderWidth: 1
                    }
                ]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Function to generate an array of random numbers
        function generateRandomData(length, min, max) {
            const data = [];
            for (let i = 0; i < length; i++) {
                data.push(Math.floor(Math.random() * (max - min + 1)) + min); // Random number between min and max
            }
            return data;
        }
    </script>

    <script>
        function initializeDataTable(idType) {
            // Determine the header and data field based on the selected ID type
            let idTypeHeader = '';
            let dateField = '';
            let idField = '';

            if (idType === 'iqama_no') {
                idTypeHeader = '{{ __('messages.employees.iqama_no') }}';
                idField = 'iqama_no';
                dateField = 'iqama_no_expiry_date';
            } else if (idType === 'passport') {
                idTypeHeader = '{{ __('messages.employees.passport') }}';
                idField = 'passport';
                dateField = 'passport_expiry_date';
            }


            // Destroy the existing DataTable if it exists
            if ($.fn.DataTable.isDataTable('#employeeIDExpireTable')) {
                $('#employeeIDExpireTable').DataTable().destroy();
            }

            // Update the table header dynamically
            $('#employeeIDExpireTable thead').html(`
                <tr>
                    <th style='color:white;'>${idTypeHeader}</th>
                    <th style='color:white;'>{{ __('messages.employees.name') }}</th>
                    <th style='color:white;'>{{ __('messages.designations.name') }}</th>
                    <th style='color:white;'>{{ __('messages.employees.expire_date') }}</th>
                </tr>
           `);

            // Define the columns based on the selected ID type
            let columns = [{
                    data: function(row) {
                        let element = document.createElement('textarea');
                        element.innerHTML = row[idField] ?? '';
                        return element.value;
                    },
                    name: idField,
                    width: '15%',
                    orderable: false
                },
                {
                    data: function(row) {
                        let element = document.createElement('textarea');
                        element.innerHTML = row.name ?? '';
                        return element.value;
                    },
                    name: 'name',
                    width: '15%',
                    orderable: false
                },
                {
                    data: function(row) {
                        let element = document.createElement('textarea');
                        element.innerHTML = row.designation.name ?? '';
                        return element.value;
                    },
                    name: 'designation.name',
                    width: '15%',
                    orderable: false
                },
                {
                    data: function(row) {
                        let dateStr = row[dateField] ?? '';
                        if (dateStr) {
                            let date = new Date(dateStr);
                            let day = ("0" + date.getDate()).slice(-2);
                            let month = ("0" + (date.getMonth() + 1)).slice(-2);
                            let year = date.getFullYear();
                            // Add an edit icon beside the date if user has permission
                            return `
                                <span class="date-value">
                                    ${day}-${month}-${year}
                                </span>
                                @can('update_employees')
                                <i class="edit-icon" data-id="${row.id}">&#9998;</i>
                                @endcan
                            `;
                        } else {
                            return '';
                        }
                    },
                    name: dateField,
                    width: '15%',
                    orderable: true,

                }
            ];

            // Reinitialize the DataTable
            $('#employeeIDExpireTable').DataTable({
                oLanguage: {
                    'sEmptyTable': Lang.get('messages.common.no_data_available_in_table'),
                    'sInfo': Lang.get('messages.common.data_base_entries'),
                    sLengthMenu: Lang.get('messages.common.menu_entry'),
                    sInfoEmpty: Lang.get('messages.common.no_entry'),
                    sInfoFiltered: Lang.get('messages.common.filter_by'),
                    sZeroRecords: Lang.get('messages.common.no_matching'),
                },
                processing: true,
                serverSide: true,
                ajax: {
                    url: route('dashboard.employees'),
                    data: function(d) {
                        d.month = $('#monthId').val(); // Get the selected month
                        d.idType = $('#idTypeSelect').val(); // Get the selected ID type
                    }
                },
                columns: columns, // Apply the dynamic columns
                responsive: true,

                // Disable pagination, info, and length menu

                info: false,


            });
        }



        // Initialize DataTable on page load with default ID type
        initializeDataTable($('#idTypeSelect').val());

        // Add event listeners to the month and ID type dropdowns
        $('#monthId').change(function() {
            $('#employeeIDExpireTable').DataTable().ajax.reload(); // Reload DataTable when month changes
        });

        // Handle change of ID type and reinitialize the DataTable
        $('#idTypeSelect').change(function() {
            let selectedIdType = $(this).val();
            initializeDataTable(selectedIdType); // Reinitialize the DataTable with new ID type
        });




        // Handle click events on the edit icon
        $('#employeeIDExpireTable').on('click', '.edit-icon', function() {
            let employeeId = $(this).data('id');
            // Redirect only if user has permission
            @can('update_employees')
                window.location.href = route('employees.edit', {
                    employee: employeeId
                });
            @else
                alert('You do not have permission to edit employees.');
            @endcan
        });





        $(document).on('click', '.pay-btn', function() {
            var accountId = $(this).data('id');
            var branchId = $(this).data('branch-id');

            // Open the modal
            $('#payAccountModal').modal('show');


            // You can store the accountId and branchId in hidden fields if needed
            $('#payAccountForm').find('input[name="account_id"]').val(accountId);
            $('#payAccountForm').find('input[name="branch_id"]').val(branchId);
        });

        $(document).on('click', '.edit-btn-cash', function() {
            var accountId = $(this).data('id');
            var branchId = $(this).data('branch-id');
            var balance = $(this).data('balance');
            var date = $(this).data('date');
            // Open the modal
            $('#updatePayAccountModal').modal('show');

            // // You can store the accountId and branchId in hidden fields if needed
            $('#payAccountFormUpdate').find('input[name="account_id"]').val(accountId);
            $('#payAccountFormUpdate').find('input[name="branch_id"]').val(branchId);
            $("#update_pay_amount").val(balance);
            $("#update_input_date").val(date);

        });

        $('#payAccountForm').on('submit', function(e) {
            e.preventDefault();

            var amount = $('#pay_amount').val();
            var accountId = $('#payAccountForm').find('input[name="account_id"]').val();
            var branchId = $('#payAccountForm').find('input[name="branch_id"]').val();
            var date = $('#input_date').val();
            // Check if any required fields are missing
            if (!amount) {
                alert('Please enter an amount.');
                return;
            }

            var formData = {
                amount: amount,
                account_id: accountId,
                branch_id: branchId,
                date: date
            };
            startLoader();
            $.ajax({
                url: "{{ route('accounts.pay-cash') }}",
                type: 'POST',
                data: formData,
                success: function(response) {

                    stopLoader();
                    if (response.status == true) {
                        $("#pay_amount").val(0)

                        displaySuccessMessage(response.message);
                        location.reload();
                    } else {
                        displayErrorMessage(response.message);
                    }

                    // You can also redirect or update the page as needed
                    $('#payAccountModal').modal('hide');
                },
                error: function(error) {
                    stopLoader();
                    alert('Error: ' + error.responseJSON.message);
                    console.log(error);
                }
            });
        });


        $('#payAccountFormUpdate').on('submit', function(e) {
            e.preventDefault();

            var amount = $('#update_pay_amount').val();
            var accountId = $('#payAccountFormUpdate').find('input[name="account_id"]').val();
            var branchId = $('#payAccountFormUpdate').find('input[name="branch_id"]').val();
            var date = $('#update_input_date').val();
            // Check if any required fields are missing
            if (!amount) {
                alert('Please enter an amount.');
                return;
            }

            var formData = {
                amount: amount,
                account_id: accountId,
                branch_id: branchId,
                date: date
            };
            startLoader();
            $.ajax({
                url: "{{ route('accounts.update-cash') }}",
                type: 'POST',
                data: formData,
                success: function(response) {

                    stopLoader();
                    if (response.status == true) {
                        $("#update_pay_amount").val(0)

                        displaySuccessMessage(response.message);
                        location.reload();
                    } else {
                        displayErrorMessage(response.message);
                    }

                    // You can also redirect or update the page as needed
                    $('#updatePayAccountModal').modal('hide');
                },
                error: function(error) {

                    stopLoader();
                    alert('Error: ' + error.responseJSON.message);
                    console.log(error);
                }
            });
        });
    </script>

    <script>
        function printItem(id) {

            const url = route('employee-salaries.payslip.download-view', {
                salarySheet: id
            });

            window.open(url, '_blank');
        }

        function downloadItem(id) {
            const url = route('employee-salaries.payslip.download', {
                salarySheet: id
            });
            window.open(url, '_blank');
        }
    </script>
    
    <script>
        'use strict';

        let tbl = $('#designationTable').DataTable({
            order: [],
            oLanguage: {
                'sEmptyTable': Lang.get('messages.common.no_data_available_in_table'),
                'sInfo': Lang.get('messages.common.data_base_entries'),
                sLengthMenu: Lang.get('messages.common.menu_entry'),
                sInfoEmpty: Lang.get('messages.common.no_entry'),
                sInfoFiltered: Lang.get('messages.common.filter_by'),
                sZeroRecords: Lang.get('messages.common.no_matching'),
            },
            processing: true,
            serverSide: true,
            ajax: {
                url: route('appointments.index'),
                beforeSend: function() {
                    startLoader();
                },
                complete: function() {
                    stopLoader();
                }
            },
            lengthMenu: [
                [10, 25, 50, 100, -1],
                [10, 25, 50, 100, "All"]
            ],
            pageLength: 100, // Default page length
            columns: [
                {
                    data: function(row) {
                        let date = new Date(row.appointment_date);
                        let formattedDate = date.toLocaleDateString('en-GB'); // dd/mm/yyyy
                        return formattedDate;
                    },
                    name: 'appointment_date',
                    className: 'text-center'
                },
                {
                    data: function(row) {
                        let time = new Date('1970-01-01T' + row.appointment_time); // assuming time is like "17:45:00"
                        let formattedTime = time.toLocaleTimeString('en-US', {
                            hour: '2-digit',
                            minute: '2-digit',
                            hour12: true
                        });
                        return formattedTime;
                    },
                    name: 'appointment_time',
                    className: 'text-center'
                },{
                    data: function(row) {
                        let element = document.createElement('textarea');
                        element.innerHTML = row.name;
                        return element.value;
                    },
                    name: 'name',
                }, {
                    data: function(row) {
                        let element = document.createElement('textarea');
                        return row.mobile;
                    },
                    name: 'mobile',
                },
                {
                    data: function(row) {
                        let element = document.createElement('textarea');
                        return row.email;
                    },
                    name: 'email',
                },
                
                {
                    data: function(row) {
                        let element = document.createElement('textarea');
                        return row.appointed_by;
                    },
                    name: 'appointed_by',
                },
                {
                    data: function(row) {
                        return renderActionButtons(row.id);
                    },
                    name: 'id',
                    width: '200px'
                }
            ],
            responsive: true,
            createdRow: function(row, data, dataIndex) {
                let today = new Date().toISOString().split('T')[0]; // Format: yyyy-mm-dd
                let appointmentDate = new Date(data.appointment_date).toISOString().split('T')[0];
            
                if (appointmentDate === today) {
                    $(row).css('background-color', '#f8d7da'); // Light red background
                    $(row).css('color', '#721c24'); // Optional: darker red text
                }
            },
        });

        $(document).on('click', '.edit-btn', function(event) {
            let did = $(event.currentTarget).data('id');
            const url = route('appointments.edit', did);
            window.location.href = url;
        });
        $(document).on('click', '.delete-btn', function(event) {
            let assetCateogryId = $(event.currentTarget).data('id');
            deleteItem(route('appointments.destroy', assetCateogryId), '#designationTable',
                '{{ __('messages.appointments.appointments') }}');
        });
    </script>

    <script>
        // Define messages for translations
        var messages = {
            delete: "{{ __('messages.common.delete') }}",
            edit: "{{ __('messages.common.edit') }}",
            view: "{{ __('messages.common.view') }}"
        };

        // Define permissions
        var permissions = {
            updateItem: "{{ auth()->user()->can('update_appointments') ? 'true' : 'false' }}",
            deleteItem: "{{ auth()->user()->can('delete_appointments') ? 'true' : 'false' }}",
            viewItem: "{{ auth()->user()->can('view_appointments') ? 'true' : 'false' }}"
        };

        // Function to render action buttons based on permissions
        function renderActionButtons(id) {
            let buttons = '';



            if (permissions.updateItem === 'true') {
                let editUrl = `{{ route('appointments.edit', ':id') }}`;
                editUrl = editUrl.replace(':id', id);
                buttons += `
                <a title="${messages.edit}" href="${editUrl}" class="btn btn-warning action-btn has-icon edit-btn" style="float:right;margin:2px;">
                    <i class="fa fa-edit"></i>
                </a>
            `;
            }
            if (permissions.viewItem === 'true') {
                let viewUrl = `{{ route('appointments.view', ':id') }}`;
                viewUrl = viewUrl.replace(':id', id);
                buttons += `
                <a title="${messages.view}" href="${viewUrl}" class="btn btn-info action-btn has-icon view-btn" style="float:right;margin:2px;">
                    <i class="fa fa-eye"></i>
                </a>
            `;
            }

            if (permissions.deleteItem === 'true') {
                buttons += `
                <a title="${messages.delete}" href="#" class="btn btn-danger action-btn has-icon delete-btn" data-id="${id}" style="float:right;margin:2px;">
                    <i class="fa fa-trash"></i>
                </a>
            `;
            }

            return buttons;
        }
    </script>
@endsection
