<div class="row">

    <div class="form-group col-sm-6">
        {{ Form::label('first_name', __('messages.member.first_name') . ':') }}<span class="required">*</span>
        {{ Form::text('first_name', null, ['class' => 'form-control', 'required', 'autocomplete' => 'off', 'placeholder' => __('messages.member.first_name')]) }}
    </div>
    <div class="form-group col-sm-3">
        {{ Form::label('last_name', __('messages.member.last_name') . ':') }}
        {{ Form::text('last_name', null, ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => __('messages.member.last_name')]) }}
    </div>
    <div class="form-group col-sm-3">
        {{ Form::label('employee_id', __('messages.employees.name') . ':') }}
        {{ Form::select('employee_id', $employees->pluck('name', 'id'), null, ['id' => 'employeeID', 'class' => 'form-control', 'placeholder' => __('messages.placeholder.select_employee')]) }}
    </div>
</div>
<div class="row">
    <div class="form-group col-sm-6">
        {{ Form::label('email', __('messages.common.email') . ':') }}<span class="required">*</span>
        {{ Form::email('email', null, ['class' => 'form-control', 'required', 'autocomplete' => 'off', 'placeholder' => __('messages.common.email')]) }}
    </div>
    <div class="form-group col-sm-3">
        {{ Form::label('password', __('messages.member.password') . ':') }}<span class="required">*</span>
        <div class="input-group">
            {{ Form::password('password', ['class' => 'form-control', 'id' => 'password', 'autocomplete' => 'off', 'required', 'min' => '6', 'max' => '10', 'placeholder' => __('messages.member.password')]) }}
            <div class="input-group-append" id="show_hide_password">
                <div class="input-group-text">
                    <button class="btn btn-default password-show" type="button"><i class="fa fa-eye-slash"
                            aria-hidden="true"></i></button>
                </div>
            </div>
        </div>
    </div>
    <div class="form-group col-sm-3">
        {{ Form::label('password', __('messages.member.password_confirmation') . ':') }}<span class="required">*</span>
        <div class="input-group">
            {{ Form::password('password_confirmation', ['class' => 'form-control', 'id' => 'cPassword', 'required', 'min' => '6', 'max' => '10', 'autocomplete' => 'off', 'placeholder' => __('messages.member.password_confirmation')]) }}
            <div class="input-group-append" id="show_hide_cPassword">
                <div class="input-group-text">
                    <button class="btn btn-default cPassword-show" type="button"><i class="fa fa-eye-slash"
                            aria-hidden="true"></i></button>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="form-group col-sm-6">
        {{ Form::label('phone', __('messages.member.phone') . ':') }}<span class="required">*</span><br>
        {{ Form::tel('phone', null, ['class' => 'form-control', 'required', 'id' => 'phoneNumber', 'onkeyup' => 'if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g,"")']) }}
        {{ Form::hidden('prefix_code', old('prefix_code'), ['id' => 'prefix_code']) }}
        <span id="valid-msg" class="hide">{{ __('messages.placeholder.valid_number') }}</span>
        <span id="error-msg" class="hide"></span>
    </div>
    <div class="form-group col-sm-6">
        {{ Form::label('facebook', __('messages.member.facebook') . ':') }}
        {{ Form::text('facebook', null, ['class' => 'form-control', 'id' => 'facebookUrl', 'autocomplete' => 'off', 'placeholder' => __('messages.member.facebook')]) }}
    </div>
</div>
<div class="row">
    <div class="form-group col-sm-6">
        {{ Form::label('linkedin', __('messages.member.linkedin') . ':') }}
        {{ Form::text('linkedin', null, ['class' => 'form-control', 'id' => 'linkedInUrl', 'autocomplete' => 'off', 'placeholder' => __('messages.member.linkedin')]) }}
    </div>
    <div class="form-group col-sm-6">
        {{ Form::label('skype', __('messages.member.skype') . ':') }}
        {{ Form::text('skype', null, ['class' => 'form-control', 'id' => 'skypeUrl', 'autocomplete' => 'off', 'placeholder' => __('messages.member.skype')]) }}
    </div>
</div>
<div class="row">
    <div class="form-group col-sm-6">
        {{ Form::label('default_language', __('messages.member.default_language') . ':') }}
        {{ Form::select('default_language', getLanguages(), null, ['id' => 'languageId', 'class' => 'form-control', 'placeholder' => __('messages.placeholder.select_language')]) }}
    </div>
    <div class="form-group  col-md-6">
        {{ Form::label('branches', __('messages.branches.branches')) }}
        {{ Form::select('branches[]', $branches ?? [], null, ['class' => 'form-control select2', 'multiple' => 'multiple']) }}
    </div>

</div>
<div class="row mb-3">
    <div class="form-group col-md-6">
        {{ Form::label('member_security', __('messages.member.member_security') . ':') }}
        <div class="custom-control custom-checkbox">
            <input type="checkbox" class="custom-control-input" name="staff_member" id="staffMember">
            <label class="custom-control-label" for="staffMember">{{ __('messages.member.staff_member') }}
            </label>
        </div>
        <div class="custom-control custom-checkbox">
            <input type="checkbox" class="custom-control-input" name="send_welcome_email" id="sendWelcomeEmail">
            <label class="custom-control-label" for="sendWelcomeEmail">{{ __('messages.member.send_welcome_email') }}
            </label>
        </div>
    </div>

</div>
<div class="row">
    <div class="form-group col-lg-3 col-md-6 col-sm-12">
        <span id="validationErrorsBox" class="text-danger"></span>
        <div class="row">
            <div class="col-6">
                {{ Form::label('logo', __('messages.member.profile') . ':', ['class' => 'profile-label-color']) }}
                <label class="image__file-upload text-white"> {{ __('messages.setting.choose') }}
                    {{ Form::file('image', ['id' => 'logo', 'class' => 'd-none', 'accept' => 'image/*']) }}
                </label>
            </div>
            <div class="col-2 pl-0 mt-1">
                <img id='logoPreview' class="img-thumbnail thumbnail-preview"
                    src="{{ asset('assets/img/infyom-logo.png') }}">
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="form-group col-sm-12">
        {{ Form::label('permissions', __('messages.member.permissions') . ':', ['class' => 'section-title']) }}
    </div>
</div>
<div class="row">
    @foreach ($permissionsArr as $type => $permissions)
        <div class="col-md-6 col-lg-4 col-xl-3 col-sm-4 permission-text">
            <div class="card-body">
                <div class="section-title mt-0">{{ $type }}</div>
                @foreach ($permissions as $permission)
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="customCheck{{ $permission['id'] }}"
                            name="permissions[]" value="{{ $permission['id'] }}">
                        <label class="custom-control-label" for="customCheck{{ $permission['id'] }}">
                            {{ $permission['display_name'] }}
                        </label>
                    </div>
                @endforeach
            </div>
        </div>
    @endforeach
</div>
<div class="row justify-content-end">
    <div class="form-group col-md-2 mr-2 ">
        <a href="{{ route('members.index') }}"
            class="btn btnCancel btn-secondary text-dark {{ app()->getLocale() === 'tr' ? 'mobile-btn-mt' : '' }}">{{ __('messages.common.cancel') }}</a>
        {{ Form::button(__('messages.common.submit'), ['type' => 'submit', 'class' => 'btn btn-primary', 'id' => 'btnSave', 'data-loading-text' => "<span class='spinner-border spinner-border-sm'></span> Processing..."]) }}

    </div>
</div>
