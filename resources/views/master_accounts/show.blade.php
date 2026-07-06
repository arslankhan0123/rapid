@extends('layouts.app')
@section('title')
    {{ __('messages.master_accounts.view_master_account') }}
@endsection
@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{ __('messages.master_accounts.view_master_account') }}</h1>
            <div class="section-header-breadcrumb">
                <a href="{{ route('master-accounts.index') }}" class="btn btn-primary form-btn float-right">Back</a>
            </div>
        </div>
        <div class="section-body">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>{{ __('messages.master_accounts.name') }}:</label>
                                <p class="form-control-static">{{ $masterAccount->name }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>{{ __('messages.master_accounts.account_number') }}:</label>
                                <p class="form-control-static">{{ $masterAccount->account_number ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>{{ __('messages.master_accounts.account_level') }}:</label>
                                <p class="form-control-static">{{ $masterAccount->account_level_label }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>{{ __('messages.master_accounts.account_type') }}:</label>
                                <p class="form-control-static">{{ $masterAccount->account_type }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>{{ __('messages.master_accounts.report_type') }}:</label>
                                <p class="form-control-static">{{ $masterAccount->report_type ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>{{ __('messages.master_accounts.amount_type') }}:</label>
                                <p class="form-control-static">{{ $masterAccount->amount_type ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>{{ __('messages.master_accounts.description') }}:</label>
                                <div class="form-control-static">{!! $masterAccount->description !!}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
