'use strict';

$(document).ready(function () {

    // Expense Category By Chart 

    let expenseCategoryNames = [];
    let expenseCategoryCount = [];
    let colors = [];
    let BgColors = [];

    $.each(expenseCategories, function (key, value) {
        const randomColor = Math.floor(Math.random() * 16777215).toString(16);
        let str = value.name;
        expenseCategoryNames.push(str.replace('&amp;', '&'));
        expenseCategoryCount.push(value.expenses_count);
        colors.push('#' + randomColor);
        BgColors.push('#' + randomColor);
    });

    let expenseCategory = document.getElementById('expenseCategoryByChart');
    let expenseCategoryChart = new Chart(expenseCategory, {
        type: 'bar',
        data: {
            labels: expenseCategoryNames,
            datasets: [
                {
                    label: [ Lang.get('messages.expense_by_category') ],
                    data: expenseCategoryCount,
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
