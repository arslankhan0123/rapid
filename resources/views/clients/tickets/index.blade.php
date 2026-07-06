@extends('clients.layouts.app')
@section('title')
    {{ __('messages.tickets') }}
@endsection
@section('page_css')
    <link href="{{ asset('assets/css/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css"/>
@endsection
@section('content')
    <section class="section">
        <div class="section-header justify-content-between">
            <h1>{{ __('messages.tickets') }}</h1>
            <a href="{{ route('client.tickets.create') }}" class="btn btn-primary">{{ __('messages.common.add') }} <i
                        class="fa fa-plus"></i></a>
        </div>
        <div class="section-body">
            @include('flash::message')
            <div class="card">
                <div class="card-body">
                    @include('clients.tickets.table')
                </div>
            </div>
        </div>
    </section>
    @include('clients.tickets.templates.templates')
@endsection
@section('page_scripts')
    <script src="{{ asset('assets/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ mix('assets/js/custom/custom-datatable.js') }}"></script>
@endsection
@section('scripts')
    <script src="{{ mix('assets/js/clients/tickets/ticket.js') }}"></script>
@endsection
