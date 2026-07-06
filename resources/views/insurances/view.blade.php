@extends('layouts.app')
@section('title')
    {{ __('messages.insurances.view') }}
@endsection
@section('page_css')
    <link href="{{ asset('assets/css/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    >
@endsection
@section('content')
    <section class="section">
        <div class="section-header item-align-right">
            <h1>{{ __('messages.insurances.view') }}</h1>
            <div class="section-header-breadcrumb float-right">
                <div class="card-header-action mr-3 select2-mobile-margin">
                </div>
            </div>
            <div class="float-right">
                <a href="{{ route('insurances.index') }}"
                    class="btn btn-primary form-btn">{{ __('messages.insurances.list') }}</a>

            </div>
        </div>
        <div class="section-body">
            <div class="card">
                <div class="card-body">
                    <div class="alert alert-danger d-none" id="validationErrorsBox"></div>
                    <div class="row">
                        <div class="form-group col-sm-12 col-md-6 ">
                            <strong> {{ Form::label('description', __('messages.allowances.date')) }}</strong>
                            <p style="color: #555;">{{ $insurance->date ?? '' }}</p>
                        </div>
                        <div class="form-group col-sm-12 col-md-6 ">
                            <strong> {{ Form::label('description', __('messages.employees.iqama_no')) }}</strong>
                            <p style="color: #555;">{{ $insurance->employee->iqama_no ?? '' }}</p>
                        </div>
                        <div class="form-group col-sm-12 col-md-6">
                            <strong> {{ Form::label('employee_id', __('messages.employees.name')) }}</strong>
                            <p style="color: #555;">{{ $insurance->employee->name }}</p>
                        </div>

                        <div class="form-group col-sm-12 col-md-6 ">
                            <strong> {{ Form::label('description', __('messages.insurances.department')) }}</strong>
                            <p style="color: #555;">{{ $insurance->employee?->department?->name??'' }}</p>

                        </div>
                        {{-- <div class="form-group col-sm-12 col-md-6 ">
                            <strong>{{ Form::label('description', __('messages.insurances.sub_department')) }}</strong>
                            <p style="color: #555;">{{ $insurance->employee->subDepartment?->name??'' }}</p>
                        </div> --}}
                        <div class="form-group col-sm-12 col-md-6 ">
                            <strong> {{ Form::label('description', __('messages.insurances.designation')) }}</strong>
                            <p style="color: #555;">{{ $insurance->employee?->designation?->name??'' }}</p>
                        </div>
                        <div class="form-group col-sm-12 col-md-6 ">
                            <strong> {{ Form::label('insurance', __('messages.branches.name')) }}</strong>
                            <p style="color: #555;">{{ $insurance->employee?->branch?->name??'' }}</p>
                        </div>
                        <div class="form-group col-sm-12 col-md-6 ">
                            <strong> {{ Form::label('insurance_type', __('messages.insurances.insurance_type')) }}</strong>
                            <p style="color: #555;">{{ $insurance->insurance_type }}</p>
                        </div>
                        <!--<div class="form-group col-sm-12 col-md-6 ">-->
                        <!--    <strong> {{ Form::label('monthly_insurance', __('messages.insurances.monthly_insurance')) }}</strong>-->
                        <!--    <p style="color: #555;">{{ $insurance->monthly_insurance }}</p>-->
                        <!--</div>-->
                        <!--<div class="form-group col-sm-12 col-md-6 ">-->
                        <!--    <strong> {{ Form::label('hourly_insurance', __('messages.insurances.hourly_insurance')) }}</strong>-->
                        <!--    <p style="color: #555;">{{ $insurance->hourly_insurance }}</p>-->
                        <!--</div>-->
                        <!--<div class="form-group col-sm-12 col-md-6 ">-->
                        <!--    <strong> {{ Form::label('worked_hours', __('messages.insurances.worked_hours')) }}</strong>-->
                        <!--    <p style="color: #555;">{{ $insurance->worked_hours }}</p>-->
                        <!--</div>-->
                        
                        <div class="form-group col-sm-12 col-md-6 ">
                            <strong> {{ Form::label('insurance', __('messages.insurances.name')) }}</strong>
                            <p style="color: #555;">{{ $insurance->insurance }}</p>
                        </div>
                        <div class="form-group col-sm-12 col-md-6 ">
                            <strong> {{ Form::label('insurance', __('messages.common.last_updated')) }}</strong>
                            <p style="color: #555;"> {{ \Carbon\Carbon::parse($insurance->updated_at)->format('d-m-Y') }}
                            </p>
                        </div>
                        <div class="form-group col-sm-12 col-md-12 mb-0">
                            <strong>{{ Form::label('description', __('messages.insurances.description')) }}</strong>
                            <div style="color: #555;"> {!! $insurance->description !!}</div>
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
