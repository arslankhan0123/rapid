@extends('layouts.app')
@section('title')
    {{ __('messages.project_calculations.add_project_calculation') }}
@endsection
@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{ __('messages.project_calculations.add_project_calculation') }}</h1>
            <div class="section-header-breadcrumb">
                <a href="{{ route('project-calculations.index') }}" class="btn btn-primary form-btn">
                    {{ __('messages.common.list') }}
                </a>
            </div>
        </div>
        <div class="section-body">
            <div class="card">
                <div class="card-body">
                    @include('layouts.errors')
                    {!! Form::open(['route' => 'project-calculations.store', 'id' => 'createProjectCalculationForm']) !!}
                    @include('project_calculations.fields')
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </section>
@endsection


@section('scripts')
    <style>
        /* Remove button style */
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
    <script>
        $(document).ready(function() {
            let valueRowIndex = 0;
            let expenseRowIndex = 0;
            let commissionRowIndex = 0;
            let insuranceRowIndex = 0;
            let partnerRowIndex = 0;

            // Update serial numbers
            function updateSerials(tableType) {
                $(`#${tableType} tr`).each(function(index) {
                    $(this).find('td:first').text(index + 1);
                });
            }

            // Add Value Row
            function addValueRow(data = {}) {
                let row = `
                <tr data-index="${valueRowIndex}">
                    <td></td>
                    <td><input type="text" class="form-control" name="values[${valueRowIndex}][category]" value="${data.category || ''}" required></td>
                    <td><input type="number" class="form-control text-right amount" name="values[${valueRowIndex}][amount]" value="${data.amount || 0}" step="0.01" required></td>
                    <td><input type="number" class="form-control text-right unit" name="values[${valueRowIndex}][unit]" value="${data.unit || 1}" step="0.01" required></td>
                    <td><input type="number" class="form-control text-right quantity" name="values[${valueRowIndex}][quantity]" value="${data.quantity || 1}" step="1" required></td>
                    <td><input type="number" class="form-control text-right gross" value="${data.gross || 0}" readonly></td>
                    <td><input type="number" class="form-control text-right discount" name="values[${valueRowIndex}][discount]" value="${data.discount || 0}" step="0.01"></td>
                    <td><input type="number" class="form-control text-right net" value="${data.net || 0}" readonly></td>
                    <td><input type="number" class="form-control text-right percentage" name="values[${valueRowIndex}][percentage]" value="${data.percentage || 0}" readonly></td>
                    <td><button type="button" class="btn btn-danger btn-sm remove-value-row"><i class="fas fa-times"></i></button></td>
                </tr>`;
                $('#valueRows').append(row);
                valueRowIndex++;
                updateSerials('valueRows');
            }

            $('#addValueRow').click(() => addValueRow());

            // Add Expense Row
            function addExpenseRow(data = {}) {
                let row = `
                <tr data-index="${expenseRowIndex}">
                    <td></td>
                    <td><input type="text" class="form-control" name="expenses[${expenseRowIndex}][name]" value="${data.name || ''}" required></td>
                    <td><input type="number" class="form-control text-right amount" name="expenses[${expenseRowIndex}][amount]" value="${data.amount || 0}" step="0.01" required></td>
                    <td><input type="number" class="form-control text-right unit" name="expenses[${expenseRowIndex}][unit]" value="${data.unit || 1}" step="0.01" required></td>
                    <td><input type="number" class="form-control text-right quantity" name="expenses[${expenseRowIndex}][quantity]" value="${data.quantity || 1}" step="1" required></td>
                    <td><input type="number" class="form-control text-right total" value="${data.total || 0}" readonly></td>
                    <td><input type="number" class="form-control text-right percentage" name="expenses[${expenseRowIndex}][percentage]" value="${data.percentage || 0}" readonly></td>
                    <td><button type="button" class="btn btn-danger btn-sm remove-expense-row"><i class="fas fa-times"></i></button></td>
                </tr>`;
                $('#expenseRows').append(row);
                expenseRowIndex++;
                updateSerials('expenseRows');
            }

            $('#addExpenseRow').click(() => addExpenseRow());

            // Add Commission Row
            function addCommissionRow(data = {}) {
                let row = `
                <tr data-index="${commissionRowIndex}">
                    <td></td>
                    <td><input type="number" class="form-control text-right commission-amount" name="commissions[${commissionRowIndex}][commission_amount]" value="${data.commission_amount || 0}" step="0.01" required></td>
                    <td><input type="number" class="form-control text-right months" name="commissions[${commissionRowIndex}][months]" value="${data.months || 1}" min="1" max="12" required></td>
                    <td><input type="number" class="form-control text-right per-month" value="0.00" readonly></td>
                    <td><button type="button" class="btn btn-danger btn-sm remove-commission-row"><i class="fas fa-times"></i></button></td>
                </tr>`;
                $('#commissionRows').append(row);
                commissionRowIndex++;
                updateSerials('commissionRows');
                calculateCommissionRow($('#commissionRows tr').last());
            }

            $('#addCommissionRow').click(() => addCommissionRow());

            // Add Insurance Row
            function addInsuranceRow(data = {}) {
                let row = `
                <tr data-index="${insuranceRowIndex}">
                    <td></td>
                    <td><input type="number" class="form-control text-right insurance-amount" name="insurances[${insuranceRowIndex}][insurance_amount]" value="${data.insurance_amount || 0}" step="0.01" required></td>
                    <td><input type="number" class="form-control text-right months" name="insurances[${insuranceRowIndex}][months]" value="${data.months || 1}" min="1" max="12" required></td>
                    <td><input type="number" class="form-control text-right per-month" value="0.00" readonly></td>
                    <td><button type="button" class="btn btn-danger btn-sm remove-insurance-row"><i class="fas fa-times"></i></button></td>
                </tr>`;
                $('#insuranceRows').append(row);
                insuranceRowIndex++;
                updateSerials('insuranceRows');
                calculateInsuranceRow($('#insuranceRows tr').last());
            }

            $('#addInsuranceRow').click(() => addInsuranceRow());

            // Add Partner Row
            function addPartnerRow(data = {}) {
                let profitPerMonth = parseFloat($('#profitPerMonth').val()) || 0;
                let row = `
                <tr data-index="${partnerRowIndex}">
                    <td></td>
                    <td><input type="text" class="form-control" name="partners[${partnerRowIndex}][name]" value="${data.name || ''}" required></td>
                    <td><input type="number" class="form-control text-right profit-per-month" value="${profitPerMonth.toFixed(2)}" readonly></td>
                    <td><input type="number" class="form-control text-right percentage" name="partners[${partnerRowIndex}][percentage]" value="${data.percentage || 0}" step="0.01" required></td>
                    <td><input type="number" class="form-control text-right per_partner" name="partners[${partnerRowIndex}][per_partner]" value="${data.per_partner || 0}" readonly></td>
                    <td><button type="button" class="btn btn-danger btn-sm remove-partner-row"><i class="fas fa-times"></i></button></td>
                </tr>`;
                $('#partnerRows').append(row);
                partnerRowIndex++;
                updateSerials('partnerRows');
            }

            $('#addPartnerRow').click(() => addPartnerRow());

            // Remove rows
            $(document).on('click', '.remove-value-row', function() {
                $(this).closest('tr').remove();
                calculateTotals();
                updateSerials('valueRows');
            });

            $(document).on('click', '.remove-expense-row', function() {
                $(this).closest('tr').remove();
                calculateTotals();
                updateSerials('expenseRows');
            });

            $(document).on('click', '.remove-commission-row', function() {
                $(this).closest('tr').remove();
                calculateTotals();
                updateSerials('commissionRows');
            });

            $(document).on('click', '.remove-insurance-row', function() {
                $(this).closest('tr').remove();
                calculateTotals();
                updateSerials('insuranceRows');
            });

            $(document).on('click', '.remove-partner-row', function() {
                $(this).closest('tr').remove();
                calculateTotals();
                updateSerials('partnerRows');
            });

            // Calculate rows and totals
            $(document).on('input',
                '.amount, .unit, .quantity, .discount, #projectMonths, .percentage, .commission-amount, .insurance-amount, .months',
                function() {
                    calculateRow($(this).closest('tr'));
                    calculateTotals();
                });

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

                // Get project months (PROJECT SUMMARY months - for partner distribution and final expense calculation)
                let projectMonths = parseFloat($('#projectMonths').val()) || 1;

                // COMBINED LOGIC:
                // 1. Your existing logic: month * expense = final expense
                // 2. New requirement: Minus commission and insurance from expense

                // Calculate final expenses: (Expenses - Commissions - Insurance) * Months
                let finalProjectExpense = (totalExpenseAmount + totalCommissionAmount + totalInsuranceAmount) *
                    projectMonths;

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

            // Initialize with one row each
            addValueRow();
            addExpenseRow();
            addCommissionRow();
            addInsuranceRow();
            addPartnerRow();

            // Initial calculation
            calculateTotals();
        });
    </script>
@endsection
{{-- @section('scripts')
    <style>
        /* Remove button style */
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
    <script>
        $(document).ready(function() {
            let valueRowIndex = 0;
            let expenseRowIndex = 0;
            let partnerRowIndex = 0;

            // Update serial numbers
            function updateSerials(tableType) {
                $(`#${tableType} tr`).each(function(index) {
                    $(this).find('td:first').text(index + 1);
                });
            }

            // Add Value Row
            function addValueRow(data = {}) {
                let row = `
                <tr data-index="${valueRowIndex}">
                    <td></td>
                    <td><input type="text" class="form-control" name="values[${valueRowIndex}][category]" value="${data.category || ''}" required></td>
                    <td><input type="number" class="form-control text-right amount" name="values[${valueRowIndex}][amount]" value="${data.amount || 0}" step="0.01" required></td>
                    <td><input type="number" class="form-control text-right unit" name="values[${valueRowIndex}][unit]" value="${data.unit || 1}" step="0.01" required></td>
                    <td><input type="number" class="form-control text-right quantity" name="values[${valueRowIndex}][quantity]" value="${data.quantity || 1}" step="1" required></td>
                    <td><input type="number" class="form-control text-right gross" value="${data.gross || 0}" readonly></td>
                    <td><input type="number" class="form-control text-right discount" name="values[${valueRowIndex}][discount]" value="${data.discount || 0}" step="0.01"></td>
                    <td><input type="number" class="form-control text-right net" value="${data.net || 0}" readonly></td>
                    <td><input type="number" class="form-control text-right percentage" name="values[${valueRowIndex}][percentage]" value="${data.percentage || 0}" readonly></td>
                    <td><button type="button" class="btn btn-danger btn-sm remove-value-row"><i class="fas fa-times"></i></button></td>
                </tr>`;
                $('#valueRows').append(row);
                valueRowIndex++;
                updateSerials('valueRows');
            }

            $('#addValueRow').click(() => addValueRow());

            // Add Expense Row
            function addExpenseRow(data = {}) {
                let row = `
                <tr data-index="${expenseRowIndex}">
                    <td></td>
                    <td><input type="text" class="form-control" name="expenses[${expenseRowIndex}][name]" value="${data.name || ''}" required></td>
                    <td><input type="number" class="form-control text-right amount" name="expenses[${expenseRowIndex}][amount]" value="${data.amount || 0}" step="0.01" required></td>
                    <td><input type="number" class="form-control text-right unit" name="expenses[${expenseRowIndex}][unit]" value="${data.unit || 1}" step="0.01" required></td>
                    <td><input type="number" class="form-control text-right quantity" name="expenses[${expenseRowIndex}][quantity]" value="${data.quantity || 1}" step="1" required></td>
                    <td><input type="number" class="form-control text-right total" value="${data.total || 0}" readonly></td>
                    <td><input type="number" class="form-control text-right percentage" name="expenses[${expenseRowIndex}][percentage]" value="${data.percentage || 0}" readonly></td>
                    <td><button type="button" class="btn btn-danger btn-sm remove-expense-row"><i class="fas fa-times"></i></button></td>
                </tr>`;
                $('#expenseRows').append(row);
                expenseRowIndex++;
                updateSerials('expenseRows');
            }

            $('#addExpenseRow').click(() => addExpenseRow());

            // Add Partner Row
            // Add Partner Row
            function addPartnerRow(data = {}) {
                let profitAmount = parseFloat($('#profitAmount').val()) || 0;
                let row = `
        <tr data-index="${partnerRowIndex}">
            <td></td>
            <td><input type="text" class="form-control" name="partners[${partnerRowIndex}][name]" value="${data.name || ''}" required></td>
            <td><input type="number" class="form-control text-right profit-amount" value="${profitAmount.toFixed(2)}" readonly></td>
            <td><input type="number" class="form-control text-right percentage" name="partners[${partnerRowIndex}][percentage]" value="${data.percentage || 0}" step="0.01" required></td>
            <td><input type="number" class="form-control text-right per_partner" name="partners[${partnerRowIndex}][per_partner]" value="${data.per_partner || 0}" readonly></td>
            <td><button type="button" class="btn btn-danger btn-sm remove-partner-row"><i class="fas fa-times"></i></button></td>
        </tr>`;
                $('#partnerRows').append(row);
                partnerRowIndex++;
                updateSerials('partnerRows');
            }

            $('#addPartnerRow').click(() => addPartnerRow());

            // Remove rows
            $(document).on('click', '.remove-value-row', function() {
                $(this).closest('tr').remove();
                calculateTotals();
                updateSerials('valueRows');
            });

            $(document).on('click', '.remove-expense-row', function() {
                $(this).closest('tr').remove();
                calculateTotals();
                updateSerials('expenseRows');
            });

            $(document).on('click', '.remove-partner-row', function() {
                $(this).closest('tr').remove();
                calculateTotals();
                updateSerials('partnerRows');
            });

            // Calculate rows and totals
            $(document).on('input', '.amount, .unit, .quantity, .discount, #projectMonths, .percentage',
                function() {
                    calculateRow($(this).closest('tr'));
                    calculateTotals();
                });

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

                    // Update profit amount display for all partner rows
                    $('#partnerRows .profit-amount').each(function() {
                        $(this).val(profitAmount.toFixed(2));
                    });
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

                // Get project months
                let projectMonths = parseFloat($('#projectMonths').val()) || 1;
                let finalProjectExpense = totalExpenseAmount * projectMonths;
                let profitAmount = totalNet - finalProjectExpense;

                // Update all displays
                $('#totalGross').text(totalGross.toFixed(2));
                $('#totalDiscount').text(totalDiscount.toFixed(2));
                $('#totalNet').text(totalNet.toFixed(2));
                $('#totalExpenses').text(totalExpenseAmount.toFixed(2));

                $('#finalProjectValue').val(totalNet.toFixed(2));
                $('#totalProjectExpenses').val(totalExpenseAmount.toFixed(2));
                $('#finalProjectExpense').val(finalProjectExpense.toFixed(2));
                $('#profitAmount').val(profitAmount.toFixed(2));

                // Update partner calculations
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

            // Initialize with one row each
            addValueRow();
            addExpenseRow();
            addPartnerRow();

            // Initial calculation
            calculateTotals();
        });
    </script>
@endsection --}}
