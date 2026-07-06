'use strict';

$('.main-sidebar').on('scroll', function () {
    sessionStorage.scrollTop = $(this).scrollTop()
})

$(document).ready(function () {
    if (sessionStorage.scrollTop != 'undefined') {
        $('.main-sidebar').scrollTop(sessionStorage.scrollTop)
    }
})

$(document).on('select2:open', () => {
    let allFound = document.querySelectorAll(
        '.select2-container--open .select2-search__field')
    allFound[allFound.length - 1].focus()
})

let jsrender = require('jsrender')
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
    },
})

window.prepareTemplateRender = function (templateSelector, data) {
    let template = jsrender.templates(templateSelector)
    return template.render(data);
};

$(document).ready(function () {
    setTimeout(function () {
        $('.main-content input:text:not([readonly="readonly"])').
            first().
            focus();
    }, 500);
});

$(function () {
    $('.modal').on('shown.bs.modal', function () {
        $(this).
            find('input:text:not([data-name="notified-date"])').
            first().
            focus()
        $(this).find('#pfCurrentPassword').first().focus()
    });
});

window.resetModalForm = function (formId, validationBox) {
    $(formId)[0].reset();
    $('select.select2Selector').each(function (index, element) {
        let drpSelector = '#' + $(this).attr('id');
        $(drpSelector).val('');
        $(drpSelector).trigger('change');
    });
    $(validationBox).hide();
};

window.printErrorMessage = function (selector, errorResult) {
    $(selector).show().html('');
    $(selector).text(errorResult.responseJSON.message);
};

window.manageAjaxErrors = function (data) {
    let errorDivId = arguments.length > 1 && arguments[1] !== undefined
        ? arguments[1]
        : 'editValidationErrorsBox';
    if (data.status == 404) {
        iziToast.error({
            title: 'Error!',
            message: data.responseJSON.message,
            position: 'topRight',
        });
    } else {
        printErrorMessage('#' + errorDivId, data);
    }
};

window.displaySuccessMessage = function (message) {
    iziToast.success({
        title: Lang.get('messages.success'),
        message: message,
        position: 'topRight',
    });
};

window.displayErrorMessage = function (message) {
    iziToast.error({
        title: Lang.get('messages.error'),
        message: message,
        position: 'topRight',
    });
};

window.processingBtn = function (selecter, btnId, state = null) {
    let loadingButton = $(selecter).find(btnId);
    if (state === 'loading') {
        loadingButton.button('loading');
    } else {
        loadingButton.button('reset');
    }
};

window.prepareTemplateRender = function (templateSelector, data) {
    let template = jsrender.templates(templateSelector);
    return template.render(data);
};

window.deleteItem = function (url, tableId, header, callFunction = null) {
    swal({
            title: Lang.get('messages.common.delete') + '!',
            text: Lang.get('messages.common.delete_confirm_common') + ' "' +
                header + '"?',
            type: 'warning',
            showCancelButton: true,
            closeOnConfirm: false,
            showLoaderOnConfirm: true,
            confirmButtonColor: '#6777ef',
            cancelButtonColor: '#d33',
            cancelButtonText: Lang.get('messages.common.no'),
            confirmButtonText: Lang.get('messages.common.yes'),
        },
        function () {
            deleteItemAjax(url, tableId, header, callFunction = null);
        });
};

function deleteItemAjax (url, tableId, header, callFunction = null) {
    $.ajax({
        url: url,
        type: 'DELETE',
        dataType: 'json',
        success: function (obj) {
            if (obj.success) {
                $(tableId).DataTable().ajax.reload(null, true);
            }
            swal({
                title: Lang.get('messages.common.deleted'),
                text: header + Lang.get('messages.common.has_been_delete'),
                type: 'success',
                confirmButtonText: Lang.get('messages.common.ok'),
                confirmButtonColor: '#6777ef',
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
                confirmButtonText: Lang.get('messages.common.ok'),
                confirmButtonColor: '#6777ef',
                timer: 5000,
            });
        },
    });
}

window.deleteItemLiveWire = function (url, header, callFunction = null) {
    swal({
            title: Lang.get('messages.common.delete') + '!',
            text: Lang.get('messages.common.delete_confirm_common') + ' "' +
                header + '"?',
            type: 'warning',
            showCancelButton: true,
            closeOnConfirm: false,
            showLoaderOnConfirm: true,
            confirmButtonColor: '#6777ef',
            cancelButtonColor: '#d33',
            cancelButtonText: Lang.get('messages.common.no'),
            confirmButtonText: Lang.get('messages.common.yes'),
        },
        function () {
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
                        text: header +
                            Lang.get('messages.common.has_been_delete'),
                        confirmButtonText: Lang.get('messages.common.ok'),
                        type: 'success',
                        confirmButtonColor: '#6777ef',
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
                        confirmButtonText: Lang.get('messages.common.ok'),
                        timer: 5000,
                    });
                },
            });
        });
};

window.format = function (dateTime) {
    let format = arguments.length > 1 && arguments[1] !== undefined
        ? arguments[1]
        : 'DD-MMM-YYYY';
    return moment(dateTime).format(format);
};

window.isValidFile = function (inputSelector, validationMessageSelector) {
    let ext = $(inputSelector).val().split('.').pop().toLowerCase();
    if ($.inArray(ext, ['gif', 'png', 'jpg', 'jpeg']) == -1) {
        $(inputSelector).val('');
        $(validationMessageSelector).
            html('The image must be a file of type: jpeg, jpg, png.').
            show();
        return false;
    }
    $(validationMessageSelector).
        html('The image must be a file of type: jpeg, jpg, png.').
        hide();
    return true;
};

window.displayPhoto = function (input, selector) {
    let displayPreview = true;
    if (input.files && input.files[0]) {
        let reader = new FileReader();
        reader.onload = function (e) {
            let image = new Image();
            image.src = e.target.result;
            image.onload = function () {
                $(selector).attr('src', e.target.result);
                displayPreview = true;
            };
        };
        if (displayPreview) {
            reader.readAsDataURL(input.files[0]);
            $(selector).show();
        }
    }
};

window.displayFavicon = function (input, selector) {
    let displayPreview = true;
    if (input.files && input.files[0]) {
        let reader = new FileReader();
        reader.onload = function (e) {
            let image = new Image();
            image.src = e.target.result;
            image.onload = function () {
                if ((image.height != 16 || image.width != 16) &&
                    (image.height != 32 || image.width != 32)) {
                    $('#favicon').val('');
                    $('#validationErrorsBox').removeClass('display-none');
                    $('#validationErrorsBox').
                        html('The image must be of pixel 16 x 16 and 32 x 32.').
                        show();
                    return false;
                }
                $(selector).attr('src', e.target.result);
                displayPreview = true;
            };
        };
        if (displayPreview) {
            reader.readAsDataURL(input.files[0]);
            $(selector).show();
        }
    }
};

window.submitForm = function (btn) {
    // disable the button
    btn.disabled = true;
    // submit the form    
    btn.form.submit();
};

window.screenLock = function () {
    $('#overlay-screen-lock').show();
    $('body').css({ 'pointer-events': 'none', 'opacity': '0.6' });
};

window.screenUnLock = function () {
    $('body').css({ 'pointer-events': 'auto', 'opacity': '1' });
    $('#overlay-screen-lock').hide();
};

window.startLoader = function () {
    $('.infy-loader').show();
};

window.stopLoader = function () {
    $('.infy-loader').hide();
};

window.onload = function () {
    window.startLoader = function () {
        $('.infy-loader').show();
    };

    window.stopLoader = function () {
        $('.infy-loader').hide();
    };

// infy loader js
    stopLoader();
};

$(document).ready(function () {
    // script to active parent menu if sub menu has currently active
    let hasActiveMenu = $(document).
        find('.nav-item.dropdown ul li').
        hasClass('active');
    if (hasActiveMenu) {
        $(document).
            find('.nav-item.dropdown ul li.active').
            parent('ul').
            css('display', 'block');
        $(document).
            find('.nav-item.dropdown ul li.active').
            parent('ul').
            parent('li').
            addClass('active');
    }
    $('#searchCustomer').attr('disabled', false);
    $('#searchCustomer').blur();
});

window.addNewlines = function (str, chr) {
    let result = '';
    while (str.length > 0) {
        result += str.substring(0, chr) + '\n';
        str = str.substring(chr);
    }
    return result;
};

window.isEmpty = function (value) {
    return value === undefined || value === null || value === '';
};

window.checkZipcode = function (zipcode) {
    if (!isEmpty(zipcode) && (zipcode.length) < 6 || (zipcode.length) > 6) {
        displayErrorMessage('Zipcode should be 6 digits');
        return false;
    }
    return true;
};

// matches screen pixels for media queries and applied the supplied css to the same
window.matchWindowScreenPixels = function (selectorObj, modulePrefix) {
    if (typeof selectorObj != 'undefined') {
        const windowWidth = $(window).innerWidth();
        if (windowWidth >= 360 && windowWidth <= 414 && modulePrefix ===
            'pro') {
            $.each(selectorObj, function (key, val) {
                $(val + ' + .bootstrap-datetimepicker-widget.dropdown-menu').
                    addClass('dtPicker414-' + modulePrefix);
            });
        }
        if (windowWidth === 375) {
            $.each(selectorObj, function (key, val) {
                $(val + ' + .bootstrap-datetimepicker-widget.dropdown-menu').
                    addClass('dtPicker375-' + modulePrefix);
            });
        }
        if (windowWidth === 360) {
            $.each(selectorObj, function (key, val) {
                $(val + ' + .bootstrap-datetimepicker-widget.dropdown-menu').
                    addClass('dtPicker360-' + modulePrefix);
            });
        } else if (windowWidth === 320) {
            $.each(selectorObj, function (key, val) {
                $(val + ' + .bootstrap-datetimepicker-widget.dropdown-menu').
                    addClass('dtPicker320-' + modulePrefix);
            });
        }
    }
};

// Global search for customer
$(document).on('keyup', '#searchCustomer', function () {
    let searchData = $(this).val();
    if (!isEmpty(searchData)) {
        $.ajax({
            url: searchCustomerUrl,
            type: 'GET',
            data: { 'searchData': searchData },
            dataType: 'json',
            success: function (result) {
                if (result.success) {
                    let customersData = '';
                    $.each(result.data, function (key, value) {
                        customersData += '<div class="search-item mb-2 mt-1"><a href="' +
                            customersUrl + '/' + value.id +
                            '" class="py-0"><div class="search-icon bg-primary text-white mr-3">' +
                            value.company_name.substring(0, 1) + '</div>' +
                            '<div class="customer-name-css">' +
                            value.company_name + '</div></a></div>';
                        if (value.website != null) {
                            customersData += '<a href="' + value.website +
                                '" class="anchor-underline customer-website-name-css mb-2">' +
                                value.website + '</a>';
                        }
                    });
                    $('#customerName').html(customersData);

                    if (result.data.length == 0) {
                        $('#customerName').
                            html(
                                '<h6 class="py-1 px-3 my-0"><i class="fab fa fa-search text-primary"></i> ' +
                                noMatchingRecordsFound + '</h6>');
                    }

                }
            },
            error: function (result) {
                displayErrorMessage(result.responseJSON.message);
            },
        });

    } else {
        $('#customerName').empty();
        $('#customerName').
            html(
                '<h6 class="py-1 px-3 my-0"><i class="fab fa fa-search text-primary"></i> ' +
                noSearchResults + '</h6>');
    }
});

window.deleteItemInputConfirmation = function (
    url, tableId, header, alertMessage, callFunction = null) {
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
                deleteItemAjax(url, tableId, header, callFunction = null);
            }
        });
};

window.deleteItemLivewire = function (model, id, header) {
    swal({
            title: Lang.get('messages.common.delete') + '!',
            text: Lang.get('messages.common.delete_confirm_common') + ' "' +
                header + '"?',
            type: 'warning',
            showCancelButton: true,
            closeOnConfirm: false,
            showLoaderOnConfirm: true,
            confirmButtonColor: '#6777EF',
            cancelButtonColor: '#C1C1C1',
            cancelButtonText: Lang.get('messages.common.no'),
            confirmButtonText: Lang.get('messages.common.yes'),
        },
        function () {
            window.livewire.emit(model, id);
        });
}

window.livewireDeleteEventListener = function (data, header) {
    swal({
        title: Lang.get('messages.common.deleted'),
        text: header + Lang.get('messages.common.has_been_delete'),
        type: 'success',
        confirmButtonText: Lang.get('messages.common.ok'),
        confirmButtonColor: '#6777EF',
        timer: 2000,
    });
};

window.livewireDeleteErrorEventListener = function (data) {
    swal({
        title: 'Error!',
        text: data,
        type: 'error',
        confirmButtonColor: '#e96767',
        timer: 2000,
    });
};

$(document).on('submit', '#addCommonTagForm', function (e) {
    e.preventDefault();
    processingBtn('#addCommonTagForm', '#btnSave', 'loading');

    $.ajax({
        url: route('tags.store'),
        type: 'POST',
        data: $(this).serialize(),
        success: function (result) {
            if (result.success) {
                displaySuccessMessage(result.message);
                $('#addCommonTagModal').modal('hide');
                let data = {
                    id: result.data.id,
                    name: result.data.name,
                };

                let newOption = new Option(data.name, data.id, false, true);
                $('#tagId').append(newOption).trigger('change');
            }
        },
        error: function (result) {
            displayErrorMessage(result.responseJSON.message);
        },
        complete: function () {
            processingBtn('#addCommonTagForm', '#btnSave');
        },
    });
});

$('#addCommonTagModal').on('hidden.bs.modal', function () {
    resetModalForm('#addCommonTagForm', '#validationErrorsBox');
});

$(document).on('submit', '#addCommonPaymentModeForm', function (e) {
    e.preventDefault();
    processingBtn('#addCommonPaymentModeForm', '#btnSave', 'loading');

    $.ajax({
        url: route('payment-modes.store'),
        type: 'POST',
        data: $(this).serialize(),
        success: function (result) {
            if (result.success) {
                displaySuccessMessage(result.message)
                $('#addCommonPaymentModeModal').modal('hide')
                let data = {
                    id: result.data.id,
                    name: result.data.name,
                }

                let newOption = new Option(data.name, data.id, false, true);
                $('#paymentMode').append(newOption).trigger('change');
            }
        },
        error: function (result) {
            displayErrorMessage(result.responseJSON.message);
        },
        complete: function () {
            processingBtn('#addCommonPaymentModeForm', '#btnSave');
        },
    });
});

$('#addCommonPaymentModeModal').on('hidden.bs.modal', function () {
    resetModalForm('#addCommonPaymentModeForm', '#validationErrorsBox');
});

window.removeWhiteSpace = function () {
    displayErrorMessage('Description field is not contain only white space');
    return false;
};
