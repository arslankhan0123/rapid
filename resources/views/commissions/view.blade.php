@extends('layouts.app')
@section('title')
    {{ __('messages.commissions.view') }}
@endsection
@section('page_css')
    <link href="{{ asset('assets/css/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
@endsection
@section('content')
    <section class="section">
        <div class="section-header item-align-right">
            <h1>{{ __('messages.commissions.view') }}</h1>
            <div class="section-header-breadcrumb float-right">
                <div class="card-header-action mr-3 select2-mobile-margin">
                </div>
            </div>
            <div class="float-right">
                <a href="{{ route('commissions.index') }}"
                    class="btn btn-primary form-btn">{{ __('messages.commissions.list') }}</a>
            </div>
        </div>
        <div class="section-body">
            <div class="card">
                <div class="card-body">

                    <div class="alert alert-danger d-none" id="validationErrorsBox"></div>
                    <div class="row">

                        <div class="form-group col-sm-12 col-md-6 ">
                            {{ Form::label('description', __('messages.commissions.employee_number') ) }}
                             <p style="color: #555;">{{ $commission->employee->id }}</p>

                        </div>
                        <div class="form-group col-sm-12 col-md-6">
                            <strong> {{ Form::label('employee_id', __('messages.commissions.employee')) }}</strong>
                            <p style="color: #555;">{{ $commission->employee->name }}</p>
                        </div>

                        <div class="form-group col-sm-12 col-md-6 ">
                            <strong> {{ Form::label('description', __('messages.commissions.department')) }}</strong>
                            <p style="color: #555;">{{ $commission->employee->department->name }}</p>
                        </div>
                        <div class="form-group col-sm-12 col-md-6 ">
                            <strong>{{ Form::label('description', __('messages.commissions.sub_department')) }}</strong>
                            <p style="color: #555;">{{ $commission->employee->subDepartment->name }}</p>
                        </div>
                        <div class="form-group col-sm-12 col-md-6 ">
                            <strong> {{ Form::label('description', __('messages.commissions.designation')) }}</strong>
                            <p style="color: #555;">{{ $commission->employee->designation->name }}</p>
                        </div>

                        <div class="form-group col-sm-12 col-md-6 ">
                            <strong> {{ Form::label('commission', __('messages.commissions.name')) }}</strong>
                            <p style="color: #555;">{{ $commission->commission }}</p>
                        </div>

                        <div class="form-group col-sm-12 col-md-6 mb-0">
                            <strong> {{ Form::label('description', __('messages.commissions.description')) }}</strong>
                            <div style="color: #555;"> {!! $commission->description !!}</div>
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
