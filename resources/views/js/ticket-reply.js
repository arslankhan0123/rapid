'use strict'

$('#replyId').summernote({
    minHeight: 160,
    toolbar: [
        ['style', ['bold', 'italic', 'underline', 'clear']],
        ['font', ['strikethrough']],
        ['para', ['paragraph']]],
})

$('.edit-reply').summernote({
    minHeight: 200,
    toolbar: [
        ['style', ['bold', 'italic', 'underline', 'clear']],
        ['font', ['strikethrough']],
        ['para', ['paragraph']]],
})

// create ticket reply js code
$(document).on('submit', '#ticketReplyStoreForm', function (e) {
    e.preventDefault()

    if ($('#replyId').val() == '') {
        displayErrorMessage('Reply field is required.')
        return false
    }

    let reply = $('<div />').html($('#replyId').summernote('code'))
    let emptyReply = reply.text().trim().replace(/ \r\n\t/g, '') === ''

    if (emptyReply) {
        displayErrorMessage(
            'Reply field is not contain only white space')
        return false
    }

    $('#btnReply').prop('disabled', true)

    $.ajax({
        url: route('ticket.reply.store'),
        type: 'POST',
        data: $(this).serialize(),
        success: function (result) {
            if (result.success) {
                displaySuccessMessage(result.message)
                window.location.href = result.data
            }
        },
        error: function (result) {
            displayErrorMessage(result.responseJSON.message)
        },
        complete: function () {
            $('#btnReply').prop('disabled', false)
        },
    })
})

// edit ticket reply js code
$(document).on('click','.edit-ticket-reply', function () {
    let editTicketReplyID = $(this).attr('data-id')
    
    $.ajax({
        url: route('ticket.reply.edit', editTicketReplyID),
        type: 'GET',
        success:function(result){
        	if (result.success) {
                let data = result.data
                $('#ticketReplyID').val(data.id)
                let element = document.createElement('textarea');
                element.innerHTML = result.data.reply;
                let ticketReply = element.value;
                $('#editReplyID').summernote('code', ticketReply)
                $('#editTicketReplyModal').append('body').modal('show')
            }
        },error: function (result) {
           displayErrorMessage(result.responseJSON.messages) 
        }
    });
})

// update ticket reply js code
$(document).on('submit', '#ticketReplyUpdateForm', function (e) {
    e.preventDefault()

    if ($('#editReplyID').val() == '') {
        displayErrorMessage('Reply field is required.')
        return false
    }

    let reply = $('<div />').html($('#editReplyID').summernote('code'))
    let emptyReply = reply.text().trim().replace(/ \r\n\t/g, '') === ''

    if (emptyReply) {
        displayErrorMessage(
            'Reply field is not contain only white space')
        return false
    }

    processingBtn('#ticketReplyUpdateForm', '#btnEditReplySave', 'loading');
    let ticketReplyID = $('#ticketReplyID').val()

    $.ajax({
        url: route('ticket.reply.update', ticketReplyID),
        type: 'PUT',
        data: $(this).serialize(),
        success: function (result) {
            if (result.success) {
                displaySuccessMessage(result.message)
                $('#editTicketReplyModal').modal('hide')
                window.location.href = result.data
            }
        },
        error: function (result) {
            displayErrorMessage(result.responseJSON.message)
        },
        complete: function () {
            processingBtn('#ticketReplyUpdateForm', '#btnEditReplySave');
        },
    })
});

// delete ticket reply js code
$(document).on('click','.delete-ticket-reply', function () {
    let deleteTicketReplyID = $(this).attr('data-id')

    swal({
            title: Lang.get('messages.common.delete') + '!',
            text: Lang.get('messages.common.delete_confirm_common') + ' "' +
                'Ticket Reply' + '"?',
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
                url: route('ticket.reply.destroy', deleteTicketReplyID),
                type: 'DELETE',
                success: function (result) {
                    if (result.success) {
                        window.location.href = result.data
                    }
                    swal({
                        title: Lang.get('messages.common.deleted'),
                        text: 'Ticket Reply' + Lang.get('messages.common.has_been_delete'),
                        type: 'success',
                        confirmButtonText: Lang.get('messages.common.ok'),
                        confirmButtonColor: '#6777ef',
                        timer: 2000,
                    });
                },
                error: function (result) {
                    swal({
                        title: '',
                        text: result.responseJSON.message,
                        type: 'error',
                        confirmButtonText: Lang.get('messages.common.ok'),
                        confirmButtonColor: '#6777ef',
                        timer: 5000,
                    });
                },
            });
        });
})
