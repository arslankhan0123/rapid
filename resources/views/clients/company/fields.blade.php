<div class="row">
    <div class="col-md-6 col-sm-12">
        <div class="form-group col-sm-12">
            {{ Form::label('company_name',__('messages.company.company').':') }}<span class="required">*</span>
            {{ Form::text('company_name',null,['class' => 'form-control', 'required','autocomplete' => 'off']) }}
        </div>
        <div class="form-group col-sm-12">
            {{ Form::label('phone',__('messages.company.phone').':') }}
            {{ Form::tel('phone',null,['class' => 'form-control', 'id' => 'phoneNumber', 'onkeyup' => 'if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g,"")']) }}
            {{ Form::hidden('prefix_code',null,['id'=>'prefix_code']) }}
            <span id="valid-msg" class="hide">{{ __('messages.placeholder.valid_number') }}</span>
            <span id="error-msg" class="hide"></span>
        </div>
        <div class="form-group col-sm-12">
            {{ Form::label('currency',__('messages.company.currency').':') }}
            <select id="currencyId" data-show-content="true" class="form-control" name="currency">
                <option value="0"
                        disabled="true" {{ isset($customer->currency) ? '' : 'selected' }}>{{__('messages.placeholder.select_currency')}}
                </option>
                @foreach($data['currencies'] as $key => $currency)
                    <option value="{{$key}}" {{ (isset($customer->currency) ? $customer->currency : getCurrencyIcon($key)) == $key ? 'selected' : ''}}>
                        &#{{getCurrencyIcon($key)}}&nbsp;&nbsp;&nbsp; {{$currency}}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="form-group col-sm-12">
            {{ Form::label('default_language',__('messages.customer.default_language').':') }}
            {{ Form::select('default_language',$data['default_language'],null,['class' => 'form-control','id' => 'languageId', 'placeholder' => __('messages.placeholder.select_language')]) }}
        </div>
    </div>
    <div class="col-md-6 col-sm-12">
        <div class="form-group col-sm-12">
            {{ Form::label('vat_number',__('messages.company.vat_number').':') }}
            {{ Form::text('vat_number',null,['class' => 'form-control','minLength' => '4', 'maxLength' => '15','autocomplete' => 'off']) }}
        </div>
        <div class="form-group col-sm-12">
            {{ Form::label('website',__('messages.customer.website').':') }}
            {{ Form::text('website',null,['class' => 'form-control','id' => 'website','autocomplete' => 'off']) }}
        </div>
        <div class="form-group col-sm-12">
            {{ Form::label('country',  __('messages.customer.country').':') }}
            {{ Form::select('country', $data['countries'], null, ['id'=>'countryId','class' => 'form-control','placeholder' => __('messages.placeholder.select_country')]) }}
        </div>
    </div>
</div>
<div class="text-right">
    {{ Form::submit(__('messages.common.save'), ['class' => 'btn btn-primary', 'id' => 'btnSave','data-loading-text'=>"<span class='spinner-border spinner-border-sm'></span> Processing..."]) }}
    <a href="{{ route('clients.dashboard') }}"
       class="btn btn-secondary text-dark">{{ __('messages.common.cancel') }}</a>
</div>

