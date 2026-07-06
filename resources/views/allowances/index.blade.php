@extends('layouts.app')
@section('title')
    {{ __('messages.allowances.allowances') }}
@endsection
@section('page_css')
    <link href="{{ asset('assets/css/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ asset('assets/css/bs4-summernote/summernote-bs4.css') }}">
@endsection
@section('content')
    <section class="section">
        <div class="section-header item-align-right">
            <h1>{{ __('messages.allowances.name') }} {{ __('messages.allowances.list') }}</h1>
            <div class="section-header-breadcrumb float-right">
                <div class="card-header-action mr-3 select2-mobile-margin">

                </div>
            </div>
            @can('create_allowances')
                <div class="float-right">
                    <a href="{{ route('allowances.create') }}" class="btn btn-primary form-btn">
                        {{ __('messages.allowances.add') }} </a>
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
                    @include('allowances.table')
                </div>
            </div>
        </div>
    </section>
    @include('allowances.templates.templates')
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

        let designationCreateUrl = route('allowances.store');
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
                url: route('allowances.index'),
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
            columns: [

                {
                    data: function(row) {
                        let element = document.createElement('textarea');
                        element.innerHTML = row.employee?.branch?.name ?? '';
                        return element.value;
                    },
                    name: 'employee.branch.name',
                    width: '10%'
                }, {
                    data: function(row) {
                        let element = document.createElement('textarea');
                        element.innerHTML = row.allowance_types.name;
                        return element.value;
                    },
                    name: 'allowance_type_id',
                    width: '13%'
                }, {
                    data: function(row) {
                        let element = document.createElement('textarea');
                        element.innerHTML = row.employee.iqama_no;
                        return element.value;
                    },
                    name: 'iqama_no',
                    width: '10%'
                }, {
                    data: function(row) {
                        let element = document.createElement('textarea');
                        element.innerHTML = row.employee.name;
                        return element.value;
                    },
                    name: 'employee.name',
                    width: '15%'
                },

                // {
                //     data: function(row) {
                //         let element = document.createElement('textarea');
                //         element.innerHTML = row.payment_type_name ?? '';
                //         return element.value;
                //     },

                //     width: '12%'
                // },

                {
                    data: function(row) {
                        let element = document.createElement('p');
                        let formattedAmount = parseFloat(row.amount).toFixed(
                            2); // Format the amount to 2 decimal places
                        element.textContent = formattedAmount;
                        element.classList.add('text-right'); // Add class to the span for right-alignment
                        return element.outerHTML;
                    },
                    name: 'amount',
                    width: '12%'
                }, {
                    data: function(row) {
                        let element = document.createElement('textarea');
                        element.innerHTML = row.description;
                        return element.value;
                    },
                    name: 'description',

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
            let did = $(event.currentTarget).data('id');
            const url = route('allowances.edit', did);
            window.location.href = url;
        });
        $(document).on('click', '.delete-btn', function(event) {
            let assetCateogryId = $(event.currentTarget).data('id');
            deleteItem(route('allowances.destroy', assetCateogryId), '#designationTable',
                '{{ __('messages.allowances.name') }}');
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
            updateItem: "{{ auth()->user()->can('update_allowances') ? 'true' : 'false' }}",
            deleteItem: "{{ auth()->user()->can('delete_allowances') ? 'true' : 'false' }}",
            viewItem: "{{ auth()->user()->can('view_allowances') ? 'true' : 'false' }}"
        };

        // Function to render action buttons based on permissions
        function renderActionButtons(id) {
            let buttons = '';
            if (permissions.updateItem === 'true') {
                let editUrl = `{{ route('allowances.edit', ':id') }}`;
                editUrl = editUrl.replace(':id', id);
                buttons += `
                <a title="${messages.edit}" href="${editUrl}" class="btn btn-warning action-btn has-icon edit-btn" style="float:right;margin:2px;">
                    <i class="fa fa-edit"></i>
                </a>
            `;
            }
            if (permissions.viewItem === 'true') {
                let viewUrl = `{{ route('allowances.view', ':id') }}`;
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
