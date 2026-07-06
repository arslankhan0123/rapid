@extends('layouts.app')
@section('title')
    {{ __('messages.company_loans.view_company_loan') }}
@endsection
@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{ __('messages.company_loans.view_company_loan') }}</h1>
            <div class="section-header-breadcrumb">
                <a href="{{ route('company-loans.index') }}" class="btn btn-primary form-btn float-right">
                    {{ __('messages.common.list') }}
                </a>
            </div>
        </div>
        <div class="section-body">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>{{ __('messages.company_loans.serial_no') }}:</label>
                                <p>{{ $companyLoan->id }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>{{ __('messages.company_loans.date') }}:</label>
                                <p>{{ \Carbon\Carbon::parse($companyLoan->date)->format('d M, Y') }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>{{ __('messages.company_loans.type') }}:</label>
                                <p>{{ ucfirst($companyLoan->type) }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>{{ __('messages.company_loans.name') }}:</label>
                                <p>{{ $companyLoan->name }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>{{ __('messages.company_loans.loan_amount') }}:</label>
                                <p>{{ number_format($companyLoan->loan_amount, 2) }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>{{ __('messages.company_loans.loan_received_date') }}:</label>
                                <p>{{ \Carbon\Carbon::parse($companyLoan->loan_received_date)->format('d M, Y') }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>{{ __('messages.company_loans.loan_refund_date') }}:</label>
                                <p>{{ \Carbon\Carbon::parse($companyLoan->loan_refund_date)->format('d M, Y') }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>{{ __('messages.company_loans.number_of_days') }}:</label>
                                <p>{{ $companyLoan->number_of_days }} Days</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
