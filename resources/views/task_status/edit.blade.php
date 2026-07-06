@extends('layouts.app')
@section('title')
    {{ __('messages.task-status.edit') }}
@endsection
@section('page_css')
    {{-- Same CSS as your create page --}}
@endsection
@section('content')

    <style>
        #addTaskModal .select2-container {
            min-width: 200px !important;
            width: 100% !important;
        }
    </style>

    <section class="section">
        <div class="section-header item-align-right">
            <h1 class=" col-lg-6">{{ __('messages.task-status.edit') }}</h1>
            <div class="text-right col-lg-6">
                <a href="{{ route('task-status.index') }}"
                    class="btn btn-primary form-btn">{{ __('messages.task-status.list') }}</a>
            </div>
        </div>
        <div class="section-body">
            <div class="card">
                <div class="card-body">

                    {{ Form::model($taskStatus, ['route' => ['task-status.update', $taskStatus->id], 'method' => 'PUT', 'id' => 'editFormDepartmentNew']) }}

                    <div class="alert alert-danger d-none" id="validationErrorsBox"></div>
                    <div class="row">
                        <div class="form-group  col-md-6">
                            {{ Form::label('admin_note', 'Branch') }}<span class="required">*</span>
                            {{ Form::select('branch_id', $usersBranches ?? [], $taskStatus->branch_id, ['class' => 'form-control select2', 'required', 'id' => 'branchSelect', 'placeholder' => 'Select Branch']) }}
                        </div>

                        <div class="form-group col-md-6">
                            {{ Form::label('employee_id', __('messages.task-status.user') . ':') }}<span
                                class="required">*</span>
                            {{ Form::select('user_id', [], $taskStatus->user_id, [
                                'class' => 'form-control',
                                'required',
                                'id' => 'employee_select',
                            ]) }}
                        </div>

                        <div class="form-group col-md-6">
                            {{ Form::label('date', __('messages.task-status.date')) }}<span class="required">*</span>
                            {{ Form::date('date', $taskStatus->date, ['class' => 'form-control', 'required', 'id' => 'task_date']) }}
                        </div>

                        {{-- <div class="form-group col-md-6">
                        {{ Form::label('duration', __('messages.timeline')) }}<span class="required">*</span>
                        {{ Form::text('duration', $taskStatus->duration, ['class' => 'form-control', 'required', 'id' => 'task_duration'  ]) }}
                    </div> --}}
                        <div class="form-group col-md-6">
                            {{ Form::label('duration', __('messages.timeline')) }}<span class="required">*</span>

                            {{-- Hidden field (stores raw minutes for DB) --}}
                            {{ Form::hidden('duration', $taskStatus->duration ?? 0, ['id' => 'hidden_task_duration']) }}

                            {{-- Visible readonly field (shows formatted time) --}}
                            {{ Form::text('duration_display', null, [
                                'class' => 'form-control',
                                'id' => 'task_duration',
                                'readonly',
                            ]) }}
                        </div>


                    </div>

                    {{-- Task Distribution Rows --}}

                    <div class="col-lg-12 pt-2">

                        <div class="row table-wrapper ">
                            <table class="table table-responsive table-md page_contents" id="itemsTable"
                                style="height: 400px; overflow: auto;">
                                <thead>
                                    <tr style="padding: 0px;">
                                        <th style="width: 10%;" class="text-center">Sl.</th>
                                        <th style="width: 20%;" class="text-center">Task Category</th>
                                        <th style="width: 20%;" class="text-center">Customer</th>
                                        <th style="width: 10%;" class="text-center">Project </th>
                                        <th style="width: 10%;" class="text-center pr-3">Start Time </th>
                                        <th style="width: 10%;" class="text-center pr-3">End Time </th>
                                        <th style="width: 10%;" class="text-center pr-0">Description</th>
                                        <th style="width: 10%;" class="pr-0">Action</th>
                                    </tr>
                                </thead>

                                <tbody id="itemRows">
                                    @php
                                        // Make sure task_distribution is decoded as array
                                        $taskDistributions = json_decode($taskStatus->task_distribution ?? '[]', true);
                                    @endphp

                                    @forelse ($taskDistributions as $index => $item)
                                        <tr class="task-row">
                                            <td class="p-1 text-center">{{ $index + 1 }}</td>

                                            {{-- Tasks (multiple select) --}}
                                            <td class="p-1">
                                                <select class="form-control select2 task-name">
                                                    @foreach ($task_list as $taskId => $taskName)
                                                        <option value="{{ $taskId }}"
                                                            @if ($taskId == $item['tasks']) selected @endif>
                                                            {{ $taskName }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </td>

                                            {{-- Customer --}}
                                            <td class="p-1">
                                                <select class="form-control select2 customer-id">
                                                    <option value="">{{ __('messages.placeholder.select_customer') }}
                                                    </option>
                                                    @foreach ($customers as $customerId => $customerName)
                                                        <option value="{{ $customerId }}"
                                                            @if (($item['customer_id'] ?? '') == $customerId) selected @endif>
                                                            {{ $customerName }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </td>

                                            {{-- Project --}}
                                            <td class="p-1">
                                                <select class="form-control select2 project_id">
                                                    <option value="">{{ __('messages.placeholder.select_project') }}
                                                    </option>
                                                    @foreach ($projects as $project)
                                                        @if ($project['customer_id'] == ($item['customer_id'] ?? ''))
                                                            <option value="{{ $project['id'] }}"
                                                                @if (($item['project_id'] ?? '') == $project['id']) selected @endif>
                                                                {{ $project['project_name'] }}
                                                            </option>
                                                        @endif
                                                    @endforeach
                                                </select>
                                            </td>

                                            {{-- Start Time --}}
                                            <td class="p-1">
                                                <input type="datetime-local" class="form-control start-time"
                                                    value="{{ $item['start_time'] ?? '' }}">
                                            </td>

                                            {{-- End Time --}}
                                            <td class="p-1">
                                                <input type="datetime-local" class="form-control end-time"
                                                    value="{{ $item['end_time'] ?? '' }}">
                                            </td>

                                            {{-- Remarks --}}
                                            <td class="p-1">
                                                <input type="text" class="form-control remarks"
                                                    value="{{ $item['remarks'] ?? '' }}">
                                            </td>

                                            {{-- Delete button --}}
                                            <td class="p-1 text-center">
                                                <button type="button" class="btn btn-danger btn-sm deleteRow">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        {{-- No rows, display empty one --}}
                                        <tr class="task-row">
                                            <td class="p-1 text-center">1</td>
                                            <td class="p-1">
                                                <select class="form-control select2 task-name">
                                                    @foreach ($task_list as $taskId => $taskName)
                                                        <option value="{{ $taskId }}">{{ $taskName }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td class="p-1">
                                                <select class="form-control select2 customer-id">
                                                    <option value="">{{ __('messages.placeholder.select_customer') }}
                                                    </option>
                                                    @foreach ($customers as $customerId => $customerName)
                                                        <option value="{{ $customerId }}">{{ $customerName }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td class="p-1">
                                                <select class="form-control select2 project_id">
                                                    <option value="">{{ __('messages.placeholder.select_project') }}
                                                    </option>
                                                </select>
                                            </td>
                                            <td class="p-1">
                                                <input type="datetime-local" class="form-control start-time">
                                            </td>
                                            <td class="p-1">
                                                <input type="datetime-local" class="form-control end-time">
                                            </td>
                                            <td class="p-1">
                                                <input type="text" class="form-control remarks">
                                            </td>
                                            <td class="p-1 text-center">
                                                <button type="button" class="btn btn-danger btn-sm deleteRow">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>

                            </table>
                            <button type="button" class="btn btn-info" id="addRow"><i class="fa fa-plus"></i> Add
                                Row</button>
                            <button type="button" class="btn btn-success ml-1" id="addTask"><i
                                    class="fa fa-plus"></i> Add Task</button>
                        </div>

                    </div>

                    <div class="text-right mr-1">
                        {{ Form::button(__('messages.common.submit'), ['type' => 'submit', 'class' => 'btn btn-primary', 'id' => 'btnSave']) }}
                    </div>
                    {{ Form::close() }}

                </div>
            </div>
        </div>
    </section>

    <div class="modal fade" id="addTaskModal" tabindex="-1" role="dialog" aria-labelledby="addTaskModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Task Category</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    {{ Form::open(['id' => 'addNewFormDepartmentNew2']) }}

                    <div class="alert alert-danger d-none" id="validationErrorsBox"></div>
                    <div class="row">

                        <div class="form-group col-12 col-md-6">
                            {{ Form::label('department', __('messages.common.department') . ':') }}<span
                                class="required">*</span>
                            {{ Form::select('department_id', $departments, null, ['class' => 'form-control select2', 'required', 'id' => 'department_select', 'placeholder' => 'All']) }}
                        </div>

                        <div class="form-group col-12 col-md-6">
                            {{ Form::label('designation', __('messages.designations.name') . ':') }}<span
                                class="required">*</span>
                            {{ Form::select('designation_id', [], null, ['class' => 'form-control select2', 'required', 'id' => 'designation_select']) }}
                        </div>
                        <div class="form-group col-sm-12">
                            {{ Form::label('title', __('messages.task-status.task') . ':') }}<span
                                class="required">*</span>
                            {{ Form::textarea('name', null, ['class' => 'form-control ', 'id' => 'new_task', 'required', 'style' => 'height:50px;', 'autocomplete' => 'off']) }}
                        </div>


                    </div>
                    <div class="text-right mr-1">
                        {{ Form::button(__('messages.common.submit'), ['type' => 'submit', 'class' => 'btn btn-primary', 'id' => 'btnSave', 'data-loading-text' => "<span class='spinner-border spinner-border-sm'></span> Processing..."]) }}

                    </div>

                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>

@endsection

@section('page_scripts')
    {{-- Same JS assets --}}
@endsection

@php
    $formattedEmployees = $employees->map(function ($e) {
        return [
            'id' => $e->id,
            'branch_id' => $e->branch_id,
            'iqama_no' => $e->iqama_no,
            'name' => $e->name,
        ];
    });
@endphp

@section('scripts')
    <script>
        const taskDistributionData = @json($taskStatus->task_distribution ?? []);
    </script>

    <script>
        const allEmployees = @json($formattedEmployees);
    </script>

    <script>
        const existingTasks = @json($taskStatus->task_distribution ?? []);
        const taskList = @json($task_list);
        const customers = @json($customers);
        const projects = @json($projects);

        $(document).ready(function() {
            $('.select2').select2();

            // Populate employee dropdown
            $('#branchSelect').on('change', function() {
                const branchId = $(this).val();
                const $employeeSelect = $('#employee_select');
                $employeeSelect.empty().append(
                    '<option value="">{{ __('messages.attendances.select_iqama') }}</option>');
                allEmployees.forEach(emp => {
                    if (emp.branch_id == branchId) {
                        $employeeSelect.append(
                            `<option value="${emp.id}">${emp.iqama_no} (${emp.name})</option>`);
                    }
                });
                $employeeSelect.val('{{ $taskStatus->user_id }}').trigger('change');
                $('#employee_select').select2();
            }).trigger('change'); // trigger on page load

            // Populate existing rows
            existingTasks.forEach((row, idx) => {
                addRow(row, idx + 1);
            });
        });

        function addRow(data = {}, index = $('#itemRows tr').length + 1) {
            let taskOptions = '';
            Object.entries(taskList).forEach(([id, name]) => {
                taskOptions +=
                    `<option value="${id}" ${data.tasks?.includes(id.toString()) ? 'selected' : ''}>${name}</option>`;
            });

            let customerOptions = `<option value="">{{ __('messages.placeholder.select_customer') }}</option>`;
            Object.entries(customers).forEach(([id, name]) => {
                customerOptions +=
                    `<option value="${id}" ${data.customer_id == id ? 'selected' : ''}>${name}</option>`;
            });

            let projectOptions = `<option value="">{{ __('messages.placeholder.select_project') }}</option>`;
            if (data.customer_id) {
                projects.filter(p => p.customer_id == data.customer_id).forEach(p => {
                    projectOptions +=
                        `<option value="${p.id}" ${data.project_id == p.id ? 'selected' : ''}>${p.project_name}</option>`;
                });
            }

            const row = `
        <tr class="task-row">
            <td class="p-1 text-center">${index}</td>
            <td class="p-1"><select class="form-control select2 task-name" multiple>${taskOptions}</select></td>
            <td class="p-1"><select class="form-control select2 customer-id">${customerOptions}</select></td>
            <td class="p-1"><select class="form-control select2 project_id">${projectOptions}</select></td>
            <td class="p-1"><input type="datetime-local" class="form-control start-time" value="${data.start_time ?? ''}"></td>
            <td class="p-1"><input type="datetime-local" class="form-control end-time" value="${data.end_time ?? ''}"></td>
            <td class="p-1"><input type="text" class="form-control remarks" value="${data.remarks ?? ''}"></td>
            <td class="p-1 text-center">
                <button type="button" class="btn btn-danger btn-sm deleteRow"><i class="fa fa-trash"></i></button>
            </td>
        </tr>`;
            $('#itemRows').append(row);
            $('.select2').select2();
        }

        $(document).on('click', '#addRow', function() {
            addRow();
        });

        $(document).on('click', '.deleteRow', function() {
            $(this).closest('tr').remove();
            $('#itemRows tr').each(function(i) {
                $(this).find('td:first').text(i + 1);
            });
        });

        $(document).on('change', '.customer-id', function() {
            const customerId = $(this).val();
            const $project = $(this).closest('tr').find('.project_id');
            $project.empty().append(`<option value="">{{ __('messages.placeholder.select_project') }}</option>`);
            projects.filter(p => p.customer_id == customerId).forEach(p => {
                $project.append(`<option value="${p.id}">${p.project_name}</option>`);
            });
        });

        $('#editFormDepartmentNew').on('submit', function(event) {
            event.preventDefault();
            processingBtn('#btnSave', 1);
            const taskDistribution = [];

            $('.task-row').each(function() {
                taskDistribution.push({
                    tasks: $(this).find('.task-name').val(),
                    customer_id: $(this).find('.customer-id').val(),
                    project_id: $(this).find('.project_id').val(),
                    start_time: $(this).find('.start-time').val(),
                    end_time: $(this).find('.end-time').val(),
                    remarks: $(this).find('.remarks').val(),
                });
            });

            if ($('#task_distribution_json').length === 0) {
                $(this).append('<input type="hidden" name="task_distribution" id="task_distribution_json">');
            }
            $('#task_distribution_json').val(JSON.stringify(taskDistribution));

            $.ajax({
                url: $(this).attr('action'),
                type: 'POST',
                data: $(this).serialize(),
                success: function(result) {
                    if (result.success) {
                        displaySuccessMessage(result.message);
                        window.location.href = "{{ route('task-status.index') }}";
                    }
                },
                error: function(result) {
                    displayErrorMessage(result.responseJSON.message);
                },
                complete: function() {
                    processingBtn('#btnSave', 0);
                }
            });
        });
    </script>

    <script>
        $(document).on('focus', 'input[type="datetime-local"]', function() {
            let now = new Date();
            let year = now.getFullYear();
            let month = String(now.getMonth() + 1).padStart(2, '0');
            let day = String(now.getDate()).padStart(2, '0');
            let hour = String(now.getHours()).padStart(2, '0');
            let minute = String(now.getMinutes()).padStart(2, '0');
            let minDateTime = `${year}-${month}-${day}T${hour}:${minute}`;
            $(this).attr('min', minDateTime);
        });
        $(document).on('focus', 'input[type="date"]', function() {
            let now = new Date();
            let year = now.getFullYear();
            let month = String(now.getMonth() + 1).padStart(2, '0');
            let day = String(now.getDate()).padStart(2, '0');
            let minDate = `${year}-${month}-${day}`;
            $(this).attr('min', minDate);
        });
    </script>

    <script>
        $(document).on('click', '#addTask', function() {
            $('#addTaskModal').modal('show');
            $(this).find('.select2').each(function() {
                $(this).select2({
                    dropdownParent: $('#addTaskModal'),
                    width: 'resolve' // This works with your CSS override
                });
            });
        });
    </script>

    <script>
        $(document).ready(function() {

            var allDesignations = @json($designations); // Get all designations data

            // Function to populate designations dropdown
            function populateDesignations(departmentId) {
                var designationSelect = $('#designation_select');
                designationSelect.empty(); // Clear current options

                // Add 'All' option
                designationSelect.append(new Option('All', ''));

                // Filter designations by department
                $.each(allDesignations, function(index, designation) {
                    if (departmentId === '' || designation.department_id == departmentId) {
                        designationSelect.append(new Option(designation.name, designation.id));
                    }
                });

                designationSelect.trigger('change'); // Trigger change event to update employees
            }

            // Initialize designations and employees with 'All' options
            populateDesignations('');


            // Handle department selection change
            $('#department_select').on('change', function() {
                var departmentId = $(this).val(); // Get selected department ID
                populateDesignations(departmentId);
            });

        });
    </script>

    <script>
        let departmentNewCreateUrl2 = route('task-assign.store');
        $(document).on('submit', '#addNewFormDepartmentNew2', function(event) {
            event.preventDefault();
            processingBtn('#addNewFormDepartmentNew2', '#btnSave', 'loading');

            var new_task = $("#new_task").val();


            $.ajax({
                url: departmentNewCreateUrl2,
                type: 'POST',
                data: $(this).serialize(),
                success: function(result) {
                    if (result.success) {
                        displaySuccessMessage(result.message);
                        const url = route('task-assign.index', );
                        //window.location.href = '/';
                        appendTaskToDropdown(new_task, new_task);
                        //displaySuccessMessage('Task added successfully!');
                        $('#addTaskModal').modal('hide');
                    }
                },
                error: function(result) {
                    displayErrorMessage(result.responseJSON.message);
                },
                complete: function() {
                    processingBtn('#addNewFormDepartmentNew2', '#btnSave');
                },
            });
        });

        function appendTaskToDropdown(taskId, taskTitle) {
            // Create new <option>
            const newOption = new Option(taskTitle, taskId, false, false);

            // Loop through all task-name dropdowns and append the new option
            $('.task-name').each(function() {
                $(this).append(newOption.cloneNode(true)).trigger('change'); // .select2 needs trigger
            });
        }
    </script>

    <script>
        // Convert minutes -> H:M
        function formatDuration(totalMinutes) {
            let hours = Math.floor(totalMinutes / 60);
            let minutes = totalMinutes % 60;
            return `${hours}h ${minutes}m`;
        }

        // Calculate difference in minutes
        function calculateDuration(start, end) {
            if (!start || !end) return 0;
            let startTime = new Date(start);
            let endTime = new Date(end);
            let diffMs = endTime - startTime;
            if (diffMs <= 0) return 0;
            return Math.floor(diffMs / (1000 * 60)); // return minutes
        }

        // Update total duration from all rows
        function updateTotalDuration() {
            let totalMinutes = 0;

            $('.task-row').each(function() {
                let start = $(this).find('.start-time').val();
                let end = $(this).find('.end-time').val();
                totalMinutes += calculateDuration(start, end);
            });

            // Update both fields
            $('#hidden_task_duration').val(totalMinutes); // for DB
            $('#task_duration').val(formatDuration(totalMinutes)); // visible
        }

        // Recalculate when start/end time changes
        $(document).on('change', '.start-time, .end-time', function() {
            updateTotalDuration();
        });

        // Recalculate when row added or deleted
        $(document).on('click', '#addRow, .deleteRow', function() {
            setTimeout(updateTotalDuration, 300);
        });

        // On page load (edit mode: show saved duration)
        $(document).ready(function() {
            let savedMinutes = parseInt($('#hidden_task_duration').val(), 10) || 0;
            $('#task_duration').val(formatDuration(savedMinutes));

            // Also recalc from rows (if times already prefilled)
            updateTotalDuration();
        });
    </script>
@endsection
