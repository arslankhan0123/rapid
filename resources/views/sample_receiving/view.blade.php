@extends('layouts.app')
@section('title')
    {{ __('messages.sample_receiving.view') }}
@endsection
@section('page_css')
    <link href="{{ asset('assets/css/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ asset('assets/css/bs4-summernote/summernote-bs4.css') }}">
@endsection
@section('content')
    <section class="section">
        <div class="section-header item-align-right">
            <h1>{{ __('messages.sample_receiving.view') }}</h1>
            <div class="section-header-breadcrumb float-right">
                <div class="card-header-action mr-3 select2-mobile-margin">

                </div>
            </div>
            <div class="float-right">
                <a href="{{ route('sample_receiving.pdf', $data['sampleReceiving']['id']) }}"
                    class="btn btnWarning form-btn mr-3 text-white">PDF</i>
                </a>
                <a href="{{ route('sample_receiving.index') }}"
                    class="btn btn-primary form-btn">{{ __('messages.assets.list') }}</i>
                </a>
            </div>
        </div>
        <div class="section-body">
            <div class="card">
                <div class="card-body">
                    <div class="alert alert-danger d-none" id="validationErrorsBox"></div>
                    <div class="row">
                        <div class="form-group  col-sm-12 col-md-6">
                            <strong> {{ Form::label('client_name', __('messages.branches.name')) }}</strong>
                            <p style="color: #555;">{{ $data['sampleReceiving']['branch']['name'] ?? '' }}</p>
                        </div>
                        <div class="form-group  col-sm-12 col-md-6">
                            <strong> {{ Form::label('client_name', __('messages.sample_receiving.client_name')) }}</strong>
                            <p style="color: #555;">{{ $data['sampleReceiving']['client_name'] ?? '' }}</p>
                        </div>
                        <div class="form-group  col-sm-12 col-md-6">
                            <strong>{{ Form::label('client_reference', __('messages.sample_receiving.client_reference')) }}</strong>
                            <div style="color: #555;"> {{ $data['sampleReceiving']['client_reference'] ?? '' }}</div>
                        </div>
                        <div class="form-group  col-sm-12 col-md-6">
                            <strong>{{ Form::label('type_of_sample', __('messages.sample_receiving.type_of_sample')) }}</strong>
                            <div style="color: #555;"> {{ $data['sampleReceiving']['type_of_sample'] ?? '' }}</div>
                        </div>
                        <div class="form-group  col-sm-12 col-md-6">
                            <strong>{{ Form::label('required_tests', __('messages.sample_receiving.required_tests')) }}</strong>
                            <div style="color: #555;"> {{ $data['sampleReceiving']['required_tests'] ?? '' }}</div>
                        </div>
                        <div class="form-group  col-sm-12 col-md-6">
                            <strong>{{ Form::label('number_of_sample', __('messages.sample_receiving.number_of_sample')) }}</strong>
                            <div style="color: #555;"> {{ $data['sampleReceiving']['number_of_sample'] ?? '' }}</div>
                        </div>
                        <div class="form-group  col-sm-12 col-md-6">
                            <strong>{{ Form::label('date', __('messages.sample_receiving.date')) }}</strong>
                            <div style="color: #555;">
                                {{ $data['sampleReceiving']['date'] ? \Carbon\Carbon::parse($data['sampleReceiving']['date'])->format('d-M-Y') : '' }}
                            </div>
                        </div>
                        <div class="form-group  col-sm-12 col-md-6">
                            <strong>{{ Form::label('time', __('messages.sample_receiving.time')) }}</strong>
                            <div style="color: #555;"> {{ $data['sampleReceiving']['time'] ?? '' }}</div>
                        </div>
                        <div class="form-group  col-sm-12 col-md-6">
                            <strong>{{ Form::label('section', __('messages.sample_receiving.section')) }}</strong>
                            <div style="color: #555;"> {{ $data['section'][0] ?? '' }}</div>
                        </div>
                        <div class="form-group  col-sm-12 col-md-6">
                            <strong>{{ Form::label('deliveredBy', __('messages.sample_receiving.delivered_by')) }}</strong>
                            <div style="color: #555;"> {{ $data['deliveredBy'][0] ?? '' }}</div>
                        </div>
                        <div class="form-group  col-sm-12 col-md-6">
                            <strong>{{ Form::label('receivedBy', __('messages.sample_receiving.received_by')) }}</strong>
                            <div style="color: #555;"> {{ $data['receivedBy'][0] ?? '' }}</div>
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
