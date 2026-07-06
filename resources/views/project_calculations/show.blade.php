@extends('layouts.app')
@section('title')
    {{ __('messages.project_calculations.view_project_calculation') }}
@endsection
@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{ __('messages.project_calculations.view_project_calculation') }}</h1>
            <div class="section-header-breadcrumb">
                <a href="{{ route('project-calculations.index') }}" class="btn btn-primary form-btn">
                    {{ __('messages.common.back') }}
                </a>
            </div>
        </div>
        <div class="section-body">
            <div class="card">
                <div class="card-body">

                    <!-- Project Info -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>{{ __('messages.project_calculations.code') }}:</label>
                                <p>{{ $projectCalculation->code }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>{{ __('messages.project_calculations.name') }}:</label>
                                <p>{{ $projectCalculation->name }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>{{ __('messages.project_calculations.customer_name') }}:</label>
                                <p>{{ $projectCalculation->customer_name }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>{{ __('messages.project_calculations.address') }}:</label>
                                <p>{{ $projectCalculation->address }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Project Values -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <h4>{{ __('messages.project_calculations.project_values') }}</h4>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>{{ __('messages.project_calculations.category') }}</th>
                                            <th>{{ __('messages.project_calculations.amount') }}</th>
                                            <th>{{ __('messages.project_calculations.unit') }}</th>
                                            <th>{{ __('messages.project_calculations.quantity') }}</th>
                                            <th>{{ __('messages.project_calculations.gross') }}</th>
                                            <th>{{ __('messages.project_calculations.discount') }}</th>
                                            <th>{{ __('messages.project_calculations.net_amount') }}</th>
                                            <th>Percentage (%)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($projectCalculation->values as $index => $value)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ $value->category }}</td>
                                                <td class="text-right">{{ number_format($value->amount, 2) }}</td>
                                                <td class="text-right">{{ number_format($value->unit, 2) }}</td>
                                                <td class="text-right">{{ number_format($value->quantity, 2) }}</td>
                                                <td class="text-right">{{ number_format($value->gross_amount, 2) }}</td>
                                                <td class="text-right">{{ number_format($value->discount, 2) }}</td>
                                                <td class="text-right">{{ number_format($value->net_amount, 2) }}</td>
                                                <td class="text-right">{{ number_format($value->percentage, 2) }}%</td>
                                            </tr>
                                        @endforeach
                                        <tr class="font-weight-bold">
                                            <td colspan="5" class="text-right">Total:</td>
                                            <td class="text-right">
                                                {{ number_format($projectCalculation->values->sum('gross_amount'), 2) }}
                                            </td>
                                            <td class="text-right">
                                                {{ number_format($projectCalculation->values->sum('discount'), 2) }}</td>
                                            <td class="text-right">
                                                {{ number_format($projectCalculation->values->sum('net_amount'), 2) }}</td>
                                            <td class="text-right">100.00%</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Project Expenses -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <h4>{{ __('messages.project_calculations.project_expenses') }}</h4>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>{{ __('messages.project_calculations.name') }}</th>
                                            <th>{{ __('messages.project_calculations.amount') }}</th>
                                            <th>{{ __('messages.project_calculations.unit') }}</th>
                                            <th>{{ __('messages.project_calculations.quantity') }}</th>
                                            <th>{{ __('messages.project_calculations.total_amount') }}</th>
                                            <th>Percentage (%)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($projectCalculation->expenses as $index => $expense)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ $expense->name }}</td>
                                                <td class="text-right">{{ number_format($expense->amount, 2) }}</td>
                                                <td class="text-right">{{ number_format($expense->unit, 2) }}</td>
                                                <td class="text-right">{{ number_format($expense->quantity, 2) }}</td>
                                                <td class="text-right">{{ number_format($expense->total_amount, 2) }}</td>
                                                <td class="text-right">{{ number_format($expense->percentage, 2) }}%</td>
                                            </tr>
                                        @endforeach
                                        <tr class="font-weight-bold">
                                            <td colspan="5" class="text-right">Total Expenses:</td>
                                            <td class="text-right">
                                                {{ number_format($projectCalculation->expenses->sum('total_amount'), 2) }}
                                            </td>
                                            <td class="text-right">100.00%</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Project Commission -->
                    @if ($projectCalculation->commissions->count() > 0)
                        <div class="row mt-4">
                            <div class="col-12">
                                <h4>Project Commission</h4>
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Commission Amount</th>
                                                <th>Months</th>
                                                <th>Per Month</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($projectCalculation->commissions as $index => $commission)
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td class="text-right">
                                                        {{ number_format($commission->commission_amount, 2) }}</td>
                                                    <td class="text-right">{{ $commission->months }}</td>
                                                    <td class="text-right">{{ number_format($commission->per_month, 2) }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                            <tr class="font-weight-bold">
                                                <td colspan="3" class="text-right">Total Commission:</td>
                                                <td class="text-right">
                                                    {{ number_format($projectCalculation->commissions->sum('commission_amount'), 2) }}
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Project Insurance -->
                    @if ($projectCalculation->insurances->count() > 0)
                        <div class="row mt-4">
                            <div class="col-12">
                                <h4>Project Insurance</h4>
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Insurance Amount</th>
                                                <th>Months</th>
                                                <th>Per Month</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($projectCalculation->insurances as $index => $insurance)
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td class="text-right">
                                                        {{ number_format($insurance->insurance_amount, 2) }}</td>
                                                    <td class="text-right">{{ $insurance->months }}</td>
                                                    <td class="text-right">{{ number_format($insurance->per_month, 2) }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                            <tr class="font-weight-bold">
                                                <td colspan="3" class="text-right">Total Insurance:</td>
                                                <td class="text-right">
                                                    {{ number_format($projectCalculation->insurances->sum('insurance_amount'), 2) }}
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Final Calculation Summary -->
                    @php
                        $totalExpenses = $projectCalculation->expenses->sum('total_amount');
                        $totalCommissions = $projectCalculation->commissions->sum('commission_amount');
                        $totalInsurances = $projectCalculation->insurances->sum('insurance_amount');
                        $finalProjectValue = $projectCalculation->values->sum('net_amount');
                        $projectMonths = $projectCalculation->partners->first()->project_months ?? 1;
                        $finalProjectExpense = ($totalExpenses - $totalCommissions - $totalInsurances) * $projectMonths;
                        $profitAmount = $finalProjectValue - $finalProjectExpense;
                        $profitPerMonth = $profitAmount / $projectMonths;
                    @endphp
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card border-0 shadow-sm">
                                <div class="card-header bg-light py-3">
                                    <h6 class="mb-0 text-dark font-weight-bold">
                                        <i class="fas fa-chart-line me-2"></i>PROJECT SUMMARY
                                    </h6>
                                </div>
                                <div class="card-body p-4">
                                    <div class="row align-items-center">
                                        <!-- Duration -->
                                        <div class="col-lg-2 col-md-3 mb-3 mb-md-0">
                                            <label class="form-label small text-muted mb-1">Duration</label>
                                            <div class="input-group input-group-sm">
                                                <input type="text" class="form-control text-center font-weight-bold"
                                                    value="{{ $projectMonths }}" readonly>
                                                <span class="input-group-text">Months</span>
                                            </div>
                                        </div>

                                        <!-- Key Metrics -->
                                        <div class="col-lg-10 col-md-9">
                                            <div class="row text-center">
                                                <!-- Project Value -->
                                                <div class="col">
                                                    <div class="metric-item">
                                                        <div class="metric-value text-success">
                                                            {{ number_format($finalProjectValue, 2) }}</div>
                                                        <div class="metric-label">Project Value</div>
                                                    </div>
                                                </div>
                                                <!-- Expenses -->
                                                <div class="col">
                                                    <div class="metric-item">
                                                        <div class="metric-value text-danger">
                                                            {{ number_format($totalExpenses, 2) }}</div>
                                                        <div class="metric-label">Expenses</div>
                                                    </div>
                                                </div>
                                                <!-- Final Expenses -->
                                                <div class="col">
                                                    <div class="metric-item">
                                                        <div class="metric-value text-warning">
                                                            {{ number_format($finalProjectExpense, 2) }}</div>
                                                        <div class="metric-label">Final Expenses</div>
                                                    </div>
                                                </div>
                                                <!-- Profit -->
                                                <div class="col">
                                                    <div class="metric-item">
                                                        <div class="metric-value text-success h5">
                                                            {{ number_format($profitAmount, 2) }}</div>
                                                        <div class="metric-label font-weight-bold text-success">PROFIT
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Project Partners -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <h4>Project Calculation Partners</h4>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Name</th>
                                            <th>Profit Per Month</th>
                                            <th>Percentage (%)</th>
                                            <th>Per Partner</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($projectCalculation->partners as $index => $partner)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ $partner->name }}</td>
                                                <td class="text-right">{{ number_format($partner->amount, 2) }}</td>
                                                <td class="text-right">{{ number_format($partner->percentage, 2) }}%</td>
                                                <td class="text-right">{{ number_format($partner->per_partner, 2) }}</td>
                                            </tr>
                                        @endforeach
                                        @php
                                            $totalPartnerPercentage = $projectCalculation->partners->sum('percentage');
                                            $totalPartnerAmount = $projectCalculation->partners->sum('per_partner');
                                            $remainingPercentage = 100 - $totalPartnerPercentage;
                                            $remainingAmount = $profitAmount - $totalPartnerAmount;
                                        @endphp
                                        <tr class="font-weight-bold">
                                            <td colspan="3" class="text-right">Total Percentage:</td>
                                            <td class="text-right">{{ number_format($totalPartnerPercentage, 2) }}%</td>
                                            <td class="text-right">{{ number_format($totalPartnerAmount, 2) }}</td>
                                        </tr>
                                        <tr class="font-weight-bold">
                                            <td colspan="3" class="text-right">Remaining Profit:</td>
                                            <td class="text-right">{{ number_format($remainingPercentage, 2) }}%</td>
                                            <td class="text-right">{{ number_format($remainingAmount, 2) }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>
@endsection
