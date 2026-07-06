@extends('manual-sales.show')
@section('section')
    <hr>
    @if (session()->has('flash_notification'))
        @foreach (session('flash_notification') as $message)
            <div class="alert alert-{{ $message['level'] }}">
                {{ $message['message'] }}
            </div>
        @endforeach
    @endif
    <div class="my-3 d-flex justify-content-between flex-sm-row flex-column">
        <div>
            <a href="#"
                class="btn text-white mt-sm-0 mt-2 mb-sm-0 mb-2 status-{{ \Illuminate\Support\Str::slug(\App\Models\Invoice::PAYMENT_STATUS[$invoice->payment_status]) }}">
                {{ \App\Models\Invoice::PAYMENT_STATUS[$invoice->payment_status] }}
            </a>
            <a href="{{ route('manual-sales.accept.invoice', $invoice->id) }}" class="btn text-white btn-danger">
                Invocie Approve
            </a>
        </div>
        <div class="d-flex justify-content-end align-items-center">
            <div class="dropdown d-inline">
                <button class="btn btnWarning text-white dropdown-toggle mr-1" type="button" id="dropdownMenuButton"
                    style="line-height: 31px;" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                    {{ __('messages.invoice.more') }}
                </button>


                <div class="dropdown-menu dropdown-menu-right">

                    @if ($invoice->status != 1 && $invoice->payment_status != 0)
                        <!--<a href="{{ route('manual-sales.approve', $invoice->id) }}" class="dropdown-item"-->
                        <!--    onclick="return confirm('Are you sure you want to approve this sale?')">-->
                        <!--    Approve-->
                        <!--</a>-->
                    @endif

                    @can('export_manual_sales')
                        <a href="{{ route('manual-sales.pdf', ['invoice' => $invoice->id]) }}"
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
            <p class="anchor-underline">
                {{ html_entity_decode($invoice->customer->company_name ?? $invoice->customer_name) }}</p>
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

        <div class="form-group col-md-4 col-12">
            {{ Form::label('currency', __('messages.invoice.currency')) }}
            <p>{{ $invoice->getCurrencyText($invoice->currency) }}</p>
        </div>

        <div class="form-group col-md-4 col-12">
            {{ Form::label('payment_modes', __('messages.payment_modes')) }}
            <p>
                Main Cash
            </p>
        </div>


        <div class="form-group col-md-4 col-12">
            {{ Form::label('hsn_tax', __('messages.project.po_number')) }}<br>
            {{ $invoice->project->po_number ?? __('messages.common.n/a') }}
        </div>
        <div class="form-group col-md-4 col-12">
            {{ Form::label('hsn_tax', 'Project Name') }}<br>
            {{ $invoice->project_name ?? __('messages.common.n/a') }}
        </div>
        <div class="form-group col-md-4 col-12">
            {{ Form::label('hsn_tax', 'Address') }}<br>
            {{ $invoice->address ?? __('messages.common.n/a') }}
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


        <table class="table table-responsive-sm table-responsive-md table-responsive-lg table-responsive-xl table-bordered">
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

                        <!-- Excluding VAT -->
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
                        </td>

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



                </div>

            </div>
        </div>

    </div>
@endsection
