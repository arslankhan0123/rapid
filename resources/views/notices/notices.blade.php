@extends('layouts.app')
@section('title')
    {{ __('messages.notices.notices') }}
@endsection
@section('page_css')
    <link href="{{ asset('assets/css/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ asset('assets/css/bs4-summernote/summernote-bs4.css') }}">
@endsection
@section('content')
    <section class="section">
        <div class="section-header item-align-right">
            <h1>{{ __('messages.notices.notices') }}</h1>
            <div class="section-header-breadcrumb float-right">
                <div class="card-header-action mr-3 select2-mobile-margin">
                </div>
            </div>
            <div class="float-right">
                <a href="{{ route('dashboard') }}" class="btn btn-primary form-btn"> <i
                        class="fas fa-arrow-left"></i></a>

            </div>
        </div>

        <div class="section-body">
            <div class="card">
                <div class="card-body">
                    @include('notices.all_table')
                </div>
            </div>
        </div>
    </section>
    @include('notices.templates.templates')
@endsection
@section('page_scripts')
    <script src="{{ asset('assets/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ mix('assets/js/custom/custom-datatable.js') }}"></script>
    <script src="{{ mix('assets/js/bs4-summernote/summernote-bs4.js') }}"></script>
    <script src="{{ mix('assets/js/select2.min.js') }}"></script>
@endsection
@section('scripts')
    <script src="{{ mix('assets/js/custom/input-price-format.js') }}"></script>
    <script>
        'use strict';



        let tbl = $('#designationTable').DataTable({
            oLanguage: {
                'sEmptyTable': Lang.get('messages.common.no_data_available_in_table'),
                'sInfo': Lang.get('messages.common.data_base_entries'),
                sLengthMenu: Lang.get('messages.common.menu_entry'),
                sInfoEmpty: Lang.get('messages.common.no_entry'),
                sInfoFiltered: Lang.get('messages.common.filter_by'),
                sZeroRecords: Lang.get('messages.common.no_matching'),
            },
            processing: true,
            serverSide: true,
            order: [
                [3, 'desc'] // Ordering by the hidden 'created_at' column (index 2) in descending order
            ],
            ajax: {
                url: route('notices.index'),
            },

            columns: [{
                    data: function(row) {
                        let element = document.createElement('textarea');
                        element.innerHTML = row.name;
                        return element.value;
                    },
                    name: 'name',
                    width: '25%'

                }, {
                    data: function(row) {
                        let element = document.createElement('textarea');
                        element.innerHTML = row
                            .description; // Assuming your data source has a 'description' field
                        return element.value;
                    },
                    name: 'description',

                },
                {
                    data: function(row) {
                        let element = document.createElement('textarea');
                        element.innerHTML = row
                            .notice_type; // Assuming your data source has a 'description' field
                        return element.value;
                    },
                    name: 'notice_type',

                },
                {
                    data: function(row) {
                        let element = document.createElement('textarea');
                        element.innerHTML = row.notice_date;

                        // Convert the date string to a Date object
                        let date = new Date(element.value);

                        // Format the date with the month as a string
                        let options = {
                            year: 'numeric',
                            month: 'long',
                            day: 'numeric'
                        };
                        let formattedDate = date.toLocaleDateString('en-US', options);

                        return formattedDate;
                    },
                    name: 'notice_date',
                    width: '10%'
                }

            ],
            responsive: true // Enable responsive features
        });
    </script>
@endsection
