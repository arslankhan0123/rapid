@extends('layouts.app')
@section('title')
    {{ __('messages.salary_generates.salary_generates') }}
@endsection
@section('page_css')
    <link href="{{ asset('assets/css/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/bs4-summernote/summernote-bs4.css') }}">
@endsection
@section('content')
    <section class="section">
        <div class="section-header item-align-right">
            <h1>{{ __('messages.salary_generates.add') }}</h1>
            <div class="section-header-breadcrumb float-right">
                <div class="card-header-action mr-3 select2-mobile-margin">
                </div>
            </div>
            <div class="float-right">
                <a href="{{ route('salary_generates.index') }}"
                    class="btn btn-primary form-btn">{{ __('messages.salary_generates.list') }} </a>
            </div>
        </div>
        <div class="section-body">
            <div class="card">
                <div class="card-body">
                    {{ Form::open(['id' => 'addNewFormDepartmentNew']) }}
                    <div class="alert alert-danger d-none" id="validationErrorsBox"></div>
                    <div class="row">
                        <div class="form-group col-md-6">
                            {{ Form::label('bonus_type_id', __('messages.transfers.from') . ':') }}<span
                                class="required">*</span>
                            {{ Form::select('branch_id', $usersBranches, null, [
                                'class' => 'form-control select2',
                                'id' => 'from_branch',
                            ]) }}
                        </div>
                        <div class="form-group col-md-6">
                            {{ Form::label('name', __('messages.salary_generates.salary_month') . ':') }}<span
                                class="required">*</span>
                            @php
                                use Illuminate\Support\Carbon;

                                // Get the current month in 'YYYY-MM' format
                                $currentMonth = Carbon::now()->format('Y-m');
                            @endphp

                            {{ Form::month('salary_month', $currentMonth, ['class' => 'form-control', 'required', 'id' => 'award_name', 'autocomplete' => 'off', 'max' => $currentMonth]) }}
                        </div>
                    </div>
                    <div class="text-right mr-1">
                        {{ Form::button('Preview', ['type' => 'submit', 'class' => 'btn btn-primary', 'id' => 'btnSave', 'data-loading-text' => "<span class='spinner-border spinner-border-sm'></span> Processing..."]) }}

                    </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </section>
    @include('salary_generates.modals.preview_salary')
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
        let departmentNewCreateUrl = route('salary_generates.store');
        $(document).on('submit', '#addNewFormDepartmentNew', function(event) {
            event.preventDefault();
            processingBtn('#addNewFormDepartmentNew', '#btnSave', 'loading');
            startLoader();
            $.ajax({
                url: departmentNewCreateUrl,
                type: 'POST',
                data: $(this).serialize(),
                success: function(result) {
                    stopLoader();
                    if (result.success) {
                        //displaySuccessMessage(result.message);
                        populateSalaryView(result.data)
                    }
                },
                error: function(result) {
                    displayErrorMessage(result.responseJSON.message);
                    stopLoader();
                },
                complete: function() {
                    stopLoader();
                    processingBtn('#addNewFormDepartmentNew', '#btnSave');
                },
            });
        });


        function populateSalaryView(data) {



            $('#salaryGenerateId').val(data.salary_info.id);
            // Convert "YYYY-MM" to "Month YYYY"
            let salaryMonthRaw = data.salary_info.salary_month; // Example: "2025-03"
            let [year, month] = salaryMonthRaw.split('-');

            var formattedMonth = new Date(year, month - 1).toLocaleString('en-US', {
                month: 'long',
                year: 'numeric'
            });

            formattedMonth += ",Branch " + data.salary_info.branch?.name ?? '';

            $('#salaryMonth').text(formattedMonth); // Outputs: "March 2025"



            let employeeHtml = `
            <div style="overflow-x: auto;">
                <table class="table-sm table-bordered" style="width:100%;">
                    <thead class="text-center">
                        <tr style="background: #2c9451;color: white;">
                            <th>SL</th>
                            <th style="white-space:nowrap;">Emp Id</th>
                            <th style="white-space:nowrap;">Employee Name</th>
                            <th style="white-space:nowrap;">Iqama</th>
                            <th>Designation</th>
                            <th style="white-space:nowrap;">Working Days</th>
                            <th>Basic</th>
                            <th>Bonuses</th>
                            <th>Overtime</th>
                            <th>Allowances </th>
                            <th>Gross</th>
                            <th>Advance</th>
                            <th>Loan</th>
                            <th>Absent Deduction</th>
                            <th>Insurance</th>
                            <th>Allowance Deduction</th>
                            <th>Deduction</th>
                            <th style="display: none">Overtime Hours</th>
                            <th style="display: none">Absence Hours</th>
                            <th style="display: none">Worked Hours</th>
                            <th style="display: none">Ideal Hours</th>
                            <th>Net</th>
                        </tr>
                    </thead>
                <tbody>`;



            data.emoloyees_info.forEach((employee, index) => {
                console.log(employee);
                employeeHtml += `
                    <tr>
                        <td>${index + 1}</td>
                        <td style="white-space:nowrap;">${employee.employee.code}</td>
                        <td style="white-space:nowrap;">${employee.employee.name}</td>
                        <td style="white-space:nowrap;">${employee.employee.iqama_no??""}</td>
                        <td>${employee.employee.designation?.name??""}</td>
                        <td style="white-space:nowrap;" class="text-center">${employee.working_days ?? 0}</td>
                        <td class="text-end">${ (employee.basic_salary ?? 0).toFixed(2) }</td>
                        <td class="text-end">${employee.total_bonus.toFixed(2)}</td>
                        <td class="text-end">${employee.total_overtimes.toFixed(2)}</td>
                        <td class="text-end">${employee.total_allowances.toFixed(2)}</td>
                        <td class="text-end">${((employee.basic_salary ?? 0) + (employee.total_bonus ?? 0) + (employee.total_overtimes ?? 0) + (employee.total_allowances ?? 0)).toFixed(2)}</td>
                        <td class="text-end">${employee.salary_advance.toFixed(2)}</td>
                        <td class="text-end">${employee.loan.toFixed(2)}</td>
                        <td class="text-end">${employee.hourly_deduction.toFixed(2)}</td>
                        <td class="text-end">${employee.total_insurance.toFixed(2)}</td>
                        <td class="text-end">${(employee.allowance_deduction ?? 0).toFixed(2)}</td>
                        <td class="text-end">${(employee.manual_deduction ?? 0).toFixed(2)}</td>
                        <td class="text-end" style="display: none">${employee.overtime_hours.toFixed(2)}</td>
                        <td class="text-end" style="display: none">${employee.absence_hours.toFixed(2)}</td>
                        <td class="text-end" style="display: none">${employee.worked_hours.toFixed(2)}</td>
                        <td class="text-end" style="display: none">${employee.working_hours.toFixed(2)}</td>
                        <td class="text-end">${employee.net_salary.toFixed(2)}</td>
                    </tr>`;
            });

            employeeHtml += `</tbody></table></div>`;

            $('#employeeList').html(employeeHtml);
            // Show the modal
            $('#previewSalarySheet').modal('show');

        }
    </script>


    <script>
        $(document).on('click', '#btnApprove', function() {
            let salaryGenerateId = $('#salaryGenerateId').val();
            if (!salaryGenerateId) {
                displayErrorMessage('Salary Generate ID is missing.');
                return;
            }
            startLoader();
            $.ajax({
                url: route('salary_generates.verify', salaryGenerateId), // Using the correct named route
                type: 'GET', // Since your route uses GET method
                success: function(response) {
                    if (response.success) {
                        displaySuccessMessage(response.message);
                        $('#previewSalarySheet').modal('hide'); // Hide modal after approval
                        // Optionally, refresh data table or page
                        stopLoader();
                    }
                },
                error: function(xhr) {
                    displayErrorMessage(xhr.responseJSON.message);
                    stopLoader();
                }
            });
        });
    </script>
@endsection
