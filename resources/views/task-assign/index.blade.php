@extends('layouts.app')
@section('title')
    {{ __('messages.task-assign.name') }}
@endsection
@section('page_css')
    <link href="{{ asset('assets/css/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ asset('assets/css/bs4-summernote/summernote-bs4.css') }}">

    <style>
        .fixed-height {
            max-height: 50px;
            /* Fixed height */
            overflow: hidden;
            /* Hide overflow */
            white-space: normal;
            /* Allow wrapping of text */
            text-overflow: ellipsis;
            /* Optional: show ellipsis for overflowing text */
        }
    </style>
@endsection
@section('content')
    <section class="section">
        <div class="section-header item-align-right">
            <h1>{{ __('messages.task-assign.name') }}</h1>
            <div class="section-header-breadcrumb float-right">
                <div class="card-header-action mr-3 select2-mobile-margin">
                </div>
            </div>
            @can('create_task_assign')
                <div class="float-right">
                    <a href="{{ route('task-assign.create') }}" class="btn btn-primary form-btn">
                        {{ __('messages.task-assign.add') }} </a>
                </div>
            @endcan

        </div>
        <div class="section-body">
            <div class="card">
                <div class="card-body">
                    @if (session()->has('flash_notification'))
                        @foreach (session('flash_notification') as $message)
                            <div class="alert alert-{{ $message['level'] }}">
                                {{ $message['message'] }}
                            </div>
                        @endforeach
                    @endif
                    @include('task-assign.table')
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
            ajax: {
                url: route('task-assign.index'),
                beforeSend: function() {
                    startLoader();
                },
                complete: function() {
                    stopLoader();
                }
            },
            lengthMenu: [
                [100, 300, 500, 999, -1],
                [100, 300, 500, 999, "All"]
            ],
            pageLength: 100, // Default page length
            columns: [{
                    data: 'updated_at', // Add the updated_at column
                    name: 'updated_at',
                    visible: false // Make the column invisible
                },
                {
                    data: function(row) {
                        let element = document.createElement('textarea');
                        element.innerHTML = row.name;
                        return element.value;
                    },
                    name: 'name',
                    width: '30%'
                },

                // {
                //     data: function(row) {
                //         let element = document.createElement('textarea');
                //         element.innerHTML = row.employee.name ?? '';
                //         return element.value;
                //     },
                //     name: 'employee.name',
                //     width: '20%',
                //     orderable: false
                // },

                {
                    data: function(row) {
                        let element = document.createElement('textarea');
                        element.innerHTML = row.designation.name ?? '';
                        return element.value;
                    },
                    name: 'designation.name',
                    width: '20%',
                    orderable: false

                }, {
                    data: function(row) {
                        let element = document.createElement('textarea');
                        element.innerHTML = row.department.name ?? '';
                        return element.value;
                    },
                    name: 'department.name',
                    width: '18%',
                    orderable: false
                }, {
                    data: function(row) {
                        // Create a div to hold the content, remove HTML tags using innerText or textContent
                        let element = document.createElement('div');
                        element.innerHTML = row.description; // Set the HTML content
                        return `<div class="fixed-height">${element.textContent || element.innerText}</div>`;
                    },
                    name: 'description',
                    width: '10%',
                    orderable: false
                }, {
                    data: function(row) {
                        return renderActionButtons(row.id);
                    },
                    name: 'id',
                    width: '200px',
                    orderable: false
                }
            ],
            order: [
                [1, 'desc']
            ],
            responsive: true // Enable responsive features
        });

        $(document).on('click', '.delete-btn', function(event) {
            let assetCateogryId = $(event.currentTarget).data('id');
            deleteItem(route('task-assign.destroy', assetCateogryId), '#designationTable',
                '{{ __('messages.task-assign.name') }}');
        });
    </script>

    <script>
        // Define messages for translations
        var messages = {
            delete: "{{ __('messages.common.delete') }}",
            edit: "{{ __('messages.common.edit') }}",
            view: "{{ __('messages.common.view') }}"
        };
        // Define permissions
        var permissions = {
            updateItem: "{{ auth()->user()->can('update_task_assign') ? 'true' : 'false' }}",
            deleteItem: "{{ auth()->user()->can('delete_task_assign') ? 'true' : 'false' }}",
            viewItem: "{{ auth()->user()->can('view_task_assign') ? 'true' : 'false' }}"
        };
        // Function to render action buttons based on permissions
        function renderActionButtons(id) {
            let buttons = '';
            if (permissions.updateItem === 'true') {
                let editUrl = `{{ route('task-assign.edit', ':id') }}`;
                editUrl = editUrl.replace(':id', id);
                buttons += `
                <a title="${messages.edit}" href="${editUrl}" class="btn btn-warning action-btn has-icon edit-btn" style="float:right;margin:2px;">
                    <i class="fa fa-edit"></i>
                </a>
            `;
            }
            if (permissions.viewItem === 'true') {
                let viewUrl = `{{ route('task-assign.view', ':id') }}`;
                viewUrl = viewUrl.replace(':id', id);
                buttons += `
                <a title="${messages.view}" href="${viewUrl}" class="btn btn-info action-btn has-icon view-btn" style="float:right;margin:2px;">
                    <i class="fa fa-eye"></i>
                </a>
            `;
            }

            if (permissions.deleteItem === 'true') {
                buttons += `
                <a title="${messages.delete}" href="#" class="btn btn-danger action-btn has-icon delete-btn" data-id="${id}" style="float:right;margin:2px;">
                    <i class="fa fa-trash"></i>
                </a>
            `;
            }
            return buttons;
        }
    </script>
@endsection
