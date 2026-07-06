'use strict'

$(document).ready(function () {
    $('.translateLanguage, #subFolderFiles').select2({
        placeholder: 'Select File',
    })
})

let lang = languageName
let file = fileName

$('.langName').keypress(function (e) {
    let regex = new RegExp(/^[a-zA-Z\s]+$/)
    let str = String.fromCharCode(!e.charCode ? e.which : e.charCode)
    if (regex.test(str)) {
        return true
    } else {
        e.preventDefault()
        return false
    }
})

$(document).on('change', '.translateLanguage', function () {
    lang = $(this).val()
    if (lang == '') {
        window.location.href = url
    } else {
        window.location.href = url + 'name=' + lang + '&file=' + file
    }
})

$(document).on('change', '#subFolderFiles', function () {
    file = $(this).val()
    if (file == '') {
        location.href = url
    } else {
        window.location.href = url + 'name=' + lang + '&file=' + file
    }
})
