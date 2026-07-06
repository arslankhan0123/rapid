<div class="row">
    <div class="form-group col-md-6 col-sm-12">
        {{ Form::label('subject', __('messages.ticket.subject').':') }}<span class="required">*</span>
        {{ Form::text('subject', null, ['class' => 'form-control', 'required','autocomplete' => 'off','placeholder'=>__('messages.ticket.subject')]) }}
    </div>
    <div class="form-group col-md-6 col-sm-12">
        {{ Form::label('email', __('messages.ticket.email').':') }}
        {{ Form::email('email', null, ['class' => 'form-control','autocomplete' => 'off','placeholder'=>__('messages.ticket.email')]) }}
    </div>
</div>
<div class="row">
    <div class="form-group col-sm-12">
        {{ Form::label('body', __('messages.common.description').':') }}<br>
        {{ Form::textarea('body', null, ['class' => 'form-control ticketBody', 'id' => 'ticketBody']) }}
    </div>
    <div class="form-group col-sm-12">
        {{ Form::label('attachments', __('messages.ticket.attachments').':',['class' => 'profile-label-color']) }} <span
                data-toggle="tooltip" data-title="{{ __('messages.ticket.you_can_add_multiples_images_and_files') }}"><i
                    class="fas fa-question-circle"></i></span>
        <div class="d-flex flex-md-nowrap flex-wrap">
            <label class="image__file-upload h-100"> {{ __('messages.setting.choose') }}
                {{ Form::file('attachments[]',['id'=>'attachment','class' => 'd-none', 'multiple']) }}
            </label>
            <div id="attachmentFileSection" class="attachment__create overflow-auto pl-md-4 pl-0"></div>
        </div>
    </div>
</div>
<div class="text-right">
    {{ Form::submit(__('messages.common.save'), ['class' => 'btn btn-primary', 'id' => 'btnSave','data-loading-text'=>"<span class='spinner-border spinner-border-sm'></span> Processing..."]) }}
    <a href="{{ route('client.tickets.index') }}"
       class="btn btn-secondary text-dark">{{ __('messages.common.cancel') }}</a>
</div>

