<div class="row">
    <input type="hidden" name="group" value="{{ \App\Models\Setting::COMPANY_INFORMATION }}">
    <div class="form-group col-md-3">
        {{ Form::label('company', __('messages.company.code')) }}<span class="required">*</span>
        {{ Form::text('code', $settings['code'] ?? null, ['class' => 'form-control', 'required', 'placeholder' => __('messages.company.code')]) }}
    </div>
    <div class="form-group col-md-6">
        {{ Form::label('company', __('messages.company.company')) }}<span class="required">*</span>
        {{ Form::text('company', $settings['company'], ['class' => 'form-control', 'id' => 'companyNameId', 'required', 'placeholder' => __('messages.company.company')]) }}
    </div>
    <div class="form-group col-md-3">
        <div class="d-flex align-items-center justify-content-between">
            <label for="tax_number" class="mr-2">
                {{ __('messages.company.tax_number') }}<span class="required">*</span>
            </label>
            <label class="switch">
                <input type="checkbox" name="vat_status" value="1"
                    {{ isset($settings['vat_status']) && $settings['vat_status'] == 1 ? 'checked' : '' }}>
                <span class="slider round"></span>
            </label>
        </div>

        {{ Form::text('vat_number', $settings['vat_number'] ?? null, ['class' => 'form-control', 'required', 'placeholder' => __('messages.company.tax_number')]) }}
    </div>



    <div class="form-group col-sm-3">
        {{ Form::label('current_currency', __('messages.company.currency')) }}<span class="required">*</span>
        {{ Form::select('current_currency', $currencies, $settings['current_currency'] ?? null, ['class' => 'form-control', 'id' => 'currencySelect']) }}
        {{-- <select id="mySelect" data-show-content="true" class="form-control" name="current_currency" required>
            @foreach (\App\Models\Setting::CURRENCIES as $key => $currency)
                <option data-content="<i class='{{ getCurrencyClass($key) }} text-black-50'></i> {{ $currency }}"
                    value="{{ $key }}" {{ getCurrentCurrency() == $key ? 'selected' : '' }}>
                    &#{{ getCurrencyForPDF($key) }} {{ $currency }}
                </option>
            @endforeach
        </select> --}}
    </div>
    <div class="form-group col-md-6">
        {{ Form::label('address', __('messages.company.bank_details')) }}
        {{ Form::textarea('bank_details', $settings['bank_details'] ?? null, ['class' => 'form-control ', 'id' => 'bank_details', 'rows' => '2', 'placeholder' => __('messages.company.bank_details')]) }}
    </div>
    <div class="form-group col-md-3">
        {{ Form::label('phone', __('messages.company.phone')) }}<br>
        {{ Form::tel('phone', $settings['phone'], ['class' => 'form-control', 'id' => 'phoneNumber', 'onkeyup' => 'if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g,"")']) }}
        {{ Form::hidden('prefix_code', old('prefix_code'), ['id' => 'prefix_code']) }}
        <span id="valid-msg" class="hide">{{ __('messages.placeholder.valid_number') }}</span>
        <span id="error-msg" class="hide"></span>
    </div>
    <div class="form-group col-md-3">
        {{ Form::label('fax', __('messages.company.fax')) }}
        {{ Form::text('fax', $settings['fax'] ?? null, ['class' => 'form-control', 'minLength' => '4', 'maxLength' => '15', 'placeholder' => __('messages.company.fax')]) }}
    </div>
    <div class="form-group col-md-3">
        {{ Form::label('mobile', __('messages.company.mobile')) }}
        {{ Form::text('mobile', $settings['mobile'] ?? null, ['class' => 'form-control', 'minLength' => '4', 'maxLength' => '15', 'placeholder' => __('messages.company.mobile')]) }}
    </div>
    <div class="form-group col-md-6">
        {{ Form::label('email', __('messages.company.email')) }}
        {{ Form::text('email', $settings['email'] ?? null, ['class' => 'form-control', 'placeholder' => __('messages.company.email')]) }}
    </div>
    <div class="form-group col-md-3">
        {{ Form::label('website', __('messages.company.website')) }}
        {{ Form::text('website', $settings['website'], ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => __('messages.company.website')]) }}
    </div>



    <div class="form-group col-md-3">
        {{ Form::label('country_id', __('messages.cities.country')) }}<span class="required">*</span>
        {{ Form::select('country_code', $countries, $settings['country_code'] ?? null, ['class' => 'form-control', 'required', 'id' => 'country_select', 'placeholder' => __('messages.cities.select_country')]) }}
    </div>

    <div class="form-group col-md-3">
        {{ Form::label('state_id', __('messages.cities.state')) }}<span class="required">*</span>
        {{ Form::select('state', [], $settings['state'] ?? null, ['class' => 'form-control', 'required', 'id' => 'state_select', 'placeholder' => __('messages.cities.select_state')]) }}
    </div>
    <div class="form-group col-md-3">
        {{ Form::label('city', __('messages.company.city')) }}
        {{ Form::text('city', $settings['city'], ['class' => 'form-control', 'placeholder' => __('messages.company.city')]) }}
    </div>
    <div class="form-group col-md-3">
        {{ Form::label('post_code', __('messages.company.post_code')) }}
        {{ Form::text('zip_code', $settings['zip_code'] ?? null, ['class' => 'form-control', 'minLength' => '4', 'maxLength' => '15', 'placeholder' => __('messages.company.post_code')]) }}
    </div>
    <div class="form-group col-md-3">
        {{ Form::label('address', __('messages.company.address')) }}
        {{ Form::textarea('address', $settings['address'], ['class' => 'form-control ', 'id' => 'addressId', 'rows' => '2', 'placeholder' => __('messages.company.address')]) }}
    </div>
    <div class="form-group col-sm-3">
        {{ Form::label('license_number', 'License Number') }}
        {{ Form::text('license_number', $settings['license_number'] ?? '', ['class' => 'form-control', 'placeholder' => 'Enter License Number']) }}
    </div>


    <div class="form-group col-sm-3">
        {{ Form::label('timezone', __('messages.company.timezone')) }}
        <select name="timezone" id="timezone">
            @foreach (timezone_identifiers_list() as $timezone)
                <option value="{{ $timezone }}"
                    {{ old('timezone', $settings['timezone'] ?? 'Asia/Riyadh') == $timezone ? 'selected' : '' }}>
                    {{ $timezone }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="form-group col-sm-3">
        {{ Form::label('date_format', __('messages.company.date_format')) }}<span class="required">*</span>
        {{ Form::select('date_format', $dateFormats, $settings['date_format'] ?? null, ['class' => 'form-control', 'id' => 'date_formatSelect']) }}
    </div>
    {{-- <div class="form-group col-sm-3">
        {{ Form::label('print_format', __('messages.common.print_format')) }}<span class="required">*</span>
        {{ Form::select('print_format', [1 => 'Format 1', 2 => 'Format 2'], $settings['print_format'] ?? 1, ['class' => 'form-control']) }}

    </div> --}}
    <div class="col-sm-9"></div>

    <div class="form-group col-xl-5 col-md-12">
        <div class="row">
            <div class="px-3">
                <label class="profile-label-color-upload profile-label-color">{{ __('messages.setting.logo') }} (PNG
                    only)<span class="required">*</span></label>
                <label class="image__file-upload"> {{ __('messages.setting.choose') }}
                    {{ Form::file('logo', ['id' => 'logo', 'class' => 'd-none', 'accept' => 'image/png']) }}
                </label>
            </div>
            <div class="px-3 preview-image-video-container">
                <img id='logoPreview' class="img-thumbnail thumbnail-preview tPreview"
                    src="{{ $settings['logo'] ? $settings['logo'] : asset('assets/img/infyom-logo.png') }}">
            </div>
        </div>
    </div>
    <div class="form-group col-xl-7 col-md-6">
        <div class="row">
            {{-- <div class=" col-md-3">
                {{ Form::label('favicon', __('messages.setting.favicon'), ['class' => 'profile-label-color']) }}<span
                    class="required">*</span>
                <label class="image__file-upload"> {{ __('messages.setting.choose') }}
                    {{ Form::file('favicon', ['id' => 'favicon', 'class' => 'd-none', 'accept' => 'image/*']) }}
                </label>
            </div> --}}
            <div class="form-group col-md-3">
                {{ Form::label('favicon', __('messages.setting.favicon'), ['class' => 'profile-label-color']) }}
                <p style="color:#555;">Static Icon is being used</p>
            </div>
            <div class="col-md-2 preview-image-video-container">
                <img id='faviconPreview' class="img-thumbnail thumbnail-preview tPreview faviconPreview"
                    src="{{ $settings['favicon'] ? $settings['favicon'] : asset('assets/img/infyom-logo.png') }}">
            </div>
            <div class="form-group col-md-3">
                {{ Form::label('post_code', __('messages.company.overtime_rate')) }}
                {{ Form::number('overtime_rate', $settings['overtime_rate'] ?? 0, ['class' => 'form-control text-right']) }}
            </div>
        </div>
    </div>

    <div class="form-group col-md-3">
        <div class="d-flex align-items-center">
            <label class="switch mb-0 mr-2">
                <input type="checkbox" name="is_round" value="1"
                    {{ isset($settings['is_round']) && $settings['is_round'] == 1 ? 'checked' : '' }}>
                <span class="slider round"></span>
            </label>
            <label for="is_round" class="mb-0">
                {{ __('Enable Rounding') }}
            </label>
        </div>
    </div>



</div>

<div class="row justify-content-end mr-1">
    {{ Form::submit(__('messages.common.save'), ['class' => 'btn btn-primary', 'style' => 'line-height:30px;']) }}
</div>
