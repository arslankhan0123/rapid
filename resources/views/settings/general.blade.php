<div class="row">
    <input type="hidden" name="group" value="{{\App\Models\Setting::GROUP_GENERAL}}">
    <div class="form-group col-sm-6">
        {{ Form::label('company_name', __('messages.setting.application_name').':') }}<span
                class="required">*</span>
        {{ Form::text('company_name', $settings['company_name'], ['class' => 'form-control','id' => 'applicationNameId', 'required','placeholder'=> __('messages.setting.application_name')]) }}
    </div>
    <div class="form-group col-sm-6">
        {{ Form::label('default_country_code', __('messages.common.default_country_code').':', ['class' => 'form-label']) }}
        <span class="required"></span>
        {{ Form::text('default_country_data', null, ['class' => 'form-control','placeholder'=>__('messages.common.default_country_code'), 'id'=>'defaultCountryData']) }}
        {{ Form::hidden('default_country_code',$settings['default_country_code'],['id'=>'defaultCountryCode',]) }}
    </div>
</div>
<div class="row">
    <div class="form-group col-md-12 col-lg-12 col-sm-12">
        {{ Form::label('term_and_conditions',__('messages.common.term_and_conditions').':') }}
        {{ Form::textarea('term_and_conditions',$settings['term_and_conditions'],['class' => 'form-control summernote-simple']) }}
    </div>
</div>
<div class="row flex-responsive-column">
    <!-- Logo Field -->
    <div class="form-group col-xl-5 col-sm-12">
        <div class="row">
            <div class="px-3">
                <label class="profile-label-color-upload profile-label-color">{{ __('messages.setting.logo').':' }}<span
                            class="required">*</span></label>
                <label class="image__file-upload"> {{ __('messages.setting.choose') }}
                    {{ Form::file('logo',['id'=>'logo','class' => 'd-none','accept' => 'image/*']) }}
                </label>
            </div>
            <div class="px-3 preview-image-video-container">
                <img id='logoPreview' class="img-thumbnail thumbnail-preview tPreview"
                     src="{{($settings['logo']) ?$settings['logo'] : asset('assets/img/infyom-logo.png')}}">
            </div>
        </div>
    </div>
    <div class="form-group col-xl-5 col-sm-12">
        <div class="row">
            <div class="px-3">
                {{ Form::label('favicon', __('messages.setting.favicon').':',['class' => 'profile-label-color']) }}<span
                        class="required">*</span>
                <label class="image__file-upload"> {{ __('messages.setting.choose') }}
                    {{ Form::file('favicon',['id'=>'favicon','class' => 'd-none','accept'=> 'image/*']) }}
                </label>
            </div>
            <div class="px-3 preview-image-video-container">
                <img id='faviconPreview' class="img-thumbnail thumbnail-preview tPreview faviconPreview"
                     src="{{($settings['favicon']) ?$settings['favicon'] : asset('assets/img/infyom-logo.png')}}">
            </div>
        </div>
    </div>
    <div class="form-group col-xl-2 col-sm-12">
        <div class="row">
            <div class="px-3">
                <label for="version"
                       class="profile-label-color">{{ __('messages.setting.current_version').':' }}</label>
                <p>{{ version() }}</p>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <!-- Submit Field -->
    <div class="form-group col-sm-12">
        {{ Form::submit(__('messages.common.save'), ['class' => 'btn btn-primary']) }}
    </div>
</div>
