'use strict';

let input = document.querySelector('#phoneNumber'),
    errorMsg = document.querySelector('#error-msg'),
    validMsg = document.querySelector('#valid-msg');

let errorMap = [
    Lang.get('messages.placeholder.invalid_number'),
    Lang.get('messages.placeholder.invalid_country_number'),
    Lang.get('messages.placeholder.too_short'),
    Lang.get('messages.placeholder.too_long'),
    Lang.get('messages.placeholder.invalid_number')]

// initialise plugin
let intl = window.intlTelInput(input, {
    initialCountry: defaultCountryCodeValue,
    separateDialCode: true,
    preferredCountries: false,
    geoIpLookup: function (success, failure) {
        $.get('//ipinfo.io', function () {}, 'jsonp').
            always(function (resp) {
                let countryCode = (resp && resp.country)
                    ? resp.country
                    : ''
                success(countryCode)
            })
    },
    utilsScript: utilsScript,
});

let getCode = intl.selectedCountryData['name'] + '+' +
    intl.selectedCountryData['dialCode']
$('#defaultCountryData').val(getCode)

let reset = function () {
    input.classList.remove('error')
    errorMsg.innerHTML = ''
    errorMsg.classList.add('hide')
    validMsg.classList.add('hide')
}

input.addEventListener('blur', function () {
    reset()
    if (input.value.trim()) {
        if (intl.isValidNumber()) {
            validMsg.classList.remove('hide');
        } else {
            input.classList.add('error');
            let errorCode = intl.getValidationError();
            errorMsg.innerHTML = errorMap[errorCode];
            errorMsg.classList.remove('hide');
        }
    }
});

// on keyup / change flag: reset
input.addEventListener('change', reset);
input.addEventListener('keyup', reset);

if (phoneNo !== '') {
    setTimeout(function () {
        $('#phoneNumber').trigger('change');
    }, 500);
}

$(document).on('blur keyup change countrychange', '#phoneNumber', function () {
    if (phoneNo !== '') {
        intl.setNumber('+' + phoneNo);
        phoneNo = '';
    }
    let getCode = intl.selectedCountryData['dialCode'];
    $('#prefix_code').val(getCode);
});

if (isEdit) {
    let getCode = intl.selectedCountryData['dialCode'];
    $('#prefix_code').val(getCode);
}

let getPhoneNumber = $('#phoneNumber').val();
let removeSpacePhoneNumber = getPhoneNumber.replace(/\s/g, '');
$('#phoneNumber').val(removeSpacePhoneNumber);


