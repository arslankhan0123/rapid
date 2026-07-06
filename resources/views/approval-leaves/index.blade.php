@extends('layouts.app')
@section('title')
    {{ __('messages.approval-leaves.name') }}
@endsection
@section('page_css')
    <link href="{{ asset('assets/css/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ asset('assets/css/bs4-summernote/summernote-bs4.css') }}">
    <link href="{{ asset('assets/css/sweetalert.css') }}" rel="stylesheet" type="text/css" />
@endsection
@section('content')
    <section class="section">
        <div class="section-header item-align-right">
            <h1>{{ __('messages.approval-leaves.name') }}</h1>
            <div class="section-header-breadcrumb float-right">
                <div class="mr-3">
                    <label for="statusDropdown">Branches</label>
                    {{ Form::select('branches', $usersBranches ?? [], null, ['id' => 'filterBranch', 'class' => 'form-control select2', 'placeholder' => __('messages.placeholder.branches')]) }}
                </div>

            </div>

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
                    @include('approval-leaves.table')
                </div>
            </div>
        </div>
    </section>
    @include('leave_applications.templates.templates')
@endsection
@section('page_scripts')
    <script src="{{ asset('assets/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ mix('assets/js/custom/custom-datatable.js') }}"></script>
    <script src="{{ mix('assets/js/bs4-summernote/summernote-bs4.js') }}"></script>
    <script src="{{ mix('assets/js/select2.min.js') }}"></script>
    <script src="{{ asset('assets/js/sweetalert.min.js') }}"></script>
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
            ajax: {
                url: route('approval-leaves.index'),
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
            order: [
                [4, 'desc'] // Ordering by the hidden 'created_at' column (index 2) in descending order
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
                        return row.branch?.name ?? "";
                    },
                    name: 'branch.name',

                }, {
                    data: function(row) {
                        let element = document.createElement('textarea');
                        if (row.employee) {
                            return row.employee.iqama_no;
                        }
                        return '';
                    },
                    name: 'iqama_no',

                }, {
                    data: function(row) {
                        let element = document.createElement('textarea');
                        if (row.employee) {
                            return row.employee.name;
                        }
                        return '';
                    },
                    name: 'employee_id',

                },
                {
                    data: function(row) {
                        let element = document.createElement('textarea');
                        if (row.leave) {
                            return row.leave.name;
                        }
                        return '';
                    },
                    name: 'leave_id',

                },
                {
                    data: function(row) {
                        let element = document.createElement('textarea');
                        return row.from_date ?? '';
                    },
                    name: 'from_date',

                },
                {
                    data: function(row) {
                        let element = document.createElement('textarea');
                        return row.end_date ?? '';
                    },
                    name: 'end_date',

                },
                {
                    data: function(row) {
                        if (row.total_days == null) {
                            return '';
                        }
                        return row.total_days.toFixed(2);
                    },
                    name: 'total_days',
                    className: 'text-center'
                },


                {
                    data: function(row) {
                        // Check if hard_copy is available and construct the URL
                        if (row.hard_copy) {
                            // Construct the URL for the file
                            const fileUrl =
                                `/uploads/public/leave_applications/${encodeURIComponent(row.hard_copy)}`;
                            // Return the anchor tag with the eye icon
                            return `<a  href="${fileUrl}" target="_blank" title="View File"><i class="fas fa-eye"></i></a>`;
                        } else {
                            return '-';
                        }
                    },
                    name: 'hard_copy',
                    orderable: false,
                    className: 'text-center'

                },
                {
                    data: function(row) {

                        let element = document.createElement('textarea');
                        return row.approved_by?.full_name ?? "";
                    },
                    name: 'approved_by.full_name',

                },
                {
                    data: function(row) {
                        let element = document.createElement('textarea');
                        return row.status ?
                            `<span class="text-success fw-bold">Approved</span>` :
                            `<span class="text-danger fw-bold">Pending</span>`;
                    },
                    name: 'from_date',

                },

                {
                    data: function(row) {
                        return renderActionButtons(row.id, row.status ?? false);
                    },
                    name: 'id',
                    width: '5%'
                }
            ],
            responsive: true // Enable responsive features
        });


        $('#filterBranch').change(function() {
            tbl.ajax.reload(); // Reload DataTable with new status filter
        });

        $(document).on('click', '.edit-btn', function(event) {
            let assetId = $(event.currentTarget).data('id');
            const url = route('approval-leaves.edit', assetId);
            window.location.href = url;
        });
        $(document).on('click', '.delete-btn', function(event) {
            let assetCateogryId = $(event.currentTarget).data('id');
            deleteItem(route('approval-leaves.destroy', assetCateogryId), '#assetTable',
                "{{ __('messages.approval-leaves.name') }}");
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
            updateItem: "{{ auth()->user()->can('approve_approval_leaves') ? 'true' : 'false' }}",
            viewItem: "{{ auth()->user()->can('view_approval_leaves') ? 'true' : 'false' }}"
        };

        // Function to render action buttons based on permissions
        function renderActionButtons(id, status = false) {
            let buttons = '';




            if (permissions.viewItem === 'true') {
                let viewUrl = `{{ route('approval-leaves.view', ':id') }}`;
                viewUrl = viewUrl.replace(':id', id);
                buttons += `
                <a title="${messages.view}" href="${viewUrl}" class="btn btn-info action-btn has-icon view-btn " style="float:right;margin:2px;">
                    <i class="fa fa-eye"></i>
                </a>
            `;
            }


            if (permissions.updateItem === 'true' && status != true) {

                buttons += `
              <a title="${messages.approve}" href="#" class="btn btn-success action-btn has-icon approve-btn d-none" data-id="${id}" style="float:right;margin:2px;">
                    <i class="fa fa-check"></i>
                </a>
            `;
            }
            return buttons;
        }

        $(document).on('click', '.approve-btn', function(e) {
            e.preventDefault();
            let id = $(this).data('id');
            swal({
                    title: 'Are you sure!!',
                    text: "Do you want to approve this leave application?",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonText: 'Confirm',
                    cancelButtonText: 'Cancel',
                    showConfirmButton: true,
                    confirmButtonColor: '#3085d6', // Optional: Change confirm button color
                    cancelButtonColor: '#d33', // Optional: Change cancel button color
                },
                function() {
                    approveLeave(id);
                });

        });

        // Function to handle approval action
        function approveLeave(id) {
            startLoader();
            $.ajax({
                url: `{{ route('approval-leaves.update', ':id') }}`.replace(':id', id),
                method: 'get',
                success: function(response) {
                    displaySuccessMessage("leave Application Approved");
                    tbl.ajax.reload();
                },
                error: function(response) {
                    displayErrorMessage("Failed to Update");
                    tbl.ajax.reload();
                }
            });
        }
    </script>
@endsection
