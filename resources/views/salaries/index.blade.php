@extends('layouts.app')
@section('title')
    {{ __('messages.salaries.salaries') }}
@endsection
@section('page_css')
    <link href="{{ asset('assets/css/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ asset('assets/css/bs4-summernote/summernote-bs4.css') }}">
@endsection
@section('content')
    <section class="section">
        <div class="section-header item-align-right">
            <h1>{{ __('messages.salaries.salaries') }}</h1>
            <div class="section-header-breadcrumb float-right">
                <div class="card-header-action mr-3 select2-mobile-margin">
                </div>
            </div>
            @can('create_salary_advances')
                <div class="float-right">
                    <a href="{{ route('salaries.create') }}" class="btn btn-primary form-btn">
                        {{ __('messages.salaries.add') }} </a>
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
                    @include('salaries.table')
                </div>
            </div>
        </div>
    </section>
    @include('salaries.templates.templates')
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
                url: route('salaries.index'),
            },
            columnDefs: [{
                targets: 1, // Assuming the date is in the first column
                render: function(data, type, row) {
                    // Parse the date string
                    let date = new Date(data);

                    // Options for formatting the date
                    let options = {
                        year: 'numeric',
                        month: 'long'
                    };

                    // Format the date to 'Month Year' (e.g., 'August 2024')
                    return date.toLocaleDateString('en-US', options);
                }
            }],
            columns: [{
                    data: function(row) {
                        let element = document.createElement('textarea');
                        if (row.employee.name) {
                            return row.employee.name;
                        }
                        return '';
                    },
                    name: 'employee_id',
                    width: '30%'
                },
                {
                    data: function(row) {
                        let element = document.createElement('textarea');
                        return row.month;
                    },
                    name: 'month',
                    width: '20%'
                },
                {

                    data: function(row) {
                        let element = document.createElement('textarea');
                        return row.salary;
                    },
                    name: 'salary',
                    width: '20%'
                },
                {

                    data: function(row) {
                        let element = document.createElement('textarea');
                        return row.is_active ? "Active" : "Inactive"
                    },
                    name: 'status',
                    width: '10%'
                },
                {
                    data: function(row) {
                        return renderActionButtons(row.id);
                    },
                    name: 'id',
                    width: '20%'
                }
            ],
            responsive: true // Enable responsive features
        });

        $(document).on('click', '.edit-btn', function(event) {
            let did = $(event.currentTarget).data('id');
            const url = route('salaries.edit', did);
            window.location.href = url;
        });

        $(document).on('click', '.delete-btn', function(event) {
            let assetCateogryId = $(event.currentTarget).data('id');
            deleteItem(route('salaries.destroy', assetCateogryId), '#designationTable',
                '{{ __('messages.salaries.name') }}');
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
            updateItem: "{{ auth()->user()->can('update_salary_advances') ? 'true' : 'false' }}",
            deleteItem: "{{ auth()->user()->can('delete_salary_advances') ? 'true' : 'false' }}",
            viewItem: "{{ auth()->user()->can('view_salary_advances') ? 'true' : 'false' }}"
        };

        // Function to render action buttons based on permissions
        function renderActionButtons(id) {
            let buttons = '';



            if (permissions.updateItem === 'true') {
                let editUrl = `{{ route('salaries.edit', ':id') }}`;
                editUrl = editUrl.replace(':id', id);
                buttons += `
                <a title="${messages.edit}" href="${editUrl}" class="btn btn-warning action-btn has-icon edit-btn" style="float:right;margin:2px;">
                    <i class="fa fa-edit"></i>
                </a>
            `;
            }
            if (permissions.viewItem === 'true') {
                let viewUrl = `{{ route('salaries.view', ':id') }}`;
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
