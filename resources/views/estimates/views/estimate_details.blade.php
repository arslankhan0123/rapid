@extends('estimates.show')
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

                    <a href="{{ route('estimates.edit', $estimate->id) }}"
                        class="dropdown-item text-content-wrap">{{ __('messages.common.edit') }}</a>

                    @can('export_quotations')
                        <a href="{{ route('estimate.pdf', ['estimate' => $estimate->id]) }}" class="dropdown-item">
                            {{ __('messages.common.download_as_pdf') }}
                        </a>
                    @endcan
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
        <div class="form-group col-md-3">
            {{ Form::label('sales_agent_id', __('messages.branches.name')) }}
            <p>{{ $estimate->branch?->name ?? '' }}</p>
        </div>
        <div class="form-group col-md-3">
            {{ Form::label('estimate_number', __('messages.estimate.estimate_number')) }}
            <p>{{ $estimate->estimate_number }}</p>
        </div>
        <div class="form-group col-md-3">
            {{ Form::label('estimate_date', __('messages.estimate.estimate_date')) }}
            <p>{{ Carbon\Carbon::parse($estimate->estimate_date)->translatedFormat('d-m-Y') }}</p>
        </div>
        <div class="form-group col-md-3">
            {{ Form::label('customer', __('messages.estimate.customer_name')) }}
            <p><a href="#" class="anchor-underline">{{ html_entity_decode($estimate->customer_name) }}</a></p>
        </div>
        <div class="form-group col-md-3">
            {{ Form::label('customer', __('messages.customer.vendor_code')) }}
            <p>{{ $estimate->vendor_code ?? '' }}</p>
        </div>
        <div class="form-group col-md-3">
            {{ Form::label('sales_agent_id', __('messages.estimate.customer_ref')) }}
            <p>{{ $estimate->reference }}</p>
        </div>


        {{-- <div class="form-group col-md-4">
            {{ Form::label('tags', __('messages.tags') ) }}
            <p>
                @forelse($estimate->tags as $tag)
                    <span class="badge border border-secondary mb-1">{{ html_entity_decode($tag->name) }} </span>
                @empty
                    {{ __('messages.common.n/a') }}
                @endforelse
            </p>
        </div> --}}

        <div class="form-group col-md-3">
            {{ Form::label('expiry_date', __('messages.estimate.expiry_date')) }}
            <p>{{ isset($estimate->estimate_expiry_date) ? Carbon\Carbon::parse($estimate->estimate_expiry_date)->translatedFormat('d-m-Y') : __('messages.common.n/a') }}
            </p>
        </div>
        <div class="form-group col-md-3">
            {{ Form::label('currency', __('messages.invoice.currency')) }}
            <p>{{ isset($estimate->currency) ? $estimate->getCurrencyText($estimate->currency) : __('messages.common.n/a') }}
            </p>
        </div>
        <div class="form-group col-md-3">
            {{ Form::label('sales_agent_id', __('messages.credit_note.reference')) }}
            <p>{{ $estimate->reference }}</p>
        </div>

        {{-- <div class="form-group col-md-4">
            {{ Form::label('discount_type', __('messages.invoice.discount_type') ) }}
            <p>{{ isset($estimate->discount_type) ? $estimate->getDiscountTypeText($estimate->discount_type) : __('messages.common.n/a') }}
            </p>
        </div> --}}

        {{-- <div class="form-group col-md-4">
            {{ Form::label('reference', __('messages.credit_note.reference') ) }}
            <p>{{ !empty($estimate->reference) ? html_entity_decode($estimate->reference) : __('messages.common.n/a') }}
            </p>
        </div> --}}
        <div class="form-group col-md-3">
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

        {{-- <div class="form-group col-md-4">
            {{ Form::label('created_at', __('messages.common.created_on')) }}
            <p><span data-toggle="tooltip" data-placement="right"
                    title="{{ Carbon\Carbon::parse($estimate->created_at)->translatedFormat('jS M, Y') }}">{{ $estimate->created_at->diffForHumans() }}</span>
            </p>
        </div> --}}
        <div class="form-group col-md-3">
            {{ Form::label('updated_at', __('messages.common.last_updated')) }}
            <p><span data-toggle="tooltip" data-placement="right"
                    title="{{ Carbon\Carbon::parse($estimate->updated_at)->translatedFormat('jS M, Y') }}">{{ $estimate->updated_at->diffForHumans() }}</span>
            </p>
        </div>
        <div class="form-group col-md-3 col-12">
            {{ Form::label('quality_no', 'Quality No') }}
            <br>{!! !empty($estimate->quality_no) ? html_entity_decode($estimate->quality_no) : __('messages.common.n/a') !!}
        </div>

        <div class="form-group col-md-3 col-12">
            {{ Form::label('admin_note', 'Subject') }}
            <br>{!! !empty($estimate->admin_note) ? html_entity_decode($estimate->admin_note) : __('messages.common.n/a') !!}
        </div>
        <div class="form-group col-lg-6 col-md-12 col-sm-12">
            {{ Form::label('client_note', 'Message') }}
            <br>{!! !empty($estimate->client_note) ? html_entity_decode($estimate->client_note) : __('messages.common.n/a') !!}
        </div>


        <div class="col-lg-12">

            <div class="row">
                <table class="table table-bordered table-responsive" id="itemsTable" style="width: 100%;">
                    <thead>
                        <tr style="padding: 0px;">
                            <th style="width: 5%;">S.</th>
                            <th style="width: 7%;">Item</th>
                            <th class="text-center" style="width: 10%;">Category</th>
                            <th class="text-center" style="width: 25%;">Description</th>
                            <th style="width: 2%;">Qty</th>
                            <th class="text-center pr-3" style="width: 8%;">Rate</th>
                            {{-- <th style="text-align:center;" class="p-0 pr-1" style="width: 5%;">Disc.</th> --}}
                            <th class="p-0 text-center pr-2 w-20" style="width: 7%;">Disc.</th>
                            <th class="p-0 text-center pr-2 w-20" style="width: 7%;">Net</th>
                        </tr>
                    </thead>
                    <tbody id="itemRows">
                        @foreach ($estimate->salesItems as $index => $item)
                            @php
                                // $netAmount = $item->quantity * $item->rate - $item->discount;
                                $netAmount = $item->quantity * $item->rate;
                            @endphp
                            <tr>
                                <td class="p-0 m-0 text-center">{{ $index + 1 }}</td> <!-- Display the row number -->
                                <td>{{ $item->item ?? 'N/A' }}</td> <!-- Item Number -->
                                <th>{{ $item->category->name ?? 'N/A' }}</th>
                                <td style="max-width:250px;">{{ $item->service->title ?? 'N/A' }}</td>
                                <!-- Item Name (Service title) -->
                                <td class="text-center ">{{ $item->quantity ?? 0 }}</td> <!-- Quantity -->
                                <td class="text-right pr-2">
                                    {{ number_format($item->rate, 2) }} <!-- Price -->
                                </td>
                                <td class="discount pr-1 text-right">
                                    {{ number_format($item->discount, 2) }}
                                </td>
                                <!-- Net Amount (Quantity * Rate - Discount) -->
                                <td style="width: 10%; text-align: right;" class="pr-1">
                                    {{ number_format($netAmount, 2) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="row">
                <table class="table table-bordered text-center">
                    <thead>
                        <tr>
                            <th>Subtotal</th>
                            <th>Total Discount</th>
                            @if ($isRound)
                                <th class="round-off-column">Round Off</th>
                            @endif
                            <th>Net Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center">{{ number_format($subtotal ?? 0, 2) }}</td>
                            <td class="text-center">{{ number_format($estimate->discount ?? 0, 2) }}</td>
                            @if ($isRound)
                                <td class="text-center round-off-column">
                                    {{ number_format($estimate->adjustment ?? 0, 2) }}</td>
                            @endif
                            <td class="text-center">{{ number_format($estimate->total_amount ?? 0, 2) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Totals Table -->
            <div class="row justify-content-between mt-3 ml-2">
                <div class="col-md-12 p-0 m-0">
                    <strong> Amount In words</strong><br>
                    {{ ucfirst($words) }} Only <br><br><br>
                    <div class=" p-0 m-0">
                        <label for="terms_conditions"><strong>Terms & Conditions:</strong></label>
                        <div class="mt-3">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>SL</th>
                                        <th class="text-center">Description</th>
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
            </div>
        </div>
        {{-- <div class="col-lg-12">

            <div class="row">
                <table class="table table-bordered table-responsive" id="itemsTable" style="width: 100%;">
                    <thead>
                        <tr style="padding: 0px;">
                            <th style="width: 5%;">S.</th>
                            <th style="width: 7%;">Item</th>
                            <th class="text-center" style="width: 10%;">Category</th>
                            <th class="text-center" style="width: 25%;">Description</th>
                            <th style="width: 2%;">Qty</th>
                            <th class="text-center pr-3" style="width: 8%;">Rate</th>
                            <th style="text-align:center;" class="p-0 pr-1" style="width: 5%;">Disc.</th>
                            <th style="text-align: center;" class="p-0 pr-1" style="width: 8%;">Taxable</th>
                            <th style="text-align: center;" class="p-0  pr-1" style="width: 3%;">Vat %</th>
                            <th style="text-align: center;" class="pr-1" style="width: 3%;">Vat $</th>
                            <th class="p-0 text-center pr-2 w-20" style="width: 7%;">Net</th>
                        </tr>
                    </thead>
                    <tbody id="itemRows">
                        @foreach ($estimate->salesItems as $index => $item)
                            @php
                                $vatAmount = ($item->quantity * $item->rate - $item->discount) * ($item->tax / 100);
                                $includingVat = $item->quantity * $item->rate + $vatAmount - $item->discount;

                            @endphp
                            <tr>
                                <td class="p-0 m-0 text-center">{{ $index + 1 }}</td> <!-- Display the row number -->
                                <td>{{ $item->item ?? 'N/A' }}</td> <!-- Item Number -->
                                <th>{{ $item->category->name ?? 'N/A' }}</th>
                                <td style="max-width:250px;">{{ $item->service->title ?? 'N/A' }}</td>
                                <!-- Item Name (Service title) -->
                                <!-- Item Name (Service title) -->
                                <td class="text-center ">{{ $item->quantity ?? 0 }}</td> <!-- Quantity -->
                                <td class="text-right pr-2">
                                    {{ number_format($item->rate, 2) }} <!-- Price -->
                                </td>

                                <td class="discount pr-1 text-right">
                                    {{ number_format($item->discount, 2) }}
                                </td>
                                <!-- Excluding VAT (Quantity * Rate) - Discount -->
                                <td class="excluding-vat pr-1 text-right">
                                    {{ number_format($item->quantity * $item->rate, 2) }}
                                </td>

                                <!-- VAT % -->
                                <td style="width: 8%; text-align: center;" class="p-0 text-right">
                                    {{ number_format($item->tax, 2) }}%
                                </td>

                                <!-- VAT Amount (Excluding VAT * VAT %) -->
                                <td class="vat-amount pr-1 text-right">
                                    {{ number_format($vatAmount ?? 0, 2) }}
                                </td>

                                <!-- Including VAT (Excluding VAT + VAT Amount) -->
                                <td style="width: 10%; text-align: right;" class="pr-1">
                                    {{ number_format($includingVat, 2) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

            </div>

            <div class="row">
                <table class="table table-bordered text-center">
                    <thead>
                        <tr>
                            <th>Subtotal</th>
                            <th>Total Discount

                            </th>
                            <th>Total Taxable</th>
                            <th>Total Vat</th>
                            <th class="round-off-column">Round Off</th>
                            <th>Net Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center">{{ number_format($subtotal ?? 0, 2) }}</td>
                            <td class="text-center">{{ number_format($estimate->discount ?? 0, 2) }}</td>
                            <td class="text-center">{{ number_format($totalTaxable ?? 0, 2) }}</td>
                            <td class="text-center">{{ number_format($totalVat ?? 0, 2) }}</td>
                            <td class="text-center round-off-column">{{ number_format($estimate->adjustment ?? 0, 2) }}
                            </td>
                            <td class="text-center">{{ number_format($estimate->total_amount ?? 0, 2) }}</td>
                        </tr>
                    </tbody>
                </table>

            </div>


            <div class="row justify-content-between mt-3 ml-2">
                <div class="col-md-12 p-0 m-0">
                    <strong> Amount In words</strong><br>
                    {{ ucfirst($words) }} Only <br><br><br>
                    <div class=" p-0 m-0">
                        <label for="terms_conditions"><strong>Terms & Conditions:</strong></label>
                        <div class="mt-3">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>SL</th>
                                        <th class="text-center">Description</th>
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

            </div>
        </div> --}}



    </div>
@endsection
