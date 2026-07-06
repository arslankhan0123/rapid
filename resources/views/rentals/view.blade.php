@extends('layouts.app')
@section('title')
    {{ __('messages.rentals.view') }}
@endsection
@section('page_css')
    <link href="{{ asset('assets/css/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
@endsection
@section('content')
    <section class="section">
        <div class="section-header item-align-right">
            <h1>{{ __('messages.rentals.view') }}</h1>
            <div class="section-header-breadcrumb float-right">
                <div class="card-header-action mr-3 select2-mobile-margin">
                </div>
            </div>
            <div class="float-right">
                <a href="{{ route('rentals.index') }}"
                    class="btn btn-primary form-btn">{{ __('messages.rentals.list') }}</a>

            </div>
        </div>
        <div class="section-body">
            <div class="card">
                <div class="card-body">
                    <div class="alert alert-danger d-none" id="validationErrorsBox"></div>
                    <div class="row">
                        <div class="form-group col-sm-12 col-md-6">
                            <strong>{{ Form::label('supplier_id', __('messages.rentals.supplier')) }}</strong>
                            <p style="color: #555;">{{ $rental->supplier->company_name }}</p>
                        </div>
                        <div class="form-group col-sm-12 col-md-6">
                            <strong> {{ Form::label('start_date', __('messages.rentals.start_date')) }}</strong>
                            <p style="color: #555;">{{ $rental->start_date }}</p>
                        </div>
                        <div class="form-group col-sm-12 col-md-6">
                            <strong>{{ Form::label('end_date', __('messages.rentals.end_date')) }}</strong>
                            <p style="color: #555;">{{ $rental->end_date }}</p>
                        </div>
                        <div class="form-group col-sm-12 col-md-6">
                            <strong>{{ Form::label('type', __('messages.rentals.type')) }}</strong>
                            <p style="color: #555;">{{ $rental->type }}</p>
                        </div>
                        <div class="form-group col-sm-12 col-md-6">
                            <strong> {{ Form::label('type', __('messages.rentals.amount')) }}</strong>
                            <p style="color: #555;">{{ $rental->amount }}</p>
                        </div>

                        <div class="form-group col-sm-12 col-md-6">
                            <strong>{{ Form::label('type', __('messages.rentals.tax')) }}</strong>
                            <p style="color: #555;">{{ $rental->tax_id }}</p>
                        </div>

                        <div class="form-group col-sm-12 col-md-6">
                            <strong> {{ Form::label('type', __('messages.rentals.tax_amount')) }}</strong>
                            <p style="color: #555;">{{ $rental->tax_amount }}</p>
                        </div>

                        <div class="form-group col-sm-12 col-md-6">
                            <strong> {{ Form::label('type', __('messages.rentals.rent_including_tax')) }}</strong>
                            <p style="color: #555;">{{ $rental->total_rent_amount }}</p>
                        </div>
                        <div class="form-group col-sm-12 col-md-6 mb-0">
                            <strong> {{ Form::label('description', __('messages.rentals.description')) }}</strong>
                            <div style="color: #555;"> {!! $rental->description !!}</div>

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
