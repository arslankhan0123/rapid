@extends('layouts.app')
@section('title')
    {{ __('messages.deductions.deductions') }}
@endsection
@section('page_css')
    <link href="{{ asset('assets/css/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ asset('assets/css/bs4-summernote/summernote-bs4.css') }}">
@endsection
@section('content')
    <section class="section">
        <div class="section-header item-align-right">
            <h1>{{ __('messages.deductions.name') }} {{ __('messages.deductions.list') }}</h1>
            <div class="section-header-breadcrumb float-right">
                <div class="card-header-action mr-3 select2-mobile-margin">
                </div>
            </div>
            @can('create_deductions')
                <div class="float-right">
                    <a href="{{ route('deductions.create') }}" class="btn btn-primary form-btn">
                        {{ __('messages.deductions.add') }} </a>
                </div>
            @endcan
        </div>
        <div class="section-body">
            <div class="card">
                <div class="card-body">
                    @include('deductions.table')
                </div>
            </div>
        </div>
    </section>
    @include('deductions.templates.templates')
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
                url: route('deductions.index'),
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
                }, {
                    data: function(row) {
                        let element = document.createElement('textarea');
                        element.innerHTML = row.deduction_types.name;
                        return element.value;
                    },
                    name: 'deduction_type_id',
                    width: '10%'
                },
                {
                    data: function(row) {
                        let element = document.createElement('textarea');
                        element.innerHTML = row.date ?? '';

                        // Check if the date is valid
                        if (element.value) {
                            let date = new Date(element.value);

                            // Ensure the date is valid
                            if (!isNaN(date)) {
                                // Format the date as 'DD-MM-YYYY'
                                let day = String(date.getDate()).padStart(2,
                                    '0'); // Ensure 2 digits for day
                                let month = String(date.getMonth() + 1).padStart(2,
                                    '0'); // Months are 0-indexed
                                let year = date.getFullYear();

                                return `${day}-${month}-${year}`; // Combine in 'DD-MM-YYYY' format
                            }
                        }

                        return '';
                    },
                    name: 'date',
                    width: '9%'
                }, {
                    data: function(row) {
                        let element = document.createElement('textarea');
                        element.innerHTML = row.employee.iqama_no ?? '';
                        return element.value;
                    },
                    name: 'employee.iqama_no',
                    width: '10%'
                }, {
                    data: function(row) {
                        let element = document.createElement('textarea');
                        element.innerHTML = row.employee.name;
                        return element.value;
                    },
                    name: 'employee.name',
                    width: '15%'
                }, {
                    data: function(row) {
                        let element = document.createElement('textarea');
                        element.innerHTML = row.employee?.branch?.name ?? '';
                        return element.value;
                    },
                    name: 'employee.branch.name',
                    width: '15%'
                }, {
                    data: function(row) {
                        let amount = isNaN(parseFloat(row.amount)) ? row.amount : parseFloat(row.amount)
                            .toFixed(2);
                        return `<div class="text-right">${amount}</div>`;
                    },
                    name: 'amount',
                    width: '12%'
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
                        return renderActionButtons(row.id);
                    },
                    name: 'id',
                    width: '10%'
                }
            ],
            responsive: true,
            order: [
                [1, 'desc']
            ],
        });

        $(document).on('click', '.edit-btn', function(event) {
            let did = $(event.currentTarget).data('id');
            const url = route('deductions.edit', did);
            window.location.href = url;
        });
        $(document).on('click', '.delete-btn', function(event) {
            let assetCateogryId = $(event.currentTarget).data('id');
            deleteItem(route('deductions.destroy', assetCateogryId), '#designationTable',
                '{{ __('messages.deductions.name') }}');
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
            updateItem: "{{ auth()->user()->can('update_deductions') ? 'true' : 'false' }}",
            deleteItem: "{{ auth()->user()->can('delete_deductions') ? 'true' : 'false' }}",
            viewItem: "{{ auth()->user()->can('view_deductions') ? 'true' : 'false' }}"
        };
        // Function to render action buttons based on permissions
        function renderActionButtons(id) {
            let buttons = '';
            if (permissions.updateItem === 'true') {
                let editUrl = `{{ route('deductions.edit', ':id') }}`;
                editUrl = editUrl.replace(':id', id);
                buttons += `
                <a title="${messages.edit}" href="${editUrl}" class="btn btn-warning action-btn has-icon edit-btn" style="float:right;margin:2px;">
                    <i class="fa fa-edit"></i>
                </a>
            `;
            }
            if (permissions.viewItem === 'true') {
                let viewUrl = `{{ route('deductions.view', ':id') }}`;
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
