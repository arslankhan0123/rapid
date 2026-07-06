@extends('layouts.app')
@section('title')
    {{ __('messages.shifts.view') }}
@endsection
@section('page_css')
    <link href="{{ asset('assets/css/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ asset('assets/css/bs4-summernote/summernote-bs4.css') }}">
@endsection
@section('content')
    <section class="section">
        <div class="section-header item-align-right">
            <h1>{{ __('messages.shifts.view') }}</h1>
            <div class="section-header-breadcrumb float-right">
                <div class="card-header-action mr-3 select2-mobile-margin">
                </div>
            </div>
            <div class="float-right">
                <a href="{{ route('shifts.index') }}" class="btn btn-primary form-btn">List</a>
            </div>
        </div>
        <div class="section-body">
            <div class="card">
                <div class="card-body">
                    <div class="modal-content">

                        <div class="modal-body">
                            <div class="alert alert-danger d-none" id="validationErrorsBox"></div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <strong>{{ Form::label('name', __('messages.shifts.name')) }}</strong>
                                        <p style="color: #555;">{{ $shift->name }}</p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <strong>{{ Form::label('color', __('messages.shifts.color')) }}</strong>
                                        <div
                                            style="width: 30px; height: 30px; border-radius: 10%; background-color: {{ $shift->color }};">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6 col-sm-12">
                                    <div class="form-group">
                                        <strong>{{ Form::label('shift_start_time', __('messages.shifts.shift_start_time')) }}</strong>
                                        <p style="color: #555;">
                                            {{ \Carbon\Carbon::parse($shift->shift_start_time)->format('h:i A') }}
                                        </p>
                                    </div>
                                </div>
                                <div class="col-md-6 col-sm-12">
                                    <div class="form-group">
                                        <strong>
                                            {{ Form::label('shift_end_time', __('messages.shifts.shift_end_time')) }}</strong>

                                        <p style="color: #555;">
                                            {{ \Carbon\Carbon::parse($shift->shift_end_time)->format('h:i A') }}
                                        </p>
                                    </div>
                                </div>

                                <div class="col-md-6 col-sm-12">
                                    <div class="form-group">
                                        <strong>{{ Form::label('lunch_start_time', __('messages.shifts.lunch_start_time')) }}</strong>

                                        <p style="color: #555;">
                                            {{ \Carbon\Carbon::parse($shift->lunch_start_time)->format(' h:i A') }}
                                        </p>
                                    </div>
                                </div>
                                <div class="col-md-6 col-sm-12">
                                    <div class="form-group">
                                        <strong>
                                            {{ Form::label('lunch_end_time', __('messages.shifts.lunch_end_time')) }}
                                        </strong>

                                        <p style="color: #555;">
                                            {{ \Carbon\Carbon::parse($shift->lunch_end_time)->format(' h:i A') }}
                                        </p>
                                    </div>
                                </div>
                                <div class="form-group col-md-12">
                                    <strong>{{ Form::label('description', __('messages.shifts.description')) }}</strong>
                                    {!! $shift->description !!}
                                </div>
                            </div>

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
