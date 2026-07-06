@extends('layouts.app')
@section('title')
    {{ __('messages.certificate.name') }}
@endsection
@section('page_css')
    <link href="{{ asset('assets/css/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ asset('assets/css/bs4-summernote/summernote-bs4.css') }}">
@endsection
@section('content')
    <section class="section">
        <div class="section-header item-align-right">
            <h1>{{ __('messages.certificate.name') }}</h1>
            <div class="section-header-breadcrumb float-right">
                <div class="card-header-action mr-3 select2-mobile-margin">
                </div>
            </div>
            @can('view_certificate')
                <!-- Export Button -->
                <div class="mr-3">
                    <button id="exportButton" class="btn btnSecondary text-white " style="background: orange !important;">
                        Export
                    </button>
                </div>
            @endcan
            @can('create_certificate')
                <div class="float-right">
                    <a href="{{ route('certificate.create') }}" class="btn btn-primary form-btn">
                        {{ __('messages.common.add') }} </a>
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
                    @include('certificate.table_unit')
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

        let categoryCreateUrl = route('certificate.store');
        let categoryUrl = route('certificate.index') + '/';

        let tbl = $('#assetCategoryTable').DataTable({
            oLanguage: {
                'sEmptyTable': Lang.get('messages.common.no_data_available_in_table'),
                'sInfo': Lang.get('messages.common.data_base_entries'),
                sLengthMenu: Lang.get('messages.common.menu_entry'),
                sInfoEmpty: Lang.get('messages.common.no_entry'),
                sInfoFiltered: Lang.get('messages.common.filter_by'),
                sZeroRecords: Lang.get('messages.common.no_matching'),
            },
            lengthMenu: [
                [10, 25, 50, 100, -1],
                [10, 25, 50, 100, "All"]
            ],
            processing: true,
            serverSide: true,
            order: [
                [1, 'desc']
            ],
            ajax: {
                url: route('certificate.index'),
            },
            columns: [{
                    data: function(row) {
                        let element = document.createElement('textarea');
                        element.innerHTML = row.certificate_number ?? '';
                        return element.value;
                    },
                    name: 'certificate_number',

                }, {
                    data: function(row) {
                        let element = document.createElement('textarea');
                        element.innerHTML = row.type?.name ?? '';
                        return element.value;
                    },
                    name: 'certificate_number',

                }, {
                    data: function(row) {
                        let element = document.createElement('textarea');
                        element.innerHTML = row.formatted_date;
                        return element.value;
                    },
                    name: 'date',

                }, {
                    data: function(row) {
                        let element = document.createElement('textarea');
                        element.innerHTML = row
                            .employee; // Assuming your data source has a 'description' field
                        return element.value;
                    },
                    name: 'employee',

                }, {
                    data: function(row) {
                        let element = document.createElement('textarea');
                        element.innerHTML = row
                            .lab_manager; // Assuming your data source has a 'description' field
                        return element.value;
                    },
                    name: 'lab_manager',

                }, {
                    data: function(row) {
                        let element = document.createElement('textarea');
                        element.innerHTML = row
                            .general_manager; // Assuming your data source has a 'description' field
                        return element.value;
                    },
                    name: 'general_manager',

                }, {
                    data: function(row) {
                        let element = document.createElement('textarea');
                        element.innerHTML = row
                            .description; // Assuming your data source has a 'description' field
                        return element.value;
                    },
                    name: 'description',
                    width: '15%'

                },
                {
                    data: function(row) {
                        return renderActionButtons(row.id);
                    },
                    name: 'id',
                    width: '10%'

                }
            ],
            responsive: true // Enable responsive features
        });
        $(document).on('click', '.edit-btn', function(event) {
            let assetCateogryId = $(event.currentTarget).data('id');
            const url = route('certificate.edit', assetCateogryId);
            window.location.href = url;
        });

        $(document).on('click', '.delete-btn', function(event) {
            let assetCateogryId = $(event.currentTarget).data('id');
            deleteItem(route('certificate.destroy', assetCateogryId), '#assetCategoryTable',
                "{{ __('messages.certificate.name') }}");
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
            updateItem: "{{ auth()->user()->can('update_certificate') ? 'true' : 'false' }}",
            deleteItem: "{{ auth()->user()->can('delete_certificate') ? 'true' : 'false' }}",
            viewItem: "{{ auth()->user()->can('view_certificate') ? 'true' : 'false' }}"
        };
        // Function to render action buttons based on permissions
        function renderActionButtons(id) {
            let buttons = '';
            if (permissions.updateItem === 'true') {
                let editUrl = `{{ route('certificate.edit', ':id') }}`;
                editUrl = editUrl.replace(':id', id);
                buttons += `
                <a title="${messages.edit}" href="${editUrl}" class="btn btn-warning action-btn has-icon edit-btn" style="float:right;margin:2px;">
                    <i class="fa fa-edit"></i>
                </a>
            `;
            }
            if (permissions.viewItem === 'true') {
                let viewUrl = `{{ route('certificate.view', ':id') }}`;
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
    <script>
        // Export button click event
        $('#exportButton').on('click', function() {
            // Build the URL
            window.location.href = `{{ route('certificate.export') }}`;
        });
    </script>
@endsection
