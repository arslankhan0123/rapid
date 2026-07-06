'use strict'

$(document).ready(function () {
    $('#mySelect').select2({
        width: '100%',
    })

    if (groupName == 'general') {
        let input2 = document.querySelector('#defaultCountryData')
        let intl2 = window.intlTelInput(input2, {
            initialCountry: defaultCountryCodeValue,
            separateDialCode: true,
            preferredCountries: false,
            geoIpLookup: function (success, failure) {
                $.get('https://ipinfo.io', function () {
                }, 'jsonp').always(function (resp) {
                    let countryCode = (resp && resp.country)
                        ? resp.country
                        : ''
                    success(countryCode)
                })
            },
            utilsScript: utilsScript,
        })

        let getCode = intl2.selectedCountryData['name'] + '+' +
            intl2.selectedCountryData['dialCode']
        $('#defaultCountryData').val(getCode)
    }
})

$(document).on('click', '.iti__standard', function () {
    $('#defaultCountryData').val($(this).text())
    $(this).attr('data-country-code')
    $('#defaultCountryCode').val($(this).attr('data-country-code'))
})

$(document).on('submit', '#settingUpdate', function () {
    if (groupName === 'company_information') {
        let address = $('#addressId').val()
        let companyName = $('#companyNameId').val()

        let emptyAddress = address.trim().replace(/ \r\n\t/g, '') === ''
        if (emptyAddress) {
            displayErrorMessage(
                'Address field is not contain only white space')
            return false;
        }

        let emptyCompanyName = companyName.trim().replace(/ \r\n\t/g, '') ===
            '';
        if (emptyCompanyName) {
            displayErrorMessage(
                'Company Name field is not contain only white space');
            return false;
        }
    }

    if (groupName === 'general') {
        let applicationName = $('#applicationNameId').val();
        let emptyApplicationName = applicationName.trim().
            replace(/ \r\n\t/g, '') === '';

        if (emptyApplicationName) {
            displayErrorMessage(
                'Application Name field is not contain only white space');
            return false;
        }
    }

    if ($('#error-msg').text() !== '') {
        $('#phoneNumber').focus();
        return false;
    }
});

$(document).on('change', '#logo', function () {
    if (isValidFile($(this), '#validationErrorsBox')) {
        displayPhoto(this, '#logoPreview');
    }
});

$(document).on('change', '#favicon', function () {
    if (isValidFile($(this), '#validationErrorsBox')) {
        displayFavicon(this, '#faviconPreview');
    }
});
