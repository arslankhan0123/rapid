<div class="alert alert-danger d-none" id="validationErrorsBox"></div>
<div class="row">
    <div class="form-group  col-sm-12">
        {{ Form::label('admin_note', 'Branch') }}<span class="required">*</span>
        {{ Form::select('branch_id', $usersBranches ?? [], $category->branch_id ?? null, ['class' => 'form-control select2', 'required', 'id' => 'branchSelect', 'placeholder' => 'Select Branch']) }}
    </div>
    <div class="form-group col-sm-12">
        {{ Form::label('receiving_no', __('messages.sample_receiving.receiving_no')) }}<span class="required">*</span>
        {{ Form::text('receiving_no', $category['id'], ['class' => 'form-control', 'required', 'id' => 'estimateNumber', 'readonly']) }}
    </div>
    <div class="form-group col-sm-12">
        {{ Form::label('client_name', __('messages.sample_receiving.client_name') . ':') }}<span
            class="required">*</span>
        <div class="input-group">
            {{ Form::text('client_name', $category['client_name'], ['class' => 'form-control col-sm-8', 'required', 'id' => 'customerNameInput', 'autocomplete' => 'off']) }}
            <div class="input-group-append col-sm-4">
                {{ Form::select('customer_id', $data['clients'], $category['client_name'], ['class' => 'form-control', 'id' => 'customerSelectBox', 'placeholder' => 'Select Client']) }}
            </div>
        </div>
    </div>
    <div class="form-group col-sm-12">
        {{ Form::label('client_reference', __('messages.sample_receiving.client_reference') . ':') }}<span
            class="required">*</span>
        {{ Form::text('client_reference', $category['client_reference'], ['class' => 'form-control', 'required', 'id' => 'client_reference', 'autocomplete' => 'off']) }}
    </div>
    <div class="form-group col-sm-12">
        {{ Form::label('client_reference', __('messages.sample_receiving.type_of_sample') . ':') }}<span
            class="required">*</span>
        {{ Form::text('type_of_sample', $category['client_reference'], ['class' => 'form-control', 'required', 'id' => 'type_of_sample', 'autocomplete' => 'off']) }}
    </div>
    <div class="form-group col-sm-12">
        {{ Form::label('required_tests', __('messages.sample_receiving.required_tests') . ':') }}<span
            class="required">*</span>
        {{ Form::text('required_tests', $category['required_tests'], ['class' => 'form-control', 'required', 'id' => 'required_tests', 'autocomplete' => 'off']) }}
    </div>
    <div class="form-group col-sm-12">
        {{ Form::label('number_of_sample', __('messages.sample_receiving.number_of_sample') . ':') }}<span
            class="required">*</span>
        {{ Form::text('number_of_sample', $category['number_of_sample'], ['class' => 'form-control', 'required', 'id' => 'number_of_sample', 'autocomplete' => 'off']) }}
    </div>
    <div class="form-group col-sm-12">
        {{ Form::label('date', __('messages.sample_receiving.date') . ':') }}<span class="required">*</span>
        <div class="input-group">
            <div class="input-group-prepend">
                <div class="input-group-text">
                    <i class="fas fa-calendar-alt"></i>
                </div>
            </div>
            {{ Form::text('date', $category['date'], ['class' => 'form-control datepicker', 'required', 'autocomplete' => 'off']) }}
        </div>
    </div>
    <div class="form-group col-sm-12">
        {{ Form::label('time', __('messages.sample_receiving.time') . ':') }}<span class="required">*</span>
        <div class="input-group">
            <div class="input-group-prepend">
                <div class="input-group-text">
                    <i class="fas fa-calendar-alt"></i>
                </div>
            </div>
            {{ Form::text('time', $category['time'], ['class' => 'form-control timepicker', 'required', 'autocomplete' => 'off']) }}
        </div>
    </div>
    <div class="form-group col-sm-12">
        {{ Form::label('section', __('messages.sample_receiving.section') . ':') }}<span class="required">*</span>
        {{ Form::select('section', $data['section'], $category['section'], ['class' => 'form-control', 'required', 'id' => 'sectionSelectBox', 'placeholder' => 'Select One']) }}
    </div>
    <div class="form-group col-sm-12">
        {{ Form::label('delivered_by', __('messages.sample_receiving.delivered_by') . ':') }}<span
            class="required">*</span>
        {{ Form::select('delivered_by', $data['employees'], $category['delivered_by'], ['class' => 'form-control', 'required', 'id' => 'deliveredBySelectBox', 'placeholder' => 'Select One']) }}
    </div>
    <div class="form-group col-sm-12">
        {{ Form::label('received_by', __('messages.sample_receiving.received_by') . ':') }}<span
            class="required">*</span>
        {{ Form::select('received_by', $data['employees'], $category['received_by'], ['class' => 'form-control', 'required', 'id' => 'receivedBySelectBox', 'placeholder' => 'Select One']) }}
    </div>
</div>
<div class="text-right">
    {{ Form::button(__('messages.common.submit'), ['type' => 'submit', 'class' => 'btn btn-primary', 'id' => 'btnSave', 'data-loading-text' => "<span class='spinner-border spinner-border-sm'></span> Processing..."]) }}
</div>
