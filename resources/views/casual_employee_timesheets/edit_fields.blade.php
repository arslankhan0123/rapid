<!-- Date Fields Section -->
<div class="row">
    <div class="form-group col-sm-6">
        {!! Form::label('timesheet_date', __('Hourly Timesheet Date') . ':') !!}
        {!! Form::date('timesheet_date', isset($hourlyTimesheets[0]) ? $hourlyTimesheets[0]->timesheet_date : null, [
            'class' => 'form-control',
            'required',
        ]) !!}
    </div>
    <div class="form-group col-sm-6">
        {!! Form::label('month_year', __('Monthly Timesheet Month') . ':') !!}
        {!! Form::month('month_year', isset($monthlyTimesheets[0]) ? $monthlyTimesheets[0]->month_year : null, [
            'class' => 'form-control',
            'required',
        ]) !!}
    </div>

    <div class="form-group col-sm-6">
        {!! Form::label('client_name', __('Client Name')) !!}
        {!! Form::text('client_name', isset($hourlyTimesheets[0]) ? $hourlyTimesheets[0]->client_name : null, [
            'class' => 'form-control',
            'required',
        ]) !!}
    </div>
    <div class="form-group col-sm-6">
        {!! Form::label('project_name', __('Project Name')) !!}
        {!! Form::text('project_name', isset($hourlyTimesheets[0]) ? $hourlyTimesheets[0]->project_name : null, [
            'class' => 'form-control',
            'required',
        ]) !!}
    </div>

</div>

<!-- Hourly Timesheet Section -->
<div class="row mt-4">
    <div class="col-12">
        <h4>{{ __('Hourly Timesheet') }}</h4>
        <div class="table-responsive">
            <table class="table table-bordered" id="hourlyTable">
                <thead>
                    <tr>
                        <th>Sl</th>
                        <th>{{ __('Iqama No') }}</th>
                        <th>{{ __('Employee Name') }}</th>
                        <th>{{ __('Designation') }}</th>
                        <th>{{ __('Working Hours') }}</th>
                        <th>{{ __('Rate Per Hour') }}</th>
                        <th>{{ __('Total Amount') }}</th>
                        <th>{{ __('Safety') }}</th>
                        <th>{{ __('Absent Deduction') }}</th>
                        <th>{{ __('Advance') }}</th>
                        <th>{{ __('Net Amount') }}</th>
                        <th>{{ __('Action') }}</th>
                    </tr>
                </thead>
                <tbody id="hourlyRows">
                    <!-- Rows will be added dynamically -->
                </tbody>
                <tfoot>
                    <tr class="font-weight-bold">
                        <td colspan="4" class="text-right">Total:</td>
                        <td class="text-right" id="hourlyWorkingHoursTotal">0.00</td>
                        <td></td>
                        <td class="text-right" id="hourlyTotalAmountTotal">0.00</td>
                        <td class="text-right" id="hourlySafetyTotal">0.00</td>
                        <td class="text-right" id="hourlyAbsentDeductionTotal">0.00</td>
                        <td class="text-right" id="hourlyAdvanceTotal">0.00</td>
                        <td class="text-right" id="hourlyNetAmountTotal">0.00</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
        <button type="button" class="btn btn-primary mt-2" id="addHourlyRow">
            <i class="fas fa-plus"></i> {{ __('Add Hourly Row') }}
        </button>
    </div>
</div>

<!-- Monthly Timesheet Section -->
<div class="row mt-4">
    <div class="col-12">
        <h4>{{ __('Monthly Timesheet') }}</h4>
        <div class="table-responsive">
            <table class="table table-bordered" id="monthlyTable">
                <thead>
                    <tr>
                        <th>Sl</th>
                        <th>{{ __('Iqama No') }}</th>
                        <th>{{ __('Employee Name') }}</th>
                        <th>{{ __('Designation') }}</th>
                        <th>{{ __('Total Days') }}</th>
                        <th>{{ __('Overtime') }}</th>
                        <th>{{ __('Basic Salary') }}</th>
                        <th>{{ __('OT Amount') }}</th>
                        <th>{{ __('Safety') }}</th>
                        <th>{{ __('Absent Deduction') }}</th>
                        <th>{{ __('Advance') }}</th>
                        <th>{{ __('Net Amount') }}</th>
                        <th>{{ __('Action') }}</th>
                    </tr>
                </thead>
                <tbody id="monthlyRows">
                    <!-- Rows will be added dynamically -->
                </tbody>
                <tfoot>
                    <tr class="font-weight-bold">
                        <td colspan="4" class="text-right">Total:</td>
                        <td class="text-right" id="monthlyTotalDaysTotal">0</td>
                        <td class="text-right" id="monthlyOvertimeTotal">0.00</td>
                        <td class="text-right" id="monthlyBasicSalaryTotal">0.00</td>
                        <td class="text-right" id="monthlyOtAmountTotal">0.00</td>
                        <td class="text-right" id="monthlySafetyTotal">0.00</td>
                        <td class="text-right" id="monthlyAbsentDeductionTotal">0.00</td>
                        <td class="text-right" id="monthlyAdvanceTotal">0.00</td>
                        <td class="text-right" id="monthlyNetAmountTotal">0.00</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
        <button type="button" class="btn btn-primary mt-2" id="addMonthlyRow">
            <i class="fas fa-plus"></i> {{ __('Add Monthly Row') }}
        </button>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-12 text-right">
        <a href="{{ route('casual-employee-timesheets.index') }}"
            class="btn btn-secondary btn-lg px-4 ml-2">{{ __('Cancel') }}</a>
        {!! Form::submit(__('Update'), ['class' => 'btn btn-primary btn-lg px-4']) !!}
    </div>
</div>
