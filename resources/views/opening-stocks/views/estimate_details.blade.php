@extends('opening-stocks.show')

@section('section')
    <hr>

    <div class="my-3 d-flex justify-content-between flex-sm-row flex-column">
        <div>
            <a href="#"
                class="btn text-white mt-sm-0 mt-2 mb-sm-0 mb-2 status-{{ \App\Models\Estimate::STATUS[$estimate->status] }}">
                {{ \App\Models\Estimate::STATUS[$estimate->status] }}
            </a>
        </div>

        <div class="d-flex justify-content-end align-items-center">
            <div class="dropdown d-inline">
                <button class="btn btnWarning text-white dropdown-toggle mr-1 mobile-font-size" type="button"
                    style="line-height: 30px;" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true"
                    aria-expanded="true">
                    {{ __('messages.estimate.more') }}
                </button>
                <div class="dropdown-menu dropdown-menu-right">
                    <a href="{{ route('opening-stocks.pdf', ['stock' => $estimate->id]) }}" class="dropdown-item">
                        {{ __('messages.common.download_as_pdf') }}
                    </a>
                    {{-- <a href="{{ route('estimate.view-as-customer', $estimate->id) }}"
                        class="dropdown-item text-content-wrap" data-toggle="tooltip" data-placement="bottom"
                        title="{{ __('messages.estimate.view_estimate_as_customer') }}"
                        data-delay='{"show":"500", "hide":"50"}'>
                        {{ __('messages.estimate.view_estimate_as_customer') }}</a> --}}
                    @if (
                        $estimate->status != \App\Models\Estimate::STATUS_DRAFT &&
                            $estimate->status != \App\Models\Estimate::STATUS_SEND &&
                            $estimate->status != \App\Models\Estimate::STATUS_EXPIRED &&
                            $estimate->status != \App\Models\Estimate::STATUS_DECLINED &&
                            $estimate->status != \App\Models\Estimate::STATUS_ACCEPTED)
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
                    @if (
                        $estimate->status != \App\Models\Estimate::STATUS_EXPIRED &&
                            $estimate->status != \App\Models\Estimate::STATUS_DRAFT)
                        <a class="dropdown-item text-content-wrap" href="#" id="markAsExpired" data-status="2"
                            data-toggle="tooltip" data-placement="bottom"
                            title="{{ __('messages.estimate.mark_as_expired') }}"
                            data-delay='{"show":"500", "hide":"50"}'>{{ __('messages.estimate.mark_as_expired') }}</a>
                    @endif
                    @if (
                        $estimate->status != \App\Models\Estimate::STATUS_DECLINED &&
                            $estimate->status != \App\Models\Estimate::STATUS_DRAFT)
                        <a class="dropdown-item text-content-wrap" href="#" id="markAsDeclined" data-status="3"
                            data-toggle="tooltip" data-placement="bottom"
                            title="{{ __('messages.estimate.mark_as_declined') }}"
                            data-delay='{"show":"500", "hide":"50"}'>{{ __('messages.estimate.mark_as_declined') }}</a>
                    @endif
                    @if (
                        $estimate->status != \App\Models\Estimate::STATUS_ACCEPTED &&
                            $estimate->status != \App\Models\Estimate::STATUS_DRAFT)
                        <a class="dropdown-item text-content-wrap" href="#" id="markAsAccepted" data-status="4"
                            data-toggle="tooltip" data-placement="bottom"
                            title="{{ __('messages.estimate.mark_as_accepted') }}"
                            data-delay='{"show":"500", "hide":"50"}'>{{ __('messages.estimate.mark_as_accepted') }}</a>
                    @endif
                </div>
            </div>
            {{-- <div class="dropdown d-inline">
                <button class="btn btn-primary dropdown-toggle ml-1 mobile-font-size" type="button"
                    style="line-height: 30px;" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true"
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
        {{-- <div class="form-group col-md-4">
            {{ Form::label('title', __('messages.estimate.title')) }}
            <p>{{ html_entity_decode($estimate->title) }}</p>
        </div> --}}
        <div class="form-group col-md-4">
            {{ Form::label('estimate_number', "Document Number") }}
            <p>{{ $estimate->estimate_number }}</p>
        </div>
        <div class="form-group col-md-4">
            {{ Form::label('estimate_date', "Document Date") }}
            <p>{{ Carbon\Carbon::parse($estimate->estimate_date)->translatedFormat('d-m-Y') }}</p>
        </div>
        <div class="form-group col-md-4 d-none">
            {{ Form::label('customer', __('messages.opening-stocks.supplier')) }}
            <p><a href="#" class="anchor-underline">{{ html_entity_decode($estimate->customer_name) }}</a></p>
        </div>

        <div class="form-group col-md-4 d-none">
            {{ Form::label('admin_note', 'Payment Mode') }}
            <p>{{ $estimate->paymentMode?->name ?? '' }}</p>
        </div>
        <div class="form-group col-md-4 d-none">
            {{ Form::label('admin_note', 'Due Days') }}
            <p>{{ $estimate->due_days ?? '' }}</p>
        </div>


        <div class="form-group col-md-4 d-none">
            {{ Form::label('expiry_date', __('messages.estimate.expiry_date')) }}
            <p>{{ isset($estimate->estimate_expiry_date) ? Carbon\Carbon::parse($estimate->estimate_expiry_date)->translatedFormat('d-m-Y') : __('messages.common.n/a') }}
            </p>
        </div>
        <div class="form-group col-md-4  d-none">
            {{ Form::label('currency', __('messages.invoice.currency')) }}
            <p>{{ isset($estimate->currency) ? $estimate->getCurrencyText($estimate->currency) : __('messages.common.n/a') }}
            </p>
        </div>
        <div class="form-group col-md-4  d-none">
            {{ Form::label('sales_agent_id', __('messages.credit_note.reference')) }}
            <p>{{ $estimate->reference }}</p>
        </div>

        <div class="form-group col-md-4 d-none">
            {{ Form::label('admin_note', 'Address') }}
            <br>
            @if (!empty($estimate->estimateAddresses) && isset($estimate->estimateAddresses[0]))
                @php
                    $address = $estimate->estimateAddresses[0];
                @endphp
                {!! html_entity_decode($address['street']) !!},
                {!! html_entity_decode($address['city']) !!},
                {!! html_entity_decode($address['state']) !!},
                {!! html_entity_decode($address['zip_code']) !!},
                {!! html_entity_decode($address['country']) !!}
            @else
                {{ __('messages.common.n/a') }}
            @endif
        </div>


        <div class="form-group col-md-4 ">
            {{ Form::label('updated_at', __('messages.common.last_updated')) }}
            <p><span data-toggle="tooltip" data-placement="right"
                    title="{{ Carbon\Carbon::parse($estimate->updated_at)->translatedFormat('jS M, Y') }}">{{ $estimate->updated_at->diffForHumans() }}</span>
            </p>
        </div>


        <div class="form-group col-md-8 col-12  d-none">
            {{ Form::label('admin_note', 'Subject') }}
            <br>{!! !empty($estimate->admin_note) ? html_entity_decode($estimate->admin_note) : __('messages.common.n/a') !!}
        </div>
        <div class="form-group col-lg-6 col-md-12 col-sm-12">
            {{ Form::label('client_note', 'Remarks') }}
            <br>{!! !empty($estimate->client_note) ? html_entity_decode($estimate->client_note) : __('messages.common.n/a') !!}
        </div>
        <div class="col-lg-12">
            <label for=""><strong>Services </strong></label>
            <div class="row">

                <table class="table table-bordered table-responsive" id="itemsTable" style="width: 100%;">
                    <thead>
                        <tr>
                            <th class="p-0 m-0 text-center" style="width: 2%;">Sl</th>
                            <th class="p-0 m-0 text-center" style="width: 10%;">Item Code</th>
                            <th class="p-0 m-0 text-center">Item Name </th>
                            <th class="p-0 m-0 text-center" style="width: 3%;">Qty</th>
                            <th class="p-0 m-0 text-center" style="width: 3%;">Unit</th>
                            <th class="p-0 m-0 text-center">Rate </th>
                            {{-- <th style="text-align: center;">Disc<br>%</th> --}}
                            <th style="text-align: center;">Excluding VAT</th>
                            <th style="text-align: center;">VAT %</th>
                            <th style="text-align: center;">VAT Amount</th>
                            <th style="text-align: center;" class="p-0 m-0">Including VAT</th>
                        </tr>
                    </thead>
                    <tbody id="itemRows">

                        @foreach ($estimate->salesItems as $index => $item)
                            @php
                                $vatAmount =
                                    $item->quantity * $item->rate * (1 - $item->discount / 100) * ($item->tax / 100);
                                $includingVat = $item->quantity * $item->rate + $vatAmount;
                            @endphp
                            <tr>
                                <td class="p-0 m-0 text-center">{{ $index + 1 }}</td> <!-- Display the row number -->
                                <td>{{ $item->service->code ?? 'N/A' }}</td> <!-- Item Number -->

                                <td style="max-width:250px;">{{ $item->service->name ?? 'N/A' }}</td>
                                <!-- Item Name (Service title) -->
                                <!-- Item Name (Service title) -->
                                <td>{{ $item->quantity ?? 0 }}</td> <!-- Quantity -->
                                <td>{{ $item->unit?->title ?? 'N/A' }}</td>
                                <td style="width: 6%; text-align: right;">
                                    {{ number_format($item->rate, 2) }} <!-- Price -->
                                </td>
                                {{-- <td style="text-align: right;">
                                    {{ $item->discount ?? 0 }} <!-- Discount -->
                                </td> --}}

                                <!-- Excluding VAT (Quantity * Rate) - Discount -->
                                <td class="excluding-vat" style="text-align: right;">
                                    {{ number_format($item->quantity * $item->rate * (1 - $item->discount / 100), 2) }}
                                </td>

                                <!-- VAT % -->
                                <td style="width: 8%; text-align: center;" class="p-0">
                                    {{ number_format($item->tax, 2) }} %
                                </td>

                                <!-- VAT Amount (Excluding VAT * VAT %) -->
                                <td class="vat-amount" style="text-align: right;">
                                    {{ number_format($vatAmount ?? 0, 2) }}
                                </td>

                                <!-- Including VAT (Excluding VAT + VAT Amount) -->
                                <td style="width: 10%; text-align: right;">
                                    {{ number_format($includingVat, 2) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

            </div>

            <!-- Totals Table -->
            <div class="row justify-content-between mt-3 ml-2">
                <div class="col-md-6 p-0 m-0">
                    <strong> Amount In words</strong><br>
                    {{ ucfirst($words) }} Only <br><br><br>
                    <div class=" p-0 m-0 d-none">
                        <label for="terms_conditions"><strong>Terms & Conditions:</strong></label>
                        <div class="mt-3">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>SL</th>
                                        <th>Description</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Loop through the terms and display them -->
                                    @foreach ($estimate->terms as $index => $estimateTerm)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $estimateTerm['description'] }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>


                </div>
                <div class="col-md-6 col-lg-4 col-xl-5 p-0 m-0 mt-2">
                    <table class="table table-bordered " style="width: 100%;">
                        <tbody>
                            <tr class="d-none">
                                <th style="text-align: right">Total Before Discount</th>
                                <td class="text-right p-1" id="totalExcludingVAT">
                                    {{ number_format($totalIncludingVAT, 2) }} SAR</td>
                            </tr>
                            <tr class="d-none">
                                <th style="text-align: right">Discount
                                    {{ isset($estimate->discount_type) ? ($estimate->discount_type == 0 ? '%' : '$') : ' ' }}
                                </th>
                                <td class="text-right p-1">{{ number_format($estimate->discount, 2) }}</td>
                            </tr>
                            <tr class="d-none">
                                <th style="text-align: right">Total After Discount</th>
                                <td class="text-right p-1" id="taxable">
                                    {{ number_format($afterDiscount, 2) }} SAR
                                </td>
                            </tr>
                            <tr class="d-none">
                                <th style="text-align: right">Total VAT</th>
                                <td class="text-right p-1" id="totalVATAmount">{{ number_format($totalVATAmount, 2) }}
                                    SAR</td>

                            </tr>
                            <tr>
                                <th style="text-align: right">Total Net</th>
                                <td class="text-right p-1" id="totalNetAmount">{{ number_format($newTotal, 2) }}
                                    SAR</td>
                            </tr>
                        </tbody>

                    </table>
                </div>
            </div>
        </div>



    </div>
@endsection
