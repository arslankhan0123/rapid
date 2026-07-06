@extends('layouts.app')
@section('title')
    {{ __('messages.lead_sources') }}
@endsection
@section('page_css')
    <link rel="stylesheet" href="{{ mix('assets/css/lead_sources/lead-sources.css') }}">
@endsection
@section('css')
    @livewireStyles
@endsection
@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{ __('messages.lead_sources') }}</h1>
            <div class="section-header-breadcrumb">
                <a href="#" class="btn btn-primary form-btn addLeadSourceModal float-right-mobile" data-toggle="modal"
                   data-target="#addModal">{{ __('messages.lead_source.add') }} </a>
            </div>
        </div>
        <div class="section-body">
            <div class="card">
                <div class="card-body">
                    @livewire('lead-sources')
                </div>
            </div>
        </div>
        @include('lead_sources.add_modal')
        @include('lead_sources.edit_modal')
    </section>
@endsection
@section('scripts')
    <script src="{{ asset('vendor/livewire/livewire.js') }}"></script>
    @include('livewire.livewire-turbo')
    <script src="{{mix('assets/js/lead-sources/lead-sources.js')}}"></script>
@endsection
