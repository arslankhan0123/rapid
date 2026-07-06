@extends('layouts.app')
@section('title')
    {{ __('messages.tickets') }}
@endsection
@section('page_css')
    <link href="{{ asset('assets/css/owl.carousel.min.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ mix('assets/css/tickets/tickets.css') }}" rel="stylesheet" type="text/css"/>
@endsection
@section('css')
    @livewireStyles
@endsection
@section('content')
    <section class="section">
        <div class="section-header ticket-sec-mbl-hdr">
            <h1>{{ __('messages.tickets') }}</h1>
            <div class="section-header-breadcrumb float-right">
                <div class="mr-3 ipad-margin-left">
                    {{Form::select('status', $statusArr, null, ['id' => 'ticketStatus', 'class' => 'form-control', 'placeholder' => __('messages.placeholder.select_status')]) }}
                </div>
            </div>
            <div class="float-right mr-3">
                {{ Form::select('priority', $ticketPriorityArr, null, ['class' => 'form-control', 'id' => 'ticketPriorityId', 'placeholder' => __('messages.placeholder.select_ticket_priority')]) }}
            </div>
            <div class="float-right d-flex">
                <a href="{{ route('tickets.kanbanList') }}"
                   class="btn btn-warning form-btn mr-2 text-nowrap">{{ __('messages.kanban_view') }}
                </a>
                <a href="{{ route('ticket.create') }}"
                   class="btn btn-primary form-btn text-nowrap">{{ __('messages.common.add') }}
                    </a>
            </div>
        </div>
        <div class="section-body">
            @include('flash::message')
            <div class="card">
                <div class="card-body">
                    @livewire('tickets')
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
        let customerId = null;
        let downloadAttachmentUrl = "{{ url('admin/tickets-attachment-download') }}";
    </script>
    <script src="{{ mix('assets/js/status-counts/status-counts.js') }}"></script>
    <script src="{{ mix('assets/js/tickets/tickets.js') }}"></script>
@endsection
