<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label(
                'code',
                __('messages.project_calculations.code') . '<span class="text-danger">*</span>',
                [],
                false,
            ) !!}
            {!! Form::text('code', null, ['class' => 'form-control', 'required', 'maxlength' => 50]) !!}
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label(
                'name',
                __('messages.project_calculations.name') . '<span class="text-danger">*</span>',
                [],
                false,
            ) !!}
            {!! Form::text('name', null, ['class' => 'form-control', 'required', 'maxlength' => 255]) !!}
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label(
                'customer_name',
                __('messages.project_calculations.customer_name') . '<span class="text-danger">*</span>',
                [],
                false,
            ) !!}
            {!! Form::text('customer_name', null, ['class' => 'form-control', 'required', 'maxlength' => 255]) !!}
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('address', __('messages.project_calculations.address'), [], false) !!}
            {!! Form::text('address', null, ['class' => 'form-control', 'maxlength' => 500]) !!}
        </div>
    </div>
</div>

<!-- Project Values Section -->
<div class="row mt-4">
    <div class="col-12">
        <h4>{{ __('messages.project_calculations.project_values') }}</h4>
        <div class="table-responsive">
            <table class="table table-bordered" id="valuesTable">
                <thead>
                    <tr>
                        <th>Sl</th>
                        <th>{{ __('messages.project_calculations.category') }}</th>
                        <th>{{ __('messages.project_calculations.amount') }}</th>
                        <th>{{ __('messages.project_calculations.unit') }}</th>
                        <th>{{ __('messages.project_calculations.quantity') }}</th>
                        <th>{{ __('messages.project_calculations.gross') }}</th>
                        <th>{{ __('messages.project_calculations.discount') }} (Amount)</th>
                        <th>{{ __('messages.project_calculations.net_amount') }}</th>
                        <th>Percentage (%)</th>
                        <th>{{ __('messages.common.action') }}</th>
                    </tr>
                </thead>
                <tbody id="valueRows">
                    <!-- Rows will be added dynamically -->
                </tbody>
                <tfoot>
                    <tr class="font-weight-bold">
                        <td colspan="5" class="text-right">Total:</td>
                        <td class="text-right" id="totalGross">0.00</td>
                        <td class="text-right" id="totalDiscount">0.00</td>
                        <td class="text-right" id="totalNet">0.00</td>
                        <td class="text-right">100.00</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
        <button type="button" class="btn btn-primary mt-2" id="addValueRow"
            style="padding:2px 6px !important; font-size:11px !important; line-height:1 !important; border-radius:3px !important;">
            <i class="fas fa-plus" style="font-size:10px !important;"></i>
            {{ __('messages.project_calculations.add_value_row') }}
        </button>
    </div>
</div>

<!-- Project Expenses Section -->
<div class="row mt-4">
    <div class="col-12">
        <h4>{{ __('messages.project_calculations.project_expenses') }}</h4>
        <div class="table-responsive">
            <table class="table table-bordered" id="expensesTable">
                <thead>
                    <tr>
                        <th>Sl</th>
                        <th>{{ __('messages.project_calculations.expense_name') }}</th>
                        <th>{{ __('messages.project_calculations.amount') }}</th>
                        <th>{{ __('messages.project_calculations.unit') }}</th>
                        <th>{{ __('messages.project_calculations.quantity') }}</th>
                        <th>{{ __('messages.project_calculations.total_amount') }}</th>
                        <th>Percentage (%)</th>
                        <th>{{ __('messages.common.action') }}</th>
                    </tr>
                </thead>
                <tbody id="expenseRows">
                    <!-- Rows will be added dynamically -->
                </tbody>
                <tfoot>
                    <tr class="font-weight-bold">
                        <td colspan="5" class="text-right">Total Expenses:</td>
                        <td class="text-right" id="totalExpenses">0.00</td>
                        <td class="text-right">100.00</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
        <button type="button" class="btn btn-primary mt-2" id="addExpenseRow"
            style="padding:2px 6px !important; font-size:11px !important; line-height:1 !important; border-radius:3px !important;">
            <i class="fas fa-plus" style="font-size:10px !important;"></i>
            {{ __('messages.project_calculations.add_expense_row') }}
        </button>
    </div>
</div>

<!-- Project Commission Section -->
<div class="row mt-4">
    <div class="col-12">
        <h4>Project Commission</h4>
        <div class="table-responsive">
            <table class="table table-bordered" id="commissionsTable">
                <thead>
                    <tr>
                        <th>Sl</th>
                        <th>Commission Amount</th>
                        <th>Project Months</th>
                        <th>Per Month</th>
                        <th>{{ __('messages.common.action') }}</th>
                    </tr>
                </thead>
                <tbody id="commissionRows">
                    <!-- Rows will be added dynamically -->
                </tbody>
                <tfoot>
                    <tr class="font-weight-bold">
                        <td colspan="3" class="text-right">Total Commission:</td>
                        <td class="text-right" id="totalCommission">0.00</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
        <button type="button" class="btn btn-primary mt-2" id="addCommissionRow">
            <i class="fas fa-plus"></i> Add Commission
        </button>
    </div>
</div>

<!-- Project Insurance Section -->
<div class="row mt-4">
    <div class="col-12">
        <h4>Project Insurance</h4>
        <div class="table-responsive">
            <table class="table table-bordered" id="insurancesTable">
                <thead>
                    <tr>
                        <th>Sl</th>
                        <th>Insurance Amount</th>
                        <th>Project Months</th>
                        <th>Per Month</th>
                        <th>{{ __('messages.common.action') }}</th>
                    </tr>
                </thead>
                <tbody id="insuranceRows">
                    <!-- Rows will be added dynamically -->
                </tbody>
                <tfoot>
                    <tr class="font-weight-bold">
                        <td colspan="3" class="text-right">Total Insurance:</td>
                        <td class="text-right" id="totalInsurance">0.00</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
        <button type="button" class="btn btn-primary mt-2" id="addInsuranceRow">
            <i class="fas fa-plus"></i> Add Insurance
        </button>
    </div>
</div>

<!-- Final Calculation Summary - Updated Design -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light py-3">
                <h6 class="mb-0 text-dark font-weight-bold">
                    <i class="fas fa-chart-line me-2"></i>PROJECT SUMMARY
                </h6>
            </div>
            <div class="card-body p-4">
                <div class="row align-items-center">

                    <!-- Duration Input -->
                    <div class="col-lg-2 col-md-3 mb-3 mb-md-0">
                        <label class="form-label small text-muted mb-1">Duration</label>
                        <div class="input-group input-group-sm">
                            <input type="number" class="form-control text-center font-weight-bold" id="projectMonths"
                                name="project_months" value="1" min="1" max="12" required>
                            <!-- Add this hidden field in your Project Summary section -->
                            <input type="hidden" id="profitPerMonth" value="0.00">
                            <span class="input-group-text">Months</span>
                        </div>
                        <small class="form-text text-muted">1 - 12 months</small>
                    </div>

                    <!-- Key Metrics -->
                    <div class="col-lg-10 col-md-9">
                        <div class="row text-center">

                            <!-- Project Value -->
                            <div class="col">
                                <div class="metric-item">
                                    <input type="text" id="finalProjectValue"
                                        class="form-control-plaintext text-success metric-value text-center"
                                        value="0.00" readonly>
                                    <div class="metric-label">Project Value</div>
                                </div>
                            </div>

                            <!-- Expenses -->
                            <div class="col">
                                <div class="metric-item">
                                    <input type="text" id="totalProjectExpenses"
                                        class="form-control-plaintext text-danger metric-value text-center"
                                        value="0.00" readonly>
                                    <div class="metric-label">Expenses</div>
                                </div>
                            </div>

                            <!-- Final Expenses -->
                            <div class="col">
                                <div class="metric-item">
                                    <input type="text" id="finalProjectExpense"
                                        class="form-control-plaintext text-warning metric-value text-center"
                                        value="0.00" readonly>
                                    <div class="metric-label">Final Expenses</div>
                                </div>
                            </div>

                            <!-- Profit -->
                            <div class="col">
                                <div class="metric-item">
                                    <input type="text" id="profitAmount"
                                        class="form-control-plaintext text-success metric-value h5 text-center font-weight-bold"
                                        value="0.00" readonly>
                                    <div class="metric-label font-weight-bold text-success">PROFIT</div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .metric-item {
        padding: 0.5rem;
    }

    .metric-value {
        font-size: 1.2rem;
        font-weight: bold;
        margin-bottom: 0.2rem;
    }

    .metric-label {
        font-size: 0.85rem;
        color: #6c757d;
        font-weight: 500;
    }

    .input-group-sm {
        max-width: 160px;
    }

    @media (max-width: 768px) {
        .metric-value {
            font-size: 1rem;
        }

        .metric-item {
            padding: 0.3rem;
        }
    }
</style>


<!-- Project Partners Section -->
<div class="row mt-4">
    <div class="col-12">
        <h4>Project Calculation Partners</h4>
        <div class="table-responsive">
            <table class="table table-bordered" id="partnersTable">
                <thead>
                    <tr>
                        <th>Sl</th>
                        <th>Name</th>
                        <th>Profit Amount</th> <!-- Changed from "Amount" to "Profit Amount" -->
                        <th>Percentage (%)</th>
                        <th>Per Partner</th>
                        <th>{{ __('messages.common.action') }}</th>
                    </tr>
                </thead>
                <tbody id="partnerRows"></tbody>
                <tfoot>
                    <tr class="font-weight-bold">
                        <td colspan="3" class="text-right">Total Percentage:</td>
                        <td class="text-right" id="totalPartnerPercentage">0.00%</td>
                        <td class="text-right" id="totalPartnerAmount">0.00</td>
                        <td></td>
                    </tr>
                    <tr class="font-weight-bold">
                        <td colspan="3" class="text-right">Remaining Profit:</td>
                        <td class="text-right" id="remainingPercentage">100.00%</td>
                        <td class="text-right" id="remainingAmount">0.00</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
        <button type="button" class="btn btn-primary mt-2" id="addPartnerRow">
            <i class="fas fa-plus"></i> Add Partner
        </button>
    </div>
</div>

{{-- <!-- Project Values Section -->
<div class="row mt-4">
    <div class="col-12">
        <h4>{{ __('messages.project_calculations.project_values') }}</h4>
        <div class="table-responsive">
            <table class="table table-bordered" id="valuesTable">
                <thead>
                    <tr>
                        <th>Sl</th>
                        <th>{{ __('messages.project_calculations.category') }}</th>
                        <th>{{ __('messages.project_calculations.amount') }}</th>
                        <th>Percentage (%)</th>
                        <th>{{ __('messages.project_calculations.unit') }}</th>
                        <th>{{ __('messages.project_calculations.quantity') }}</th>
                        <th>{{ __('messages.project_calculations.gross') }}</th>
                        <th>{{ __('messages.project_calculations.discount') }} (Amount)</th>
                        <th>{{ __('messages.project_calculations.net_amount') }}</th>
                        <th>{{ __('messages.common.action') }}</th>
                    </tr>
                </thead>
                <tbody id="valueRows">
                    <!-- Rows will be added dynamically -->
                </tbody>
                <tfoot>
                    <tr class="font-weight-bold">
                        <td colspan="5" class="text-right">Total:</td>
                        <td class="text-right" id="totalGross">0.00</td>
                        <td class="text-right" id="totalDiscount">0.00</td>
                        <td class="text-right" id="totalNet">0.00</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
        <button type="button" class="btn btn-primary mt-2" id="addValueRow"
            style="padding:2px 6px !important; font-size:11px !important; line-height:1 !important; border-radius:3px !important;">
            <i class="fas fa-plus" style="font-size:10px !important;"></i>
            {{ __('messages.project_calculations.add_value_row') }}
        </button>
    </div>
</div>

<!-- Project Expenses Section -->
<div class="row mt-4">
    <div class="col-12">
        <h4>{{ __('messages.project_calculations.project_expenses') }}</h4>
        <div class="table-responsive">
            <table class="table table-bordered" id="expensesTable">
                <thead>
                    <tr>
                        <th>Sl</th>
                        <th>{{ __('messages.project_calculations.expense_name') }}</th>
                        <th>{{ __('messages.project_calculations.amount') }}</th>
                        <th>{{ __('messages.project_calculations.percentage') }} (%)</th>
                        <th>{{ __('messages.project_calculations.unit') }}</th>
                        <th>{{ __('messages.project_calculations.quantity') }}</th>
                        <th>{{ __('messages.project_calculations.total_amount') }}</th>
                        <th>{{ __('messages.common.action') }}</th>
                    </tr>
                </thead>
                <tbody id="expenseRows">
                    <!-- Rows will be added dynamically -->
                </tbody>
                <tfoot>
                    <tr class="font-weight-bold">
                        <td colspan="5" class="text-right">Total Expenses:</td>
                        <td class="text-right" id="totalExpenses">0.00</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
        <button type="button" class="btn btn-primary mt-2" id="addExpenseRow"
            style="padding:2px 6px !important; font-size:11px !important; line-height:1 !important; border-radius:3px !important;">
            <i class="fas fa-plus" style="font-size:10px !important;"></i>
            {{ __('messages.project_calculations.add_expense_row') }}
        </button>
    </div>
</div>

<!-- Project Partners Section -->
<div class="row mt-4">
    <div class="col-12">
        <h4>Project Calculation Partners</h4>
        <div class="table-responsive">
            <table class="table table-bordered" id="partnersTable">
                <thead>
                    <tr>
                        <th>Sl</th>
                        <th>Name</th>
                        <th>Amount</th>
                        <th>Percentage (%)</th>
                        <th>Per Partner</th>
                        <th>{{ __('messages.common.action') }}</th>
                    </tr>
                </thead>
                <tbody id="partnerRows"></tbody>
            </table>
        </div>
        <button type="button" class="btn btn-primary mt-2" id="addPartnerRow">
            <i class="fas fa-plus"></i> Add Partner
        </button>
    </div>
</div>



<!-- Overall Summary -->
<div class="row mt-4">
    <div class="col-md-6 offset-md-6">
        <table class="table table-bordered">
            <tr class="font-weight-bold">
                <td>Total Project Value:</td>
                <td class="text-right" id="summaryTotalNet">0.00</td>
            </tr>
            <tr class="font-weight-bold">
                <td>Total Expenses:</td>
                <td class="text-right" id="summaryTotalExpenses">0.00</td>
            </tr>
            <tr class="font-weight-bold text-success">
                <td>Net Profit:</td>
                <td class="text-right" id="summaryNetProfit">0.00</td>
            </tr>
        </table>
    </div>
</div> --}}

<div class="row mt-4">
    <div class="col-md-12 text-right">
        <a href="{{ route('project-calculations.index') }}"
            class="btn btn-secondary btn-lg px-4 ml-2">{{ __('messages.common.cancel') }}</a>
        {!! Form::submit(__('messages.common.save'), ['class' => 'btn btn-primary btn-lg px-4']) !!}

    </div>
</div>
