@extends('layouts.app')
@section('title')
    {{ __('messages.task-status.add') }}
@endsection
@section('page_css')
    <link href="{{ asset('assets/css/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ asset('assets/css/bs4-summernote/summernote-bs4.css') }}">
    <link href="{{ asset('css/bootstrap-datetimepicker.css') }}" rel="stylesheet" type="text/css" />
@endsection
@section('content')
    <section class="section">
        <div class="section-header item-align-right">
            <h1>{{ __('messages.task-status.add') }}</h1>
            <div class="section-header-breadcrumb float-right">
                <div class="card-header-action mr-3 select2-mobile-margin">
                </div>
            </div>
            <div class="float-right">
                <a href="{{ route('task-status.index') }}"
                    class="btn btn-primary form-btn">{{ __('messages.task-status.list') }} </a>
            </div>
        </div>
        <div class="section-body">
            <div class="card">
                <div class="card-body">

                    {{ Form::open(['id' => 'addNewFormDepartmentNew']) }}

                    <div class="alert alert-danger d-none" id="validationErrorsBox"></div>
                    <div class="row">
                        <div class="form-group  col-md-6">
                            {{ Form::label('admin_note', 'Branch') }}<span class="required">*</span>
                            {{ Form::select('branch_id', $usersBranches ?? [], null, ['class' => 'form-control select2', 'required', 'id' => 'branchSelect', 'placeholder' => 'Select Branch']) }}
                        </div>
                        <div class="form-group col-md-6">
                            {{ Form::label('employee_id', __('messages.task-status.user') . ':') }}<span
                                class="required">*</span>
                            {{ Form::select('user_id', [], null, [
                                'class' => 'form-control',
                                'required',
                                'id' => 'employee_select',
                                'placeholder' => __('messages.attendances.select_iqama'),
                            ]) }}
                        </div>
                        <!-- User Selection -->
                        {{-- <div class="form-group col-md-6 col-sm-12">
                            {{ Form::label('user_id', __('messages.task-status.user')) }}<span class="required">*</span>
                            {{ Form::select('user_id', $users, auth()->user()->id ?? null, ['class' => 'form-control select2', 'required', 'id' => 'user_id', auth()->user()->is_admin ? '' : 'disabled']) }}
                            @if (!auth()->user()->is_admin)
                                {{ Form::hidden('user_id', auth()->user()->id) }}
                            @endif
                        </div> --}}
                        <div class="form-group col-md-6 col-md-6">
                            {{ Form::label('date', __('messages.task-status.date')) }}<span class="required">*</span>
                            {{ Form::date('date', \Carbon\Carbon::today()->toDateString(), ['class' => 'form-control', 'required', 'id' => 'task_date']) }}
                        </div>
                        <div class="form-group col-md-6">
                            {{ Form::label('duration', __('messages.timeline')) }}<span
                                class="required">*</span>
                            {{ Form::text('duration', null, ['class' => 'form-control', 'required', 'id' => 'task_duration' ]) }}
                        </div> 
                    </div>
                    
                    
                    
                    <div class="col-lg-12 pt-2">

                        <div class="row table-wrapper ">
                            <table class="table table-responsive table-md page_contents" id="itemsTable" style="height: 400px; overflow: auto;">
                                <thead>
                                    <tr style="padding: 0px;">
                                        <th style="width: 10%;">Sl.</th>
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
                                    <!-- Default row -->
                                    <tr data-index="0" class="task-row">
                                    <td class="p-1 text-center">1</td>
                                    <td class="p-1">
                                        <select class="form-control select2 task-name" >
                                            @foreach ($task_list as $id => $task)
                                                <option value="{{ $id }}">{{ $task }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="p-1">
                                        <select class="form-control select2 customer-id">
                                            <option value="">{{ __('messages.placeholder.select_customer') }}</option>
                                            @foreach ($customers as $id => $customer)
                                                <option value="{{ $id }}">{{ $customer }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="p-1">
                                        <select class="form-control select2 project_id">
                                            <option value="">{{ __('messages.placeholder.select_project') }}</option>
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
                                        <button type="button" class="btn btn-danger btn-sm deleteRow"><i class="fa fa-trash"></i></button>
                                    </td>
                                </tr>
                                
                                </tbody>
                            </table>
                            <button type="button" class="btn btn-info" id="addRow"><i class="fa fa-plus"></i> Add Row</button>
                        </div> 
                        
                    </div>


                    <div class="text-right mr-1">
                        {{ Form::button(__('messages.common.submit'), ['type' => 'submit', 'class' => 'btn btn-primary', 'id' => 'btnSave', 'data-loading-text' => "<span class='spinner-border spinner-border-sm'></span> Processing..."]) }}
                    </div>
                    {{ Form::close() }}

                </div>
            </div>
        </div>
    </section>
    
        <!-- Task Assign Modal -->
    <div class="modal fade" id="taskAssignModal" tabindex="-1" role="dialog" aria-labelledby="taskAssignModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="taskAssignModalLabel">{{ __('messages.task-assign.add') }}</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            @include('task_assign.partials.create') {{-- Move your existing Task Assign form here as a partial --}}
          </div>
        </div>
      </div>
    </div>
    

@endsection
@section('page_scripts')
    <script src="{{ asset('assets/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ mix('assets/js/custom/custom-datatable.js') }}"></script>
    <script src="{{ mix('assets/js/bs4-summernote/summernote-bs4.js') }}"></script>
    <script src="{{ mix('assets/js/select2.min.js') }}"></script>

    <script src="{{ asset('js/bootstrap-datetimepicker.min.js') }}"></script>
@endsection
@section('scripts')
    <script src="{{ mix('assets/js/custom/input-price-format.js') }}"></script>
    <script>
        const departmentNewCreateUrl = "{{ route('task-status.store') }}";
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

        $('#branchSelect').on('change', function() {
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
    <script>
        var projects = @json($projects);
    
        // Add Row
        $('#addRow').on('click', function () {
            const index = $('#itemRows tr').length + 1;
            const newRow = `
            <tr class="task-row">
                <td class="p-1 text-center">${index}</td>
                <td class="p-1">
                    <select class="form-control select2 task-name">
                        @foreach ($task_list as $id => $task)
                            <option value="{{ $id }}">{{ $task }}</option>
                        @endforeach
                    </select>
                </td>
                <td class="p-1">
                    <select class="form-control select2 customer-id">
                        <option value="">{{ __('messages.placeholder.select_customer') }}</option>
                        @foreach ($customers as $id => $customer)
                            <option value="{{ $id }}">{{ $customer }}</option>
                        @endforeach
                    </select>
                </td>
                <td class="p-1">
                    <select class="form-control select2 project_id">
                        <option value="">{{ __('messages.placeholder.select_project') }}</option>
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
                    <button type="button" class="btn btn-danger btn-sm deleteRow"><i class="fa fa-trash"></i></button>
                </td>
            </tr>
            `;
            $('#itemRows').append(newRow);
            $('.select2').select2(); // Re-initialize select2
        });
    
        // Delete Row
        $(document).on('click', '.deleteRow', function () {
            $(this).closest('tr').remove();
            $('#itemRows tr').each(function (i) {
                $(this).find('td:first').text(i + 1); // Reset serial numbers
            });
        });
    
        // Dynamically populate project dropdown based on customer selection
        $(document).on('change', '.customer-id', function () {
            var $row = $(this).closest('tr');
            var customerId = $(this).val();
            var $projectDropdown = $row.find('.project_id');
    
            // Clear and reset
            $projectDropdown.empty().append('<option value="">{{ __('messages.placeholder.select_project') }}</option>');
    
            if (customerId) {
                var filteredProjects = projects.filter(function (project) {
                    return project.customer_id == customerId;
                });
    
                $.each(filteredProjects, function (index, project) {
                    $projectDropdown.append('<option value="' + project.id + '">' + project.project_name + '</option>');
                });
    
                $projectDropdown.trigger('change'); // Refresh Select2
            }
        });
    
        // Submit form
        $('#addNewFormDepartmentNew').on('submit', function (event) {
            event.preventDefault();
            processingBtn('#btnSave', 1);
    
            const taskDistribution = [];
    
            $('.task-row').each(function () {
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
                url: departmentNewCreateUrl,
                type: 'POST',
                data: $(this).serialize(),
                success: function (result) {
                    if (result.success) {
                        displaySuccessMessage(result.message);
                        window.location.href = route('task-status.index');
                    }
                },
                error: function (result) {
                    displayErrorMessage(result.responseJSON.message);
                },
                complete: function () {
                    processingBtn('#btnSave', 0);
                }
            });
        });
    </script>
    
    <script>
        $(document).on('focus', 'input[type="datetime-local"]', function () {
            let now = new Date();
            let year = now.getFullYear();
            let month = String(now.getMonth() + 1).padStart(2, '0');
            let day = String(now.getDate()).padStart(2, '0');
            let hour = String(now.getHours()).padStart(2, '0');
            let minute = String(now.getMinutes()).padStart(2, '0');
            let minDateTime = `${year}-${month}-${day}T${hour}:${minute}`;
            $(this).attr('min', minDateTime);
        });
        $(document).on('focus', 'input[type="date"]', function () {
            let now = new Date();
            let year = now.getFullYear();
            let month = String(now.getMonth() + 1).padStart(2, '0');
            let day = String(now.getDate()).padStart(2, '0');
            let minDate = `${year}-${month}-${day}`;
            $(this).attr('min', minDate);
        });
    </script>

@endsection
