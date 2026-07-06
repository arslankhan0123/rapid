@extends('layouts.app')
@section('title')
    {{ __('messages.documents.documents') }}
@endsection
@section('page_css')
    <link href="{{ asset('assets/css/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
@endsection
@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{ __('messages.documents.documents') }}</h1>
            <div class="section-header-breadcrumb">
                <a href="{{ route('documents.create') }}" class="btn btn-primary form-btn">
                    {{ __('messages.documents.add') }}
                </a>
            </div>
        </div>
        <div class="section-body">
            <div class="card">
                <div class="card-body">
                    @if (auth()->id() === 1)
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <label for="user_filter">{{ __('messages.documents.filter_by_user') }}</label>
                                <select class="form-control" id="user_filter">
                                    <option value="">{{ __('messages.documents.all_users') }}</option>
                                    @foreach ($users as $id => $name)
                                        <option value="{{ $id }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    @endif
                    @include('documents.table')
                </div>
            </div>
        </div>
    </section>
@endsection

@section('page_scripts')
    <script src="{{ asset('assets/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ mix('assets/js/custom/custom-datatable.js') }}"></script>
    <script src="{{ mix('assets/js/select2.min.js') }}"></script>
@endsection

@section('scripts')
    <script>
        let documentsUrl = "{{ route('documents.index') }}";
        let userId = "{{ auth()->id() }}";

        // Pass document module permissions to JS
        let permissions = {
            viewItem: @json(auth()->user()->can('view_documents')),
            updateItem: @json(auth()->user()->can('update_documents')),
            deleteItem: @json(auth()->user()->can('delete_documents')),
            downloadItem: @json(auth()->user()->can('download_documents'))
        };

        $(document).ready(function() {
            // Initialize Select2
            $('#user_filter').select2({
                width: '100%'
            });

            let tbl = $('#documentsTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: documentsUrl,
                    data: function(d) {
                        d.user_id = $('#user_filter').val();
                    }
                },
                columns: [{
                        data: 'document',
                        name: 'document',
                        width: '25%'
                    },
                    {
                        data: 'description',
                        name: 'description',
                        width: '30%'
                    },
                    {
                        data: 'user.first_name',
                        name: 'user.first_name',
                        width: '15%',
                        visible: userId === '1', // only show for admin
                        render: function(data, type, row) {
                            return row.user.first_name + ' ' + row.user.last_name;
                        }
                    },
                    {
                        data: 'created_at',
                        name: 'created_at',
                        width: '15%',
                        render: function(data) {
                            return moment(data).format('DD-MM-YYYY HH:mm');
                        },
                        className: "text-center"
                    },
                    {
                        data: function(row) {
                            return renderActionButtons(row.id);
                        },
                        name: 'id',
                        width: '15%',
                        className: "text-center"
                    }
                ]
            });

            // Filter by user
            $('#user_filter').on('change', function() {
                tbl.ajax.reload();
            });

            // Delete document
            $(document).on('click', '.delete-btn', function(event) {
                let documentId = $(event.currentTarget).data('id');
                deleteItem(route('documents.destroy', documentId), '#documentsTable',
                    '{{ __('messages.documents.document') }}');
            });
        });

        // Messages
        var messages = {
            delete: "{{ __('messages.common.delete') }}",
            view: "{{ __('messages.common.view') }}",
            download: "{{ __('messages.documents.download') }}"
        };

        // Render action buttons dynamically based on permissions
        function renderActionButtons(id) {
            let buttons =
                '<div style="display:flex;flex-direction:row;align-items:center;justify-content:center;gap:2px;">';

            if (permissions.deleteItem) {
                buttons += `<a title="${messages.delete}" href="#"
                class="btn btn-danger btn-sm d-flex align-items-center justify-content-center delete-btn"
                data-id="${id}" style="width:36px;height:36px;border-radius:6px;margin:0;">
                <i class="fas fa-trash" style="font-size:14px;"></i>
            </a>`;
            }

            if (permissions.viewItem) {
                let viewUrl = `{{ route('documents.show', ':id') }}`;
                viewUrl = viewUrl.replace(':id', id);
                buttons += `
        <a title="${messages.view}" href="${viewUrl}"
           class="btn btn-primary btn-sm d-flex align-items-center justify-content-center"
           style="width:36px !important;height:36px !important;min-width:36px !important;max-width:36px !important;border-radius:6px;margin:0;padding:0 !important;">
            <i class="fas fa-eye" style="font-size:14px;width:14px;height:14px;"></i>
        </a>
    `;
            }


            if (permissions.downloadItem) {
                let downloadUrl = `{{ route('documents.download', ':id') }}`.replace(':id', id);
                buttons += `<a title="${messages.download}" href="${downloadUrl}"
                class="btn btn-info btn-sm d-flex align-items-center justify-content-center"
                style="width:36px;height:36px;border-radius:6px;margin:0;">
                <i class="fas fa-download" style="font-size:14px;"></i>
            </a>`;
            }

            buttons += '</div>';
            return buttons;
        }
    </script>
@endsection
