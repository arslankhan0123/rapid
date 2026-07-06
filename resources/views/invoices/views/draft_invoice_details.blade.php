@extends('invoices.show')
@section('section')
    <hr>
    <div class="my-3 d-flex justify-content-between flex-sm-row flex-column">
        <div>
            <a href="#"
                class="btn text-white mt-sm-0 mt-2 mb-sm-0 mb-2 status-{{ \Illuminate\Support\Str::slug(\App\Models\Invoice::PAYMENT_STATUS[$invoice->payment_status]) }}">
                {{ \App\Models\Invoice::PAYMENT_STATUS[$invoice->payment_status] }}
            </a>
        </div>
        <div class="d-flex justify-content-end align-items-center">
            <div class="dropdown d-inline">
                <button class="btn btnWarning text-white dropdown-toggle mr-1" type="button" id="dropdownMenuButton"
                    style="line-height: 31px;" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                    {{ __('messages.invoice.more') }}
                </button>
                <div class="dropdown-menu dropdown-menu-right">
                    @can('export_invoices')
                        <a href="{{ route('invoice.pdf', ['invoice' => $invoice->id]) }}"
                            class="dropdown-item">{{ __('messages.common.download_as_pdf') }}
                        </a>
                    @endcan

                    <a href="#" id="btnSend" class="dropdown-item">
                        {{ __('messages.common.send_email') }}
                    </a>
                    <a href="#" id="btnSendSMS" class="dropdown-item">
                        {{ __('messages.common.send_sms') }}
                    </a>
                    <a href="#" id="btnSendWhatsapp" class="dropdown-item">
                        {{ __('messages.common.send_whatsapp') }}
                    </a>
                    {{-- <a href="{{ route('invoice.view-as-customer', $invoice->id) }}" class="dropdown-item text-content-wrap"
                        data-toggle="tooltip" data-placement="bottom"
                        title="{{ __('messages.invoice.view_invoice_as_customer') }}"
                        data-delay='{"show":"500", "hide":"50"}'>
                        {{ __('messages.invoice.view_invoice_as_customer') }}</a> --}}
                    @if (
                        $invoice->payment_status != \App\Models\Invoice::STATUS_DRAFT &&
                            $invoice->payment_status != \App\Models\Invoice::STATUS_UNPAID &&
                            $invoice->payment_status != \App\Models\Invoice::STATUS_PAID)
                        <a id="markAsSent" class="dropdown-item text-content-wrap" href="#" data-status="1"
                            data-toggle="tooltip" data-placement="bottom" title="{{ __('messages.invoice.mark_as_sent') }}"
                            data-delay='{"show":"500", "hide":"50"}'>{{ __('messages.invoice.mark_as_sent') }}</a>
                    @elseif($invoice->payment_status == \App\Models\Invoice::STATUS_DRAFT)
                        <a id="markAsCancelled" class="dropdown-item text-content-wrap" href="#" data-status="4"
                            data-toggle="tooltip" data-placement="bottom"
                            title="{{ __('messages.invoice.mark_as_cancelled') }}"
                            data-delay='{"show":"500", "hide":"50"}'>{{ __('messages.invoice.mark_as_cancelled') }}</a>
                    @endif
                    @if ($invoice->payment_status == \App\Models\Invoice::STATUS_UNPAID)
                        <a class="dropdown-item text-content-wrap" href="#" id="markAsCancelled" data-status="4"
                            data-toggle="tooltip" data-placement="bottom"
                            title="{{ __('messages.invoice.mark_as_cancelled') }}"
                            data-delay='{"show":"500", "hide":"50"}'>{{ __('messages.invoice.mark_as_cancelled') }}</a>
                    @elseif($invoice->payment_status == \App\Models\Invoice::STATUS_CANCELLED)
                        <a class="dropdown-item text-content-wrap" href="#" id="unmarkAsCancelled" data-status="1"
                            data-toggle="tooltip" data-placement="bottom"
                            title="{{ __('messages.invoice.unmark_as_cancelled') }}"
                            data-delay='{"show":"500", "hide":"50"}'>{{ __('messages.invoice.unmark_as_cancelled') }}</a>
                    @endif
                </div>
            </div>
            {{-- @if ($invoice->payment_status != \App\Models\Invoice::STATUS_CANCELLED)
                <div class="dropdown d-inline">
                    <a href="#" class="btn btn-primary ml-1  {{ $invoice->payment_status != 2 ? '' : 'disabled' }}"
                        style="line-height: 31px;" data-toggle="modal" id="addPayment" data-target="#addPaymentModa"
                        data-id={{ $invoice->id }}><i class="fas fa-plus"></i> {{ __('messages.invoice.payments') }}</a>
                </div>
            @endif --}}
        </div>
    </div>

    <hr>
    <div class="row">
        {{-- <div class="form-group col-md-4 col-12">
            {{ Form::label('title', __('messages.invoice.title')) }}
            <p>{{ html_entity_decode($invoice->title) }}</p>
        </div> --}}
        <div class="form-group col-md-4 col-12">
            {{ Form::label('hsn_tax', __('messages.branches.name')) }}<br>
            {{ $invoice->branch?->name ?? __('messages.common.n/a') }}
        </div>
        <div class="form-group col-md-4 col-12">
            {{ Form::label('invoice_number', __('messages.invoice.invoice_number')) }}
            <p>{{ $invoice->invoice_number }}</p>
        </div>
        <div class="form-group col-md-4 col-12">
            {{ Form::label('customer', __('messages.invoice.customer')) }}
            <p><a href="{{ url('admin/customers/' . $invoice->customer->id) }}"
                    class="anchor-underline">{{ html_entity_decode($invoice->customer->company_name) }}</a></p>
        </div>
        <div class="form-group col-md-4 col-12">
            {{ Form::label('invoice_number', __('messages.customer.vendor_code')) }}
            <p>{{ $invoice->vendor_code ?? '' }}</p>
        </div>
        <div class="form-group col-md-4 col-12">
            {{ Form::label('invoice_date', __('messages.invoice.invoice_date')) }}
            <p>{{ Carbon\Carbon::parse($invoice->invoice_date)->translatedFormat('jS M, Y') }}</p>
        </div>
        <div class="form-group col-md-4 col-12">
            {{ Form::label('invoice_date', 'Invoice Month') }}
            <p>{{ Carbon\Carbon::parse($invoice->invoice_month)->translatedFormat(' M, Y') }}</p>
        </div>
        <div class="form-group col-md-4 col-12">
            {{ Form::label('due_date', __('messages.invoice.due_date')) }}
            <p>{{ isset($invoice->due_date) ? Carbon\Carbon::parse($invoice->due_date)->translatedFormat('jS M, Y') : __('messages.common.n/a') }}
            </p>
        </div>
        {{-- <div class="form-group col-md-4 col-12">
            {{ Form::label('sales_agent_id', __('messages.invoice.sale_agent')) }}
            <p>
                @if (!empty($invoice->sales_agent_id))
                    <a href="{{ url('admin/members/' . $invoice->sales_agent_id) }}" class="anchor-underline">
                        {{ html_entity_decode($invoice->user->full_name) }}
                    </a>
                @else
                    {{ __('messages.common.n/a') }}
                @endif
            </p>
        </div> --}}
        <div class="form-group col-md-4 col-12">
            {{ Form::label('currency', __('messages.invoice.currency')) }}
            <p>{{ $invoice->getCurrencyText($invoice->currency) }}</p>
        </div>
        {{-- <div class="form-group col-md-4 col-12">
            {{ Form::label('discount_type', __('messages.invoice.discount_type') ) }}
            <p>{{ $invoice->getDiscountTypeText($invoice->discount_type) }}</p>
        </div> --}}
        {{-- <div class="form-group col-md-4 col-12">
            {{ Form::label('tags', __('messages.tags')) }}
            <p>
                @forelse($invoice->tags as $tag)
                    <span class="badge border border-secondary mb-1">{{ html_entity_decode($tag->name) }}</span>
                @empty
                    {{ __('messages.common.n/a') }}
                @endforelse
            </p>
        </div> --}}
        {{-- <div class="form-group col-md-4 col-12">
            {{ Form::label('hsn_tax', __('messages.invoice.hsn_tax') ) }}<br>
            {{ !empty($invoice->hsn_tax) ? $invoice->hsn_tax : __('messages.common.n/a') }}
        </div> --}}
        <div class="form-group col-md-4 col-12">
            {{ Form::label('payment_modes', __('messages.payment_modes')) }}
            <p>
                @forelse($invoice->paymentModes as $paymentMode)
                    <span class="badge badge-light mb-1">{{ html_entity_decode($paymentMode->name) }} </span>
                @empty
                    {{ __('messages.common.n/a') }}
                @endforelse
            </p>
        </div>
        <div class="form-group col-md-4 col-12">
            {{ Form::label('hsn_tax', __('messages.invoice.project')) }}<br>
            {{ $invoice->project ? $invoice->project->project_name : __('messages.common.n/a') }}
        </div>
        <div class="form-group col-md-4 col-12">
            {{ Form::label('hsn_tax', __('messages.project.project_location')) }}<br>
            {{ $invoice->project->project_location ?? __('messages.common.n/a') }}
        </div>
        <div class="form-group col-md-4 col-12">
            {{ Form::label('hsn_tax', __('messages.project.po_number')) }}<br>
            {{ $invoice->project->po_number ?? __('messages.common.n/a') }}
        </div>

        <div class="form-group col-md-4 col-12">
            {{ Form::label('created_at', __('messages.common.created_on')) }}
            <p><span data-toggle="tooltip" data-placement="right"
                    title="{{ Carbon\Carbon::parse($invoice->created_at)->translatedFormat('jS M, Y') }}">{{ $invoice->created_at->diffForHumans() }}</span>
            </p>
        </div>
        <div class="form-group col-md-4 col-12">
            {{ Form::label('updated_at', __('messages.common.last_updated')) }}
            <p><span data-toggle="tooltip" data-placement="right"
                    title="{{ Carbon\Carbon::parse($invoice->updated_at)->translatedFormat('jS M, Y') }}">{{ $invoice->updated_at->diffForHumans() }}</span>
            </p>
        </div>
        {{-- <div class="form-group col-md-4 col-12">
            {{ Form::label('admin_text', __('messages.invoice.admin_note') ) }}<br>
            {!! !empty($invoice->admin_text) ? html_entity_decode($invoice->admin_text) : __('messages.common.n/a') !!}
        </div> --}}
        <div class="col-12">
            <div class="row">
                @foreach ($invoice->invoiceAddresses as $address)
                    @if ($address->type == 1)
                        <div class="form-group col-md-4 col-12">
                            {{ Form::label('bill_to', __('messages.invoice.bill_to')) }}
                            <div>
                                {{ html_entity_decode($address->street) }},
                                {{ $address->city }},
                                {{ $address->state }},
                                {{ $address->country }},
                                {{ $address->zip_code }}
                            </div>
                        </div>
                    @else
                        <div class="form-group col-md-4 col-12">
                            {{ Form::label('ship_to', __('messages.invoice.ship_to')) }}
                            <div>
                                {{ html_entity_decode($address->street) }},
                                {{ $address->city }},
                                {{ $address->state }},
                                {{ $address->country }},
                                {{ $address->zip_code }}
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>

        </div>
        <table class="table table-responsive-sm table-responsive-md table-responsive-lg table-responsive-xl table-bordered">
            <thead>
                <tr style="padding: 0px;">
                    <th style="width: 5%;">S.</th>
                    <th style="width: 7%;">Item</th>
                    <th class="text-center" style="width: 15%;">Category</th>
                    <th class="text-center" style="width: 30%;">Description</th>
                    <th style="width: 4%;">Qty</th>
                    <th class="text-center pr-3" style="width: 10%;">Rate</th>
                    <th style="width: 4%;">Disc.</th>
                    {{-- <th style="text-align: center;" class="p-0 pr-1" style="width: 8%;">Taxable</th>
                    <th style="text-align: center;" class="p-0  pr-1" style="width: 3%;">Vat %</th>
                    <th style="text-align: center;" class="pr-1" style="width: 3%;">Vat $</th> --}}
                    <th class="p-0 text-center pr-2 w-20" style="width: 7%;">Net</th>
                </tr>
            </thead>

            <tbody>
                @foreach ($invoice->salesItems as $index => $item)
                    @php
                        // Calculate VAT amount separately
                        $vatAmount = ($item->quantity * $item->rate - $item->discount) * ($item->tax / 100);

                        // Calculate amount excluding VAT (Net without VAT)
                        $netAmount = $item->quantity * $item->rate - $item->discount;
                    @endphp

                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ html_entity_decode($item->item ?? __('messages.common.n/a')) }}</td>
                        <td>{{ html_entity_decode($item->category->name ?? '') }}</td>
                        <td>{{ html_entity_decode($item->service->title ?? '') }}</td>
                        <td class="text-center">{{ $item->quantity ?? 0 }}</td>

                        <td class="text-right pr-2">
                            {{ number_format($item->rate, 2) }}
                        </td>

                        <td class="discount pr-1 text-right">
                            {{ number_format($item->discount, 2) }}
                        </td>

                        {{-- <!-- Excluding VAT -->
                        <td class="excluding-vat pr-1 text-right">
                            {{ number_format($item->quantity * $item->rate, 2) }}
                        </td>

                        <!-- VAT % -->
                        <td style="width: 8%; text-align: center;" class="p-0 text-right">
                            {{ number_format($item->tax, 2) }}%
                        </td>

                        <!-- VAT Amount -->
                        <td class="vat-amount pr-1 text-right">
                            {{ number_format($vatAmount ?? 0, 2) }}
                        </td> --}}

                        <!-- Net Amount (excluding VAT) -->
                        <td style="width: 10%; text-align: right;" class="pr-1">
                            {{ number_format($netAmount, 2) }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>


        <div class="col-lg-12">

            <div class="row">
                <table class="table table-bordered text-center">
                    <thead>
                        <tr>
                            <th>Subtotal</th>
                            <th>Absent Deduction</th>
                            <th>Allowance Deduction</th>
                            <th>Damage Deduction</th>
                            <th>Total Discount</th>
                            <th>Total Taxable</th>
                            <th>Total Vat</th>
                            <th class="round-off-column">Round Off</th>
                            <th>Net Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center">{{ number_format($subtotal ?? 0, 2) }}</td>
                            <td class="text-center">{{ number_format($invoice->absent_deduction ?? 0, 2) }}</td>
                            <td class="text-center">{{ number_format($invoice->allowance_deduction ?? 0, 2) }}</td>
                            <td class="text-center">{{ number_format($invoice->damage_deduction ?? 0, 2) }}</td>
                            <td class="text-center">{{ number_format($invoice->discount ?? 0, 2) }}</td>
                            <td class="text-center">{{ number_format($totalTaxable ?? 0, 2) }}</td>
                            <td class="text-center">{{ number_format($totalVat ?? 0, 2) }}</td>
                            <td class="text-center round-off-column">{{ number_format($invoice->adjustment ?? 0, 2) }}
                            </td>
                            <td class="text-center">{{ number_format($invoice->total_amount ?? 0, 2) }}</td>
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
                        <div style="width: 100%;background:lightblue;color:black;height:40px;border-radius:5px;">
                            <label for="terms_conditions"
                                style="font-weight: bold;line-height:40px;padding-left:10px;"><strong>Terms &
                                    Conditions:</strong></label>
                        </div>

                        <div class="mt-3">
                            <table class="table table-bordered">

                                <tbody>
                                    <!-- Loop through the terms and display them -->
                                    @if ($invoice->project)
                                        @foreach ($invoice->project?->terms as $index => $estimateTerm)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ $estimateTerm['description'] }}</td>
                                            </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>


                </div>

            </div>
        </div>
        {{-- <div class="col-12">
            <div class="row">
                <div class="form-group col-lg-6 col-md-12 col-sm-12">
                    {{ Form::label('client_note', __('messages.invoice.client_note') ) }}
                    <br>{!! !empty($invoice->client_note) ? html_entity_decode($invoice->client_note) : __('messages.common.n/a') !!}
                </div>
                <div class="form-group col-lg-6 col-md-12 col-sm-12">
                    {{ Form::label('terms_conditions', __('messages.invoice.terms_conditions') ) }}
                    <br>{!! !empty($invoice->term_conditions)
                        ? html_entity_decode($invoice->term_conditions)
                        : __('messages.common.n/a') !!}
                </div>
            </div>
        </div> --}}
    </div>
@endsection
