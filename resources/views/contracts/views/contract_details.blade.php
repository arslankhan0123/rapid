@extends('contracts.show')
@section('section')
    <div class="row">
        <div class="col-md-3">
            <div class="form-group">
                {{ Form::label('subject', __('messages.contract.subject').':') }}
                <p>{{ html_entity_decode($contract->subject) }}</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                {{ Form::label('customer_id', __('messages.contract.customer_id').':') }}
                <p><a href="{{ url('admin/customers',$contract->customer->id) }}"
                      class="anchor-underline">{{ html_entity_decode($contract->customer->company_name) }}</a></p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                {{ Form::label('contract_type_id', __('messages.contract.contract_type_id').':') }}
                <p>{{ html_entity_decode($contract->type->name) }}</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                {{ Form::label('contract_value', __('messages.contract.contract_value').':') }}
                <p>
                    @if((isset($contract->contract_value)))
                        <span><i class="{{ getCurrencyClass(getCurrentCurrency()) }}"></i></span>
                    @endif
                    {{ (isset($contract->contract_value)) ? number_format($contract->contract_value, 2) : __('messages.common.n/a') }}
                </p>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-3">
            <div class="form-group">
                {{ Form::label('start_date', __('messages.contract.start_date').':') }}
                <p>{{ isset($contract->start_date) ? Carbon\Carbon::parse($contract->start_date)->translatedFormat('jS M, Y H:i A') : __('messages.common.n/a') }}</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                {{ Form::label('end_date', __('messages.contract.end_date').':') }}
                <p>{{ isset($contract->end_date) ? Carbon\Carbon::parse($contract->end_date)->translatedFormat('jS M, Y H:i A') : __('messages.common.n/a')}}</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                {{ Form::label('created_at', __('messages.common.created_on').':') }}<br>
                <span data-toggle="tooltip" data-placement="right"
                      title="{{ Carbon\Carbon::parse($contract->created_at)->translatedFormat('jS M, Y') }}">{{ $contract->created_at->diffForHumans() }}</span>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                {{ Form::label('updated_at', __('messages.common.last_updated').':') }}
                <br>
                <span data-toggle="tooltip" data-placement="right"
                      title="{{ Carbon\Carbon::parse($contract->updated_at)->translatedFormat('jS M, Y') }}">{{ $contract->updated_at->diffForHumans() }}</span>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                {{ Form::label('description', __('messages.contract.description').':') }}
                <br>
                {!! !empty($contract->description) ? html_entity_decode($contract->description) : __('messages.common.n/a')!!}
            </div>
        </div>
    </div>
@endsection
