@extends('layouts.app')
@section('title')
    {{ __('messages.branches.name') }}
@endsection
@section('page_css')
    <link href="{{ asset('assets/css/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ asset('assets/css/bs4-summernote/summernote-bs4.css') }}">
@endsection
@section('content')
    <section class="section">
        <div class="section-header item-align-right">
            <h1>{{ __('messages.branches.name') }}</h1>
            <div class="section-header-breadcrumb float-right">
                <div class="card-header-action mr-3 select2-mobile-margin">
                </div>
            </div>
            @can('create_branches')
                <div class="float-right">
                    <a href="{{ route('branches.create') }}" id="btnAdd" class="btn btn-primary form-btn">
                        {{ __('messages.branches.add') }} </a>
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
                    @include('branches.table')
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

        let designationCreateUrl = route('departments.store');
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
            ajax: {
                url: route('branches.index'),
                dataSrc: function(json) {
                    // Always show the button
                    $('#btnAdd').show();

                    // Return the data to populate the DataTable
                    return json.data;
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
            order: [[1, 'asc']],
            columns: [{
                    data: function(row) {
                        // Decode HTML entities
                        let element = document.createElement('textarea');
                        element.innerHTML = row.company_name ?? '';
                        return element.value; // Decoded plain text
                    },
                    name: 'company_name',
                    width: '15%'
                }, {
                    data: function(row) {
                        let element = document.createElement('textarea');
                        return row.name;
                    },
                    name: 'name',
                    width: '15%'
                }, {
                    data: function(row) {
                        let element = document.createElement('textarea');
                        if (row.vat_number) {
                            return row.vat_number;
                        }
                        return '';
                    },
                    name: 'vat_number',
                    width: '15%'
                }, {
                    data: function(row) {
                        let element = document.createElement('textarea');
                        if (row.phone) {
                            return row.phone;
                        }
                        return '';
                    },
                    name: 'phone',
                    width: '20%'

                },
                {
                    data: function(row) {
                        let element = document.createElement('textarea');
                        if (row.country && row.country.name) {
                            return row.country.name;
                        }
                        return '';
                    },
                    name: 'country',
                    orderable: false, // Disable sorting
                    searchable: false // Disable searching
                },
                {
                    data: function(row) {
                        let element = document.createElement('textarea');
                        if (row.city) {
                            return row.city;
                        }
                        return '';
                    },
                    name: 'city',
                    width: '10%'
                },
                {
                    data: function(row) {
                        return row.bank?.name ?? '';

                    },
                    name: 'bank.name',
                    width: '10%'
                },
                {
                    data: function(row) {
                        return renderActionButtons(row.id);
                    },
                    name: 'id',
                    width: '25%'
                }
            ],
            responsive: true // Enable responsive features
        });


        $(document).on('click', '.edit-btn', function(event) {
            let did = $(event.currentTarget).data('id');
            const url = route('branches.edit', did);
            window.location.href = url;
        });

        $(document).on('click', '.delete-btn', function(event) {
            let assetCateogryId = $(event.currentTarget).data('id');
            deleteItem(route('branches.destroy', assetCateogryId), '#designationTable',
                '{{ __('messages.branches.name') }}');
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
            updateItem: "{{ auth()->user()->can('update_branches') ? 'true' : 'false' }}",
            deleteItem: "{{ auth()->user()->can('delete_branches') ? 'true' : 'false' }}",
            viewItem: "{{ auth()->user()->can('view_branches') ? 'true' : 'false' }}"
        };

        // Function to render action buttons based on permissions
        function renderActionButtons(id) {
            let buttons = '';



            if (permissions.updateItem === 'true') {
                let editUrl = `{{ route('branches.edit', ':id') }}`;
                editUrl = editUrl.replace(':id', id);
                buttons += `
                <a title="${messages.edit}" href="${editUrl}" class="btn btn-warning action-btn has-icon edit-btn" style="float:right;margin:2px;">
                    <i class="fa fa-edit"></i>
                </a>
            `;
            }
            if (permissions.viewItem === 'true') {
                let viewUrl = `{{ route('branches.view', ':id') }}`;
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
