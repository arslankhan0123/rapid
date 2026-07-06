'use strict';

$(document).ready(function () {
    $('#memberStatus').select2();
});

$(document).on('change', '#memberStatus', function () {
    window.livewire.emit('filterStatus', $(this).val());
});

$(document).on('mouseenter', '.livewire-card', function () {
    $(this).find('.action-dropdown').removeClass('d-none');
});

$(document).on('mouseleave', '.livewire-card', function () {
    $(this).find('.action-dropdown').addClass('d-none');
    $(this).parent().trigger('click');
});

window.sendVerificationEmail = function (url) {
    $.ajax({
        url: url,
        type: 'post',
        beforeSend: function () {
            startLoader();
        },
        success: function (result) {
            if (result.success) {
                swal({
                    title: 'Success!',
                    text: result.message,
                    type: 'success',
                    confirmButtonColor: '#6777EF',
                    timer: 2000,
                });
                window.livewire.emit('refresh');
            }
        },
        error: function (error) {
            manageAjaxErrors(error);
        },
        complete: function () {
            stopLoader();
            $('.email-btn').html('<i class="fas fa-sync font-size-12px"></i>');
        },
    });
};

$(document).on('click', '.delete-btn', function (event) {
    let memberId = $(event.currentTarget).data('id');
    let alertMessage = '<div class="alert alert-warning swal__alert">\n' +
        '<strong class="swal__text-warning">' + Lang.get('messages.member.delete_member_confirm') +
        '</strong><div class="swal__text-message">' + Lang.get('messages.member.by_deleting_this_member') +
        '</div></div>';

    deleteItemInputConfirmation(route('members.destroy', memberId), Lang.get('messages.lead.member'),
        alertMessage);
});

$(document).on('click', '.email-btn', function (event) {
    $(this).html('<i class="fas fa-sync font-size-12px fa-spin"></i>');
    let userId = $(event.currentTarget).attr('data-id');
    sendVerificationEmail(route('email-send', userId));
});

// Member activation deactivation change event
$(document).on('change', '.is-administrator', function (event) {
    let memberId = $(event.currentTarget).attr('data-id');
    activeDeActiveAdministrator(memberId);
});

// activate de-activate Member
window.activeDeActiveAdministrator = function (id) {
    $.ajax({
        url: route('members.active.deactive', id),
        method: 'post',
        cache: false,
        beforeSend: function () {
            startLoader();
        },
        success: function (result) {
            if (result.success) {
                window.livewire.emit('refresh');
            }
        },
        error: function (result) {
            displayErrorMessage(result.responseJSON.message);
            setTimeout(location.reload(true), 700);
        },
        complete: function () {
            stopLoader();
        },
    });
};

function deleteItemAjax (url, header, callFunction = null) {
    $.ajax({
        url: url,
        type: 'DELETE',
        dataType: 'json',
        success: function (obj) {
            if (obj.success) {
                window.livewire.emit('refresh');
            }
            swal({
                title: Lang.get('messages.common.deleted'),
                text: header + Lang.get('messages.common.has_been_delete'),
                type: 'success',
                confirmButtonText: Lang.get('messages.common.ok'),
                confirmButtonColor: '#6777EF',
                timer: 2000,
            });
            if (callFunction) {
                eval(callFunction);
            }
        },
        error: function (data) {
            swal({
                title: '',
                text: data.responseJSON.message,
                type: 'error',
                confirmButtonColor: '#6777ef',
                timer: 5000,
            });
        },
    });
}

window.deleteItemInputConfirmation = function (
    url, header, alertMessage, callFunction = null) {
    swal({
            type: 'input',
            inputPlaceholder: deleteConfirm + ' "' + deleteWord + '" ' +
                toTypeDelete + ' ' + header + '.',
            title: deleteHeading + ' !',
            text: alertMessage,
            html: true,
            showCancelButton: true,
            closeOnConfirm: false,
            showLoaderOnConfirm: true,
            confirmButtonColor: '#6777ef',
            cancelButtonColor: '#d33',
            cancelButtonText: noMessages,
            confirmButtonText: yesMessages,
            imageUrl: baseUrl + 'img/warning.png',
        },
        function (inputVal) {
            if (inputVal === false) {
                return false;
            }
            if (inputVal == '' || inputVal.toLowerCase() != 'delete') {
                swal.showInputError(
                    'Please type "delete" to delete this ' + header + '.');
                $('.sa-input-error').css('top', '23px!important');
                $(document).find('.sweet-alert.show-input :input').val('');
                return false;
            }
            if (inputVal.toLowerCase() === 'delete') {
                deleteItemAjax(url, header, callFunction = null);
            }
        });
};

$(document).on('click', '.email-verify-btn', function (event) {
    let userId = $(event.currentTarget).attr('data-id');
    emailVerified(route('email-verify', userId));
});

window.emailVerified = function (url) {
    $.ajax({
        url: url,
        method: 'post',
        beforeSend: function () {
            startLoader();
        },
        success: function (result) {
            if (result.success) {
                location.reload(true);
            }
        },
        error: function (error) {
            manageAjaxErrors(error);
        },
        complete: function () {
            stopLoader();
            $('.email-verified-btn').
                html('<i class="fas fa-mail-bulk font-size-12px"></i>');
        },
    });
};
