@extends('layouts.app')
@section('title')
    {{ __('messages.designations.view') }}
@endsection
@section('page_css')
    <link href="{{ asset('assets/css/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ asset('assets/css/bs4-summernote/summernote-bs4.css') }}">
@endsection
@section('content')
    <section class="section">
        <div class="section-header item-align-right">
            <h1>{{ __('messages.designations.view') }}</h1>
            <div class="section-header-breadcrumb float-right">
                <div class="card-header-action mr-3 select2-mobile-margin">
                </div>
            </div>
            <div class="float-right">
                <a href="{{ route('designations.index') }}"
                    class="btn btn-primary form-btn">{{ __('messages.designations.list') }}</a>

            </div>
        </div>
        <div class="section-body">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="form-group col-sm-12 col-md-4">
                            <strong>{{ Form::label('title', __('messages.designations.name')) }}</strong>
                            <p style="color: #555;">{{ $designation->name }}</p>
                        </div>
                        {{-- <div class="form-group col-sm-12 col-md-4">
                            <strong>{{ Form::label('title', __('messages.designations.sub_department')) }}</strong>
                            <p style="color: #555;">{{ $designation->subDepartment->name }}</p>
                        </div> --}}
                        <div class="form-group col-sm-12 col-md-4">
                            <strong>{{ Form::label('title', __('messages.designations.department')) }}</strong>
                            <p style="color: #555;">{{ $designation->department->name }}</p>
                        </div>

                        <div class="form-group col-sm-12 col-md-12 mb-0">
                            <strong>
                                {{ Form::label('description', __('messages.designations.description')) }}</strong>
                            {!! $designation->description !!}
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
