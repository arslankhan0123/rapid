'use strict';

$(document).ready(function () {

    $('#monthId').select2({
        width: '130px',
    });

    window.loadContractList = function (data, isDestroy = false) {
        if (isDestroy) {
            $('#contractExpiredTable').DataTable().clear();
            $('#contractExpiredTable').DataTable().destroy();
        }

        $.each(data, function (key, value) {
            let data = [
                {
                    'subject': value.subject,
                    'customer': value.customer.company_name,
                    'startDate': moment(value.start_date).
                        locale(currentLocale).
                        format('Do MMM, Y'),
                    'endDate': moment(value.end_date).
                        locale(currentLocale).
                        format('Do MMM, Y'),
                }];

            const expiredContractsHtml = prepareTemplateRender(
                '#expiredContractsTemplate', data);
            $('.expiring-contracts').append(expiredContractsHtml);
        });

        if (isDestroy) {
            $('#contractExpiredTable').DataTable({
                oLanguage: {
                    'sEmptyTable': Lang.get('messages.common.no_data_available_in_table'),
                    'sInfo': Lang.get('messages.common.data_base_entries'),
                    sLengthMenu: Lang.get('messages.common.menu_entry'),
                    sInfoEmpty: Lang.get('messages.common.no_entry'),
                    sInfoFiltered: Lang.get('messages.common.filter_by'),
                    sZeroRecords: Lang.get('messages.common.no_matching'),
                },
            });
        }
    };

    loadContractList(expiringContractLists);

    $('#contractExpiredTable').DataTable({
        oLanguage: {
            'sEmptyTable': Lang.get(
                'messages.common.no_data_available_in_table'),
            'sInfo': Lang.get('messages.common.data_base_entries'),
            sLengthMenu: Lang.get('messages.common.menu_entry'),
            sInfoEmpty: Lang.get('messages.common.no_entry'),
            sInfoFiltered: Lang.get('messages.common.filter_by'),
            sZeroRecords: Lang.get('messages.common.no_matching'),
        },
    })

    // Lead Overview Chart JS 

    let leadStatusName = [];
    let leadStatusColor = [];
    let leadStatusCount = [];
    $.each(leadData, function (key, value) {
        let str = value.name;
        leadStatusName.push(str.replace('&amp;', '&'));
        leadStatusColor.push(value.color);
        leadStatusCount.push(value.leads_count);
    });

    let leadChartId = document.getElementById('leadChartId');
    let leadChart = new Chart(leadChartId, {
        type: 'doughnut',
        data: {
            labels: leadStatusName,
            datasets: [
                {
                    data: leadStatusCount,
                    backgroundColor: leadStatusColor,
                    hoverOffset: 4,
                }],
        },
        options: {
            legend: {
                display: false,
            },
        },
    });

    // Project Status Chart JS 

    let projectStatusCount = [];
    $.each(projectStatusCounts, function (key, value) {
        projectStatusCount.push(value);
    });

    projectStatusCount.pop();

    let projectChartId = document.getElementById('projectChartId');
    let projectChart = new Chart(projectChartId, {
        type: 'doughnut',
        data: {
            labels: projectStatus,
            datasets: [
                {
                    data: projectStatusCount,
                    backgroundColor: [
                        '#fc544b',
                        '#6777ef',
                        '#ffa426',
                        '#3abaf4',
                        '#47c363',
                    ],
                    hoverOffset: 4,
                }],
        },
        options: {
            legend: {
                display: false,
            },
        },
    });

    // Tickets Status Chart JS
    let ticketStatusName = [];
    let ticketStatusColor = [];
    let ticketStatusCount = [];

    $.each(ticketStatusData, function (key, value) {
        let str = value.name;
        ticketStatusName.push(str.replace('&amp;', '&'));
        ticketStatusColor.push(value.pick_color);
        ticketStatusCount.push(value.tickets_count);
    });

    let ticketChartId = document.getElementById('ticketChartId');
    let ticketChart = new Chart(ticketChartId, {
        type: 'doughnut',
        data: {
            labels: ticketStatusName,
            datasets: [
                {
                    data: ticketStatusCount,
                    backgroundColor: ticketStatusColor,
                }],
        },
        options: {
            legend: {
                display: false,
            },
        },
    });
    
    
    // Weekly Payment Chart JS
    
    let weekNames = Object.keys(currentWeekInvoices);
    let currentWeekPayment = Object.values(currentWeekInvoices);
    let lastWeekPayment = Object.values(lastWeekInvoices);

    let weeklyPaymentChart = document.getElementById('weeklyPaymentChart');
    let paymentChart = new Chart(weeklyPaymentChart, {
        type: 'bar',
        data: {
            labels: weekNames,
            datasets: [
                {
                    label: [' This Week Payments '],
                    data: currentWeekPayment,
                    backgroundColor: '#d3ebd3',
                    borderColor: '#91cb41',
                    borderWidth: 2,
                },
                {
                    label: [' Last Week Payments '],
                    data: lastWeekPayment,
                    backgroundColor: '#e29ed4',
                    borderColor: '#d36dbe',
                    borderWidth: 2,
                },
            ],
        },
        options: {
            responsive: true,
            scales: {
                yAxes: [
                    {
                        ticks: {
                            min: 0,
                        },
                    }],
            },
        },
    });

    // Income Vs Expenses Chart Js

    let monthNames = Object.keys(incomeAndExpenseData.income);
    let incomeMonthly = Object.values(incomeAndExpenseData.income);
    let expenseMonthly = Object.values(incomeAndExpenseData.expenses);

    let incomeVsExpense = document.getElementById('incomeVsExpenseChart');

    let incomeVsExpenseChart = new Chart(incomeVsExpense, {
        type: 'bar',
        data: {
            labels: monthNames,
            datasets: [
                {
                    label: [ Lang.get('messages.incomes') ],
                    data: incomeMonthly,
                    backgroundColor: '#d3ebd3',
                    borderColor: '#91cb41',
                    borderWidth: 2,
                },
                {
                    label: [ Lang.get('messages.expenses') ],
                    data: expenseMonthly,
                    backgroundColor: '#feabb3',
                    borderColor: '#fd6c7b',
                    borderWidth: 2,
                },
            ],
        },
        options: {
            responsive: true,
            scales: {
                yAxes: [
                    {
                        ticks: {
                            min: 0,
                        },
                    }],
            },
        },
    });

    // Contract expired filter as per month
    $(document).on('change', '#monthId', function () {
        let month = $(this).val();

        $.ajax({
            url: route('contract.month.filter'),
            type: 'POST',
            data: { month: month },
            success: function (result) {
                if (result.success) {
                    if (result.data.length > 0) {
                        loadContractList(result.data, true);
                    } else {
                        $('#contractExpiredTable').DataTable().clear().draw();
                    }
                }
            },
        });
    });

});

