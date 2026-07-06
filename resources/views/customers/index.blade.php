@extends('layouts.app')
@section('title')
    {{ __('messages.customer.customers') }}
@endsection
@section('css')
    @livewireStyles
@endsection
@section('page_css')
    <link rel="stylesheet" href="{{ mix('assets/css/customers/customers.css') }}">
@endsection
@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{ __('messages.customer.customers') }}</h1>
            <div class="section-header-breadcrumb">
                @can('create_customers')
                    <a href="{{ route('customers.create') }}"
                        class="btn btn-primary form-btn float-right-mobile">{{ __('messages.common.add') }}

                    </a>
                @endcan

            </div>
        </div>
        <div class="section-body">
            @include('flash::message')
            <div class="card">
                <div class="card-body">
                    @livewire('customers')
                </div>
            </div>
        </div>
    </section>
@endsection
@section('scripts')
    <script src="{{ asset('vendor/livewire/livewire.js') }}"></script>
    @include('livewire.livewire-turbo')
    <script src="{{ mix('assets/js/customers/customers.js') }}"></script>
@endsection
