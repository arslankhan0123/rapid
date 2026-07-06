<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            {{ Form::label('name', __('messages.lead.client_name')) }}<span class="required">*</span>
            {{ Form::text('name', null, ['class' => 'form-control', 'required', 'autocomplete' => 'off']) }}
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            {{ Form::label('product_group_id', __('messages.lead.select_groups')) }}<span class="required">*</span>
            {{ Form::select('product_group_id', $data['product_groups'], null, ['id' => 'product_group_id', 'required', 'class' => 'form-control', 'placeholder' => __('messages.lead.select_groups')]) }}
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            {{ Form::label('product_id', __('messages.lead.select_product')) }}<span class="required">*</span>
            {{ Form::select('product_id', [], null, ['id' => 'product_id', 'required', 'class' => 'form-control', 'placeholder' => __('messages.lead.select_product')]) }}
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-3">
        <div class="form-group">
            {{ Form::label('budget', __('messages.lead.budget') . ':') }}<span class="required">*</span>
            {{ Form::text('budget', null, ['class' => 'form-control price-input', 'required', 'autocomplete' => 'off']) }}
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            {{ Form::label('priority', __('messages.lead.priority') . ':') }}<span class="required">*</span>
            {{ Form::select('priority', ['High' => 'High', 'Medium' => 'Medium', 'Low' => 'low'], null, ['required', 'class' => 'form-control']) }}
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            {{ Form::label('start_date', __('messages.lead.start_date') . ':') }}<span class="required">*</span>
            {{ Form::date('start_date', null, ['required', 'class' => 'form-control']) }}
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            {{ Form::label('assignee', __('messages.lead.assignee') . ':') }}<span class="required">*</span>
            {{ Form::select('assignee', $data['assigned'], null, ['id' => 'assignee', 'required', 'class' => 'form-control', 'placeholder' => __('messages.lead.select_assignee')]) }}
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            {{ Form::label('contact', __('messages.lead.contact')) }}<span class="required">*</span>
            {{ Form::text('contact', null, ['class' => 'form-control', 'required', 'autocomplete' => 'off']) }}
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            {{ Form::label('position', __('messages.contact.position')) }}
            {{ Form::text('position', null, ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => __('messages.contact.position')]) }}
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            {{ Form::label('source', __('messages.lead.source')) }}<span class="required">*</span>
            {{ Form::select('source_id', $data['sources'], null, ['id' => 'source_id', 'class' => 'form-control', 'placeholder' => __('messages.placeholder.select_source'), 'required']) }}
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-3">
        <div class="form-group">
            {{ Form::label('employees', __('messages.lead.employees')) }}
            {{ Form::text('employees', null, ['class' => 'form-control', 'autocomplete' => 'off']) }}
        </div>
    </div>

    <div class="col-md-3">
        <div class="form-group">
            {{ Form::label('branches', __('messages.lead.branches')) }}
            {{ Form::text('branches', null, ['class' => 'form-control', 'autocomplete' => 'off']) }}
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            {{ Form::label('business', __('messages.lead.business')) }}
            {{ Form::text('business', null, ['class' => 'form-control', 'autocomplete' => 'off']) }}
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            {{ Form::label('automation', __('messages.lead.automation')) }}<span class="required">*</span>
            {{ Form::select('automation', [1 => 'Yes', 0 => 'No'], 0, ['class' => 'form-control', 'required', 'autocomplete' => 'off']) }}
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-3">
        <div class="form-group">
            {{ Form::label('status', __('messages.common.status')) }}<span class="required">*</span>
            {{ Form::select('status_id', $data['status'], null, ['id' => 'status_Id', 'class' => 'form-control', 'placeholder' => __('messages.placeholder.select_status'), 'required']) }}
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            {{ Form::label('default_language', __('messages.customer.default_language')) }}
            {{ Form::select('default_language', $data['languages'], null, ['id' => 'language_Id', 'class' => 'form-control', 'placeholder' => __('messages.placeholder.select_language')]) }}
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            {{ Form::label('mobile', __('messages.lead.mobile')) }}<span class="required">*</span><br>
            {{ Form::tel('mobile', null, ['class' => 'form-control', 'required', 'id' => 'phoneNumber', 'onkeyup' => 'if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g,"")']) }}
            {{ Form::hidden('prefix_code', old('prefix_code'), ['id' => 'prefix_code']) }}
            <span id="valid-msg" class="hide">{{ __('messages.placeholder.valid_number') }}</span>
            <span id="error-msg" class="hide"></span>
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            {{ Form::label('whatsapp', __('messages.lead.whatsapp')) }}
            {{ Form::text('whatsapp', null, ['class' => 'form-control', 'autocomplete' => 'off']) }}
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-3">
        <div class="form-group">
            {{ Form::label('phone', __('messages.lead.phone')) }}
            {{ Form::text('phone', null, ['class' => 'form-control', 'autocomplete' => 'off']) }}
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            {{ Form::label('fax', __('messages.lead.fax')) }}
            {{ Form::text('fax', null, ['class' => 'form-control', 'autocomplete' => 'off']) }}
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            {{ Form::label('email', __('messages.lead.email')) }}
            {{ Form::text('email', null, ['class' => 'form-control', 'autocomplete' => 'off']) }}
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            {{ Form::label('website', __('messages.lead.website')) }}
            {{ Form::text('website', null, ['class' => 'form-control', 'autocomplete' => 'off']) }}
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-3">
        <div class="form-group">
            {{ Form::label('country_id', __('messages.customer.country') . ':') }}<span class="required">*</span>
            {{ Form::select('country_id', $countries, null, ['id' => 'country_id', 'required', 'class' => 'form-control', 'placeholder' => __('messages.placeholder.select_country')]) }}
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            {{ Form::label('state_id', __('messages.lead.state') . ':') }}<span class="required">*</span>
            {{ Form::select('state_id', [], null, ['id' => 'state_id', 'required', 'class' => 'form-control', 'placeholder' => __('messages.lead.select_state')]) }}
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            {{ Form::label('city_id', __('messages.lead.city') . ':') }}
            {{ Form::select('city_id', [], null, ['id' => 'city_id', 'class' => 'form-control', 'placeholder' => __('messages.lead.select_city')]) }}
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            {{ Form::label('area_id', __('messages.lead.area') . ':') }}
            {{ Form::select('area_id', [], null, ['id' => 'area_id', 'class' => 'form-control', 'placeholder' => __('messages.lead.select_area')]) }}
        </div>
    </div>
</div>



<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            {{ Form::label('facebook', __('messages.lead.facebook')) }}
            {{ Form::text('facebook', null, ['class' => 'form-control', 'autocomplete' => 'off']) }}
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            {{ Form::label('instagram', __('messages.lead.instagram')) }}
            {{ Form::text('instagram', null, ['class' => 'form-control', 'autocomplete' => 'off']) }}
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            {{ Form::label('linkedin', __('messages.lead.linkedin')) }}
            {{ Form::text('linkedin', null, ['class' => 'form-control', 'autocomplete' => 'off']) }}
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            {{ Form::label('location', __('messages.lead.location')) }}
            {{ Form::text('location', null, ['class' => 'form-control', 'autocomplete' => 'off']) }}
        </div>
    </div>
</div>
<div class="row">
    <div class="form-group col-md-12 ">
        {{ Form::label('description', __('messages.lead.notes') . ':') }}
        {{ Form::textarea('description', null, ['class' => 'form-control', 'id' => 'leadDescription', 'placeholder' => __('messages.common.description')]) }}
    </div>
</div>


<div class="row justify-content-end mr-1">
    <input type="hidden" name="inserted_by" value="{{ auth()->user()->id }}">
    {{ Form::button(__('messages.common.submit'), ['type' => 'submit', 'class' => 'btn btn-primary', 'id' => 'btnSave']) }}
</div>
