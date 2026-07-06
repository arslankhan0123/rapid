@extends('layouts.app')
@section('title')
    {{ __('messages.revokes.revokes') }}
@endsection
@section('page_css')
    <link href="{{ asset('assets/css/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ asset('assets/css/bs4-summernote/summernote-bs4.css') }}">
@endsection
@section('content')
    <section class="section">
        <div class="section-header item-align-right">
            <h1>{{ __('messages.revokes.revokes') }}</h1>
            <div class="section-header-breadcrumb float-right">
                <div class="card-header-action mr-3 select2-mobile-margin">
                </div>
            </div>
            @can('create_revokes')
                <div class="float-right">
                    <a href="{{ route('revokes.create') }}" class="btn btn-primary form-btn">
                        {{ __('messages.revokes.revoke_termination') }}
                    </a>
                </div>
            @endcan
            {{-- @can('create_terminations')
                <div class="float-right">
                    <a href="{{ route('terminations.create') }}" class="btn btn-primary form-btn">
                        {{ __('messages.terminations.add') }} </a>
                </div>
            @endcan --}}
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
                    @include('revokes.table')
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

        let tbl = $('#revokeTable').DataTable({
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
                url: route('revokes.index'),
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
                        if (row.employee.iqama_no) {
                            return row.employee.iqama_no;
                        }
                        return '';
                    },
                    name: 'iqama_no',
                    width: '10%'
                },
                {
                    data: function(row) {
                        let element = document.createElement('textarea');
                        if (row.employee.name) {
                            return row.employee.name;
                        }
                        return '';
                    },
                    name: 'employee.name',
                    width: '15%'
                },
                {
                    data: function(row) {
                        let element = document.createElement('textarea');
                        return row.date ? moment(row.date).format('DD-MM-YYYY') : '';
                    },
                    name: 'date',
                    width: '15%'
                },
                {
                    data: function(row) {
                        let element = document.createElement('textarea');
                        return row.employee?.branch?.name ?? '';
                    },
                    name: 'employee.branch.name',
                    width: '10%'
                },
                {
                    data: function(row) {
                        let element = document.createElement('textarea');
                        element.innerHTML = row.reason || '';
                        return element.value;
                    },
                    name: 'reason',
                    width: '20%'
                },
                {
                    data: function(row) {
                        return row.status ? "<span class='text-success text-center d-block'>Yes</span>" :
                            "<span class='text-danger text-center d-block'>No</span>";
                    },
                    name: 'status',
                    width: '5%'
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
            const url = route('revokes.edit', did);
            window.location.href = url;
        });

        $(document).on('click', '.delete-btn', function(event) {
            let revokeId = $(event.currentTarget).data('id');
            deleteItem(route('revokes.destroy', revokeId), '#revokeTable', '{{ __('messages.revokes.delete') }}');
        });

        $(document).on('click', '.view-btn', function(event) {
            let did = $(event.currentTarget).data('id');
            const url = route('revokes.show', did);
            window.location.href = url;
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
            updateItem: "{{ auth()->user()->can('update_revokes') ? 'true' : 'false' }}",
            deleteItem: "{{ auth()->user()->can('delete_revokes') ? 'true' : 'false' }}",
            viewItem: "{{ auth()->user()->can('view_revokes') ? 'true' : 'false' }}"
        };
        // Function to render action buttons based on permissions
        function renderActionButtons(id) {
            let buttons = '';
            if (permissions.updateItem === 'true') {
                let editUrl = `{{ route('revokes.edit', ':id') }}`;
                editUrl = editUrl.replace(':id', id);
                buttons += `
                <a title="${messages.edit}" href="${editUrl}" class="btn btn-warning action-btn has-icon edit-btn" style="float:right;margin:2px;">
                    <i class="fa fa-edit"></i>
                </a>
            `;
            }
            if (permissions.viewItem === 'true') {
                let viewUrl = `{{ route('revokes.show', ':id') }}`;
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
