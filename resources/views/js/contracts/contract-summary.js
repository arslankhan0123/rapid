'use strict';

$(document).ready(function () {

    // Contract Summary For Contract Type (Bar Chart)
    let contractTypeName = [];
    let contractTypeCount = [];
    let contractsColors = [];
    let contractsBgColors = [];
    $.each(contractTypeData, function (key, value) {
        let str = value.name;
        contractTypeName.push(str.replace('&amp;', '&'));
        contractTypeCount.push(value.contracts_count);
        contractsColors.push('#e5e5e5');
        contractsBgColors.push('#d9c0c0');
    });

    let contractSummaryChart = document.getElementById('contractBarChart');
    let contractChart = new Chart(contractSummaryChart, {
        type: 'bar',
        data: {
            labels: contractTypeName,
            datasets: [
                {
                    label: [ Lang.get('messages.contract_type.contract_by_type') ],
                    data: contractTypeCount,
                    backgroundColor: contractsColors,
                    borderColor: contractsBgColors,
                    borderWidth: 2,
                }]
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
        }
    });

    // Contract Summary For Value By Type (Line Chart)
    let contractValues = [];
    let contractTypeNames = [];

    $.each(contractValueData, function (key, value) {
        contractTypeNames.push(value.name);
        contractValues.push(value.total_contract_value ? value.total_contract_value : 0);
    });

    let contractSummaryValueChart = document.getElementById(
        'contractLineChart');
    let contractValueChart = new Chart(contractSummaryValueChart, {
        type: 'line',
        data: {
            labels: contractTypeName,
            datasets: [
                {
                    label: [ Lang.get('messages.contract_type.contract_value_by_type') ],
                    data: contractValues,
                    backgroundColor: ['#d2ead2'],
                    borderColor: ['#a2d46a'],
                    borderWidth: 2,
                }]
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
        }
    });
    
});
