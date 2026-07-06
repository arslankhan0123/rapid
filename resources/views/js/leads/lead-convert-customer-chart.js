'use strict';

$(document).ready(function () {

    // Lead Convert Customer Chart

    let colors = [];
    let BgColors = [];

    $.each(leads, function () {
        const randomColor = Math.floor(Math.random() * 16777215).toString(16);
        colors.push('#' + randomColor);
        BgColors.push('#' + randomColor);
    });

    let leadConvertCustomer = document.getElementById('leadConvertChart');
    let leadConvertCustomerChart = new Chart(leadConvertCustomer, {
        type: 'bar',
        data: {
            labels: currentMonthDates,
            datasets: [
                {
                    label: [ Lang.get('messages.lead_convert_to_customer') ],
                    data: leads,
                    backgroundColor: colors,
                    borderColor: BgColors,
                    borderWidth: 2,
                }],
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
            legend: {
                display: false,
            },
        },

    });

});
