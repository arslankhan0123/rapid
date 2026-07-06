@extends('layouts.app')
@section('title')
    {{ __('messages.contracts') }}
@endsection
@section('page_css')
    <link rel="stylesheet" href="{{ mix('assets/css/contracts/contracts.css') }}">
@endsection
@section('css')
    @livewireStyles
@endsection
@section('content')
    <section class="section">
        <div class="section-header contract-sec-header-mbl">
            <h1>{{ __('messages.contracts') }}</h1>
            <div class="section-header-breadcrumb float-right">
                <div class="card-header-action mr-2 select2-mobile-margin">
                    {{Form::select('type', $typeArr, null, ['id' => 'filterType', 'class' => 'form-control', 'placeholder' =>__('messages.placeholder.select_contract_type')]) }}
                </div>
            </div>
            <div class="float-right btn-mobile-d-unset">
                <a href="{{ route('contracts.contractSummary') }}"
                   class="btn btn-warning form-btn mr-2 text-nowrap btn-margin-bottom">{{ __('messages.contract.contract_summary') }}
                </a>
                <a href="{{ route('contracts.create') }}"
                   class="btn btn-primary form-btn text-nowrap btn-align-right">{{ __('messages.contract.add') }} <i
                            class="fas fa-plus"></i>
                </a>
            </div>
        </div>
        @include('flash::message')
        <div class="section-body">
            <div class="card">
                <div class="card-body">
                    @livewire('contracts')
                </div>
            </div>
        </div>
    </section>
@endsection
@section('scripts')
    <script src="{{ asset('vendor/livewire/livewire.js') }}"></script>
    @include('livewire.livewire-turbo')
    <script src="{{mix('assets/js/contracts/contracts.js')}}"></script>
@endsection
