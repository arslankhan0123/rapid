@extends('layouts.app')
@section('title')
    {{ __('messages.allowances.view') }}
@endsection
@section('page_css')
    <link href="{{ asset('assets/css/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ asset('assets/css/bs4-summernote/summernote-bs4.css') }}">
@endsection
@section('content')
    <section class="section">
        <div class="section-header item-align-right">
            <h1>{{ __('messages.allowances.view') }}</h1>
            <div class="section-header-breadcrumb float-right">
                <div class="card-header-action mr-3 select2-mobile-margin">
                </div>
            </div>
            <div class="float-right">
                <a href="{{ route('allowances.index') }}"
                    class="btn btn-primary form-btn">{{ __('messages.allowances.list') }}</a>
            </div>
        </div>
        <div class="section-body">
            <div class="card">
                <div class="card-body">

                    <div class="alert alert-danger d-none" id="validationErrorsBox"></div>
                    <div class="row">
                          <div class="form-group col-sm-12 col-md-6">
                            <strong> {{ Form::label('title', __('messages.branches.name')) }}</strong>
                            <p style="color: #555;">{{ $allowance->employee?->branch?->name??'' }}</p>
                        </div>
                        <div class="form-group col-sm-6 mb-0">
                            <strong>{{ Form::label('description', __('messages.common.date')) }}</strong>
                            <p style="color: #555;">{{ \Carbon\Carbon::parse($allowance->date)->format('d M Y') }}</p>
                        </div>
                        <div class="form-group col-sm-12 col-md-6">
                            <strong> {{ Form::label('title', __('messages.employees.iqama_no')) }}</strong>
                            <p style="color: #555;">{{ $allowance->employee->iqama_no }}</p>
                        </div>
                        <div class="form-group col-sm-12 col-md-6">
                            <strong> {{ Form::label('title', __('messages.employees.name')) }}</strong>
                            <p style="color: #555;">{{ $allowance->employee->name }}</p>
                        </div>
                        <div class="form-group col-sm-12 col-md-6">
                            <strong> {{ Form::label('title', __('messages.designations.name')) }}</strong>
                            <p style="color: #555;">{{ $allowance->employee->designation->name ?? '' }}</p>
                        </div>
                        <div class="form-group col-sm-12 col-md-6">
                            <strong> {{ Form::label('title', __('messages.allowances.type')) }}</strong>
                            <p style="color: #555;">{{ $allowance->allowanceTypes->name }}</p>
                        </div>
                        {{-- <div class="form-group col-sm-12 col-md-6">
                            <strong> {{ Form::label('title', __('messages.allowances.payment_type')) }}</strong>
                            <p style="color: #555;">{{ $payment_types[$allowance->payment_type] ?? '' }}</p>
                        </div> --}}

                        <!--<div class="form-group col-sm-12 col-md-6">-->
                        <!--    <strong> {{ Form::label('title', __('messages.allowances.monthly_allowance')) }}</strong>-->
                        <!--    <p style="color: #555;">{{ $allowance->monthly_allowance }}</p>-->
                        <!--</div>-->
                        
                        <!-- <div class="form-group col-sm-12 col-md-6">-->
                        <!--    <strong> {{ Form::label('title', __('messages.allowances.hourly_allowance')) }}</strong>-->
                        <!--    <p style="color: #555;">{{ $allowance->hourly_allowance }}</p>-->
                        <!--</div>-->
                        <!-- <div class="form-group col-sm-12 col-md-6">-->
                        <!--    <strong> {{ Form::label('title', __('messages.allowances.worked_hours')) }}</strong>-->
                        <!--    <p style="color: #555;">{{ $allowance->worked_hours }}</p>-->
                        <!--</div>-->
                         <div class="form-group col-sm-12 col-md-6">
                            <strong> {{ Form::label('title', __('messages.allowances.amount')) }}</strong>
                            <p style="color: #555;">{{ $allowance->amount }}</p>
                        </div>
                        <div class="form-group col-sm-6 mb-0">
                            <strong>{{ Form::label('description', __('messages.common.created_on')) }}</strong>
                            <div style="color: #555;">{{ $allowance->created_at->format('d M Y') }}</div>
                        </div>
                        <div class="form-group col-sm-12 col-md-6 mb-0">
                            <strong>{{ Form::label('description', __('messages.allowances.description')) }}</strong>
                            <div style="color: #555;"> {!! $allowance->description !!}
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
