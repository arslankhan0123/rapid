@extends('layouts.app')
@section('title')
    {{ __('messages.retirements.view') }}
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
            <h1>{{ __('messages.retirements.view') }}</h1>
            <div class="section-header-breadcrumb float-right">
                <div class="card-header-action mr-3 select2-mobile-margin">
                </div>
            </div>
            <div class="float-right">
                <a href="{{ route('retirements.index') }}"
                    class="btn btn-primary form-btn">{{ __('messages.retirements.list') }}</a>

            </div>
        </div>
        <div class="section-body">
            <div class="card">
                <div class="card-body">
                    <div class="alert alert-danger d-none" id="validationErrorsBox"></div>
                    <div class="row">
                        <div class="col-md-3 col-sm-12 ">
                            <div class="form-group">
                                <strong>{{ Form::label('employee_id', __('messages.common.date')) }}</strong>
                                <p style="color: #555;">{{ \Carbon\Carbon::parse($retirement->date)->format('d M Y') }}
                                </p>
                            </div>
                        </div>
                        <div class="form-group col-sm-12 col-md-3">
                            <strong> {{ Form::label('title', __('messages.employees.iqama_no')) }}</strong>
                            <p style="color: #555;">{{ $retirement->employee->iqama_no ?? '' }}</p>
                        </div>
                        <div class="form-group col-sm-12 col-md-3">
                            <strong>{{ Form::label('employee_id', __('messages.retirements.employee')) }}</strong>
                            <p style="color: #555;">{{ $retirement->employee->name }}</p>
                        </div>

                        <div class="form-group col-sm-12 col-md-3">
                            <strong> {{ Form::label('title', __('messages.designations.name')) }}</strong>
                            <p style="color: #555;">{{ $retirement->employee->designation->name ?? '' }}</p>
                        </div>
                        <div class="form-group col-sm-12 col-md-3">
                            <strong> {{ Form::label('title', __('messages.retirements.name')) }}</strong>
                            <p style="color: #555;">{{ $retirement->name }}</p>
                        </div>
                        <div class="form-group col-sm-12 col-md-3">
                            <strong> {{ Form::label('title', __('messages.branches.name')) }}</strong>
                            <p style="color: #555;">{{ $retirement->employee?->branch?->name ?? '' }}</p>
                        </div>
                        <div class="form-group col-sm-12 col-md-3 ">

                            {{ Form::label('status', __('messages.retirements.status')) }}
                            <p style="color: #555;">{{ $retirement->status ? 'Yes' : 'No' }}</p>

                        </div>
                        <div class="form-group col-sm-12 col-md-12 mb-0">
                            <strong>{{ Form::label('description', __('messages.assets.category_description')) }}</strong>
                            <div style="color: #555;"> {!! $retirement->description !!}</div>
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
