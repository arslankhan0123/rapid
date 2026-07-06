'use strict';

$(document).ready(function () {
    $('#expenseCategory').select2({
        width: '225px',
    });
    $('#expenseCategoryCustomer').select2({
        width: '225px',
    });
});

$(document).on('mouseenter', '.expense-card', function () {
    $(this).find('.expense-action-btn').removeClass('d-none');
});

$(document).on('mouseleave', '.expense-card', function () {
    $(this).find('.expense-action-btn').addClass('d-none');
    $(this).parent().trigger('click');
});

$(document).on('change', '#expenseCategory', function () {
    window.livewire.emit('filterCategory', $(this).val());
});

$(document).on('change', '#expenseCategoryCustomer', function () {
    window.livewire.emit('filterCategory', $(this).val());
});

$(document).on('click', '.delete-btn', function () {
    let expenseId = $(this).attr('data-id');
    deleteItemLiveWire(route('expenses.destroy', expenseId), Lang.get('messages.common.expense'));
});

document.addEventListener('DOMContentLoaded', function (event) {
    Livewire.hook('message.received', (message, component) => {
        setTimeout(function () {
            $(document).find('#expenseCategoryCustomer').select2('destroy');
            $(document).find('#expenseCategoryCustomer').select2();
            $(document).find('.select2').removeClass('opacity-0');
            $('#expenseCategoryCustomer').
                select2({
                    width: '225px',
                });
        }, 200);
    });
});
