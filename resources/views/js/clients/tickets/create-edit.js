'use strict'

$(document).ready(function () {
    $('#tagId').select2({
        width: '100%',
        placeholder: Lang.get('messages.placeholder.select_tags'),
    })

    $('#priorityId').select2({
        width: '100%',
    })

    $('#serviceId').select2({
        width: '100%',
    })

    $('#departmentId').select2({
        width: '100%',
    })

    $('#assignToId').select2({
        width: '100%',
    })

    $('#ticketStatusId').select2({
        width: '100%',
        placeholder: Lang.get('messages.placeholder.select_status'),
    })

    $('.ticketBody').summernote({
        dialogsInBody: true,
        minHeight: 150,
        toolbar: [
            ['style', ['bold', 'italic', 'underline', 'clear']],
            ['font', ['strikethrough']],
            ['para', ['paragraph']],
        ],
    })

    $(document).on('mouseenter', '.ticket-attachment', function () {
        $(this).find('.attachment-delete').removeClass('d-none')
    })

    $(document).on('mouseleave', '.ticket-attachment', function () {
        $(this).find('.attachment-delete').addClass('d-none')
    })

    $(document).on('click', '.attachment-delete', function (event) {
        let ticketAttachmentId = $(event.currentTarget).data('id')
        swal({
                title: Lang.get('messages.common.delete') + '!',
                text: Lang.get('messages.common.delete_attachment'),
                type: 'warning',
                showCancelButton: true,
                closeOnConfirm: false,
                showLoaderOnConfirm: true,
                confirmButtonColor: '#6777ef',
                cancelButtonColor: '#d33',
                cancelButtonText: 'No',
                confirmButtonText: 'Yes',
            },
            function () {
                $.ajax({
                    url: ticketAttachmentUrl,
                    type: 'DELETE',
                    dataType: 'json',
                    data: { mediaId: ticketAttachmentId },
                    success: function (obj) {
                        if (obj.success) {
                            window.location.reload()
                        }
                        swal({
                            title: Lang.get('messages.common.deleted'),
                            text: Lang.get(
                                'messages.common.attachment_deleted'),
                            type: 'success',
                            confirmButtonColor: '#6777ef',
                            timer: 2000,
                        })
                    },
                    error: function (data) {
                        swal({
                            title: '',
                            text: data.responseJSON.message,
                            type: 'error',
                            confirmButtonColor: '#6777ef',
                            timer: 5000,
                        })
                    },
                })
            })
    })

    document.querySelector('#attachment').
        addEventListener('change', handleFileSelect, false)
    let selDiv = document.querySelector('#attachmentFileSection')

    function handleFileSelect (e) {
        if (!e.target.files || !window.FileReader) return

        selDiv.innerHTML = ''
        let files = e.target.files
        for (let i = 0; i < files.length; i++) {
            let f = files[i]
            let reader = new FileReader()
            reader.onload = function (e) {
                if (f.type.match('image*')) {
                    let html = '<img class=\'img-thumbnail thumbnail-preview ticket-attachment\' src="' +
                        e.target.result + '">'
                    selDiv.innerHTML += html
                } else if (f.type.match('pdf*')) {
                    let html = '<img class=\'img-thumbnail thumbnail-preview ticket-attachment\' src="/assets/img/pdf_icon.png">'
                    selDiv.innerHTML += html
                } else if (f.type.match('zip*')) {
                    let html = '<img class=\'img-thumbnail thumbnail-preview ticket-attachment\' src="/assets/img/zip_icon.png">'
                    selDiv.innerHTML += html
                } else if (f.type.match('sheet*')) {
                    let html = '<img class=\'img-thumbnail thumbnail-preview ticket-attachment\' src="/assets/img/xlsx_icon.png">'
                    selDiv.innerHTML += html
                } else if (f.type.match('text*')) {
                    let html = '<img class=\'img-thumbnail thumbnail-preview ticket-attachment\' src="/assets/img/txt_icon.png">'
                    selDiv.innerHTML += html
                } else if (f.type.match('msword*')) {
                    let html = '<img class=\'img-thumbnail thumbnail-preview ticket-attachment\' src="/assets/img/doc_icon.png">'
                    selDiv.innerHTML += html
                } else {
                    selDiv.innerHTML += f.name
                }

            }
            reader.readAsDataURL(f)
        }
    }

    $(document).on('mouseenter', '.ticket-attachment', function () {
        $(this).find('.ticket-attachment__icon').removeClass('d-none')
    })

    $(document).on('mouseleave', '.ticket-attachment', function () {
        $(this).find('.ticket-attachment__icon').addClass('d-none')
    })

})

$(document).on('submit', '#createTicket', function () {
    let loadingButton = jQuery(this).find('#btnSave')
    loadingButton.button('loading')
    if ($('#error-msg').text() !== '') {
        return false
    }

    let description = $('<div />').html($('#ticketBody').summernote('code'))
    let empty = description.text().trim().replace(/ \r\n\t/g, '') === ''

    if ($('#ticketBody').summernote('isEmpty')) {
        $('#ticketBody').val('')
    } else if (empty) {
        displayErrorMessage(
            'Description field is not contain only white space')
        let loadingButton = jQuery(this).find('#btnSave')
        loadingButton.button('reset')
        return false
    }
})

