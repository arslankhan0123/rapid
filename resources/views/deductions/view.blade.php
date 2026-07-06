@extends('layouts.app')
@section('title')
    {{ __('messages.deductions.view') }}
@endsection
@section('page_css')
    <link href="{{ asset('assets/css/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ asset('assets/css/bs4-summernote/summernote-bs4.css') }}">
@endsection
@section('content')
    <section class="section">
        <div class="section-header item-align-right">
            <h1>{{ __('messages.deductions.view') }}</h1>
            <div class="section-header-breadcrumb float-right">
                <div class="card-header-action mr-3 select2-mobile-margin">
                </div>
            </div>
            <div class="float-right">
                <a href="{{ route('deductions.index') }}"
                    class="btn btn-primary form-btn">{{ __('messages.deductions.list') }}</a>
            </div>
        </div>
        <div class="section-body">
            <div class="card">
                <div class="card-body">
                    <div class="alert alert-danger d-none" id="validationErrorsBox"></div>
                    <div class="row">
                        <div class="form-group col-sm-12 col-md-6">
                            <strong> {{ Form::label('title', __('messages.common.date')) }}</strong>
                            <p style="color: #555;">{{ \Carbon\Carbon::parse($deduction->date)->format('d M y') ?? '' }}</p>

                        </div>
                        <div class="form-group col-sm-12 col-md-6">
                            <strong> {{ Form::label('title', __('messages.employees.id')) }}</strong>
                            <p style="color: #555;">{{ $deduction->employee->iqama_no ?? '' }}</p>
                        </div>
                        <div class="form-group col-sm-12 col-md-6">
                            <strong> {{ Form::label('title', __('messages.employees.name')) }}</strong>
                            <p style="color: #555;">{{ $deduction->employee->name }}</p>
                        </div>
                        <div class="form-group col-sm-12 col-md-6">
                            <strong> {{ Form::label('title', __('messages.designations.name')) }}</strong>
                            <p style="color: #555;">{{ $deduction->employee->designation->name ?? '' }}</p>
                        </div>
                        <div class="form-group col-sm-12 col-md-6">
                            <strong> {{ Form::label('title', __('messages.branches.name')) }}</strong>
                            <p style="color: #555;">{{ $deduction->employee?->branch?->name ?? '' }}</p>
                        </div>
                        <div class="form-group col-sm-12 col-md-6">
                            <strong> {{ Form::label('title', __('messages.deductions.type')) }}</strong>
                            <p style="color: #555;">{{ $deduction->deductionTypes->name }}</p>
                        </div>
                        <div class="form-group col-sm-12 col-md-6">
                            <strong> {{ Form::label('title', __('messages.deductions.amount')) }}</strong>
                            <p style="color: #555;">{{ $deduction->amount }}</p>
                        </div>
                        <div class="form-group col-sm-6 mb-0">
                            <strong>{{ Form::label('description', __('messages.common.created_on')) }}</strong>
                            <div style="color: #555;">{{ $deduction->created_at->format('d M Y') }}</div>
                        </div>
                        <div class="form-group col-sm-12 mb-0">
                            <strong>{{ Form::label('description', __('messages.deductions.description')) }}</strong>
                            <div style="color: #555;"> {!! $deduction->description !!}</div>
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
