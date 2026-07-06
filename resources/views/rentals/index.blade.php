@extends('layouts.app')
@section('title')
    {{ __('messages.rentals.rentals') }}
@endsection
@section('page_css')
    <link href="{{ asset('assets/css/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ asset('assets/css/bs4-summernote/summernote-bs4.css') }}">
@endsection
@section('content')
    <section class="section">
        <div class="section-header item-align-right">
            <h1>{{ __('messages.rentals.rentals') }}</h1>
            <div class="section-header-breadcrumb float-right">
                <div class="card-header-action mr-3 select2-mobile-margin">
                </div>
            </div>
            @can('create_rentals')
                <div class="float-right">
                    <a href="{{ route('rentals.create') }}" class="btn btn-primary form-btn">
                        {{ __('messages.rentals.add') }} </a>
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
                    @include('rentals.table')
                </div>
            </div>
        </div>
    </section>
    @include('rentals.templates.templates')
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
                url: route('rentals.index'),
            },
            columns: [{
                    data: function(row) {
                        let element = document.createElement('textarea');
                        return row.supplier.company_name;
                    },
                    name: 'supplier_id',
                    width: '15%'
                },
                {
                    data: function(row) {
                        // Parse the start_date string into a Date object
                        let date = new Date(row.start_date);

                        // Options for formatting the date
                        let options = {
                            year: 'numeric',
                            month: 'short',
                            day: 'numeric'
                        };

                        // Format the date
                        let formattedDate = date.toLocaleDateString('en-US', options);

                        // Return the formatted date
                        return formattedDate;
                    },
                    name: 'start_date',
                    width: '10%'
                },
                {
                    data: function(row) {
                        // Parse the start_date string into a Date object
                        let date = new Date(row.end_date);

                        // Options for formatting the date
                        let options = {
                            year: 'numeric',
                            month: 'short',
                            day: 'numeric'
                        };

                        // Format the date
                        let formattedDate = date.toLocaleDateString('en-US', options);

                        // Return the formatted date
                        return formattedDate;
                    },
                    name: 'end_date',
                    width: '10%'
                }, {

                    data: function(row) {
                        let element = document.createElement('textarea');
                        return row.type
                    },
                    name: 'type',
                    width: '10%'
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
                        let element = document.createElement('textarea');
                        return row.tax_amount;
                    },
                    name: 'tax_amount',
                    width: '5%'
                },
                {
                    data: function(row) {
                        let element = document.createElement('textarea');
                        return row.total_rent_amount;
                    },
                    name: 'total_rent_amount',
                    width: '5%'
                },
                {
                    data: function(row) {
                        return renderActionButtons(row.id);
                    },
                    name: 'id',
                    width:'200px'
                }
            ],
            responsive: true // Enable responsive features
        });

        $(document).on('click', '.edit-btn', function(event) {
            let did = $(event.currentTarget).data('id');
            const url = route('rentals.edit', did);
            window.location.href = url;
        });

        $(document).on('click', '.delete-btn', function(event) {
            let assetCateogryId = $(event.currentTarget).data('id');
            deleteItem(route('rentals.destroy', assetCateogryId), '#designationTable',
                '{{ __('messages.rentals.name') }}');
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
            updateItem: "{{ auth()->user()->can('update_rentals') ? 'true' : 'false' }}",
            deleteItem: "{{ auth()->user()->can('delete_rentals') ? 'true' : 'false' }}",
            viewItem: "{{ auth()->user()->can('view_rentals') ? 'true' : 'false' }}"
        };
        // Function to render action buttons based on permissions
        function renderActionButtons(id) {
            let buttons = '';
            if (permissions.updateItem === 'true') {
                let editUrl = `{{ route('rentals.edit', ':id') }}`;
                editUrl = editUrl.replace(':id', id);
                buttons += `
                <a title="${messages.edit}" href="${editUrl}" class="btn btn-warning action-btn has-icon edit-btn" style="float:right;margin:2px;">
                    <i class="fa fa-edit"></i>
                </a>
            `;
            }
            if (permissions.viewItem === 'true') {
                let viewUrl = `{{ route('rentals.view', ':id') }}`;
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
