'use strict';

$(document).ready(function () {

    // Contract Summary for Contract Type In Client Side

    let contractTypeNames = [];
    let contractsColors = [];
    let contractsBgColors = [];
    let contractCounts = [];
    $.each(contractTypesData, function (key, value) {
        let str = value.name;
        contractTypeNames.push(str.replace('&amp;', '&'));
        contractCounts.push(value.contracts_customer_count);
        contractsColors.push('#e5e5e5');
        contractsBgColors.push('#d9c0c0');
    });
    
    let clientContractChart = document.getElementById('clientContractChartId');
    let ContractChart = new Chart(clientContractChart, {
        type: 'bar',
        data: {
            labels: contractTypeNames,
            datasets: [
                {
                    label: 'Contracts By Type ',
                    data: contractCounts,
                    backgroundColor: contractsColors,
                    borderColor: contractsBgColors,
                    borderWidth: 2,
                }],
        },
        options: {
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
