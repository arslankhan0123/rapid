@extends('layouts.app')
@section('title')
    {{ __('View Timesheet Batch') }}
@endsection
@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{ __('View Timesheet Batch') }}</h1>
            <div class="section-header-breadcrumb">
                <a href="{{ route('casual-employee-timesheets.index') }}" class="btn btn-primary form-btn">
                    {{ __('Back') }}
                </a>
            </div>
        </div>
        <div class="section-body">
            <div class="card">
                <div class="card-body">
                    <!-- Batch Information -->
                    {{-- <div class="row mb-4">
                        <div class="col-12">
                            <h4 class="text-primary">{{ __('Batch Information') }}</h4>
                            <div class="row">
                                <div class="form-group col-sm-6">
                                    <strong>{{ __('Batch ID') }}:</strong>
                                    {{ $batchId }}
                                </div>
                                <div class="form-group col-sm-6">
                                    <strong>{{ __('Total Employees') }}:</strong>
                                    {{ $hourlyTimesheets->count() + $monthlyTimesheets->count() }}
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-sm-6">
                                    <strong>{{ __('Hourly Entries') }}:</strong>
                                    {{ $hourlyTimesheets->count() }}
                                </div>
                                <div class="form-group col-sm-6">
                                    <strong>{{ __('Monthly Entries') }}:</strong>
                                    {{ $monthlyTimesheets->count() }}
                                </div>
                            </div>
                        </div>
                    </div> --}}
                    <div class="row mb-4">
                        <div class="col-12">
                            <h4 class="text-primary">{{ __('Batch Information') }}</h4>
                            <div class="row">
                                <div class="form-group col-sm-6">
                                    <strong>{{ __('Batch ID') }}:</strong>
                                    {{ $batchId }}
                                </div>
                                <div class="form-group col-sm-6">
                                    <strong>{{ __('Total Employees') }}:</strong>
                                    {{ $hourlyTimesheets->count() + $monthlyTimesheets->count() }}
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-sm-6">
                                    <strong>{{ __('Client Name') }}:</strong>
                                    {{ $clientName }}
                                </div>
                                <div class="form-group col-sm-6">
                                    <strong>{{ __('Project Name') }}:</strong>
                                    {{ $projectName }}
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-sm-6">
                                    <strong>{{ __('Hourly Entries') }}:</strong>
                                    {{ $hourlyTimesheets->count() }}
                                </div>
                                <div class="form-group col-sm-6">
                                    <strong>{{ __('Monthly Entries') }}:</strong>
                                    {{ $monthlyTimesheets->count() }}
                                </div>
                            </div>
                        </div>
                    </div>


                    <!-- Hourly Timesheets Section -->
                    @if ($hourlyTimesheets->count() > 0)
                        <div class="row mb-4">
                            <div class="col-12">
                                <h4 class="text-info">{{ __('Hourly Timesheets') }}</h4>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>{{ __('Iqama No') }}</th>
                                                <th>{{ __('Employee Name') }}</th>
                                                <th>{{ __('Designation') }}</th>
                                                <th>{{ __('Working Hours') }}</th>
                                                <th>{{ __('Rate/Hour') }}</th>
                                                <th>{{ __('Total Amount') }}</th>
                                                <th>{{ __('Safety') }}</th>
                                                <th>{{ __('Absent Deduction') }}</th>
                                                <th>{{ __('Advance') }}</th>
                                                <th>{{ __('Net Amount') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($hourlyTimesheets as $index => $timesheet)
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td>{{ $timesheet->casualEmployee->iqama_no }}</td>
                                                    <td>{{ $timesheet->casualEmployee->name }}</td>
                                                    <td>{{ $timesheet->casualEmployee->designations }}</td>
                                                    <td class="text-right">
                                                        {{ number_format($timesheet->working_hours, 2) }}</td>
                                                    <td class="text-right">SAR
                                                        {{ number_format($timesheet->rate_per_hour, 2) }}</td>
                                                    <td class="text-right">SAR
                                                        {{ number_format($timesheet->total_amount, 2) }}</td>
                                                    <td class="text-right">SAR {{ number_format($timesheet->safety, 2) }}
                                                    </td>
                                                    <td class="text-right">SAR
                                                        {{ number_format($timesheet->absent_deduction, 2) }}</td>
                                                    <td class="text-right">SAR {{ number_format($timesheet->advance, 2) }}
                                                    </td>
                                                    <td class="text-right font-weight-bold">SAR
                                                        {{ number_format($timesheet->net_amount, 2) }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr class="font-weight-bold">
                                                <td colspan="4" class="text-right">Total:</td>
                                                <td class="text-right">
                                                    {{ number_format($hourlyTimesheets->sum('working_hours'), 2) }}</td>
                                                <td></td>
                                                <td class="text-right">SAR
                                                    {{ number_format($hourlyTimesheets->sum('total_amount'), 2) }}</td>
                                                <td class="text-right">SAR
                                                    {{ number_format($hourlyTimesheets->sum('safety'), 2) }}</td>
                                                <td class="text-right">SAR
                                                    {{ number_format($hourlyTimesheets->sum('absent_deduction'), 2) }}</td>
                                                <td class="text-right">SAR
                                                    {{ number_format($hourlyTimesheets->sum('advance'), 2) }}</td>
                                                <td class="text-right">SAR
                                                    {{ number_format($hourlyTimesheets->sum('net_amount'), 2) }}</td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Monthly Timesheets Section -->
                    @if ($monthlyTimesheets->count() > 0)
                        <div class="row mb-4">
                            <div class="col-12">
                                <h4 class="text-success">{{ __('Monthly Timesheets') }}</h4>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>{{ __('Iqama No') }}</th>
                                                <th>{{ __('Employee Name') }}</th>
                                                <th>{{ __('Designation') }}</th>
                                                <th>{{ __('Total Days') }}</th>
                                                <th>{{ __('Overtime') }}</th>
                                                <th>{{ __('Basic Salary') }}</th>
                                                <th>{{ __('OT Amount') }}</th>
                                                <th>{{ __('Safety') }}</th>
                                                <th>{{ __('Absent Deduction') }}</th>
                                                <th>{{ __('Advance') }}</th>
                                                <th>{{ __('Net Amount') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($monthlyTimesheets as $index => $timesheet)
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td>{{ $timesheet->casualEmployee->iqama_no }}</td>
                                                    <td>{{ $timesheet->casualEmployee->name }}</td>
                                                    <td>{{ $timesheet->casualEmployee->designations }}</td>
                                                    <td class="text-right">{{ $timesheet->total_days }}</td>
                                                    <td class="text-right">{{ number_format($timesheet->overtime, 2) }}
                                                    </td>
                                                    <td class="text-right">SAR
                                                        {{ number_format($timesheet->basic_salary, 2) }}</td>
                                                    <td class="text-right">SAR
                                                        {{ number_format($timesheet->ot_amount, 2) }}</td>
                                                    <td class="text-right">SAR {{ number_format($timesheet->safety, 2) }}
                                                    </td>
                                                    <td class="text-right">SAR
                                                        {{ number_format($timesheet->absent_deduction, 2) }}</td>
                                                    <td class="text-right">SAR {{ number_format($timesheet->advance, 2) }}
                                                    </td>
                                                    <td class="text-right font-weight-bold">SAR
                                                        {{ number_format($timesheet->net_amount, 2) }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr class="font-weight-bold">
                                                <td colspan="4" class="text-right">Total:</td>
                                                <td class="text-right">{{ $monthlyTimesheets->sum('total_days') }}</td>
                                                <td class="text-right">
                                                    {{ number_format($monthlyTimesheets->sum('overtime'), 2) }}</td>
                                                <td class="text-right">SAR
                                                    {{ number_format($monthlyTimesheets->sum('basic_salary'), 2) }}</td>
                                                <td class="text-right">SAR
                                                    {{ number_format($monthlyTimesheets->sum('ot_amount'), 2) }}</td>
                                                <td class="text-right">SAR
                                                    {{ number_format($monthlyTimesheets->sum('safety'), 2) }}</td>
                                                <td class="text-right">SAR
                                                    {{ number_format($monthlyTimesheets->sum('absent_deduction'), 2) }}
                                                </td>
                                                <td class="text-right">SAR
                                                    {{ number_format($monthlyTimesheets->sum('advance'), 2) }}</td>
                                                <td class="text-right">SAR
                                                    {{ number_format($monthlyTimesheets->sum('net_amount'), 2) }}</td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Batch Summary -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <h4 class="text-primary">{{ __('Batch Summary') }}</h4>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <h5 class="card-title">{{ __('Financial Summary') }}</h5>
                                            <table class="table table-sm table-borderless">
                                                <tr>
                                                    <td><strong>Total Hourly Amount:</strong></td>
                                                    <td class="text-right">SAR
                                                        {{ number_format($hourlyTimesheets->sum('net_amount'), 2) }}</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Total Monthly Amount:</strong></td>
                                                    <td class="text-right">SAR
                                                        {{ number_format($monthlyTimesheets->sum('net_amount'), 2) }}</td>
                                                </tr>
                                                <tr class="font-weight-bold">
                                                    <td><strong>Grand Total:</strong></td>
                                                    <td class="text-right text-success">SAR
                                                        {{ number_format($hourlyTimesheets->sum('net_amount') + $monthlyTimesheets->sum('net_amount'), 2) }}
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <h5 class="card-title">{{ __('Employee Summary') }}</h5>
                                            <table class="table table-sm table-borderless">
                                                <tr>
                                                    <td><strong>Hourly Employees:</strong></td>
                                                    <td class="text-right">{{ $hourlyTimesheets->count() }}</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Monthly Employees:</strong></td>
                                                    <td class="text-right">{{ $monthlyTimesheets->count() }}</td>
                                                </tr>
                                                <tr class="font-weight-bold">
                                                    <td><strong>Total Employees:</strong></td>
                                                    <td class="text-right text-primary">
                                                        {{ $hourlyTimesheets->count() + $monthlyTimesheets->count() }}</td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Created Information -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h5 class="card-title">{{ __('Batch Information') }}</h5>
                                    <div class="row">
                                        <div class="form-group col-sm-6">
                                            <strong>{{ __('Batch Created At') }}:</strong>
                                            @if ($hourlyTimesheets->count() > 0)
                                                {{ $hourlyTimesheets->first()->created_at->format('d M, Y h:i A') }}
                                            @elseif($monthlyTimesheets->count() > 0)
                                                {{ $monthlyTimesheets->first()->created_at->format('d M, Y h:i A') }}
                                            @endif
                                        </div>
                                        <div class="form-group col-sm-6">
                                            <strong>{{ __('Last Updated') }}:</strong>
                                            @if ($hourlyTimesheets->count() > 0)
                                                {{ $hourlyTimesheets->first()->updated_at->format('d M, Y h:i A') }}
                                            @elseif($monthlyTimesheets->count() > 0)
                                                {{ $monthlyTimesheets->first()->updated_at->format('d M, Y h:i A') }}
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('page_scripts')
    <style>
        .table td,
        .table th {
            vertical-align: middle;
        }

        .card-title {
            border-bottom: 2px solid #007bff;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
    </style>
@endsection
