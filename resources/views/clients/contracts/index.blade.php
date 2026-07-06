@extends('clients.layouts.app')

@section('title')
    {{ __('messages.contracts') }}
@endsection
@section('page_css')
    <link href="{{ mix('assets/css/clients/contracts/contracts.css') }}" rel="stylesheet" type="text/css"/>
@endsection
@section('css')
    @livewireStyles
@endsection
@section('content')
    <section class="section">
        <div class="section-header item-align-right">
            <h1>{{ __('messages.contracts') }}</h1>
            <div class="section-header-breadcrumb float-right">
                <div class="card-header-action mr-3 select2-mobile-margin">
                    {{Form::select('type', $typeArr, null, ['id' => 'filterType', 'class' => 'form-control', 'placeholder' => __('messages.placeholder.select_contract_type')]) }}
                </div>
            </div>
            <div class="float-right">
                <a href="{{ route('contracts.contract-summary') }}"
                   class="btn btn-warning">{{ __('messages.contract.contract_summary') }}</a>
            </div>
        </div>
        <div class="section-body">
            <div class="card">
                <div class="card-body">
                    @livewire('clients.contracts')
                </div>
            </div>
        </div>
    </section>
@endsection
@section('page_scripts')
@endsection
@section('scripts')
    <script src="{{ asset('vendor/livewire/livewire.js') }}"></script>
    @include('livewire.livewire-turbo')
    <script>
        let contractUrl = "{{ route('clients.contracts.index') }}";
        let viewAsCustomer = 'view-as-customer';
    </script>
    <script src="{{ mix('assets/js/clients/contracts/contracts.js') }}"></script>
@endsection
