@extends('layouts.app')
@section('title')
    {{ __('messages.notices.name') }}
@endsection
@section('page_css')
    <link href="{{ asset('assets/css/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ asset('assets/css/bs4-summernote/summernote-bs4.css') }}">
    <style>


        .switch {
            position: relative;
            display: inline-block;
            width: 30px;
            /* Adjusted width */
            height: 17px;
            /* Adjusted height */
        }

        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            -webkit-transition: .4s;
            transition: .4s;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 13px;
            /* Adjusted height */
            width: 13px;
            /* Adjusted width */
            left: 2px;
            /* Adjusted position */
            bottom: 2px;
            /* Adjusted position */
            background-color: white;
            -webkit-transition: .4s;
            transition: .4s;
        }

        input:checked+.slider {
            background-color: #2196F3;
        }

        input:focus+.slider {
            box-shadow: 0 0 1px #2196F3;
        }

        input:checked+.slider:before {
            -webkit-transform: translateX(13px);
            /* Adjusted transform */
            -ms-transform: translateX(13px);
            /* Adjusted transform */
            transform: translateX(13px);
            /* Adjusted transform */
        }

        /* Rounded sliders */
        .slider.round {
            border-radius: 17px;
            /* Adjusted border-radius */
        }

        .slider.round:before {
            border-radius: 50%;
        }
    </style>
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
                <a href="{{ route('notices.create') }}" class="btn btn-primary form-btn">
                    {{ __('messages.notices.add') }} </a>
            </div>
        </div>

        <div class="section-body">
            <div class="card">
                <div class="card-body">
                    @include('notices.table')
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

        let designationCreateUrl = route('notices.store');
        $(document).on('submit', '#addNewForm', function(event) {
            event.preventDefault();
            processingBtn('#addNewForm', '#btnSave', 'loading');


            let description = $('<div />').
            html($('#createDescription').summernote('code'));
            let empty = description.text().trim().replace(/ \r\n\t/g, '') === '';

            if ($('#createDescription').summernote('isEmpty')) {
                $('#createDescription').val('');
            } else if (empty) {
                displayErrorMessage(
                    'Description field is not contain only white space');
                processingBtn('#addNewForm', '#btnSave', 'reset');
                return false;
            }
            $.ajax({
                url: designationCreateUrl,
                type: 'POST',
                data: $(this).serialize(),
                success: function(result) {
                    if (result.success) {
                        displaySuccessMessage(result.message);
                        $('#addModal').modal('hide');
                        $('#designation_name').val('');
                    }
                },
                error: function(result) {
                    displayErrorMessage(result.responseJSON.message);
                },
                complete: function() {
                    processingBtn('#addNewForm', '#btnSave');
                },
            });
        });

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
            columnDefs: [{
                targets: 6, // Adjust the index for the hidden 'created_at' column
                orderable: true,
                visible: false, // Hide the 'created_at' column
            }],
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
                },
                {
                    data: function(row) {
                        let element = document.createElement('textarea');
                        element.innerHTML = row.show == 1 ? "Yes" :
                            ''; // Assuming your data source has a 'description' field
                        return element.value;
                    },
                    name: 'show',
                    width: '5%'

                },
                // DataTable configuration
                // {
                //     data: function(row) {
                //         let container = $('<label>', {
                //             class: 'switch'
                //         });
                //         let checkbox = $('<input>', {
                //             type: 'checkbox',
                //             checked: row.show == 1 ? true : false,
                //             'data-id': row.id // Set the data-id attribute
                //         });
                //         let slider = $('<span>', {
                //             class: 'slider round'
                //         });
                //         container.append(checkbox).append(slider);
                //         return container.prop('outerHTML');
                //     },
                //     name: 'notice_type',
                // },

                {
                    data: function(row) {
                        let data = [{
                            'id': row.id
                        }];
                        return prepareTemplateRender('#productUnitActionTemplate', data);
                    },
                    name: 'id',
                },
                {
                    data: 'created_at', // Ensure this matches the data key for created_at in your data source
                    name: 'created_at'
                }
            ],
            responsive: true // Enable responsive features
        });

        $(document).on('click', '.edit-btn', function(event) {
            let did = $(event.currentTarget).data('id');
            const url = route('notices.edit', did);
            window.location.href = url;
        });

        $(document).on('click', '.delete-btn', function(event) {
            let assetCateogryId = $(event.currentTarget).data('id');
            deleteItem(route('notices.destroy', assetCateogryId), '#designationTable',
                '{{ __('messages.notices.name') }}');
        });
    </script>
@endsection
