@extends('layouts.app')
@section('title')
    {{ __('messages.task-status.name') }}
@endsection
@section('page_css')
    <link href="{{ asset('assets/css/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ asset('assets/css/bs4-summernote/summernote-bs4.css') }}">
@endsection
@section('content')
    <section class="section">
        <div class="section-header item-align-right row pt-0 pb-0">
            <div class="col-md-2">
                <h1>{{ __('messages.task-status.name') }}</h1>
            </div>
            <div class="col-md-10  float-right  p-0 m-0 pt-2">
                <div class="row float-right" style="width: 100%">
                    <div class="col-md-2">
                        <div class="form-group">
                            {{ Form::label('start_date', __('messages.task-status.start_date')) }}
                            {{ Form::date('start_date', null, ['class' => 'form-control', 'required', 'id' => 'start_date']) }}
                        </div>
                    </div>

                    <div class="col-md-2">
                        <div class="form-group">
                            {{ Form::label('end_date', __('messages.task-status.end_date')) }}
                            {{ Form::date('end_date', null, ['class' => 'form-control', 'required', 'id' => 'end_date']) }}
                        </div>
                    </div>

                    <div class="col-md-2">
                        <div class="form-group">
                            {{ Form::label('month', __('messages.task-status.month')) }}
                            {{ Form::month('month', null, ['class' => 'form-control', 'required', 'id' => 'month']) }}
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            {{ Form::label('month', __('messages.branches.name')) }}
                            {{ Form::select('branches', $usersBranches ?? [], null, ['id' => 'filterBranch', 'class' => 'form-control select2', 'placeholder' => __('messages.placeholder.branches')]) }}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            {{ Form::label('employee_id', __('messages.task-status.user')) }}
                            {{ Form::select('user_id', [], null, [
                                'class' => 'form-control',
                                'required',
                                'id' => 'employee_select',
                                'placeholder' => __('messages.attendances.select_iqama'),
                            ]) }}
                        </div>
                    </div>

                    <div class="col-md-1  " style="margin-top: 30px;">
                        @can('create_task_status')
                            <a href="{{ route('task-status.create') }}" class="btn btn-info">
                                Add
                            </a>
                        @endcan

                    </div>
                </div>

                @if (!auth()->user()->is_admin)
                @endif
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
                    @include('task_status.table')
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

        let designationCreateUrl = route('task-status.store');
        $(document).on('submit', '#addNewForm', function(event) {
            event.preventDefault();
            processingBtn('#addNewForm', '#btnSave', 'loading');


            let description = $('<div />').
            html($('#createDescription').summernote('code'));
            let empty = description.text().trim().replace(/ \r\n\t/g, '') === '';

            if ($('#createDescription').summernote('isEmpty')) {
                $('#createDescription').val('');
            } else if (empty) {
                displayErrorMessage(
                    'Description field is not contain only white space');
                processingBtn('#addNewForm', '#btnSave', 'reset');
                return false;
            }
            $.ajax({
                url: designationCreateUrl,
                type: 'POST',
                data: $(this).serialize(),
                success: function(result) {
                    if (result.success) {
                        displaySuccessMessage(result.message);
                        $('#addModal').modal('hide');
                        $('#designation_name').val('');
                        const url = route('task-status.index', );
                        window.location.href = url;
                    }
                },
                error: function(result) {
                    displayErrorMessage(result.responseJSON.message);
                },
                complete: function() {
                    processingBtn('#addNewForm', '#btnSave');
                },
            });
        });

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
                url: route('task-status.index'), // Adjust the URL for fetching data
                method: 'GET',
                data: function(d) {
                    d.start_date = $('#start_date').val();
                    d.end_date = $('#end_date').val();
                    d.month = $('#month').val();
                    d.user_id = $('#employee_select').val();
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
                    name: 'task',
                    width: '12%'
                }, {
                    data: function(row) {
                        // Create a new Date object from the row.date
                        const date = new Date(row.date);
                        // Format the date to 'd-m-Y' format
                        const day = String(date.getDate()).padStart(2,
                            '0'); // Get the day and pad with leading zero
                        const month = String(date.getMonth() + 1).padStart(2,
                            '0'); // Get the month (0-indexed) and pad
                        const year = date.getFullYear(); // Get the full year
                        return `${day}-${month}-${year}`; // Return formatted date
                    },
                    name: 'date',
                    className: 'text-center',
                    width: '10%'
                }, {
                    data: function(row) {
                        return row.user?.name ?? "";

                    },
                    name: 'user.first_name',
                    width: '15%'
                },
                {
                    data: function(row) {
                        if (!row.duration) return '0h 0m';

                        let totalMinutes = parseInt(row.duration, 10);
                        let hours = Math.floor(totalMinutes / 60);
                        let minutes = totalMinutes % 60;

                        return `${hours}h ${minutes}m`;
                    },
                    name: 'duration',
                    width: '8%',
                    createdCell: function(td, cellData, rowData, row, col) {
                        $(td).css('text-align', 'center'); // Center the text in the cell
                    }
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

        $(document).on('click', '.delete-btn', function(event) {
            let assetCateogryId = $(event.currentTarget).data('id');
            deleteItem(route('task-status.destroy', assetCateogryId), '#designationTable',
                '{{ __('messages.task-status.name') }}');
        });

        // Event listeners for inputs
        $('#start_date, #end_date, #month, #employee_select,#filterBranch').change(function() {
            // Trigger DataTable redraw
            tbl.ajax.reload();
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
            updateItem: "{{ auth()->user()->can('update_task_status') ? 'true' : 'false' }}",
            deleteItem: "{{ auth()->user()->can('delete_task_status') ? 'true' : 'false' }}",
            viewItem: "{{ auth()->user()->can('view_task_status') ? 'true' : 'false' }}"
        };
        // Function to render action buttons based on permissions
        function renderActionButtons(id) {
            let buttons = '';
            if (permissions.updateItem === 'true') {
                let editUrl = `{{ route('task-status.edit', ':id') }}`;
                editUrl = editUrl.replace(':id', id);
                buttons += `
                <a title="${messages.edit}" href="${editUrl}" class="btn btn-warning action-btn has-icon edit-btn" style="float:right;margin:2px;">
                    <i class="fa fa-edit"></i>
                </a>
            `;
            }
            if (permissions.viewItem === 'true') {
                let viewUrl = `{{ route('task-status.view', ':id') }}`;
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

    <script>
        const allEmployees = {!! json_encode(
            $employees->map(function ($employee) {
                return [
                    'id' => $employee->id,
                    'branch_id' => $employee->branch_id,
                    'iqama_no' => $employee->iqama_no,
                    'name' => $employee->name,
                ];
            }),
        ) !!};

        $('#filterBranch').on('change', function() {
            const selectedBranchId = $(this).val(); // Get the selected branch ID
            const $employeeSelect = $('#employee_select'); // Reference the employee select dropdown

            // Clear the current options in the employee select dropdown
            $employeeSelect.empty();
            $employeeSelect.append('<option value="">' + "{{ __('messages.attendances.select_iqama') }}" +
                '</option>');

            // Filter employees by branch_id and populate the dropdown
            allEmployees.forEach(function(employee) {
                if (employee.branch_id == selectedBranchId) {
                    $employeeSelect.append(
                        '<option value="' + employee.id + '">' + employee.iqama_no + ' (' + employee
                        .name + ')</option>'
                    );
                }
            });

            $('#employee_select').select2();


        });
    </script>
@endsection
