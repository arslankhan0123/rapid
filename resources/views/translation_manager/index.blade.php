@extends('layouts.app')
@section('title')
    {{__('messages.translation_manager')}}
@endsection
@section('page_css')
    <link href="{{ asset('assets/css/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css"/>
@endsection
@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{__('messages.translation_manager')}}</h1>
            <div class="section-header-breadcrumb">
                <a href="javascript:void(0)" class="btn btn-primary addLanguageModal form-btn float-right-mobile">
                    {{ __('messages.common.add') }} <i class="fas fa-plus "></i>
                </a>
            </div>
        </div>
        <div class="section-body">
            <div class="card">
                <div class="card-body">
                    @include('translation_manager.table')
                </div>
            </div>
        </div>
        @include('translation_manager.add_modal')
        @include('translation_manager.edit_modal')
        @include('translation_manager.templates.templates')
    </section>
@endsection
@section('page_scripts')
    <script src="{{ asset('assets/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ mix('assets/js/custom/custom-datatable.js') }}"></script>
@endsection
@section('scripts')
    <script src="{{mix('assets/js/language_translate/create-edit.js')}}"></script>
@endsection
