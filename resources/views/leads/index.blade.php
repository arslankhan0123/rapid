@extends('layouts.app')
@section('title')
    {{ __('messages.leads') }}
@endsection
@section('css')
    @livewireStyles
@endsection
@section('page_css')
    <link href="{{ asset('assets/css/owl.carousel.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ mix('assets/css/leads/leads.css') }}" rel="stylesheet" type="text/css" />
@endsection
@section('content')
    <section class="section">
        <div class="section-header mobile-sec-hdr">
            <h1>{{ __('messages.leads') }}</h1>
            <div class="section-header-breadcrumb float-right">
                <div class="card-header-action mr-2 ipad-margin-left">
                    {{ Form::select('status', $statusArr, null, ['id' => 'filter_status', 'class' => 'form-control', 'placeholder' => __('messages.placeholder.select_status')]) }}
                </div>
            </div>
            <div class="float-right mr-2">
                {{ Form::select('lead_source', $leadSourceArr, null, ['id' => 'leadSourceId', 'class' => 'form-control', 'placeholder' => __('messages.placeholder.select_lead_source')]) }}
            </div>
            <div class="float-right d-flex flex-lg-nowrap flex-wrap">
                <div class="custom-sm-width">
                    <a href="{{ route('leads.kanbanList') }}"
                        class="btn btn-warning form-btn mr-2 text-nowrap mt-lg-0 mt-2">{{ __('messages.kanban_view') }}
                    </a>
                </div>
                <div>
                    <a href="{{ route('leads.leadConvertChart') }}"
                        class="btn btn-info form-btn mr-2 mt-lg-0 mt-2">{{ __('messages.lead.chart') }}
                    </a>
                </div>
                <div>
                    @can('create_leads')
                        <a href="{{ route('leads.create') }}"
                            class="btn btn-primary form-btn text-nowrap mt-lg-0 mt-2 mr-2">{{ __('messages.common.add') }}
                            <i class="fas fa-plus"></i>
                        </a>
                    @endcan

                </div>
            </div>
        </div>
        <div class="section-body">
            @include('flash::message')
            <div class="card">
                <div class="card-body">
                    @livewire('leads')
                </div>
            </div>
        </div>
        @include('leads.templates.templates')
    </section>
@endsection
@section('page_scripts')
    <script src="{{ asset('assets/js/owl.carousel.min.js') }}"></script>
@endsection
@section('scripts')
    <script src="{{ asset('vendor/livewire/livewire.js') }}"></script>
    @include('livewire.livewire-turbo')
    <script>
        let leadUrl = "{{ route('leads.index') }}/";
        let ownerId = null;
        let ownerType = null;
        let currentCurrentSymbol = "{{ getCurrencyClass() }}";
    </script>
    <script src="{{ mix('assets/js/custom/get-price-format.js') }}"></script>
    <script src="{{ mix('assets/js/status-counts/status-counts.js') }}"></script>
    <script src="{{ mix('assets/js/leads/leads.js') }}"></script>
@endsection
