@extends('layouts.app')
@section('title')
    {{ __('messages.services') }}
@endsection
@section('page_css')
    <link href="{{ asset('assets/css/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css"/>
    <link rel="stylesheet" href="{{ mix('assets/css/services/services.css') }}">
@endsection
@section('css')

@endsection
@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{ __('messages.services') }}</h1>
            <div class="section-header-breadcrumb">
                <a href="#" class="btn btn-primary form-btn addServiceModal float-right-mobile" data-toggle="modal"
                   data-target="#addModal">{{ __('messages.service.add') }} </a>
            </div>
        </div>
        <div class="section-body">
            <div class="card">
                <div class="card-body">
                    @include('services.table')
                </div>
            </div>
        </div>
        @include('services.add_modal')
        @include('services.edit_modal')
        @include('services.templates.templates')
    </section>
@endsection
@section('page_scripts')
    <script src="{{ asset('assets/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ mix('assets/js/custom/custom-datatable.js') }}"></script>
@endsection
@section('scripts')
    <script src="{{mix('assets/js/services/services.js')}}"></script>
@endsection
