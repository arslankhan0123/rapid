@extends('clients.layouts.app')
@section('title')
    {{ __('messages.projects') }}
@endsection
@section('page_css')
    <link href="{{ asset('assets/css/owl.carousel.min.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ mix('assets/css/clients/projects/projects.css') }}" rel="stylesheet" type="text/css"/>
@endsection
@section('css')
    @livewireStyles
@endsection
@section('content')
    <section class="section">
        <div class="section-header item-align-right">
            <h1>{{ __('messages.projects') }}</h1>
            <div class="section-header-breadcrumb">
                <div class="card-header-action mr-3 float-right">
                    {{Form::select('status', $billingType, null, ['id' => 'billing_type', 'class' => 'form-control', 'placeholder' =>__('messages.placeholder.select_billing_type')]) }}
                </div>
            </div>
            <div class="float-right mr-3">
                {{Form::select('status', $statusArr, null, ['id' => 'filter_status', 'class' => 'form-control', 'placeholder' => __('messages.placeholder.select_status')]) }}
            </div>
        </div>
        <div class="section-body">
            @include('flash::message')
            <div class="card">
                <div class="card-body">
                    @livewire('clients.projects')
                </div>
            </div>
        </div>
    </section>
@endsection
@section('page_scripts')
    <script src="{{ asset('assets/js/owl.carousel.min.js') }}"></script>
@endsection
@section('scripts')
    <script src="{{ asset('vendor/livewire/livewire.js') }}"></script>
    @include('livewire.livewire-turbo')
    <script>
        let projectUrl = "{{ route('clients.projects.index') }}";
        let customerId = null;
    </script>
    <script src="{{mix('assets/js/clients/projects/projects.js')}}"></script>
    <script src="{{mix('assets/js/status-counts/status-counts.js')}}"></script>
@endsection
