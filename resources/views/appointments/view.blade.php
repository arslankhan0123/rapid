@extends('layouts.app')
@section('title')
    {{ __('messages.appointments.view') }}
@endsection
@section('page_css')
    <link href="{{ asset('assets/css/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ asset('assets/css/bs4-summernote/summernote-bs4.css') }}">
@endsection
@section('content')
    <section class="section">
        <div class="section-header item-align-right">
            <h1>{{ __('messages.appointments.view') }}</h1>
            <div class="section-header-breadcrumb float-right">
                <div class="card-header-action mr-3 select2-mobile-margin">
                </div>
            </div>
            <div class="float-right">
                <a href="{{ route('appointments.index') }}"
                    class="btn btn-primary form-btn">{{ __('messages.appointments.list') }}</a>
            </div>
        </div>
        <div class="section-body">
            <div class="card">
                <div class="card-body">

                    <div class="alert alert-danger d-none" id="validationErrorsBox"></div>
                    <div class="row">
                        <div class="form-group col-sm-12 col-md-6">
                            <strong> {{ Form::label('name', __('messages.appointments.name')) }}</strong>
                            <p style="color: #555;">{{ $appointment->name }}</p>
                        </div>

                        <div class="form-group col-sm-12 col-md-6">
                            <strong>{{ Form::label('mobile', __('messages.appointments.mobile')) }}</strong>
                            <p style="color: #555;">{{ $appointment->mobile }}</p>
                        </div>
                        
                        <div class="form-group col-sm-12 col-md-6">
                            <strong>{{ Form::label('email', __('messages.appointments.email')) }}</strong>
                            <p style="color: #555;">{{ $appointment->email }}</p>
                        </div>
                        
                        <div class="form-group col-sm-12 col-md-6">
                            <strong>{{ Form::label('appointment_date', __('messages.appointments.appointment_date')) }}</strong>
                            <p style="color: #555;">{{ $appointment->appointment_date }}</p>
                        </div>
                        
                        <div class="form-group col-sm-12 col-md-6">
                            <strong>{{ Form::label('appointment_time', __('messages.appointments.appointment_time')) }}</strong>
                            <p style="color: #555;">{{ $appointment->appointment_time }}</p>
                        </div>
                        
                        <div class="form-group col-sm-12 col-md-6">
                            <strong>{{ Form::label('appointed_by', __('messages.appointments.appointed_by')) }}</strong>
                            <p style="color: #555;">
                                {{ $appointment->appointed_by ?? 'N/A' }}
                            </p>
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
