@extends('layouts.app')
@section('title')
    {{ __('messages.print-checks.name') }}
@endsection
@section('page_css')
    <link href="{{ asset('assets/css/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ asset('assets/css/bs4-summernote/summernote-bs4.css') }}">
@endsection
@section('content')
    <section class="section">
        <div class="section-header item-align-right">
            <h1>{{ __('messages.print-checks.menu') }}</h1>
            <div class="section-header-breadcrumb float-right">
                <div class="card-header-action mr-3 select2-mobile-margin">
                </div>
            </div>
            @can('create_print_checks')
                <div class="float-right">
                    {{ Form::select('branches', $usersBranches ?? [], null, ['id' => 'filterBranch', 'class' => 'form-control select2', 'placeholder' => __('messages.placeholder.branches')]) }}
                    <a href="{{ route('print-checks.create') }}" class="btn btn-primary"
                        style="line-height: 30px;">{{ __('messages.print-checks.add') }} </a>
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
                    @include('print-checks.table_unit')
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
        let tbl = $('#assetTable').DataTable({
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
            order: [
                [3, 'desc'] // Ordering by the hidden 'created_at' column (index 2) in descending order
            ],
            ajax: {
                url: route('print-checks.index'),
                data: function(d) {
                    d.filterBranch = $("#filterBranch").val();
                },
                beforeSend: function() {
                    startLoader();
                },
                complete: function() {
                    stopLoader();
                }
            },
            order: [
                [0, 'desc'] // Ordering by the hidden 'created_at' column (index 2) in descending order
            ],
            columnDefs: [{
                targets: 0, // Adjust the index for the hidden 'created_at' column
                orderable: true,
                visible: false, // Hide the 'created_at' column
            }],
            columns: [{
                    data: 'updated_at', // Ensure this matches the data key for created_at in your data source
                    name: 'updated_at'
                }, {
                    data: function(row) {
                        let element = document.createElement('textarea');
                        element.innerHTML = row.branch?.name ?? '';
                        return element.value;
                    },
                    name: 'branch.name',
                }, {
                    data: function(row) {
                        let element = document.createElement('textarea');
                        element.innerHTML = row.formatted_date;
                        return element.value;
                    },
                    name: 'date',
                },
                {
                    data: function(row) {
                        let element = document.createElement('textarea');
                        element.innerHTML = row.check_number;
                        return element.value;
                    },
                    name: 'check_number',
                },
                {
                    data: function(row) {
                        let element = document.createElement('textarea');
                        element.innerHTML = row.issue_name;
                        return element.value;
                    },
                    name: 'issue_name',
                },
                {
                    data: function(row) {
                        let element = document.createElement('textarea');
                        element.innerHTML = row.issue_place;
                        return element.value;
                    },
                    name: 'issue_place',
                },
                {
                    data: function(row) {
                        let element = document.createElement('textarea');
                        element.innerHTML = row.bank?.name ?? '';
                        return element.value;
                    },
                    name: 'bank.name'

                },
                {
                    data: function(row) {
                        let element = document.createElement('textarea');
                        element.innerHTML = row.amount ?? 0;
                        return element.value;
                    },
                    name: 'amount',
                    className: 'text-right'
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

        $('#filterBranch').change(function() {
            tbl.ajax.reload(); // Reload DataTable with new status filter
        });



        $(document).on('click', '.edit-btn', function(event) {
            let assetId = $(event.currentTarget).data('id');
            const url = route('print-checks.edit', assetId);
            window.location.href = url;
        });
        $(document).on('click', '.delete-btn', function(event) {
            let assetCateogryId = $(event.currentTarget).data('id');
            deleteItem(route('print-checks.destroy', assetCateogryId), '#assetTable',
                "{{ __('messages.print-checks.menu') }}");
        });
    </script>


    <!-- AJAX script -->
    <script type="text/javascript">
        let assetCreateUrl = route('print-checks.store');
        let assetUrl = route('print-checks.index') + '/';

        $(document).on('submit', '#addAssetForm', function(event) {
            event.preventDefault();
            processingBtn('#addAssetForm', '#btnSave', 'loading');
            var formData = new FormData(this);
            $.ajax({
                type: 'POST',
                url: assetCreateUrl, // Update with your actual route
                data: formData,
                processData: false,
                contentType: false,
                beforeSend: function() {
                    $('#btnSave').attr('disabled', true).html(
                        "<span class='spinner-border spinner-border-sm'></span> Processing..."
                    );
                },
                success: function(response) {
                    if (response.success) {
                        displaySuccessMessage(response.message);
                        $('#addModal').modal('hide');
                        $('#assetTable').DataTable().ajax.reload(null, false);
                    }
                },
                error: function(result) {
                    displayErrorMessage(response.responseJSON.message);
                },
                complete: function() {
                    processingBtn('#addNewForm', '#btnSave');
                },
            });
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
            updateItem: "{{ auth()->user()->can('update_print_checks') ? 'true' : 'false' }}",
            deleteItem: "{{ auth()->user()->can('delete_print_checks') ? 'true' : 'false' }}",
            viewItem: "{{ auth()->user()->can('view_print_checks') ? 'true' : 'false' }}"
        };
        // Function to render action buttons based on permissions
        function renderActionButtons(id) {
            let buttons = '';
            if (permissions.updateItem === 'true') {
                let editUrl = `{{ route('print-checks.edit', ':id') }}`;
                editUrl = editUrl.replace(':id', id);
                buttons += `
                <a title="${messages.edit}" href="${editUrl}" class="btn btn-warning action-btn has-icon edit-btn" style="float:right;margin:2px;">
                    <i class="fa fa-edit"></i>
                </a>
            `;
            }
            if (permissions.viewItem === 'true') {
                let viewUrl = `{{ route('print-checks.view', ':id') }}`;
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
