@extends('layouts.app')
@section('title')
    {{ __('messages.awards.view') }}
@endsection
@section('page_css')
    <link href="{{ asset('assets/css/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/bs4-summernote/summernote-bs4.css') }}">
@endsection
@section('content')
    <section class="section">
        <div class="section-header item-align-right">
            <h1>{{ __('messages.awards.view') }}</h1>
            <div class="section-header-breadcrumb float-right">
                <div class="card-header-action mr-3 select2-mobile-margin">
                </div>
            </div>
            <div class="float-right">
                <a href="{{ route('awards.index') }}" class="btn btn-primary form-btn">{{ __('messages.awards.list') }}</a>

            </div>
        </div>
        <div class="section-body">
            <div class="card">
                <div class="card-body">
                    <div class="alert alert-danger d-none" id="validationErrorsBox"></div>
                    <div class="row">
                        <div class="form-group col-sm-12 col-md-4">
                            <strong>{{ Form::label('name', __('messages.awards.award_name')) }}</strong>
                            <p style="color: #555;">{{ $award->name }}</p>
                        </div>

                        <div class="form-group col-sm-12 col-md-4">
                            <strong>{{ Form::label('employee_id', __('messages.awards.employee')) }}</strong>
                            <p style="color: #555;">{{ $award->employee->name }}</p>
                        </div>

                        <div class="form-group col-sm-12 col-md-4">
                            <strong> {{ Form::label('gift', __('messages.awards.gift')) }}</strong>
                            <p style="color: #555;">{{ $award->gift }}</p>
                        </div>

                        <div class="form-group col-sm-12 col-md-4">
                            <strong>{{ Form::label('date', __('messages.awards.date')) }}</strong>
                            <p style="color: #555;">{{ $award->date }}</p>
                        </div>

                        <div class="form-group col-sm-12 col-md-4">
                            <strong>{{ Form::label('award_by', __('messages.awards.by')) }}</strong>
                            <p style="color: #555;">{{ $award->awardedBy->name }}</p>
                        </div>

                        <div class="form-group col-sm-12 col-md-12 mb-0">
                            <strong> {{ Form::label('description', __('messages.awards.description')) }}</strong>
                            <div style="color: #555;"> {!! $award->description !!}</div>
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
