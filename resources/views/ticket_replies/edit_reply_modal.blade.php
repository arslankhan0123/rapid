<div class="modal fade" role="dialog" id="editTicketReplyModal" style="opacity: 1; z-index: 9999">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('messages.ticket.edit_reply') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            {{ Form::open(['id' => 'ticketReplyUpdateForm']) }}
            <div class="modal-body">
                <div class="alert alert-danger d-none" id="editValidationErrorsBox"></div>
                {{ Form::hidden('ticket_reply_id',null,['id'=>'ticketReplyID']) }}
                <div class="row">
                    <div class="form-group col-sm-12 mb-0">
                        {{ Form::label('reply',__('messages.ticket.reply').':') }}
                        {{ Form::textarea('reply', null, ['class' => 'form-control edit-reply','id' => 'editReplyID']) }}
                    </div>
                </div>
                <div class="text-right">
                    {{ Form::button(__('messages.common.save'), ['type'=>'submit','class' => 'btn btn-primary','id'=>'btnEditReplySave','data-loading-text'=>"<span class='spinner-border spinner-border-sm'></span> Processing..."]) }}
                    <button type="button" id="btnEditCancel" class="btn btn-light ml-1"
                            data-dismiss="modal">{{ __('messages.common.cancel') }}
                    </button>
                </div>
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>
