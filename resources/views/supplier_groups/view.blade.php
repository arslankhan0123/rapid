@extends('layouts.app')
@section('title')
    {{ __('messages.supplier_groups.view') }}
@endsection
@section('page_css')
    <link href="{{ asset('assets/css/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ asset('assets/css/bs4-summernote/summernote-bs4.css') }}">
@endsection
@section('content')
    <section class="section">
        <div class="section-header item-align-right">
            <h1>{{ __('messages.supplier_groups.view') }}</h1>
            <div class="section-header-breadcrumb float-right">
                <div class="card-header-action mr-3 select2-mobile-margin">
                </div>
            </div>
            <div class="float-right">
                <a href="{{ route('supplier-groups.index') }}" class="btn btn-primary form-btn">{{ __('messages.supplier_groups.list') }} </a>

            </div>
        </div>
        <div class="section-body">
            <div class="card">
                <div class="card-body">
                        <div class="row">
                            <div class="form-group col-sm-12">
                                 <strong>{{ Form::label('state', __('messages.supplier_groups.name') ) }}</strong>
                                <p style="color: #555;">{{ $supplierGroup->name }}</p>
                            </div>
                            <div class="form-group col-sm-12 mb-0">
                                   <strong>{{ Form::label('description', __('messages.supplier_groups.description') ) }}</strong>
                                <div style="color: #555;">  {!! $supplierGroup->description !!}</div>

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
