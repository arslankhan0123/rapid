@extends('layouts.app')
@section('title')
    {{ __('messages.terms.view') }}
@endsection

@section('page_css')
    <link href="{{ asset('assets/css/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ asset('assets/css/bs4-summernote/summernote-bs4.css') }}">
@endsection

@section('content')
    <section class="section">
        <div class="section-header item-align-right">
            <h1>{{ __('messages.terms.terms') }}</h1>
            <div class="section-header-breadcrumb float-right">
                <div class="card-header-action mr-3 select2-mobile-margin"></div>
            </div>
            <div class="float-right">
                <a href="{{ route('terms.index') }}" class="btn btn-primary form-btn">List</a>
                {{-- <a href="{{ route('terms.edit', $term->id) }}" class="btn btn-warning form-btn">Edit</a> --}}
            </div>
        </div>

        <div class="section-body">
            <div class="card">
                <div class="card-body">

                    <div class="row">
                        <div class="form-group col-sm-12 mb-0">
                            <label class="font-weight-bold">
                                {{ __('messages.assets.category_description') }}:
                            </label>
                            <div class="border p-3 rounded bg-light">
                                {{-- Render description with HTML --}}
                                {!! $term->terms !!}
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
