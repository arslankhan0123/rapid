@extends('layouts.app')
@section('title')
    {{ __('messages.holidays.view') }}
@endsection
@section('page_css')
    <link href="{{ asset('assets/css/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ asset('assets/css/bs4-summernote/summernote-bs4.css') }}">
@endsection
@section('content')
    <section class="section">
        <div class="section-header item-align-right">
            <h1>{{ __('messages.holidays.view') }}</h1>
            <div class="section-header-breadcrumb float-right">
                <div class="card-header-action mr-3 select2-mobile-margin">
                </div>
            </div>
            <div class="float-right">
                <a href="{{ route('holidays.index') }}"
                    class="btn btn-primary form-btn">{{ __('messages.holidays.list') }}</a>
            </div>
        </div>
        <div class="section-body">
            <div class="card">
                <div class="card-body">

                    <div class="alert alert-danger d-none" id="validationErrorsBox"></div>
                    <div class="row">
                        <div class="form-group col-sm-12 col-md-6">
                            <strong> {{ Form::label('name', __('messages.holidays.holiday_name')) }}</strong>
                            <p style="color: #555;">{{ $holiday->name }}</p>
                        </div>

                        <div class="form-group col-sm-12 col-md-6">
                            <strong>{{ Form::label('from_date', __('messages.holidays.from_date')) }}</strong>
                            <p style="color: #555;">{{ $holiday->from_date }}</p>
                        </div>

                        <div class="form-group col-sm-12 col-md-6">
                            <strong>{{ Form::label('end_date', __('messages.holidays.end_date')) }}</strong>
                            <p style="color: #555;">{{ $holiday->end_date }}</p>
                        </div>

                        <div class="form-group col-sm-12 col-md-6">
                            <strong> {{ Form::label('total_days', __('messages.holidays.total_days')) }}</strong>
                            <p style="color: #555;">{{ $holiday->days_count }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
@section('page_scripts')
    <script src="{{ asset('assets/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ mix('assets/js/custom/custom-datatable.js') }}"></script>
    <script src="{{ mix('assets/js/bs4-summernote/summernote-bs4.js') }}"></script>
    <script src="{{ mix('assets/js/select2.min.js') }}"></script>
@endsection
@section('scripts')
    <script src="{{ mix('assets/js/custom/input-price-format.js') }}"></script>

@endsection
