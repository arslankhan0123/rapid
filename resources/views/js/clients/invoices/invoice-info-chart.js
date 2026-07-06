'use strict';

$(document).ready(function () {

    // Invoice Info Chart

    let allMonthNames = Object.values(allMonths);
    let paidInvoices = Object.values(invoicesData.paid);
    let unpaidInvoices = Object.values(invoicesData.unpaid);

    let invoiceInfoChart = document.getElementById('invoiceInfoChart');
    let invoiceChart = new Chart(invoiceInfoChart, {
        type: 'bar',
        data: {
            labels: allMonthNames,
            datasets: [
                {
                    label: [' Unpaid '],
                    data: unpaidInvoices,
                    backgroundColor: '#feb8bf',
                    borderColor: '#fa5768',
                    borderWidth: 2,
                },
                {
                    label: [' Paid '],
                    data: paidInvoices,
                    backgroundColor: '#daedbe',
                    borderColor: '#bce77e',
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

});
