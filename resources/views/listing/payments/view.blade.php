@extends('layouts.app')
@section('title')
    View Payment
@endsection
@section('page_css')
    <link href="{{ asset('assets/css/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ asset('assets/css/bs4-summernote/summernote-bs4.css') }}">
@endsection
@section('content')
    <section class="section">
        <div class="section-header item-align-right">
            <h1> View Payment</h1>
            <div class="section-header-breadcrumb float-right">
                <div class="card-header-action mr-3 select2-mobile-margin">
                </div>
            </div>
            <div class="float-right">
                <a href="{{ route('payments.list.index') }}"
                    class="btn btn-primary form-btn">{{ __('messages.task-assign.list') }} </a>
            </div>
        </div>
        <div class="section-body">
            <div class="card">
                <div class="card-body">

                    <div class="modal-body">

                        <div class="row">

                            <div class="form-group col-sm-6">
                                {{ Form::label('payment_mode', __('messages.invoice.invoice_number') ) }}
                                <p>{{ $payment->invoice->invoice_number ?? '' }}</p>
                            </div>

                            <div class="form-group col-sm-6">
                                {{ Form::label('amount_received', __('messages.payment.amount_received') ) }}
                                <p>{{ $payment->amount_received ?? '' }}</p>
                            </div>

                            <div class="form-group col-sm-6">
                                {{ Form::label('payment_date', __('messages.payment.payment_date') ) }}
                                <p>{{ \Carbon\Carbon::parse($payment->payment_date)->format('d-m-Y') ?? '' }}
                                </p>
                            </div>

                            <div class="form-group col-sm-6">
                                {{ Form::label('payment_mode', __('messages.payment.payment_mode') ) }}
                                <p>{{ $payment->paymentMode->name ?? '' }}</p>
                            </div>

                            <div class="form-group col-sm-6">
                                {{ Form::label('transaction_id', __('messages.payment.transaction_id') ) }}
                                <p>{{ $payment->transaction_id ?? '' }}</p>
                            </div>

                            <div class="form-group col-sm-12 mb-2">
                                {{ Form::label('note', __('messages.payment.note') ) }}

                                {!! $payment->note != null ? html_entity_decode($payment->note) : __('messages.common.n/a') !!}
                            </div>

                        </div>

                    </div>

                </div>
            </div>
        </div>
    </section>
@endsection
@section('page_scripts')
@endsection
@section('scripts')
@endsection
