@extends('layouts.app')
@section('title')
    {{ __('messages.areas.view') }}
@endsection
@section('page_css')
    <link href="{{ asset('assets/css/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ asset('assets/css/bs4-summernote/summernote-bs4.css') }}">
@endsection
@section('content')
    <section class="section">
        <div class="section-header item-align-right">
            <h1>{{ __('messages.areas.view') }}</h1>
            <div class="section-header-breadcrumb float-right">
                <div class="card-header-action mr-3 select2-mobile-margin">
                </div>
            </div>
            <div class="float-right">
                <a href="{{ route('areas.index') }}" class="btn btn-primary form-btn">{{ __('messages.areas.list') }} </a>
            </div>
        </div>
        <div class="section-body">
            <div class="card">
                <div class="card-body">
                    <div class="modal-content">

                        <div class="modal-body">
                            <div class="alert alert-danger d-none" id="validationErrorsBox"></div>
                            <div class="row">
                                <div class="form-group col-sm-12">
                                    {{ Form::label('title', __('messages.areas.areas') . ':') }}<span
                                        class="required">*</span>
                                    {{ Form::text('name', $area->name, ['class' => 'form-control', 'required', 'id' => 'designation_name', 'autocomplete' => 'off', 'readonly']) }}
                                </div>
                                <div class="form-group col-sm-12">
                                    {{ Form::label('title', __('messages.areas.country') . ':') }}<span
                                        class="required">*</span>
                                    {{ Form::text('country_id', $area->country->name ?? null, ['class' => 'form-control', 'required', 'id' => 'designation_name', 'autocomplete' => 'off', 'readonly']) }}
                                </div>
                                <div class="form-group col-sm-12">
                                    {{ Form::label('title', __('messages.areas.state') . ':') }}<span
                                        class="required">*</span>
                                    {{ Form::text('state_id', $area->state->name ?? null, ['class' => 'form-control', 'required', 'id' => 'designation_name', 'autocomplete' => 'off', 'readonly']) }}
                                </div>
                                <div class="form-group col-sm-12">
                                    {{ Form::label('title', __('messages.areas.city') . ':') }}<span
                                        class="required">*</span>
                                    {{ Form::text('city_id', $area->city->name ?? null, ['class' => 'form-control', 'required', 'id' => 'designation_name', 'autocomplete' => 'off', 'readonly']) }}
                                </div>

                                <div class="form-group col-sm-12 mb-0">
                                    {{ Form::label('description', __('messages.assets.category_description') . ':') }}
                                    {{ Form::textarea('description', $area->description, ['class' => 'form-control summernote-simple', 'id' => 'createDescription', 'readonly']) }}
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
