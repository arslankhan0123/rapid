@extends('layouts.app')
@section('title')
    {{ __('messages.print-checks.view') }}
@endsection
@section('page_css')
    <link href="{{ asset('assets/css/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ asset('assets/css/bs4-summernote/summernote-bs4.css') }}">
@endsection
@section('content')
    <section class="section">
        <div class="section-header item-align-right">
            <h1>{{ __('messages.print-checks.view') }}</h1>
            <div class="section-header-breadcrumb float-right">
                <div class="card-header-action mr-3 select2-mobile-margin">
                </div>
            </div>
            <div class="float-right">
                <a href="{{ route('print-checks.export.print', ['check' => $check->id]) }}" class="btn btn-warning "
                    target="_blank" style="line-height:30px;">{{ __('messages.print-checks.print') }}</i>
                </a>
                <a href="{{ route('print-checks.index') }}" class="btn btn-primary"
                    style="line-height:30px;">{{ __('messages.print-checks.list') }}</i>
                </a>
            </div>
        </div>
        <div class="section-body">
            <div class="card">
                <div class="card-body">


                    <div class="row">
                        <div class="ol-sm- 12 col-md-4">
                            <!-- col-lg-6 for large screens, col-12 for extra small screens -->
                            <div class="form-group">
                                <strong>{{ Form::label('name', __('messages.branches.name')) }}</strong>
                                <p style="color: #555;">{{ $check->branch?->name ?? '' }}</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <!-- col-lg-6 for large screens, col-12 for extra small screens -->
                            <div class="form-group">
                                <strong>{{ Form::label('name', __('messages.print-checks.date')) }}</strong>
                                <p style="color: #555;">{{ $check->formatted_date ?? '' }}</p>
                            </div>
                        </div>
                        <div class=" col-md-4">
                            <!-- col-lg-6 for large screens, col-12 for extra small screens -->
                            <div class="form-group">
                                <strong>{{ Form::label('name', __('messages.print-checks.check_number')) }}</strong>
                                <p style="color: #555;">{{ $check->check_number ?? '' }}</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <!-- col-lg-6 for large screens, col-12 for extra small screens -->
                            <div class="form-group">
                                <strong>{{ Form::label('name', __('messages.print-checks.issue_name')) }}</strong>
                                <p style="color: #555;">{{ $check->issue_name ?? '' }}</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <!-- col-lg-6 for large screens, col-12 for extra small screens -->
                            <div class="form-group">
                                <strong>{{ Form::label('name',"Place Of Issue") }}</strong>
                                <p style="color: #555;">{{ $check->issue_place ?? '' }}</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <!-- col-lg-6 for large screens, col-12 for extra small screens -->
                            <div class="form-group">
                                <strong>{{ Form::label('name', __('messages.print-checks.amount')) }}</strong>
                                <p style="color: #555;">{{ $check->amount ?? '' }}</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <!-- col-lg-6 for large screens, col-12 for extra small screens -->
                            <div class="form-group">
                                <strong>{{ Form::label('name', __('messages.print-checks.bank')) }}</strong>
                                <p style="color: #555;">{{ $check->bank?->name ?? '' }}</p>
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
