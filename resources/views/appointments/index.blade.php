@extends('layouts.app')
@section('title')
    {{ __('messages.appointments.appointments') }}
@endsection
@section('page_css')
    <link href="{{ asset('assets/css/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ asset('assets/css/bs4-summernote/summernote-bs4.css') }}">
@endsection
@section('content')
    <section class="section">
        <div class="section-header item-align-right">
            <h1>{{ __('messages.appointments.appointments') }} {{ __('messages.appointments.list') }}</h1>
            <div class="section-header-breadcrumb float-right">
                <div class="card-header-action mr-3 select2-mobile-margin">
                </div>
            </div>
            @can('create_appointments')
                <div class="float-right">
                    <a href="{{ route('appointments.create') }}" class="btn btn-primary form-btn">
                        {{ __('messages.appointments.add') }} </a>
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
                    @include('appointments.table')
                </div>
            </div>
        </div>
    </section>
    @include('appointments.templates.templates')
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
            order: [],
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
                url: route('appointments.index'),
                beforeSend: function() {
                    startLoader();
                },
                complete: function() {
                    stopLoader();
                }
            },
            lengthMenu: [
                [10, 25, 50, 100, -1],
                [10, 25, 50, 100, "All"]
            ],
            pageLength: 100, // Default page length
            columns: [
                {
                    data: function(row) {
                        let date = new Date(row.appointment_date);
                        let formattedDate = date.toLocaleDateString('en-GB'); // dd/mm/yyyy
                        return formattedDate;
                    },
                    name: 'appointment_date',
                    className: 'text-center'
                },
                {
                    data: function(row) {
                        let time = new Date('1970-01-01T' + row.appointment_time); // assuming time is like "17:45:00"
                        let formattedTime = time.toLocaleTimeString('en-US', {
                            hour: '2-digit',
                            minute: '2-digit',
                            hour12: true
                        });
                        return formattedTime;
                    },
                    name: 'appointment_time',
                    className: 'text-center'
                },{
                    data: function(row) {
                        let element = document.createElement('textarea');
                        element.innerHTML = row.name;
                        return element.value;
                    },
                    name: 'name',
                }, {
                    data: function(row) {
                        let element = document.createElement('textarea');
                        return row.mobile;
                    },
                    name: 'mobile',
                },
                {
                    data: function(row) {
                        let element = document.createElement('textarea');
                        return row.email;
                    },
                    name: 'email',
                },
                
                {
                    data: function(row) {
                        let element = document.createElement('textarea');
                        return row.appointed_by;
                    },
                    name: 'appointed_by',
                },
                {
                    data: function(row) {
                        return renderActionButtons(row.id);
                    },
                    name: 'id',
                    width: '200px'
                }
            ],
            responsive: true,
            createdRow: function(row, data, dataIndex) {
                let today = new Date().toISOString().split('T')[0]; // Format: yyyy-mm-dd
                let appointmentDate = new Date(data.appointment_date).toISOString().split('T')[0];
            
                if (appointmentDate === today) {
                    $(row).css('background-color', '#f8d7da'); // Light red background
                    $(row).css('color', '#721c24'); // Optional: darker red text
                }
            },
        });

        $(document).on('click', '.edit-btn', function(event) {
            let did = $(event.currentTarget).data('id');
            const url = route('appointments.edit', did);
            window.location.href = url;
        });
        $(document).on('click', '.delete-btn', function(event) {
            let assetCateogryId = $(event.currentTarget).data('id');
            deleteItem(route('appointments.destroy', assetCateogryId), '#designationTable',
                '{{ __('messages.appointments.appointments') }}');
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
            updateItem: "{{ auth()->user()->can('update_appointments') ? 'true' : 'false' }}",
            deleteItem: "{{ auth()->user()->can('delete_appointments') ? 'true' : 'false' }}",
            viewItem: "{{ auth()->user()->can('view_appointments') ? 'true' : 'false' }}"
        };

        // Function to render action buttons based on permissions
        function renderActionButtons(id) {
            let buttons = '';



            if (permissions.updateItem === 'true') {
                let editUrl = `{{ route('appointments.edit', ':id') }}`;
                editUrl = editUrl.replace(':id', id);
                buttons += `
                <a title="${messages.edit}" href="${editUrl}" class="btn btn-warning action-btn has-icon edit-btn" style="float:right;margin:2px;">
                    <i class="fa fa-edit"></i>
                </a>
            `;
            }
            if (permissions.viewItem === 'true') {
                let viewUrl = `{{ route('appointments.view', ':id') }}`;
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
