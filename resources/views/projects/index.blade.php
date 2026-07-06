@extends('layouts.app')
@section('title')
    {{ __('messages.projects') }}
@endsection
@section('page_css')
    <link href="{{ asset('assets/css/owl.carousel.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ mix('assets/css/projects/projects.css') }}" rel="stylesheet" type="text/css" />
@endsection
@section('css')
    @livewireStyles
@endsection
@section('content')
    <div class="section">
        <div class="section-header mobile-sec-header">
            <h1>{{ __('messages.projects') }}</h1>
            <div class="section-header-breadcrumb float-right">

                <div class="card-header-action mr-3" style="width:180px;">
                    {{ Form::select('customer_id', $customers ?? [], null, [
                        'id' => 'filterCustomer',
                        'class' => 'form-control select2',
                        'placeholder' => __('messages.placeholder.select_customer'),
                    ]) }}
                </div>

                <div class="card-header-action mr-3 select2-mobile-margin" style="width:180px;">
                    {{ Form::select('branches', $usersBranches ?? [], null, [
                        'id' => 'filterBranch',
                        'class' => 'form-control select2',
                        'placeholder' => __('messages.placeholder.branches'),
                    ]) }}
                </div>

                <div class="card-header-action mr-3" style="width:180px;">
                    {{ Form::select('status', $billingType, null, [
                        'id' => 'billing_type',
                        'class' => 'form-control',
                        'placeholder' => __('messages.placeholder.select_billing_type'),
                    ]) }}
                </div>
            </div>
            <div class="float-right mr-3" style="width:180px;">
                {{ Form::select('status', $statusArr, null, [
                    'id' => 'filter_status',
                    'class' => 'form-control',
                    'placeholder' => __('messages.placeholder.select_status'),
                ]) }}
            </div>
            <div class="float-right">
                @can('create_projects')
                    <a href="{{ route('projects.create') }}" class="btn btn-primary form-btn">
                        {{ __('messages.common.add') }}
                    </a>
                @endcan
            </div>
        </div>

        <div class="section-body">
            @include('flash::message')
            <div class="card">
                <div class="card-body">
                    @livewire('projects')
                </div>
            </div>
        </div>
    </div>
@endsection
@section('page_scripts')
    <script src="{{ asset('assets/js/owl.carousel.min.js') }}"></script>
@endsection
@section('scripts')
    <script src="{{ asset('vendor/livewire/livewire.js') }}"></script>
    @include('livewire.livewire-turbo')
    <script>
        let customerId = null;
    </script>
    <script src="{{ url('assets/js/projects/projects.js') }}"></script>
    <script src="{{ mix('assets/js/status-counts/status-counts.js') }}"></script>
@endsection
