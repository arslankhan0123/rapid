@extends('layouts.app')
@section('title')
    {{ __('messages.assets.menu') }}
@endsection
@section('page_css')
    <link href="{{ asset('assets/css/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ asset('assets/css/bs4-summernote/summernote-bs4.css') }}">
@endsection
@section('content')
    <section class="section">
        <div class="section-header item-align-right">
            <h1>{{ __('messages.assets.menu') }}</h1>
            <div class="section-header-breadcrumb float-right">
                <div class="card-header-action mr-3 select2-mobile-margin">
                </div>
            </div>
            @can('create_assets')
                <div class="float-right">

                    {{ Form::select('branches', $usersBranches ?? [], null, ['id' => 'filterBranch', 'class' => 'form-control select2', 'placeholder' => __('messages.placeholder.branches')]) }}

                    <a href="{{ route('assets.create') }}" class="btn btn-primary"
                        style="line-height: 30px;">{{ __('messages.assets.add_assets') }} </a>
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
                    @include('assets.table_unit')
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
                url: route('assets.index'),
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
            lengthMenu: [
                [100, 300, 500, 999, -1],
                [100, 300, 500, 999, "All"]
            ],
            pageLength: 100, // Default page length
            columns: [{
                    data: function(row) {
                        let element = document.createElement('textarea');
                        element.innerHTML = row.branch?.name ?? '';
                        return element.value;
                    },
                    name: 'branch.name',
                }, {
                    data: function(row) {
                        let element = document.createElement('textarea');
                        element.innerHTML = row.name;
                        return element.value;
                    },
                    name: 'name',
                }, {
                    data: function(row) {
                        if (row.category && row.category.title) {
                            let element = document.createElement('textarea');
                            element.innerHTML = row.category.title;
                            return element.value;
                        } else {
                            return ''; // Or handle accordingly if category title is not available
                        }
                    },
                    name: 'category.title',
                    orderable: false
                }, {
                    data: function(row) {
                        let element = document.createElement('textarea');
                        element.innerHTML = row
                            .company_asset_code;
                        return element.value;
                    },
                    name: 'company_asset_code',
                },
                {
                    data: function(row) {
                        let element = document.createElement('textarea');
                        element.innerHTML = row.is_working;
                        return element.value;
                    },
                    name: 'is_working',
                },
                {
                    data: function(row) {
                        if (row.employee && row.employee.name) {
                            return row.employee.name;
                        } else {
                            return ''; // Or handle accordingly if name is not available
                        }
                    },
                    name: 'employee.name', // Corrected 'employe.name' to 'employee.name'
                    orderable: false
                },
                {
                    data: function(row) {
                        let element = document.createElement('textarea');
                        element.innerHTML = row.warranty_end_date;
                        return element.value;
                    },
                    name: 'warranty_end_date',

                },
                {
                    data: function(row) {
                        return renderActionButtons(row.id);
                    },
                    name: 'id',
                }
            ],
            responsive: true // Enable responsive features
        });

        $('#filterBranch').change(function() {
            tbl.ajax.reload(); // Reload DataTable with new status filter
        });
        $(document).on('submit', '#editFormNew', function(e) {
            e.preventDefault();
            processingBtn('#editFormNew', '#btnSave', 'loading');
            let id = $('#asset_id').val();
            var formData = new FormData(this);
            $.ajax({
                type: 'post',
                url: route('assets.update', id),
                data: formData,
                processData: false,
                contentType: false,
                success: function(result) {
                    if (result.success) {
                        displaySuccessMessage(result.message);
                        $('.modal').modal('hide');
                        $('#assetTable').DataTable().ajax.reload(null, false);
                    }
                },
                error: function(result) {
                    displayErrorMessage(result.responseJSON.message);
                },
                complete: function() {
                    processingBtn('#editForm', '#btnEditSave');
                },
            });
        });



        $(document).on('click', '.edit-btn', function(event) {
            let assetId = $(event.currentTarget).data('id');
            const url = route('assets.edit', assetId);
            window.location.href = url;
        });
        $(document).on('click', '.delete-btn', function(event) {
            let assetCateogryId = $(event.currentTarget).data('id');
            deleteItem(route('assets.destroy', assetCateogryId), '#assetTable',
                "{{ __('messages.assets.menu') }}");
        });
    </script>


    <!-- AJAX script -->
    <script type="text/javascript">
        let assetCreateUrl = route('assets.store');
        let assetUrl = route('assets.index') + '/';

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
        $('#image').on('change', function() {
            const previewDiv = document.getElementById('image-preview');
            const previewImg = document.getElementById('preview-img');
            const file = this.files[0];

            if (file) {
                const reader = new FileReader();

                reader.onload = function(e) {
                    previewDiv.style.display = 'block';
                    previewImg.src = e.target.result;
                }

                reader.readAsDataURL(file);
            }
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
            updateItem: "{{ auth()->user()->can('update_assets') ? 'true' : 'false' }}",
            deleteItem: "{{ auth()->user()->can('delete_assets') ? 'true' : 'false' }}",
            viewItem: "{{ auth()->user()->can('view_assets') ? 'true' : 'false' }}"
        };
        // Function to render action buttons based on permissions
        function renderActionButtons(id) {
            let buttons = '';
            if (permissions.updateItem === 'true') {
                let editUrl = `{{ route('assets.edit', ':id') }}`;
                editUrl = editUrl.replace(':id', id);
                buttons += `
                <a title="${messages.edit}" href="${editUrl}" class="btn btn-warning action-btn has-icon edit-btn" style="float:right;margin:2px;">
                    <i class="fa fa-edit"></i>
                </a>
            `;
            }
            if (permissions.viewItem === 'true') {
                let viewUrl = `{{ route('assets.view', ':id') }}`;
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
