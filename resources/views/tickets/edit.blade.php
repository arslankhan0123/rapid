@extends('layouts.app')
@section('title')
    {{ __('messages.ticket.edit_ticket') }}
@endsection
@section('page_css')
    <link href="{{ asset('assets/css/select2.min.css') }}" rel="stylesheet" type="text/css"/>
    <link rel="stylesheet" href="{{ asset('assets/css/bs4-summernote/summernote-bs4.css') }}">
@endsection
@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{ __('messages.ticket.edit_ticket') }}</h1>
            <div class="section-header-breadcrumb">
                <a href="javascript:void(0)" class="btn btn-primary form-btn mr-3 float-right-mobile text-nowrap"
                   id="ticketContactBtn">
                    <span id="ticketContact">{{__('messages.ticket.ticket_without_contact')}}</span> &nbsp;<i
                            class="fas fa-envelope"
                            id="ticketContactIcon"></i>
                </a>
            </div>
            <div class="float-right">
                <a href="{{ url()->previous() }}" class="btn btn-primary form-btn float-right-mobile">
                    {{ __('messages.common.back') }}
                </a>
            </div>
        </div>
        <div class="section-body">
            @include('layouts.errors')
            <div class="card">
                <div class="card-body">
                    {{ Form::model($ticket, ['route' => ['ticket.update', $ticket->id], 'id' => 'editTicket' ,'method' => 'put', 'files' => true]) }}

                    @include('tickets.edit_fields')

                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </section>
    @include('tags.common_tag_modal')
    @include('tickets.ticket_priority_modal')
    @include('departments.add_modal')
    @include('services.add_modal')
    @include('tickets.ticket_predefined_modal')
    @include('tickets.ticket_status_modal')
@endsection
@section('page_scripts')
    <script src="{{ asset('assets/js/select2.min.js') }}"></script>
    <script src="{{ mix('assets/js/bs4-summernote/summernote-bs4.js') }}"></script>
@endsection
@section('scripts')
    <script>
        let ticketAttachmentUrl = "{{ url('admin/ticket-attachment-delete') }}";
    </script>
    <script src="{{mix('assets/js/tickets/create-edit.js')}}"></script>
@endsection
