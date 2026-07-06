@extends('layouts.app')
@section('title')
    {{ __('messages.project_calculations.edit_project_calculation') }}
@endsection

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{ __('messages.project_calculations.edit_project_calculation') }}</h1>
            <div class="section-header-breadcrumb">
                <a href="{{ route('project-calculations.index') }}" class="btn btn-primary form-btn">
                    {{ __('messages.common.back') }}
                </a>
            </div>
        </div>

        <div class="section-body">
            <div class="card">
                <div class="card-body">
                    @include('layouts.errors')

                    {!! Form::model($projectCalculation, [
                        'route' => ['project-calculations.update', $projectCalculation->id],
                        'method' => 'put',
                        'id' => 'editProjectCalculationForm',
                    ]) !!}

                    @include('project_calculations.fields')

                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </section>
@endsection


@section('scripts')
    <script>
        // Initial row indices
        let valueRowIndex = {{ $projectCalculation->values->count() }};
        let expenseRowIndex = {{ $projectCalculation->expenses->count() }};
        let commissionRowIndex = {{ $projectCalculation->commissions->count() }};
        let insuranceRowIndex = {{ $projectCalculation->insurances->count() }};
        let partnerRowIndex = {{ $projectCalculation->partners->count() }};

        // =========================
        // FUNCTIONS
        // =========================
        function addValueRow(category = '', amount = 0, unit = 1, quantity = 1, discount = 0, gross = 0, net = 0,
            percentage = 0) {
            category = category ?? '';
            amount = parseFloat(amount) || 0;
            unit = parseFloat(unit) || 1;
            quantity = parseInt(quantity) || 1;
            discount = parseFloat(discount) || 0;
            gross = parseFloat(gross) || 0;
            net = parseFloat(net) || 0;
            percentage = parseFloat(percentage) || 0;

            let row = `
        <tr data-index="${valueRowIndex}">
            <td class="sl-number">${valueRowIndex + 1}</td>
            <td><input type="text" class="form-control" name="values[${valueRowIndex}][category]" value="${category}" required></td>
            <td><input type="number" class="form-control text-right amount" name="values[${valueRowIndex}][amount]" value="${amount}" step="0.01" required></td>
            <td><input type="number" class="form-control text-right unit" name="values[${valueRowIndex}][unit]" value="${unit}" step="0.01" required></td>
            <td><input type="number" class="form-control text-right quantity" name="values[${valueRowIndex}][quantity]" value="${quantity}" min="1" step="1" required></td>
            <td><input type="number" class="form-control text-right gross" value="${gross}" readonly></td>
            <td><input type="number" class="form-control text-right discount" name="values[${valueRowIndex}][discount]" value="${discount}" step="0.01"></td>
            <td><input type="number" class="form-control text-right net" value="${net}" readonly></td>
            <td><input type="number" class="form-control text-right percentage" name="values[${valueRowIndex}][percentage]" value="${percentage}" readonly></td>
            <td>
                <button type="button" class="btn btn-remove btn-sm remove-value-row">
                    <i class="fas fa-times"></i>
                </button>
            </td>
        </tr>`;
            $('#valueRows').append(row);
            valueRowIndex++;
            updateSlNumbers('#valueRows');
            calculateRow($('#valueRows tr').last());
            calculateTotals();
        }

        function addExpenseRow(name = '', amount = 0, unit = 1, quantity = 1, total = 0, percentage = 0) {
            name = name ?? '';
            amount = parseFloat(amount) || 0;
            unit = parseFloat(unit) || 1;
            quantity = parseInt(quantity) || 1;
            total = parseFloat(total) || 0;
            percentage = parseFloat(percentage) || 0;

            let row = `
        <tr data-index="${expenseRowIndex}">
            <td class="sl-number">${expenseRowIndex + 1}</td>
            <td><input type="text" class="form-control" name="expenses[${expenseRowIndex}][name]" value="${name}" required></td>
            <td><input type="number" class="form-control text-right amount" name="expenses[${expenseRowIndex}][amount]" value="${amount}" step="0.01" required></td>
            <td><input type="number" class="form-control text-right unit" name="expenses[${expenseRowIndex}][unit]" value="${unit}" step="0.01" required></td>
            <td><input type="number" class="form-control text-right quantity" name="expenses[${expenseRowIndex}][quantity]" value="${quantity}" min="1" step="1" required></td>
            <td><input type="number" class="form-control text-right total" value="${total}" readonly></td>
            <td><input type="number" class="form-control text-right percentage" name="expenses[${expenseRowIndex}][percentage]" value="${percentage}" readonly></td>
            <td>
                <button type="button" class="btn btn-remove btn-sm remove-expense-row">
                    <i class="fas fa-times"></i>
                </button>
            </td>
        </tr>`;
            $('#expenseRows').append(row);
            expenseRowIndex++;
            updateSlNumbers('#expenseRows');
            calculateRow($('#expenseRows tr').last());
            calculateTotals();
        }

        function addCommissionRow(commission_amount = 0, months = 1, per_month = 0) {
            commission_amount = parseFloat(commission_amount) || 0;
            months = parseInt(months) || 1;
            per_month = parseFloat(per_month) || 0;

            let row = `
        <tr data-index="${commissionRowIndex}">
            <td class="sl-number">${commissionRowIndex + 1}</td>
            <td><input type="number" class="form-control text-right commission-amount" name="commissions[${commissionRowIndex}][commission_amount]" value="${commission_amount}" step="0.01" required></td>
            <td><input type="number" class="form-control text-right months" name="commissions[${commissionRowIndex}][months]" value="${months}" min="1" max="12" required></td>
            <td><input type="number" class="form-control text-right per-month" value="${per_month}" readonly></td>
            <td>
                <button type="button" class="btn btn-remove btn-sm remove-commission-row">
                    <i class="fas fa-times"></i>
                </button>
            </td>
        </tr>`;
            $('#commissionRows').append(row);
            commissionRowIndex++;
            updateSlNumbers('#commissionRows');
            calculateCommissionRow($('#commissionRows tr').last());
        }

        function addInsuranceRow(insurance_amount = 0, months = 1, per_month = 0) {
            insurance_amount = parseFloat(insurance_amount) || 0;
            months = parseInt(months) || 1;
            per_month = parseFloat(per_month) || 0;

            let row = `
        <tr data-index="${insuranceRowIndex}">
            <td class="sl-number">${insuranceRowIndex + 1}</td>
            <td><input type="number" class="form-control text-right insurance-amount" name="insurances[${insuranceRowIndex}][insurance_amount]" value="${insurance_amount}" step="0.01" required></td>
            <td><input type="number" class="form-control text-right months" name="insurances[${insuranceRowIndex}][months]" value="${months}" min="1" max="12" required></td>
            <td><input type="number" class="form-control text-right per-month" value="${per_month}" readonly></td>
            <td>
                <button type="button" class="btn btn-remove btn-sm remove-insurance-row">
                    <i class="fas fa-times"></i>
                </button>
            </td>
        </tr>`;
            $('#insuranceRows').append(row);
            insuranceRowIndex++;
            updateSlNumbers('#insuranceRows');
            calculateInsuranceRow($('#insuranceRows tr').last());
        }

        function addPartnerRow(name = '', percentage = 0, per_partner = 0) {
            name = name ?? '';
            percentage = parseFloat(percentage) || 0;
            per_partner = parseFloat(per_partner) || 0;
            let profitPerMonth = parseFloat($('#profitPerMonth').val()) || 0;

            let row = `
        <tr data-index="${partnerRowIndex}">
            <td class="sl-number">${partnerRowIndex + 1}</td>
            <td><input type="text" class="form-control" name="partners[${partnerRowIndex}][name]" value="${name}" required></td>
            <td><input type="number" class="form-control text-right profit-per-month" value="${profitPerMonth.toFixed(2)}" readonly></td>
            <td><input type="number" class="form-control text-right percentage" name="partners[${partnerRowIndex}][percentage]" value="${percentage}" step="0.01" required></td>
            <td><input type="number" class="form-control text-right per_partner" name="partners[${partnerRowIndex}][per_partner]" value="${per_partner}" readonly></td>
            <td>
                <button type="button" class="btn btn-remove btn-sm remove-partner-row">
                    <i class="fas fa-times"></i>
                </button>
            </td>
        </tr>`;
            $('#partnerRows').append(row);
            partnerRowIndex++;
            updateSlNumbers('#partnerRows');
        }

        function calculateRow(row) {
            // Value rows calculation: amount × unit × quantity = gross
            if (row.find('.gross').length) {
                let amount = parseFloat(row.find('.amount').val()) || 0;
                let unit = parseFloat(row.find('.unit').val()) || 0;
                let quantity = parseFloat(row.find('.quantity').val()) || 0;
                let discount = parseFloat(row.find('.discount').val()) || 0;
                let gross = amount * unit * quantity;
                let net = gross - discount;

                row.find('.gross').val(gross.toFixed(2));
                row.find('.net').val(net.toFixed(2));
            }

            // Expense rows calculation: amount × unit × quantity = total
            if (row.find('.total').length) {
                let amount = parseFloat(row.find('.amount').val()) || 0;
                let unit = parseFloat(row.find('.unit').val()) || 0;
                let quantity = parseFloat(row.find('.quantity').val()) || 0;
                let total = amount * unit * quantity;

                row.find('.total').val(total.toFixed(2));
            }

            // Commission rows calculation
            if (row.find('.commission-amount').length) {
                calculateCommissionRow(row);
            }

            // Insurance rows calculation
            if (row.find('.insurance-amount').length) {
                calculateInsuranceRow(row);
            }

            // Partner rows calculation
            if (row.find('.per_partner').length) {
                let percentage = parseFloat(row.find('.percentage').val()) || 0;
                let profitPerMonth = parseFloat($('#profitPerMonth').val()) || 0;
                let projectMonths = parseFloat($('#projectMonths').val()) || 1;
                let perPartnerPerMonth = profitPerMonth * (percentage / 100);
                let totalPerPartner = perPartnerPerMonth * projectMonths;

                row.find('.per_partner').val(totalPerPartner.toFixed(2));
                row.find('.profit-per-month').val(profitPerMonth.toFixed(2));
            }
        }

        function calculateCommissionRow(row) {
            let commissionAmount = parseFloat(row.find('.commission-amount').val()) || 0;
            let commissionMonths = parseFloat(row.find('.months').val()) || 1;
            let perMonth = commissionAmount / commissionMonths;

            row.find('.per-month').val(perMonth.toFixed(2));
        }

        function calculateInsuranceRow(row) {
            let insuranceAmount = parseFloat(row.find('.insurance-amount').val()) || 0;
            let insuranceMonths = parseFloat(row.find('.months').val()) || 1;
            let perMonth = insuranceAmount / insuranceMonths;

            row.find('.per-month').val(perMonth.toFixed(2));
        }

        function calculateTotals() {
            let totalGross = 0,
                totalDiscount = 0,
                totalNet = 0;
            let totalExpenseAmount = 0;
            let totalCommissionAmount = 0;
            let totalInsuranceAmount = 0;

            // Calculate value totals
            $('#valueRows tr').each(function() {
                let gross = parseFloat($(this).find('.gross').val()) || 0;
                let net = parseFloat($(this).find('.net').val()) || 0;

                totalGross += gross;
                totalNet += net;
                totalDiscount += gross - net;
            });

            // Calculate expense totals
            $('#expenseRows tr').each(function() {
                totalExpenseAmount += parseFloat($(this).find('.total').val()) || 0;
            });

            // Calculate commission totals
            $('#commissionRows tr').each(function() {
                totalCommissionAmount += parseFloat($(this).find('.commission-amount').val()) || 0;
                calculateCommissionRow($(this));
            });

            // Calculate insurance totals
            $('#insuranceRows tr').each(function() {
                totalInsuranceAmount += parseFloat($(this).find('.insurance-amount').val()) || 0;
                calculateInsuranceRow($(this));
            });

            // Update value percentages
            $('#valueRows tr').each(function() {
                let gross = parseFloat($(this).find('.gross').val()) || 0;
                let percentage = totalGross > 0 ? (gross / totalGross) * 100 : 0;
                $(this).find('.percentage').val(percentage.toFixed(2));
            });

            // Update expense percentages
            $('#expenseRows tr').each(function() {
                let total = parseFloat($(this).find('.total').val()) || 0;
                let percentage = totalExpenseAmount > 0 ? (total / totalExpenseAmount) * 100 : 0;
                $(this).find('.percentage').val(percentage.toFixed(2));
            });

            // Get project months (PROJECT SUMMARY months)
            let projectMonths = parseFloat($('#projectMonths').val()) || 1;

            // COMBINED LOGIC:
            // 1. Your existing logic: month * expense = final expense
            // 2. New requirement: Minus commission and insurance from expense

            // Calculate final expenses: (Expenses - Commissions - Insurance) * Months
            let finalProjectExpense = (totalExpenseAmount + totalCommissionAmount + totalInsuranceAmount) * projectMonths;

            // Calculate profit amount (Total Net - Final Expense)
            let profitAmount = totalNet - finalProjectExpense;

            // Calculate profit per month (based on PROJECT SUMMARY months - for partners only)
            let profitPerMonth = profitAmount / projectMonths;

            // Update all displays
            $('#totalGross').text(totalGross.toFixed(2));
            $('#totalDiscount').text(totalDiscount.toFixed(2));
            $('#totalNet').text(totalNet.toFixed(2));
            $('#totalExpenses').text(totalExpenseAmount.toFixed(2));
            $('#totalCommission').text(totalCommissionAmount.toFixed(2));
            $('#totalInsurance').text(totalInsuranceAmount.toFixed(2));

            $('#finalProjectValue').val(totalNet.toFixed(2));
            $('#totalProjectExpenses').val(totalExpenseAmount.toFixed(2));
            $('#finalProjectExpense').val(finalProjectExpense.toFixed(2));
            $('#profitAmount').val(profitAmount.toFixed(2));
            $('#profitPerMonth').val(profitPerMonth.toFixed(2));

            // Add hidden input for project months to be submitted with form
            if (!$('#projectMonthsInput').length) {
                $('<input>').attr({
                    type: 'hidden',
                    id: 'projectMonthsInput',
                    name: 'project_months',
                    value: projectMonths
                }).appendTo('form');
            } else {
                $('#projectMonthsInput').val(projectMonths);
            }

            // Update partner calculations with monthly distribution (based on PROJECT SUMMARY months)
            let totalPartnerPercentage = 0;
            let totalPartnerAmount = 0;

            $('#partnerRows tr').each(function() {
                let percentage = parseFloat($(this).find('.percentage').val()) || 0;
                let perPartnerPerMonth = profitPerMonth * (percentage / 100);
                let totalPerPartner = perPartnerPerMonth * projectMonths;

                $(this).find('.per_partner').val(totalPerPartner.toFixed(2));
                $(this).find('.profit-per-month').val(profitPerMonth.toFixed(2));

                totalPartnerPercentage += percentage;
                totalPartnerAmount += totalPerPartner;
            });

            let remainingPercentage = 100 - totalPartnerPercentage;
            let remainingAmount = profitAmount - totalPartnerAmount;

            $('#totalPartnerPercentage').text(totalPartnerPercentage.toFixed(2) + '%');
            $('#totalPartnerAmount').text(totalPartnerAmount.toFixed(2));
            $('#remainingPercentage').text(remainingPercentage.toFixed(2) + '%');
            $('#remainingAmount').text(remainingAmount.toFixed(2));
        }

        function updateSlNumbers(tableSelector) {
            $(tableSelector + ' tr').each(function(index) {
                $(this).find('.sl-number').text(index + 1);
            });
        }

        // =========================
        // DOCUMENT READY
        // =========================
        $(document).ready(function() {
            // Add new rows
            $('#addValueRow').click(() => addValueRow());
            $('#addExpenseRow').click(() => addExpenseRow());
            $('#addCommissionRow').click(() => addCommissionRow());
            $('#addInsuranceRow').click(() => addInsuranceRow());
            $('#addPartnerRow').click(() => addPartnerRow());

            // Input change events
            $(document).on('input',
                '.amount, .unit, .quantity, .discount, #projectMonths, .percentage, .commission-amount, .insurance-amount, .months',
                function() {
                    calculateRow($(this).closest('tr'));
                    calculateTotals();
                });

            // Remove row events
            $(document).on('click', '.remove-value-row', function() {
                $(this).closest('tr').remove();
                updateSlNumbers('#valueRows');
                calculateTotals();
            });

            $(document).on('click', '.remove-expense-row', function() {
                $(this).closest('tr').remove();
                updateSlNumbers('#expenseRows');
                calculateTotals();
            });

            $(document).on('click', '.remove-commission-row', function() {
                $(this).closest('tr').remove();
                updateSlNumbers('#commissionRows');
                calculateTotals();
            });

            $(document).on('click', '.remove-insurance-row', function() {
                $(this).closest('tr').remove();
                updateSlNumbers('#insuranceRows');
                calculateTotals();
            });

            $(document).on('click', '.remove-partner-row', function() {
                $(this).closest('tr').remove();
                updateSlNumbers('#partnerRows');
                calculateTotals();
            });

            // Pre-populate values
            @foreach ($projectCalculation->values as $value)
                addValueRow(
                    '{{ addslashes($value->category) }}',
                    '{{ $value->amount }}',
                    '{{ $value->unit }}',
                    '{{ $value->quantity }}',
                    '{{ $value->discount }}',
                    '{{ $value->gross_amount }}',
                    '{{ $value->net_amount }}',
                    '{{ $value->percentage }}'
                );
            @endforeach

            // Pre-populate expenses
            @foreach ($projectCalculation->expenses as $expense)
                addExpenseRow(
                    '{{ addslashes($expense->name) }}',
                    '{{ $expense->amount }}',
                    '{{ $expense->unit }}',
                    '{{ $expense->quantity }}',
                    '{{ $expense->total_amount }}',
                    '{{ $expense->percentage }}'
                );
            @endforeach

            // Pre-populate commissions
            @foreach ($projectCalculation->commissions as $commission)
                addCommissionRow(
                    '{{ $commission->commission_amount }}',
                    '{{ $commission->months }}',
                    '{{ $commission->per_month }}'
                );
            @endforeach

            // Pre-populate insurances
            @foreach ($projectCalculation->insurances as $insurance)
                addInsuranceRow(
                    '{{ $insurance->insurance_amount }}',
                    '{{ $insurance->months }}',
                    '{{ $insurance->per_month }}'
                );
            @endforeach

            // Pre-populate partners
            @foreach ($projectCalculation->partners as $partner)
                addPartnerRow(
                    '{{ addslashes($partner->name) }}',
                    '{{ $partner->percentage }}',
                    '{{ $partner->per_partner ?? 0 }}'
                );
            @endforeach

            // Set project months (use stored value or default to 1)
            $('#projectMonths').val(
                '{{ old('project_months', $projectCalculation->partners->first()->project_months ?? 1) }}');

            // Add hidden profitPerMonth field
            if (!$('#profitPerMonth').length) {
                $('<input>').attr({
                    type: 'hidden',
                    id: 'profitPerMonth',
                    value: '0.00'
                }).appendTo('form');
            }

            // Initial calculation
            calculateTotals();
        });
    </script>
    <style>
        /* Initial white button with red cross */
        .btn-remove {
            background-color: white;
            color: red;
            border: 1px solid red;
            transition: all 0.2s ease;
        }

        /* Full red on hover */
        .btn-remove:hover {
            background-color: red;
            color: white;
            border: 1px solid red;
        }

        /* Remove number input arrows (Chrome, Safari, Edge, Opera) */
        input[type=number]::-webkit-outer-spin-button,
        input[type=number]::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        /* Remove number input arrows (Firefox) */
        input[type=number] {
            -moz-appearance: textfield;
        }
    </style>
@endsection
{{-- @section('scripts')
    <script>
        // Initial row indices
        let valueRowIndex = {{ $projectCalculation->values->count() }};
        let expenseRowIndex = {{ $projectCalculation->expenses->count() }};
        let partnerRowIndex = {{ $projectCalculation->partners->count() }};

        // =========================
        // FUNCTIONS
        // =========================
        function addValueRow(category = '', amount = 0, unit = 1, quantity = 1, discount = 0, gross = 0, net = 0,
            percentage = 0) {
            category = category ?? '';
            amount = parseFloat(amount) || 0;
            unit = parseFloat(unit) || 1;
            quantity = parseInt(quantity) || 1;
            discount = parseFloat(discount) || 0;
            gross = parseFloat(gross) || 0;
            net = parseFloat(net) || 0;
            percentage = parseFloat(percentage) || 0;

            let row = `
        <tr data-index="${valueRowIndex}">
            <td class="sl-number">${valueRowIndex + 1}</td>
            <td><input type="text" class="form-control" name="values[${valueRowIndex}][category]" value="${category}" required></td>
            <td><input type="number" class="form-control text-right amount" name="values[${valueRowIndex}][amount]" value="${amount}" step="0.01" required></td>
            <td><input type="number" class="form-control text-right unit" name="values[${valueRowIndex}][unit]" value="${unit}" step="0.01" required></td>
            <td><input type="number" class="form-control text-right quantity" name="values[${valueRowIndex}][quantity]" value="${quantity}" min="1" step="1" required></td>
            <td><input type="number" class="form-control text-right gross" value="${gross}" readonly></td>
            <td><input type="number" class="form-control text-right discount" name="values[${valueRowIndex}][discount]" value="${discount}" step="0.01"></td>
            <td><input type="number" class="form-control text-right net" value="${net}" readonly></td>
            <td><input type="number" class="form-control text-right percentage" name="values[${valueRowIndex}][percentage]" value="${percentage}" readonly></td>
            <td>
                <button type="button" class="btn btn-remove btn-sm remove-value-row">
                    <i class="fas fa-times"></i>
                </button>
            </td>
        </tr>`;
            $('#valueRows').append(row);
            valueRowIndex++;
            updateSlNumbers('#valueRows');
            calculateRow($('#valueRows tr').last());
            calculateTotals();
        }

        function addExpenseRow(name = '', amount = 0, unit = 1, quantity = 1, total = 0, percentage = 0) {
            name = name ?? '';
            amount = parseFloat(amount) || 0;
            unit = parseFloat(unit) || 1;
            quantity = parseInt(quantity) || 1;
            total = parseFloat(total) || 0;
            percentage = parseFloat(percentage) || 0;

            let row = `
        <tr data-index="${expenseRowIndex}">
            <td class="sl-number">${expenseRowIndex + 1}</td>
            <td><input type="text" class="form-control" name="expenses[${expenseRowIndex}][name]" value="${name}" required></td>
            <td><input type="number" class="form-control text-right amount" name="expenses[${expenseRowIndex}][amount]" value="${amount}" step="0.01" required></td>
            <td><input type="number" class="form-control text-right unit" name="expenses[${expenseRowIndex}][unit]" value="${unit}" step="0.01" required></td>
            <td><input type="number" class="form-control text-right quantity" name="expenses[${expenseRowIndex}][quantity]" value="${quantity}" min="1" step="1" required></td>
            <td><input type="number" class="form-control text-right total" value="${total}" readonly></td>
            <td><input type="number" class="form-control text-right percentage" name="expenses[${expenseRowIndex}][percentage]" value="${percentage}" readonly></td>
            <td>
                <button type="button" class="btn btn-remove btn-sm remove-expense-row">
                    <i class="fas fa-times"></i>
                </button>
            </td>
        </tr>`;
            $('#expenseRows').append(row);
            expenseRowIndex++;
            updateSlNumbers('#expenseRows');
            calculateRow($('#expenseRows tr').last());
            calculateTotals();
        }

        function addPartnerRow(name = '', percentage = 0, per_partner = 0) {
            name = name ?? '';
            percentage = parseFloat(percentage) || 0;
            per_partner = parseFloat(per_partner) || 0;
            let profitAmount = parseFloat($('#profitAmount').val()) || 0;

            let row = `
        <tr data-index="${partnerRowIndex}">
            <td class="sl-number">${partnerRowIndex + 1}</td>
            <td><input type="text" class="form-control" name="partners[${partnerRowIndex}][name]" value="${name}" required></td>
            <td><input type="number" class="form-control text-right profit-amount" value="${profitAmount.toFixed(2)}" readonly></td>
            <td><input type="number" class="form-control text-right percentage" name="partners[${partnerRowIndex}][percentage]" value="${percentage}" step="0.01" required></td>
            <td><input type="number" class="form-control text-right per_partner" name="partners[${partnerRowIndex}][per_partner]" value="${per_partner}" readonly></td>
            <td>
                <button type="button" class="btn btn-remove btn-sm remove-partner-row">
                    <i class="fas fa-times"></i>
                </button>
            </td>
        </tr>`;
            $('#partnerRows').append(row);
            partnerRowIndex++;
            updateSlNumbers('#partnerRows');
            calculatePartnerAmounts();
        }

        function calculateRow(row) {
            // Value rows calculation: amount × unit × quantity = gross
            if (row.find('.gross').length) {
                let amount = parseFloat(row.find('.amount').val()) || 0;
                let unit = parseFloat(row.find('.unit').val()) || 0;
                let quantity = parseFloat(row.find('.quantity').val()) || 0;
                let discount = parseFloat(row.find('.discount').val()) || 0;
                let gross = amount * unit * quantity;
                let net = gross - discount;

                row.find('.gross').val(gross.toFixed(2));
                row.find('.net').val(net.toFixed(2));
            }

            // Expense rows calculation: amount × unit × quantity = total
            if (row.find('.total').length) {
                let amount = parseFloat(row.find('.amount').val()) || 0;
                let unit = parseFloat(row.find('.unit').val()) || 0;
                let quantity = parseFloat(row.find('.quantity').val()) || 0;
                let total = amount * unit * quantity;

                row.find('.total').val(total.toFixed(2));
            }

            // Partner rows calculation
            if (row.find('.per_partner').length) {
                let percentage = parseFloat(row.find('.percentage').val()) || 0;
                let profitAmount = parseFloat($('#profitAmount').val()) || 0;
                let perPartner = profitAmount * (percentage / 100);

                row.find('.per_partner').val(perPartner.toFixed(2));
                row.find('.profit-amount').val(profitAmount.toFixed(2));
            }
        }

        function calculateTotals() {
            let totalGross = 0,
                totalDiscount = 0,
                totalNet = 0;
            let totalExpenseAmount = 0;

            // Calculate value totals
            $('#valueRows tr').each(function() {
                let gross = parseFloat($(this).find('.gross').val()) || 0;
                let net = parseFloat($(this).find('.net').val()) || 0;

                totalGross += gross;
                totalNet += net;
                totalDiscount += gross - net;
            });

            // Calculate expense totals for percentage calculation
            $('#expenseRows tr').each(function() {
                totalExpenseAmount += parseFloat($(this).find('.total').val()) || 0;
            });

            // Update value percentages (gross percentage of total gross)
            $('#valueRows tr').each(function() {
                let gross = parseFloat($(this).find('.gross').val()) || 0;
                let percentage = totalGross > 0 ? (gross / totalGross) * 100 : 0;
                $(this).find('.percentage').val(percentage.toFixed(2));
            });

            // Update expense percentages (total percentage of total expenses)
            $('#expenseRows tr').each(function() {
                let total = parseFloat($(this).find('.total').val()) || 0;
                let percentage = totalExpenseAmount > 0 ? (total / totalExpenseAmount) * 100 : 0;
                $(this).find('.percentage').val(percentage.toFixed(2));
            });

            // Get project months from the input field
            let projectMonths = parseFloat($('#projectMonths').val()) || 1;
            let finalProjectExpense = totalExpenseAmount * projectMonths;
            let profitAmount = totalNet - finalProjectExpense;

            // Update all displays
            $('#totalGross').text(totalGross.toFixed(2));
            $('#totalDiscount').text(totalDiscount.toFixed(2));
            $('#totalNet').text(totalNet.toFixed(2));
            $('#totalExpenses').text(totalExpenseAmount.toFixed(2));

            // Update final calculation summary
            $('#finalProjectValue').val(totalNet.toFixed(2));
            $('#totalProjectExpenses').val(totalExpenseAmount.toFixed(2));
            $('#finalProjectExpense').val(finalProjectExpense.toFixed(2));
            $('#profitAmount').val(profitAmount.toFixed(2));

            // Add hidden input for project months to be submitted with form
            if (!$('#projectMonthsInput').length) {
                $('<input>').attr({
                    type: 'hidden',
                    id: 'projectMonthsInput',
                    name: 'project_months',
                    value: projectMonths
                }).appendTo('form');
            } else {
                $('#projectMonthsInput').val(projectMonths);
            }

            // Update partner calculations
            calculatePartnerAmounts();
        }

        function calculatePartnerAmounts() {
            let profitAmount = parseFloat($('#profitAmount').val()) || 0;
            let totalPartnerPercentage = 0;
            let totalPartnerAmount = 0;

            $('#partnerRows tr').each(function() {
                let percentage = parseFloat($(this).find('.percentage').val()) || 0;
                let perPartner = profitAmount * (percentage / 100);

                $(this).find('.per_partner').val(perPartner.toFixed(2));
                $(this).find('.profit-amount').val(profitAmount.toFixed(2));

                totalPartnerPercentage += percentage;
                totalPartnerAmount += perPartner;
            });

            let remainingPercentage = 100 - totalPartnerPercentage;
            let remainingAmount = profitAmount - totalPartnerAmount;

            $('#totalPartnerPercentage').text(totalPartnerPercentage.toFixed(2) + '%');
            $('#totalPartnerAmount').text(totalPartnerAmount.toFixed(2));
            $('#remainingPercentage').text(remainingPercentage.toFixed(2) + '%');
            $('#remainingAmount').text(remainingAmount.toFixed(2));
        }

        function updateSlNumbers(tableSelector) {
            $(tableSelector + ' tr').each(function(index) {
                $(this).find('.sl-number').text(index + 1);
            });
        }

        // =========================
        // DOCUMENT READY
        // =========================
        $(document).ready(function() {
            // Add new rows
            $('#addValueRow').click(() => addValueRow());
            $('#addExpenseRow').click(() => addExpenseRow());
            $('#addPartnerRow').click(() => addPartnerRow());

            // Input change events
            $(document).on('input', '.amount, .unit, .quantity, .discount, .percentage', function() {
                calculateRow($(this).closest('tr'));
                calculateTotals();
            });

            // Project months change event
            $(document).on('input', '#projectMonths', function() {
                calculateTotals();
            });

            // Remove row events
            $(document).on('click', '.remove-value-row', function() {
                $(this).closest('tr').remove();
                updateSlNumbers('#valueRows');
                calculateTotals();
            });

            $(document).on('click', '.remove-expense-row', function() {
                $(this).closest('tr').remove();
                updateSlNumbers('#expenseRows');
                calculateTotals();
            });

            $(document).on('click', '.remove-partner-row', function() {
                $(this).closest('tr').remove();
                updateSlNumbers('#partnerRows');
                calculateTotals();
            });

            // Pre-populate values
            @foreach ($projectCalculation->values as $value)
                addValueRow(
                    '{{ addslashes($value->category) }}',
                    '{{ $value->amount }}',
                    '{{ $value->unit }}',
                    '{{ $value->quantity }}',
                    '{{ $value->discount }}',
                    '{{ $value->gross_amount }}',
                    '{{ $value->net_amount }}',
                    '{{ $value->percentage }}'
                );
            @endforeach

            // Pre-populate expenses
            @foreach ($projectCalculation->expenses as $expense)
                addExpenseRow(
                    '{{ addslashes($expense->name) }}',
                    '{{ $expense->amount }}',
                    '{{ $expense->unit }}',
                    '{{ $expense->quantity }}',
                    '{{ $expense->total_amount }}',
                    '{{ $expense->percentage }}'
                );
            @endforeach

            // Pre-populate partners
            @foreach ($projectCalculation->partners as $partner)
                addPartnerRow(
                    '{{ addslashes($partner->name) }}',
                    '{{ $partner->percentage }}',
                    '{{ $partner->per_partner ?? 0 }}'
                );
            @endforeach

            // Set project months (use stored value or default to 1)
            $('#projectMonths').val('{{ old('project_months', 1) }}');

            // Initial calculation
            calculateTotals();
        });
    </script>
    <style>
        /* Initial white button with red cross */
        .btn-remove {
            background-color: white;
            color: red;
            border: 1px solid red;
            transition: all 0.2s ease;
        }

        /* Full red on hover */
        .btn-remove:hover {
            background-color: red;
            color: white;
            border: 1px solid red;
        }

        /* Remove number input arrows (Chrome, Safari, Edge, Opera) */
        input[type=number]::-webkit-outer-spin-button,
        input[type=number]::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        /* Remove number input arrows (Firefox) */
        input[type=number] {
            -moz-appearance: textfield;
        }
    </style>
@endsection --}}
