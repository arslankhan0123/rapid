@extends('layouts.app')
@section('title')
    {{ __('messages.master_accounts.master_accounts') }}
@endsection
@section('page_css')
    <link href="{{ asset('assets/css/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
@endsection
<style>
    /* Ensure the table cell is flex and centers buttons */
    td .action-btns-wrapper {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 4px;
        /* slightly smaller spacing */
    }

    /* Square action buttons */
    td .action-btns-wrapper .btn {
        width: 30px;
        /* slightly smaller square */
        height: 30px;
        /* match width */
        padding: 0;
        /* remove default padding */
        display: flex;
        /* center icon */
        justify-content: center;
        align-items: center;
        border-radius: 4px;
        /* optional slight rounding */
        font-size: 14px;
        /* smaller icon */
    }

    /* Space between buttons */
    td .action-btns-wrapper a {
        margin-right: 4px;
        /* slightly smaller spacing */
    }

    /* Remove margin from last button */
    td .action-btns-wrapper a:last-child {
        margin-right: 0;
    }

    /* Optional: align buttons vertically in table cell */
    td .btn-group-td {
        display: flex;
        justify-content: center;
        gap: 4px;
        /* match wrapper gap */
    }
</style>
@section('content')
    <section class="section">
        <div class="section-header item-align-right">
            <h1>{{ __('messages.master_accounts.master_accounts') }}</h1>
            <div class="section-header-breadcrumb float-right">
                <div class="card-header-action mr-3 select2-mobile-margin">
                </div>
            </div>
            @can('create_master_accounts')
                <div class="float-right">
                    <a href="{{ route('master-accounts.create') }}" class="btn btn-primary form-btn">
                        {{ __('messages.master_accounts.add') }}
                    </a>
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
                    @include('master_accounts.table')
                </div>
            </div>
        </div>
    </section>
@endsection
@section('page_scripts')
    <script src="{{ asset('assets/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ mix('assets/js/custom/custom-datatable.js') }}"></script>
    <script src="{{ mix('assets/js/select2.min.js') }}"></script>

    {{-- ✅ DataTables Buttons --}}
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
@endsection

@section('scripts')
    <script>
        'use strict';

        let tbl = $('#masterAccountTable').DataTable({
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
                url: route('master-accounts.index'),
                beforeSend: function() {
                    startLoader();
                },
                complete: function() {
                    stopLoader();
                }
            },

            lengthMenu: [
                [10, 25, 50, 100, 300, 500, -1],
                [10, 25, 50, 100, 300, 500, "All"]
            ],
            pageLength: 25,

            columns: [{
                    data: row => row.account_number ? row.account_number : 'N/A',
                    name: 'account_number',
                    width: '15%'
                },
                {
                    data: 'name',
                    name: 'name',
                    width: '10%'
                },
                {
                    data: row => row.account_level,
                    name: 'account_level',
                    width: '10%'
                },
                {
                    data: row => row.account_type,
                    name: 'account_type',
                    width: '10%'
                },
                {
                    data: row => row.report_type ? row.report_type : 'N/A',
                    name: 'report_type',
                    orderable: true,
                    searchable: true,
                    width: '15%'
                },
                {
                    data: row => row.amount_type ? row.amount_type : 'N/A',
                    name: 'amount_type',
                    orderable: true,
                    searchable: true,
                    width: '10%'
                },
                {
                    data: row => renderActionButtons(row.id),
                    name: 'id',
                    width: '7%'
                }
            ],
            responsive: true,

            dom: '<"d-flex justify-content-between align-items-center mb-2"<"d-flex flex-column"<"d-flex mb-1"B><"d-flex"l>><"d-flex"f>>rtip',

            buttons: [{
                    extend: 'csvHtml5',
                    text: 'CSV',
                    className: 'btn btn-sm btn-outline-dark me-2',
                    bom: true,
                    charset: 'utf-8'
                },
                {
                    extend: 'pdfHtml5',
                    text: 'PDF',
                    className: 'btn btn-sm btn-outline-dark',
                    orientation: 'landscape',
                    pageSize: 'A4',
                    exportOptions: {
                        columns: ':visible'
                    },
                    customize: function(doc) {
                        doc.defaultStyle.fontSize = 10;
                    }
                }
            ]
        });

        // Action Buttons Events
        $(document).on('click', '.edit-btn', function(e) {
            window.location.href = route('master-accounts.edit', $(e.currentTarget).data('id'));
        });

        $(document).on('click', '.delete-btn', function(e) {
            let masterAccountId = $(e.currentTarget).data('id');
            deleteItem(route('master-accounts.destroy', masterAccountId), '#masterAccountTable',
                '{{ __('messages.master_accounts.master_account') }}');
        });

        $(document).on('click', '.view-btn', function(e) {
            window.location.href = route('master-accounts.show', $(e.currentTarget).data('id'));
        });

        // Messages + Permissions
        var messages = {
            delete: "{{ __('messages.common.delete') }}",
            edit: "{{ __('messages.common.edit') }}",
            view: "{{ __('messages.common.view') }}"
        };
        var permissions = {
            updateItem: "{{ auth()->user()->can('update_master_accounts') ? 'true' : 'false' }}",
            deleteItem: "{{ auth()->user()->can('delete_master_accounts') ? 'true' : 'false' }}",
            viewItem: "{{ auth()->user()->can('view_master_accounts') ? 'true' : 'false' }}"
        };

        function renderActionButtons(id) {
            let buttons = '';

            if (permissions.updateItem === 'true') {
                let editUrl = `{{ route('master-accounts.edit', ':id') }}`.replace(':id', id);
                buttons +=
                    `<a title="${messages.edit}" href="${editUrl}" class="btn btn-warning btn-sm edit-btn"><i class="fa fa-edit"></i></a>`;
            }

            if (permissions.viewItem === 'true') {
                let viewUrl = `{{ route('master-accounts.show', ':id') }}`.replace(':id', id);
                buttons +=
                    `<a title="${messages.view}" href="${viewUrl}" class="btn btn-info btn-sm view-btn"><i class="fa fa-eye"></i></a>`;
            }

            if (permissions.deleteItem === 'true') {
                buttons +=
                    `<a title="${messages.delete}" href="#" class="btn btn-danger btn-sm delete-btn" data-id="${id}"><i class="fa fa-trash"></i></a>`;
            }

            return `<div class="action-btns-wrapper">${buttons}</div>`;
        }
    </script>
@endsection
