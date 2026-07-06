@extends('layouts.app')
@section('title')
    {{ __('messages.shifts.shifts') }}
@endsection
@section('page_css')
    <link href="{{ asset('assets/css/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ asset('assets/css/bs4-summernote/summernote-bs4.css') }}">
@endsection
@section('content')
    <section class="section">
        <div class="section-header item-align-right">
            <h1>{{ __('messages.shifts.name') }} {{ __('messages.shifts.list') }}</h1>
            <div class="section-header-breadcrumb float-right">
                <div class="card-header-action mr-3 select2-mobile-margin">
                </div>
            </div>
            @can('create_shifts')
                <div class="float-right">
                    <a href="{{ route('shifts.create') }}" class="btn btn-primary form-btn">
                        {{ __('messages.shifts.add') }} </a>
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
                    @include('shifts.table')
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
                url: route('shifts.index'),
            },
            columns: [{
                    data: function(row) {
                        let element = document.createElement('textarea');
                        return row.name;
                    },
                    name: 'name',
                    width: '10%'
                }, {
                    data: function(row) {
                        // Convert the shift_start_time to 12-hour format
                        return formatTimeTo12Hour(row.shift_start_time);
                    },
                    name: 'shift_start_time',
                    width: '10%'
                },
                {
                    data: function(row) {
                        // Convert the shift_end_time to 12-hour format
                        return formatTimeTo12Hour(row.shift_end_time);
                    },
                    name: 'shift_end_time',
                    width: '10%'
                },
                {
                    data: function(row) {
                        // Convert the lunch_start_time to 12-hour format
                        return row.lunch_start_time ? formatTimeTo12Hour(row.lunch_start_time) : '';
                    },
                    name: 'lunch_start_time',
                    width: '12%'
                },
                {
                    data: function(row) {
                        // Convert the lunch_end_time to 12-hour format
                        return row.lunch_end_time ? formatTimeTo12Hour(row.lunch_end_time) : '';
                    },
                    name: 'lunch_end_time',
                    width: '10%'
                }, {
                    data: function(row) {
                        return row.color; // Return color data
                    },
                    name: 'color',
                    width: '10%',
                    render: function(data, type, row) {
                        // Return a styled div with the background color set to the value of `data`
                        return '<div style="width: 30px; height: 30px; border-radius: 10%; background-color: ' +
                            data + ';"></div>';
                    }
                }, {

                    data: function(row) {
                        let element = document.createElement('textarea');
                        element.innerHTML = row
                            .description; // Assuming your data source has a 'description' field
                        return element.value;
                    },
                    name: 'description',
                    width: '20%'
                }, {
                    data: function(row) {
                        return renderActionButtons(row.id);
                    },
                    name: 'id',
                }
            ],
            responsive: true // Enable responsive features
        });

        $(document).on('click', '.edit-btn', function(event) {
            let did = $(event.currentTarget).data('id');
            const url = route('shifts.edit', did);
            window.location.href = url;
        });
        $(document).on('click', '.delete-btn', function(event) {
            let assetCateogryId = $(event.currentTarget).data('id');
            deleteItem(route('shifts.destroy', assetCateogryId), '#designationTable',
                '{{ __('messages.shifts.name') }}');
        });

        function formatTimeTo12Hour(time24) {
            if (!time24) return ''; // Handle null or empty values

            let [hours, minutes] = time24.split(':').map(Number);
            let ampm = hours >= 12 ? 'PM' : 'AM';
            hours = hours % 12;
            hours = hours || 12; // the hour '0' should be '12'
            minutes = minutes < 10 ? '0' + minutes : minutes;

            return `${hours}:${minutes} ${ampm}`;
        }
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
            updateItem: "{{ auth()->user()->can('update_shifts') ? 'true' : 'false' }}",
            deleteItem: "{{ auth()->user()->can('delete_shifts') ? 'true' : 'false' }}",
            viewItem: "{{ auth()->user()->can('view_shifts') ? 'true' : 'false' }}"
        };

        // Function to render action buttons based on permissions
        function renderActionButtons(id) {
            let buttons = '';



            if (permissions.updateItem === 'true') {
                let editUrl = `{{ route('shifts.edit', ':id') }}`;
                editUrl = editUrl.replace(':id', id);
                buttons += `
                <a title="${messages.edit}" href="${editUrl}" class="btn btn-warning action-btn has-icon edit-btn" style="float:right;margin:2px;">
                    <i class="fa fa-edit"></i>
                </a>
            `;
            }
            if (permissions.viewItem === 'true') {
                let viewUrl = `{{ route('shifts.view', ':id') }}`;
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
