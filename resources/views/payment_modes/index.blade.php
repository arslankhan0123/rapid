@extends('layouts.app')
@section('title')
    {{ __('messages.payment_modes') }}
@endsection
@section('page_css')
    <link rel="stylesheet" href="{{ asset('assets/css/bs4-summernote/summernote-bs4.css') }}">
    <link rel="stylesheet" href="{{ mix('assets/css/payment_modes/payment-modes.css') }}">
@endsection
@section('css')
    @livewireStyles
@endsection
@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{ __('messages.payment_modes') }}</h1>
            <div class="section-header-breadcrumb float-right">
                <div class="card-header-action mr-3 select2-mobile-margin">
                    {{ Form::select('active', $activePaymentMode, null, ['id' => 'filterActivePaymentMode', 'class' => 'form-control','placeholder' => __('messages.placeholder.select_status')]) }}
                </div>
            </div>
            <div class="float-right">
                <a href="#" class="btn btn-primary form-btn" data-toggle="modal"
                   data-target="#addModal">{{ __('messages.common.add') }} </a>
            </div>
        </div>
        <div class="section-body">
            <div class="card">
                <div class="card-body">
                    @livewire('payment-modes')
                </div>
            </div>
        </div>
    </section>
    @include('payment_modes.add_modal')
    @include('payment_modes.edit_modal')
    @include('payment_modes.show_modal')
@endsection
@section('page_scripts')
    <script src="{{ mix('assets/js/bs4-summernote/summernote-bs4.js') }}"></script>
@endsection
@section('scripts')
    <script src="{{ asset('vendor/livewire/livewire.js') }}"></script>
    @include('livewire.livewire-turbo')
    <script src="{{ mix('assets/js/payment-modes/payment-modes.js') }}"></script>
@endsection
