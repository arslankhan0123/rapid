@extends('estimates.show')
@section('section')
    <div class="my-3 d-flex justify-content-between flex-sm-row flex-column">
        <div>
            {{-- <a href="#"
               class="btn text-white mt-sm-0 mt-2 mb-sm-0 mb-2 status-{{ \App\Models\Estimate::STATUS[$estimate->status] }}">
                {{ \App\Models\Estimate::STATUS[$estimate->status] }}
            </a> --}}
        </div>
        <div class="d-flex justify-content-end align-items-center">
            <div class="dropdown d-inline">

                <a href="{{ route('estimate.pdf', ['estimate' => $estimate->id]) }}" class="btn btn-warning">
                    {{ __('messages.common.download_as_pdf') }}
                </a>
                {{-- <div class="dropdown-menu dropdown-menu-right">
                    <a href="{{ route('estimate.pdf', ['estimate' => $estimate->id]) }}" class="dropdown-item">
                        {{ __('messages.common.download_as_pdf') }}
                    </a>
                    <a href="{{ route('estimate.view-as-customer', $estimate->id) }}"
                        class="dropdown-item text-content-wrap" data-toggle="tooltip" data-placement="bottom"
                        title="{{ __('messages.estimate.view_estimate_as_customer') }}"
                        data-delay='{"show":"500", "hide":"50"}'>
                        {{ __('messages.estimate.view_estimate_as_customer') }}</a>
                    @if ($estimate->status != \App\Models\Estimate::STATUS_DRAFT && $estimate->status != \App\Models\Estimate::STATUS_SEND && $estimate->status != \App\Models\Estimate::STATUS_EXPIRED && $estimate->status != \App\Models\Estimate::STATUS_DECLINED && $estimate->status != \App\Models\Estimate::STATUS_ACCEPTED)
                        <a class="dropdown-item text-content-wrap" href="#" id="markAsDraft" data-status="0"
                            data-toggle="tooltip" data-placement="bottom"
                            title="{{ __('messages.estimate.mark_as_draft') }}"
                            data-delay='{"show":"500", "hide":"50"}'>{{ __('messages.estimate.mark_as_draft') }}</a>
                    @endif
                    @if ($estimate->status != \App\Models\Estimate::STATUS_SEND && $estimate->status != \App\Models\Estimate::STATUS_DRAFT)
                        <a class="dropdown-item text-content-wrap" href="#" id="markAsSend" data-status="1"
                            data-toggle="tooltip" data-placement="bottom" title="{{ __('messages.estimate.mark_as_send') }}"
                            data-delay='{"show":"500", "hide":"50"}'>{{ __('messages.estimate.mark_as_send') }}</a>
                    @endif
                    @if ($estimate->status != \App\Models\Estimate::STATUS_EXPIRED && $estimate->status != \App\Models\Estimate::STATUS_DRAFT)
                        <a class="dropdown-item text-content-wrap" href="#" id="markAsExpired" data-status="2"
                            data-toggle="tooltip" data-placement="bottom"
                            title="{{ __('messages.estimate.mark_as_expired') }}"
                            data-delay='{"show":"500", "hide":"50"}'>{{ __('messages.estimate.mark_as_expired') }}</a>
                    @endif
                    @if ($estimate->status != \App\Models\Estimate::STATUS_DECLINED && $estimate->status != \App\Models\Estimate::STATUS_DRAFT)
                        <a class="dropdown-item text-content-wrap" href="#" id="markAsDeclined" data-status="3"
                            data-toggle="tooltip" data-placement="bottom"
                            title="{{ __('messages.estimate.mark_as_declined') }}"
                            data-delay='{"show":"500", "hide":"50"}'>{{ __('messages.estimate.mark_as_declined') }}</a>
                    @endif
                    @if ($estimate->status != \App\Models\Estimate::STATUS_ACCEPTED && $estimate->status != \App\Models\Estimate::STATUS_DRAFT)
                        <a class="dropdown-item text-content-wrap" href="#" id="markAsAccepted" data-status="4"
                            data-toggle="tooltip" data-placement="bottom"
                            title="{{ __('messages.estimate.mark_as_accepted') }}"
                            data-delay='{"show":"500", "hide":"50"}'>{{ __('messages.estimate.mark_as_accepted') }}</a>
                    @endif
                </div> --}}
            </div>
            {{-- <div class="dropdown d-inline">
                <button class="btn btn-primary dropdown-toggle ml-1 mobile-font-size" type="button" id="dropdownMenuButton"
                    data-toggle="dropdown" aria-haspopup="true"
                    aria-expanded="true">{{ __('messages.estimate.convert_estimate') }}
                </button>
                <div class="dropdown-menu dropdown-menu-right">
                    <a href="#" class="dropdown-item" id="convertToInvoice">{{ __('messages.invoice.invoice') }}</a>
                </div>
            </div> --}}
        </div>
    </div>
    <hr>
    <div class="row">

        <div class="form-group col-md-4 col-12">
            {{ Form::label('customer', __('messages.estimate.customer_name')) }}
            <p class="anchor-underline">{{ html_entity_decode($estimate->customer_name ?? '') }}</p>
        </div>
        <div class="form-group col-md-4 col-12">
            {{ Form::label('customer', __('messages.estimate.reference')) }}
            <p class="anchor-underline">{{ html_entity_decode($estimate->reference ?? '') }}</p>
        </div>
        <div class="form-group col-md-4 col-12">
            {{ Form::label('estimate_number', __('messages.estimate.estimate_number')) }}
            <p>{{ $estimate->estimate_number }}</p>
        </div>

        <div class="form-group col-md-4 col-12">
            {{ Form::label('estimate_date', __('messages.estimate.estimate_date')) }}
            <p>{{ Carbon\Carbon::parse($estimate->estimate_date)->translatedFormat('d-m-Y') }}</p>
        </div>
        <div class="form-group col-md-4 col-12">
            {{ Form::label('reference', __('messages.company.mobile')) }}
            <p>{{ !empty($estimate->mobile) ? html_entity_decode($estimate->mobile) : __('messages.common.n/a') }}
            </p>
        </div>
        <div class="form-group col-md-4 col-12">
            {{ Form::label('reference', __('messages.company.address')) }}
            <p>{{ !empty($estimate->address) ? html_entity_decode($estimate->address) : __('messages.common.n/a') }}
            </p>
        </div>
        <div class="form-group col-md-4 col-12">
            {{ Form::label('reference', __('messages.estimate.email')) }}
            <p>{{ !empty($estimate->email) ? html_entity_decode($estimate->email) : __('messages.common.n/a') }}
            </p>
        </div>

        <div class="form-group col-md-4 col-12">
            {{ Form::label('expiry_date', __('messages.estimate.expiry_date')) }}
            <p>{{ isset($estimate->estimate_expiry_date) ? Carbon\Carbon::parse($estimate->estimate_expiry_date)->translatedFormat('d-m-Y') : __('messages.common.n/a') }}
            </p>
        </div>
        {{-- <div class="form-group col-md-4 col-12">
            {{ Form::label('sales_agent_id', __('messages.invoice.sale_agent') ) }}
            <p>
                @if (!empty($estimate->sales_agent_id))
                    <a href="{{ url('admin/members/' . $estimate->sales_agent_id) }}" class="anchor-underline">
                        {{ html_entity_decode($estimate->user->full_name) }}
                    </a>
                @else
                    {{ __('messages.common.n/a') }}
                @endif
            </p>
        </div>
        <div class="form-group col-md-4 col-12">
            {{ Form::label('currency', __('messages.invoice.currency') ) }}
            <p>{{ isset($estimate->currency) ? $estimate->getCurrencyText($estimate->currency) : __('messages.common.n/a') }}
            </p>
        </div>
        <div class="form-group col-md-4 col-12">
            {{ Form::label('discount_type', __('messages.invoice.discount_type') ) }}
            <p>{{ isset($estimate->discount_type) ? $estimate->getDiscountTypeText($estimate->discount_type) : __('messages.common.n/a') }}
            </p>
        </div> --}}



        <div class="form-group col-md-4 col-12">
            {{ Form::label('created_at', __('messages.common.created_on')) }}
            <p><span data-toggle="tooltip" data-placement="right"
                    title="{{ Carbon\Carbon::parse($estimate->created_at)->translatedFormat('jS M, Y') }}">{{ $estimate->created_at->diffForHumans() }}</span>
            </p>
        </div>
        <div class="form-group col-md-4 col-12">
            {{ Form::label('updated_at', __('messages.common.last_updated')) }}
            <p><span data-toggle="tooltip" data-placement="right"
                    title="{{ Carbon\Carbon::parse($estimate->updated_at)->translatedFormat('jS M, Y') }}">{{ $estimate->updated_at->diffForHumans() }}</span>
            </p>
        </div>

        {{-- <div class="col-12">
            <div class="row">
                @foreach ($estimate->estimateAddresses as $address)
                    @if ($address->type == 1)
                        <div class="form-group col-md-4 col-12">
                            {{ Form::label('bill_to', __('messages.invoice.bill_to') ) }}
                            <div>{{ html_entity_decode($address->street) }},</div>
                            <div>{{ $address->city }},</div>
                            <div>{{ $address->state }},</div>
                            <div>{{ $address->country }},</div>
                            <div>{{ $address->zip_code }}</div>
                        </div>
                    @else
                        <div class="form-group col-md-4 col-12">
                            {{ Form::label('bill_to', __('messages.invoice.ship_to') ) }}
                            <div>{{ html_entity_decode($address->street) }},</div>
                            <div>{{ $address->city }},</div>
                            <div>{{ $address->state }},</div>
                            <div>{{ $address->country }},</div>
                            <div>{{ $address->zip_code }}</div>
                        </div>
                    @endif
                @endforeach
            </div>
        </div> --}}
        <div class="table-responsive"> <!-- Responsive wrapper for the table -->
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>{{ __('messages.estimate.sl') }}</th>
                        <th>{{ __('messages.department.departments') }}</th>
                        <th class="text-right itemRate">{{ __('messages.estimate.rate') }}</th>
                        <th class="text-right itemTotal">Remarks</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($employeeQuotations as $quotation)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <div style="min-width:150px;">
                                    {{ $quotation->employee->name ?? '' }}
                                </div>
                            </td>
                            <td class="text-right">{{ $quotation['rate'] }}</td>
                            <td class="text-right">{{ $quotation['remarks'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- <div class="items-container-footer d-flex w-100 justify-content-end">
            <table class="table float-right col-4 text-right">
                <tr>
                    <td class="font-weight-bold">{{ __('messages.estimate.sub_total')  }}</td>
                    <td class="amountData"><i class="{{ getCurrencyClassFromIndex($estimate->currency) }}"></i>
                        {{ number_format($estimate->sub_total, 2) }}
                    </td>
                </tr>
                <tr>
                    <td class="font-weight-bold">{{ __('messages.estimate.discount')  }}</td>
                    <td>{{ formatNumber($estimate->discount) }}{{ isset($estimate->discount_symbol) && $estimate->discount_symbol == 1 ? '%' : '' }}
                    </td>
                </tr>
                @foreach ($estimate->salesTaxes as $commonTax)
                    <tr>
                        <td class="font-weight-bold">{{ __('messages.products.tax') }} {{ $commonTax->tax }}<i
                                class="fas fa-percentage"></i></td>
                        <td class="itemRate"><i class="{{ getCurrencyClassFromIndex($estimate->currency) }}"></i>
                            {{ number_format($commonTax->amount, 2) }}
                        </td>
                    </tr>
                @endforeach
                <tr>
                    <td class="font-weight-bold">{{ __('messages.estimate.adjustment')  }}</td>
                    <td class="itemRate"><i class="{{ getCurrencyClassFromIndex($estimate->currency) }}"></i>
                        {{ number_format($estimate->adjustment, 2) }}
                    </td>
                </tr>
                <tr>
                    <td class="font-weight-bold">{{ __('messages.estimate.total')  }}</td>
                    <td class="amountData"><i class="{{ getCurrencyClassFromIndex($estimate->currency) }}"></i>
                        {{ number_format($estimate->total_amount, 2) }}
                    </td>
                </tr>

            </table>
        </div> --}}

        <div class="row " style="margin-top: 20px;">
            <div class="form-group col-lg-12 col-md-12 col-sm-12">
                {{ Form::label('terms_conditions', __('messages.estimate.terms_conditions')) }}
                <br>{!! !empty($estimate->term_conditions)
                    ? html_entity_decode($estimate->term_conditions)
                    : __('messages.common.n/a') !!}
            </div>
        </div>
    </div>
    </div>
@endsection
