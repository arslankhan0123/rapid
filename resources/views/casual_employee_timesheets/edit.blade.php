@extends('layouts.app')
@section('title')
    {{ __('Edit Casual Employee Timesheet') }}
@endsection
@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{ __('Edit Casual Employee Timesheet') }}</h1>
            <div class="section-header-breadcrumb">
                <a href="{{ route('casual-employee-timesheets.index') }}" class="btn btn-primary form-btn">
                    {{ __('Back to List') }}
                </a>
            </div>
        </div>
        <div class="section-body">
            <div class="card">
                <div class="card-body">
                    @include('layouts.errors')
                    {!! Form::open([
                        'route' => ['casual-employee-timesheets.update', $batchId],
                        'method' => 'put',
                        'id' => 'editTimesheetForm',
                    ]) !!}
                    @include('casual_employee_timesheets.edit_fields')
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </section>
@endsection

@section('scripts')
    <style>
        .btn-remove {
            background-color: white;
            color: red;
            border: 1px solid red;
            transition: all 0.2s ease;
        }

        .btn-remove:hover {
            background-color: red;
            color: white;
            border: 1px solid red;
        }

        input[type=number]::-webkit-outer-spin-button,
        input[type=number]::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        input[type=number] {
            -moz-appearance: textfield;
        }
    </style>
    <script>
        $(document).ready(function() {
            let hourlyRowIndex = 0;
            let monthlyRowIndex = 0;
            let casualEmployees = @json($casualEmployees);
            let isInitialLoad = true; // Add this flag

            // Update serial numbers
            function updateSerials(tableType) {
                $(`#${tableType} tr`).each(function(index) {
                    $(this).find('td:first').text(index + 1);
                });
            }

            // Get employee details
            function getEmployeeDetails(employeeId, row) {
                $.ajax({
                    url: "{{ route('get-employee-details', '') }}/" + employeeId,
                    type: 'GET',
                    success: function(response) {
                        row.find('.employee-name').val(response.name);
                        row.find('.employee-designation').val(response.designations);
                        console.log('Employee Details:', response);
                        if (!isInitialLoad) {
                            calculateHourlyRow(row);
                            calculateMonthlyRow(row);
                            calculateTotals();
                        }
                    },
                    error: function() {
                        alert('Error fetching employee details');
                    }
                });
            }

            // Add Hourly Row
            function addHourlyRow(data = {}) {
                let row = `
            <tr data-index="${hourlyRowIndex}">
                <td></td>
                <td>
                    <select class="form-control iqama-no" name="hourly_entries[${hourlyRowIndex}][casual_employee_id]" required>
                        <option value="">Select Iqama No</option>
                        @foreach ($casualEmployees as $employee)
                            <option value="{{ $employee->id }}" ${data.casual_employee_id == {{ $employee->id }} ? 'selected' : ''}>{{ $employee->iqama_no }}</option>
                        @endforeach
                    </select>
                </td>
                <td><input type="text" class="form-control employee-name" value="${data.employee_name || ''}" readonly></td>
                <td><input type="text" class="form-control employee-designation" value="${data.designations || ''}" readonly></td>
                <td><input type="number" class="form-control text-right working-hours" name="hourly_entries[${hourlyRowIndex}][working_hours]" value="${data.working_hours || 0}" step="0.01" required></td>
                <td><input type="number" class="form-control text-right rate-per-hour" name="hourly_entries[${hourlyRowIndex}][rate_per_hour]" value="${data.rate_per_hour || 0}" step="0.01" required></td>
                <td>
                    <input type="number" class="form-control text-right total-amount" value="${data.total_amount || 0}" readonly>
                    <input type="hidden" class="form-control total-amount-hidden" name="hourly_entries[${hourlyRowIndex}][total_amount]" value="${data.total_amount || 0}">
                </td>
                <td><input type="number" class="form-control text-right safety" name="hourly_entries[${hourlyRowIndex}][safety]" value="${data.safety || 0}" step="0.01" required></td>
                <td><input type="number" class="form-control text-right absent-deduction" name="hourly_entries[${hourlyRowIndex}][absent_deduction]" value="${data.absent_deduction || 0}" step="0.01" required></td>
                <td><input type="number" class="form-control text-right advance" name="hourly_entries[${hourlyRowIndex}][advance]" value="${data.advance || 0}" step="0.01" required></td>
                <td>
                    <input type="number" class="form-control text-right net-amount" value="${data.net_amount || 0}" readonly>
                    <input type="hidden" class="form-control net-amount-hidden" name="hourly_entries[${hourlyRowIndex}][net_amount]" value="${data.net_amount || 0}">
                </td>
                <td><button type="button" class="btn btn-danger btn-sm remove-hourly-row"><i class="fas fa-times"></i></button></td>
            </tr>`;
                $('#hourlyRows').append(row);

                // Set employee details if data provided
                if (data.casual_employee_id) {
                    getEmployeeDetails(data.casual_employee_id, $('#hourlyRows tr').last());
                }

                hourlyRowIndex++;
                updateSerials('hourlyRows');
            }

            $('#addHourlyRow').click(() => addHourlyRow());

            // Add Monthly Row
            function addMonthlyRow(data = {}) {
                let row = `
            <tr data-index="${monthlyRowIndex}">
                <td></td>
                <td>
                    <select class="form-control iqama-no" name="monthly_entries[${monthlyRowIndex}][casual_employee_id]" required>
                        <option value="">Select Iqama No</option>
                        @foreach ($casualEmployees as $employee)
                            <option value="{{ $employee->id }}" ${data.casual_employee_id == {{ $employee->id }} ? 'selected' : ''}>{{ $employee->iqama_no }}</option>
                        @endforeach
                    </select>
                </td>
                <td><input type="text" class="form-control employee-name" value="${data.employee_name || ''}" readonly></td>
                <td><input type="text" class="form-control employee-designation" value="${data.designations || ''}" readonly></td>
                <td><input type="number" class="form-control text-right total-days" name="monthly_entries[${monthlyRowIndex}][total_days]" value="${data.total_days || 0}" step="1" required></td>
                <td><input type="number" class="form-control text-right overtime" name="monthly_entries[${monthlyRowIndex}][overtime]" value="${data.overtime || 0}" step="0.01" required></td>
                <td><input type="number" class="form-control text-right basic-salary" name="monthly_entries[${monthlyRowIndex}][basic_salary]" value="${data.basic_salary || 0}" step="0.01" required></td>
                <td>
                    <input type="number" class="form-control text-right ot-amount" value="${data.ot_amount || 0}" readonly>
                    <input type="hidden" class="form-control ot-amount-hidden" name="monthly_entries[${monthlyRowIndex}][ot_amount]" value="${data.ot_amount || 0}">
                </td>
                <td><input type="number" class="form-control text-right safety" name="monthly_entries[${monthlyRowIndex}][safety]" value="${data.safety || 0}" step="0.01" required></td>
                <td><input type="number" class="form-control text-right absent-deduction" name="monthly_entries[${monthlyRowIndex}][absent_deduction]" value="${data.absent_deduction || 0}" step="0.01" required></td>
                <td><input type="number" class="form-control text-right advance" name="monthly_entries[${monthlyRowIndex}][advance]" value="${data.advance || 0}" step="0.01" required></td>
                <td>
                    <input type="number" class="form-control text-right net-amount" value="${data.net_amount || 0}" readonly>
                    <input type="hidden" class="form-control net-amount-hidden" name="monthly_entries[${monthlyRowIndex}][net_amount]" value="${data.net_amount || 0}">
                </td>
                <td><button type="button" class="btn btn-danger btn-sm remove-monthly-row"><i class="fas fa-times"></i></button></td>
            </tr>`;
                $('#monthlyRows').append(row);

                // Set employee details if data provided
                if (data.casual_employee_id) {
                    getEmployeeDetails(data.casual_employee_id, $('#monthlyRows tr').last());
                }

                monthlyRowIndex++;
                updateSerials('monthlyRows');
            }

            $('#addMonthlyRow').click(() => addMonthlyRow());

            // Remove rows
            $(document).on('click', '.remove-hourly-row', function() {
                $(this).closest('tr').remove();
                calculateTotals();
                updateSerials('hourlyRows');
            });

            $(document).on('click', '.remove-monthly-row', function() {
                $(this).closest('tr').remove();
                calculateTotals();
                updateSerials('monthlyRows');
            });

            // Employee selection change
            $(document).on('change', '.iqama-no', function() {
                let employeeId = $(this).val();
                let row = $(this).closest('tr');

                if (employeeId) {
                    getEmployeeDetails(employeeId, row);
                } else {
                    row.find('.employee-name').val('');
                    row.find('.employee-designation').val('');
                }
            });

            // Calculate rows
            $(document).on('input',
                '.working-hours, .rate-per-hour, .safety, .absent-deduction, .advance, .total-days, .overtime, .basic-salary',
                function() {
                    let row = $(this).closest('tr');

                    if (row.find('.working-hours').length) {
                        calculateHourlyRow(row);
                    } else if (row.find('.total-days').length) {
                        calculateMonthlyRow(row);
                    }

                    calculateTotals();
                });

            function calculateHourlyRow(row) {
                let workingHours = parseFloat(row.find('.working-hours').val()) || 0;
                let ratePerHour = parseFloat(row.find('.rate-per-hour').val()) || 0;
                let safety = parseFloat(row.find('.safety').val()) || 0;
                let absentDeduction = parseFloat(row.find('.absent-deduction').val()) || 0;
                let advance = parseFloat(row.find('.advance').val()) || 0;

                let totalAmount = workingHours * ratePerHour;
                let netAmount = totalAmount + safety - absentDeduction - advance;

                row.find('.total-amount').val(totalAmount.toFixed(2));
                row.find('.total-amount-hidden').val(totalAmount.toFixed(2));
                row.find('.net-amount').val(netAmount.toFixed(2));
                row.find('.net-amount-hidden').val(netAmount.toFixed(2));
            }

            function calculateMonthlyRow(row) {
                let totalDays = parseFloat(row.find('.total-days').val()) || 0;
                let overtime = parseFloat(row.find('.overtime').val()) || 0;
                let basicSalary = parseFloat(row.find('.basic-salary').val()) || 0;
                let safety = parseFloat(row.find('.safety').val()) || 0;
                let absentDeduction = parseFloat(row.find('.absent-deduction').val()) || 0;
                let advance = parseFloat(row.find('.advance').val()) || 0;

                // Calculate OT amount (you can modify this formula as needed)
                let otAmount = overtime * (basicSalary / 30 / 8) * 1.5; // Assuming 1.5x rate for OT
                let netAmount = basicSalary + otAmount + safety - absentDeduction - advance;

                row.find('.ot-amount').val(otAmount.toFixed(2));
                row.find('.ot-amount-hidden').val(otAmount.toFixed(2));
                row.find('.net-amount').val(netAmount.toFixed(2));
                row.find('.net-amount-hidden').val(netAmount.toFixed(2));
            }

            function calculateTotals() {
                let hourlyTotals = {
                    workingHours: 0,
                    totalAmount: 0,
                    safety: 0,
                    absentDeduction: 0,
                    advance: 0,
                    netAmount: 0
                };

                let monthlyTotals = {
                    totalDays: 0,
                    overtime: 0,
                    basicSalary: 0,
                    otAmount: 0,
                    safety: 0,
                    absentDeduction: 0,
                    advance: 0,
                    netAmount: 0
                };

                // Calculate hourly totals
                $('#hourlyRows tr').each(function() {
                    hourlyTotals.workingHours += parseFloat($(this).find('.working-hours').val()) || 0;
                    hourlyTotals.totalAmount += parseFloat($(this).find('.total-amount').val()) || 0;
                    hourlyTotals.safety += parseFloat($(this).find('.safety').val()) || 0;
                    hourlyTotals.absentDeduction += parseFloat($(this).find('.absent-deduction').val()) ||
                    0;
                    hourlyTotals.advance += parseFloat($(this).find('.advance').val()) || 0;
                    hourlyTotals.netAmount += parseFloat($(this).find('.net-amount').val()) || 0;
                });

                // Calculate monthly totals
                $('#monthlyRows tr').each(function() {
                    monthlyTotals.totalDays += parseFloat($(this).find('.total-days').val()) || 0;
                    monthlyTotals.overtime += parseFloat($(this).find('.overtime').val()) || 0;
                    monthlyTotals.basicSalary += parseFloat($(this).find('.basic-salary').val()) || 0;
                    monthlyTotals.otAmount += parseFloat($(this).find('.ot-amount').val()) || 0;
                    monthlyTotals.safety += parseFloat($(this).find('.safety').val()) || 0;
                    monthlyTotals.absentDeduction += parseFloat($(this).find('.absent-deduction').val()) ||
                        0;
                    monthlyTotals.advance += parseFloat($(this).find('.advance').val()) || 0;
                    monthlyTotals.netAmount += parseFloat($(this).find('.net-amount').val()) || 0;
                });

                // Update hourly totals display
                $('#hourlyWorkingHoursTotal').text(hourlyTotals.workingHours.toFixed(2));
                $('#hourlyTotalAmountTotal').text(hourlyTotals.totalAmount.toFixed(2));
                $('#hourlySafetyTotal').text(hourlyTotals.safety.toFixed(2));
                $('#hourlyAbsentDeductionTotal').text(hourlyTotals.absentDeduction.toFixed(2));
                $('#hourlyAdvanceTotal').text(hourlyTotals.advance.toFixed(2));
                $('#hourlyNetAmountTotal').text(hourlyTotals.netAmount.toFixed(2));

                // Update monthly totals display
                $('#monthlyTotalDaysTotal').text(monthlyTotals.totalDays);
                $('#monthlyOvertimeTotal').text(monthlyTotals.overtime.toFixed(2));
                $('#monthlyBasicSalaryTotal').text(monthlyTotals.basicSalary.toFixed(2));
                $('#monthlyOtAmountTotal').text(monthlyTotals.otAmount.toFixed(2));
                $('#monthlySafetyTotal').text(monthlyTotals.safety.toFixed(2));
                $('#monthlyAbsentDeductionTotal').text(monthlyTotals.absentDeduction.toFixed(2));
                $('#monthlyAdvanceTotal').text(monthlyTotals.advance.toFixed(2));
                $('#monthlyNetAmountTotal').text(monthlyTotals.netAmount.toFixed(2));
            }

            // Load existing data WITHOUT recalculating
            @if (isset($hourlyTimesheets) && $hourlyTimesheets->count() > 0)
                @foreach ($hourlyTimesheets as $hourly)
                    addHourlyRow({
                        casual_employee_id: {{ $hourly->casual_employee_id }},
                        working_hours: {{ $hourly->working_hours }},
                        rate_per_hour: {{ $hourly->rate_per_hour }},
                        total_amount: {{ $hourly->total_amount }},
                        safety: {{ $hourly->safety }},
                        absent_deduction: {{ $hourly->absent_deduction }},
                        advance: {{ $hourly->advance }},
                        net_amount: {{ $hourly->net_amount }},
                        employee_name: "{{ $hourly->casualEmployee->name }}",
                        designations: "{{ $hourly->casualEmployee->designations }}"
                    });
                @endforeach
            @else
                addHourlyRow();
            @endif

            @if (isset($monthlyTimesheets) && $monthlyTimesheets->count() > 0)
                @foreach ($monthlyTimesheets as $monthly)
                    addMonthlyRow({
                        casual_employee_id: {{ $monthly->casual_employee_id }},
                        total_days: {{ $monthly->total_days }},
                        overtime: {{ $monthly->overtime }},
                        basic_salary: {{ $monthly->basic_salary }},
                        ot_amount: {{ $monthly->ot_amount }},
                        safety: {{ $monthly->safety }},
                        absent_deduction: {{ $monthly->absent_deduction }},
                        advance: {{ $monthly->advance }},
                        net_amount: {{ $monthly->net_amount }},
                        employee_name: "{{ $monthly->casualEmployee->name }}",
                        designations: "{{ $monthly->casualEmployee->designations }}"
                    });
                @endforeach
            @else
                addMonthlyRow();
            @endif

            // Calculate totals only (without recalculating individual rows)
            calculateTotals();

            // Set flag to false after initial load
            isInitialLoad = false;
        });
    </script>
@endsection
