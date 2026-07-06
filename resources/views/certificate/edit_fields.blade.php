<div class="alert alert-danger d-none" id="validationErrorsBox"></div>
<div class="row">
    <div class="form-group col-sm-12">
        {{ Form::label('transfer_id', __('messages.certificate.certificate_number')) }}<span class="required">*</span>
        {{ Form::text('certificate_number', $category->certificate_number, ['class' => 'form-control', 'required', 'id' => 'transfer_id', 'autocomplete' => 'off', 'readonly']) }}
    </div>
    <div class="form-group  col-sm-12">
        {{ Form::label('category_id', __('messages.certificate.type')) }}<span class="required">*</span>
        {{ Form::select('type_id', $certificateTypes, $category->type_id ?? null, ['class' => 'form-control select2', 'required', 'id' => 'select_category', 'autocomplete' => 'off']) }}
    </div>
    <div class="form-group col-sm-12">
        {{ Form::label('employee', __('messages.certificate.employee') . ':') }}<span class="required">*</span>
        <div class="input-group">
            {{ Form::text('employee', $category['employee'], ['class' => 'form-control', 'required', 'id' => 'customerNameInput', 'autocomplete' => 'off']) }}
            <div class="input-group-append col-sm-4">
                {{ Form::select('customer_id', $data['clients'], null, ['class' => 'form-control', 'id' => 'customerSelectBox', 'placeholder' => 'Select Client']) }}
            </div>
        </div>
    </div>
    <div class="form-group col-sm-12">
        {{ Form::label('lab_manager', __('messages.certificate.lab_manager') . ':') }}<span class="required">*</span>
        {{ Form::text('lab_manager', $category['lab_manager'], ['class' => 'form-control', 'required', 'id' => 'lab_manager', 'autocomplete' => 'off']) }}
    </div>
    <div class="form-group col-sm-12">
        {{ Form::label('general_manager', __('messages.certificate.general_manager') . ':') }}<span
            class="required">*</span>
        {{ Form::text('general_manager', $category['general_manager'], ['class' => 'form-control', 'required', 'id' => 'general_manager', 'autocomplete' => 'off']) }}
    </div>
    <div class="form-group col-sm-12">
        {{ Form::label('date', __('messages.certificate.date') . ':') }}<span class="required">*</span>
        <div class="input-group">
            <div class="input-group-prepend">
                <div class="input-group-text">
                    <i class="fas fa-calendar-alt"></i>
                </div>
            </div>
            {{ Form::text('date', $category['date'], ['class' => 'form-control datepicker', 'required', 'autocomplete' => 'off']) }}
        </div>
    </div>
    <div class="form-group col-sm-12 mb-0">
        {{ Form::label('description', __('messages.certificate.description') . ':') }}
        {{ Form::textarea('description', $category['description'], ['class' => 'form-control summernote-simple', 'id' => 'createDescription']) }}
    </div>
</div>
<div class="text-right">
    {{ Form::button(__('messages.common.submit'), ['type' => 'submit', 'class' => 'btn btn-primary', 'id' => 'btnSave', 'data-loading-text' => "<span class='spinner-border spinner-border-sm'></span> Processing..."]) }}
</div>
