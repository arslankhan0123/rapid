@extends('layouts.app')
@section('title')
    {{ __('messages.increments.view') }}
@endsection
@section('page_css')
    <link href="{{ asset('assets/css/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ asset('assets/css/bs4-summernote/summernote-bs4.css') }}">
@endsection
@section('content')
    <section class="section">
        <div class="section-header item-align-right">
            <h1>{{ __('messages.increments.view') }}</h1>
            <div class="section-header-breadcrumb float-right">
                <div class="card-header-action mr-3 select2-mobile-margin">
                </div>
            </div>
            <div class="float-right">
                <a href="{{ route('increments.index') }}"
                    class="btn btn-primary form-btn">{{ __('messages.increments.list') }}</a>
            </div>
        </div>
        <div class="section-body">
            <div class="card">
                <div class="card-body">
                    <div class="alert alert-danger d-none" id="validationErrorsBox"></div>
                    <div class="row">
                        <div class="form-group col-md-4 col-sm-12">
                            {{ Form::label('approved_date', __('messages.common.date')) }}
                            <p style="color: #555;">{{ \Carbon\Carbon::parse($increment->date)->format('d M Y') }}</p>

                        </div>
                        <div class="form-group col-md-4  col-sm-12">
                            <strong>{{ Form::label('title', __('messages.increments.name')) }}</strong>
                            <p style="color: #555;">{{ $increment->name??'' }}</p>
                        </div>
                        <div class="form-group col-md-4  col-sm-12">
                            <strong> {{ Form::label('title', __('messages.employees.iqama_no')) }}</strong>
                            <p style="color: #555;">{{ $increment->employee?->iqama_no??'' }}</p>
                        </div>
                        <div class="form-group col-md-4  col-sm-12">
                            <strong> {{ Form::label('title', __('messages.employees.name')) }}</strong>
                            <p style="color: #555;">{{ $increment->employee?->name??'' }}</p>
                        </div>
                        <div class="form-group  col-md-4  col-sm-12">
                            <strong> {{ Form::label('title', __('messages.department.name')) }}</strong>
                            <p style="color: #555;">{{ $increment->employee->department?->name ?? '' }}</p>
                        </div>
                        <div class="form-group  col-md-4  col-sm-12">
                            <strong> {{ Form::label('title', __('messages.designations.name')) }}</strong>
                            <p style="color: #555;">{{ $increment->employee?->designation?->name ?? '' }}</p>
                        </div>
                        <div class="form-group col-md-4  col-sm-12">
                            <strong>{{ Form::label('title', __('messages.branches.name')) }}</strong>
                            <p style="color: #555;">{{ $increment->branch?->name ?? '' }}</p>
                        </div>
                         <div class="form-group col-md-4  col-sm-12">
                            <strong>{{ Form::label('title', __('messages.common.amount')) }}</strong>
                            <p style="color: #555;">{{ $increment->amount??0 }}</p>
                        </div>
                        <div class="form-group col-sm-12 mb-0">
                            <strong>{{ Form::label('description', __('messages.increments.description')) }}</strong>
                            {!! $increment->description !!}
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
