@extends('layouts.app')
@section('title')
    {{ __('messages.salaries.view') }}
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
            <h1>{{ __('messages.salaries.view') }}</h1>
            <div class="section-header-breadcrumb float-right">
                <div class="card-header-action mr-3 select2-mobile-margin">
                </div>
            </div>
            <div class="float-right">
                <a href="{{ route('salaries.index') }}" class="btn btn-primary form-btn">List</a>

            </div>
        </div>
        <div class="section-body">
            <div class="card">
                <div class="card-body">
                    <div class="modal-content">

                        <div class="modal-body">
                            <div class="alert alert-danger d-none" id="validationErrorsBox"></div>
                            <div class="row">
                                <div class="form-group  col-sm-12 col-md-6">
                                    <strong>{{ Form::label('employee_id', __('messages.salaries.select_employee') . ':') }}</strong>
                                    <p style="color: #555;">{{ $salary->employee->name }}</p>
                                </div>

                                <div class="form-group  col-sm-12 col-md-6">
                                    <strong>{{ Form::label('salary', __('messages.salaries.salary_amount') . ':') }}</strong>
                                    <p style="color: #555;">{{ $salary->salary }}</p>
                                </div>

                                <div class="form-group  col-sm-12 col-md-6">
                                    <strong> {{ Form::label('month', __('messages.salaries.month') . ':') }}</strong>
                                    <p style="color: #555;">
                                        {{ $salary->month ? \Carbon\Carbon::parse($salary->month)->format('Y-m') : null }}
                                    </p>
                                </div>

                                <div class="form-group  col-sm-12 col-md-6 mb-0">
                                    <strong>{{ Form::label('is_active', __('messages.salaries.is_active')) }}</strong>
                                    <p style="color: #555;">{{ $salary->is_active?__('messages.salaries.active'):__('messages.salaries.inactive') }}</p>
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
