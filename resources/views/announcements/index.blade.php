@extends('layouts.app')
@section('title')
    {{ __('messages.announcements') }}
@endsection
@section('page_css')
    <link href="{{ asset('css/bootstrap-datetimepicker.css') }}" rel="stylesheet" type="text/css"/>
    <link rel="stylesheet" href="{{ asset('assets/css/bs4-summernote/summernote-bs4.css') }}">
    <link rel="stylesheet" href="{{ mix('assets/css/clients/announcements/announcements.css') }}">
    <link href="{{ asset('assets/css/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css"/>
@endsection
@section('content')
    <section class="section">
        <div class="section-header announcement-sec-header">
            <h1>{{ __('messages.announcements') }}</h1>
            <div class="section-header-breadcrumb float-right">
                <div class="card-header-action mr-3">
                    {{ Form::select('status', $statusArr, null, ['id' => 'filterAnnouncementStatus', 'class' => 'form-control', 'placeholder' => __('messages.placeholder.select_status')]) }}
                </div>
            </div>
            <div class="float-right mr-3">
                {{ Form::select('show_to_client', $showToClientArr, null, ['class' => 'form-control', 'id' => 'showToClientId', 'placeholder' => __('messages.placeholder.select_show_to_client')]) }}
            </div>
            <div class="float-right">
                @can('manage_calenders')
                    <a href="{{ route('calendars.index') }}" class="btn btn-primary form-btn mr-2"><i
                                class="fas fa-calendar mr-1 select2-mobile-margin"></i>{{ __('messages.calendars') }}
                    </a>
                @endcan
                <a href="#" class="btn btn-primary form-btn addAnnouncementModal" data-toggle="modal"
                   data-target="#addModal">{{ __('messages.announcement.add') }} </a>
            </div>
        </div>
        <div class="section-body">
            <div class="card">
                <div class="card-body">
                    @include('announcements.table')
                </div>
            </div>
        </div>
        @include('announcements.add_modal')
        @include('announcements.edit_modal')
        @include('announcements.templates.template')
    </section>
@endsection
@section('page_scripts')
    <script src="{{ asset('assets/js/moment.min.js') }}"></script>
    <script src="{{ asset('assets/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ mix('assets/js/custom/custom-datatable.js') }}"></script>
    <script src="{{ asset('js/bootstrap-datetimepicker.min.js') }}"></script>
    <script src="{{ mix('assets/js/bs4-summernote/summernote-bs4.js') }}"></script>
@endsection
@section('scripts')
    <script src="{{ mix('assets/js/announcements/announcements.js') }}"></script>
@endsection
