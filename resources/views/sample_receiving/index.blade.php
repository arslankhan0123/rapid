@extends('layouts.app')
@section('title')
    {{ __('messages.sample_receiving.name') }}
@endsection
@section('page_css')
    <link href="{{ asset('assets/css/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ asset('assets/css/bs4-summernote/summernote-bs4.css') }}">
@endsection
@section('content')
    <section class="section">
        <div class="section-header item-align-right">
            <h1>{{ __('messages.sample_receiving.name') }}</h1>
            <div class="section-header-breadcrumb float-right">
                <div class="card-header-action mr-3 select2-mobile-margin">
                    <div class="mr-2">

                        {{ Form::select('branches', $usersBranches ?? [], null, ['id' => 'filterBranch', 'class' => 'form-control select2', 'placeholder' => __('messages.placeholder.branches')]) }}
                    </div>
                </div>
            </div>

            @can('view_sample_receiving')
                <!-- Export Button -->
                <div class="mr-3">
                    <button id="exportButton" class="btn btnSecondary text-white " style="background: orange !important;">
                        Export
                    </button>
                </div>
            @endcan
            @can('create_sample_receiving')
                <div class="float-right">

                    <a href="{{ route('sample_receiving.create') }}" class="btn btn-primary" style="line-height: 30px;">
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
                    @include('sample_receiving.table_unit')
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

        let categoryCreateUrl = route('sample_receiving.store');
        let categoryUrl = route('sample_receiving.index') + '/';

        let tbl = $('#assetCategoryTable').DataTable({
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
                [1, 'desc']
            ],
            ajax: {
                url: route('sample_receiving.index'),
                data: function(d) {
                    d.filterBranch = $("#filterBranch").val();
                },
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
                    data: function(row) {
                        let element = document.createElement('textarea');
                        element.innerHTML = row.branch?.name ??
                            ''; // Assuming your data source has a 'description' field
                        return element.value;
                    },
                    name: 'branch.name',
                    width: '8%'
                }, {
                    data: function(row) {
                        let element = document.createElement('textarea');
                        element.innerHTML = row.formatted_date;
                        return element.value;
                    },
                    name: 'date',
                    width: '10%'
                }, {
                    data: function(row) {
                        let element = document.createElement('textarea');
                        element.innerHTML = row
                            .time; // Assuming your data source has a 'description' field
                        return element.value;
                    },
                    name: 'time',
                    width: '10%'
                }, {
                    data: function(row) {
                        let element = document.createElement('textarea');
                        element.innerHTML = row.category?.name ??
                            ''; // Assuming your data source has a 'description' field
                        return element.value;
                    },
                    name: 'category.name',
                    width: '5%'
                }, {
                    data: function(row) {
                        let element = document.createElement('textarea');
                        element.innerHTML = row
                            .client_name; // Assuming your data source has a 'description' field
                        return element.value;
                    },
                    name: 'client_name',
                    width: '8%'
                }, {
                    data: function(row) {
                        let element = document.createElement('textarea');
                        element.innerHTML = row
                            .client_reference; // Assuming your data source has a 'description' field
                        return element.value;
                    },
                    name: 'client_reference',
                    width: '10%'
                }, {
                    data: function(row) {
                        let element = document.createElement('textarea');
                        element.innerHTML = row
                            .type_of_sample; // Assuming your data source has a 'description' field
                        return element.value;
                    },
                    name: 'type_of_sample',
                    width: '10%'
                }, {
                    data: function(row) {
                        let element = document.createElement('textarea');
                        element.innerHTML = row
                            .required_tests; // Assuming your data source has a 'description' field
                        return element.value;
                    },
                    name: 'required_tests',
                    width: '8%'
                }, {
                    data: function(row) {
                        let element = document.createElement('textarea');
                        element.innerHTML = row
                            .number_of_sample; // Assuming your data source has a 'number_of_sample' field
                        return element.value;
                    },
                    name: 'number_of_sample',
                    width: '10%'
                }, {
                    data: function(row) {
                        let element = document.createElement('textarea');
                        element.innerHTML = row.delivered_by?.name ??
                            ''; // Assuming your data source has a 'number_of_sample' field
                        return element.value;
                    },
                    name: 'deliveredBy.name',
                    width: '5%'
                }, {
                    data: function(row) {
                        let element = document.createElement('textarea');
                        element.innerHTML = row.received_by?.name ??
                            ''; // Assuming your data source has a 'received_by' field
                        return element.value;
                    },
                    name: 'receivedBy.name',
                    width: '7%'
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
        $('#filterBranch').change(function() {
            tbl.ajax.reload(); // Reload DataTable with new status filter
        });
        $(document).on('click', '.edit-btn', function(event) {
            let assetCateogryId = $(event.currentTarget).data('id');
            const url = route('sample_receiving.edit', assetCateogryId);
            window.location.href = url;
        });

        $(document).on('click', '.delete-btn', function(event) {
            let assetCateogryId = $(event.currentTarget).data('id');
            deleteItem(route('sample_receiving.destroy', assetCateogryId), '#assetCategoryTable',
                "{{ __('messages.sample_receiving.name') }}");
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
            updateItem: "{{ auth()->user()->can('update_sample_receiving') ? 'true' : 'false' }}",
            deleteItem: "{{ auth()->user()->can('delete_sample_receiving') ? 'true' : 'false' }}",
            viewItem: "{{ auth()->user()->can('view_sample_receiving') ? 'true' : 'false' }}"
        };
        // Function to render action buttons based on permissions
        function renderActionButtons(id) {
            let buttons = '';
            if (permissions.updateItem === 'true') {
                let editUrl = `{{ route('sample_receiving.edit', ':id') }}`;
                editUrl = editUrl.replace(':id', id);
                buttons += `
                <a title="${messages.edit}" href="${editUrl}" class="btn btn-warning action-btn has-icon edit-btn" style="float:right;margin:2px;">
                    <i class="fa fa-edit"></i>
                </a>
            `;
            }
            if (permissions.viewItem === 'true') {
                let viewUrl = `{{ route('sample_receiving.view', ':id') }}`;
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
            window.location.href = `{{ route('sample_receiving.export') }}`;
        });
    </script>
@endsection
