<div class="row">
    <div class="form-group col-sm-6 col-xl-3 col-lg-4 col-md-6">
        {{ Form::label('subject', __('messages.ticket.subject').':') }}<span class="required">*</span>
        {{ Form::text('subject', null, ['class' => 'form-control', 'required','autocomplete' => 'off','placeholder'=>__('messages.ticket.subject')]) }}
    </div>
    <div class="form-group col-sm-6 col-xl-3 col-lg-4 col-md-6" id="contactCol">
        {{ Form::label('contact_id', __('messages.ticket.contact').':') }}
        {{ Form::select('contact_id', $data['contacts'],null, ['id'=>'contactId','class' => 'form-control','placeholder' => __('messages.placeholder.select_contact')]) }}
    </div>
    <div class="form-group col-sm-6 d-none col-xl-3 col-lg-4 col-md-6" id="nameCol">
        {{ Form::label('name', __('messages.ticket.name').':') }}
        {{ Form::text('name', null, ['class' => 'form-control','autocomplete' => 'off','placeholder'=>__('messages.ticket.name')]) }}
    </div>
    <div class="form-group col-sm-6 col-xl-3 col-lg-4 col-md-6">
        {{ Form::label('email', __('messages.ticket.email').':') }}
        {{ Form::email('email', null, ['class' => 'form-control','autocomplete' => 'off','placeholder'=>__('messages.ticket.email')]) }}
    </div>
    <div class="form-group col-sm-6 col-xl-3 col-lg-4 col-md-6">
        {{ Form::label('department_id', __('messages.ticket.department').':') }}
        <div class="input-group">
            {{ Form::select('department_id', $data['departments'],null, ['id'=>'departmentId','class' => 'form-control','placeholder' => __('messages.placeholder.select_department')]) }}
            <div class="input-group-append plus-icon-height">
                <div class="input-group-text">
                    <a href="#" data-toggle="modal" data-target="#addDepartmentModal"><i class="fa fa-plus"></i></a>
                </div>
            </div>
        </div>
    </div>
    <div class="form-group col-sm-6 col-xl-3 col-lg-4 col-md-6">
        {{ Form::label('cc', __('messages.ticket.cc').':') }}
        {{ Form::email('cc', null, ['class' => 'form-control','autocomplete' => 'off','placeholder'=>__('messages.ticket.cc')]) }}
    </div>
    <div class="form-group col-sm-6 col-xl-3 col-lg-4 col-md-6">
        {{ Form::label('assign_to', __('messages.ticket.assign_to').':') }}
        {{ Form::select('assign_to', $data['assignTo'],null, ['id'=>'assignToId','class' => 'form-control','placeholder' => __('messages.placeholder.select_member')]) }}
    </div>
    <div class="form-group col-sm-6 col-xl-3 col-lg-4 col-md-6">
        {{ Form::label('priority_id', __('messages.ticket.priority').':') }}
        <div class="input-group">
            {{ Form::select('priority_id', $data['priority'],null, ['id'=>'priorityId','class' => 'form-control','placeholder' => __('messages.placeholder.select_priority')]) }}
            <div class="input-group-append plus-icon-height">
                <div class="input-group-text">
                    <a href="#" data-toggle="modal" data-target="#addTicketPriorityModal"><i class="fa fa-plus"></i></a>
                </div>
            </div>
        </div>
    </div>
    <div class="form-group col-sm-6 col-xl-3 col-lg-4 col-md-6">
        {{ Form::label('service_id', __('messages.ticket.service').':') }}
        <div class="input-group">
            {{ Form::select('service_id', $data['services'],null, ['id'=>'serviceId','class' => 'form-control','placeholder' => __('messages.placeholder.select_service')]) }}
            <div class="input-group-append plus-icon-height">
                <div class="input-group-text">
                    <a href="#" data-toggle="modal" data-target="#addModal"><i class="fa fa-plus"></i></a>
                </div>
            </div>
        </div>
    </div>
    <div class="form-group col-sm-6 col-xl-3 col-lg-4 col-md-6">
        {{ Form::label('tags', __('messages.ticket.tags').':') }}
        <div class="input-group">
            {{ Form::select('tags[]', $data['tags'],null, ['id'=>'tagId','class' => 'form-control', 'multiple' => 'multiple']) }}
            <div class="input-group-append plus-icon-height">
                <div class="input-group-text">
                    <a href="#" data-toggle="modal" data-target="#addCommonTagModal"><i class="fa fa-plus"></i></a>
                </div>
            </div>
        </div>
    </div>
    <div class="form-group col-sm-6 col-xl-3 col-lg-4 col-md-6">
        {{ Form::label('ticket_status_id', __('messages.common.status').':') }}<span class="required">*</span>
        <div class="input-group">
            {{ Form::select('ticket_status_id', $data['ticketStatus'],null, ['id'=>'ticketStatusId','class' => 'form-control','required','placeholder' => __('messages.placeholder.select_status')]) }}
            <div class="input-group-append">
                <div class="input-group-text">
                    <a href="#" data-toggle="modal" data-target="#addTicketStatusModal"><i class="fa fa-plus"></i></a>
                </div>
            </div>
        </div>
    </div>
    <div class="form-group col-sm-6 col-xl-3 col-lg-4 col-md-6">
        {{ Form::label('predefined_reply_id', __('messages.ticket.predefined_reply').':') }}
        <div class="input-group">
            {{ Form::select('predefined_reply_id', $data['predefinedReplies'],null, ['id'=>'predefinedReplyId','class' => 'form-control','placeholder' => __('messages.placeholder.select_predefined_reply')]) }}
            <div class="input-group-append plus-icon-height">
                <div class="input-group-text">
                    <a href="#" data-toggle="modal" data-target="#addTicketPredefinedModal"><i
                                class="fa fa-plus"></i></a>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="form-group col-md-12">
        {{ Form::label('body', __('messages.common.description').':') }}
        {{ Form::textarea('body', null, ['class' => 'form-control ticketBody', 'id' => 'ticketBody']) }}
    </div>
    <div class="form-group col-sm-12 col-md-12 col-xl-12">
        {{ Form::label('attachments', __('messages.ticket.attachments').':',['class' => 'profile-label-color']) }} <span
                data-toggle="tooltip" data-title="{{ __('messages.ticket.you_can_add_multiples_images_and_files') }}"><i
                    class="fas fa-question-circle"></i></span>
        <div class="d-flex mb-3 overflow-hidden flex-md-nowrap flex-wrap">
            <label class="image__file-upload text-white mb-4 h-100"> {{ __('messages.setting.choose') }}
                {{ Form::file('attachments[]',['id'=>'attachment','class' => 'd-none', 'multiple']) }}
            </label>
            <div id="attachmentFileSection" class="attachment__create overflow-auto"></div>
            @if(count($ticket->media) > 0)
                <div class="pl-md-4 pl-0 mt-md-0 mt-5">
                    <div class="gallery gallery-md file-grp">
                        @foreach($ticket->media as $media)
                            <div class="gallery-item ticket-attachment"
                                 data-image="{{ mediaUrlEndsWith($media->getFullUrl()) }}"
                                 data-title="{{ $media->name }}"
                                 href="{{ mediaUrlEndsWith($media->getFullUrl()) }}" title="{{ $media->name }}">
                                <div class="ticket-attachment__icon d-none">
                                    <a href="{{ $media->getFullUrl() }}" target="_blank"
                                       class="text-decoration-none text-primary"
                                       title="{{ __('messages.common.view') }}"><i class="fas fa-eye"></i>
                                    </a>
                                    <a href="javascript:void(0)"
                                       class="text-danger text-decoration-none attachment-delete"
                                       data-id="{{ $media->id }}"
                                       title="{{ __('messages.common.delete') }}"><i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
    <div class="col-sm-12">
        {{ Form::button(__('messages.common.save'), ['type'=>'submit','class' => 'btn btn-primary', 'id' => 'btnSave','data-loading-text'=>"<span class='spinner-border spinner-border-sm'></span> Processing..."]) }}
        <a href="{{ url()->previous() }}"
           class="btn btn-secondary text-dark">{{ __('messages.common.cancel') }}</a>
    </div>
</div>
