<hr>
<div class="my-3 d-flex justify-content-between">
    <div>
        <a href="#"
            class="btn text-white mx-1 status-{{ \App\Models\CreditNote::PAYMENT_STATUS[$creditNote->payment_status] }}">
            {{ \App\Models\CreditNote::PAYMENT_STATUS[$creditNote->payment_status] }}
        </a>
    </div>
    <div>
        <div class="dropdown d-inline mx-1">
            <button class="btn btnWarning text-white dropdown-toggle" type="button" id="dropdownMenuButton"
                data-toggle="dropdown" style="line-height: 31px;" aria-haspopup="true" aria-expanded="true">
                {{ __('messages.invoice.more') }}
            </button>
            <div class="dropdown-menu dropdown-menu-right">
                @can('export_credit_notes')
                    <a href="{{ route('credit-note.pdf', ['creditNote' => $creditNote->id]) }}" class="dropdown-item">
                        {{ __('messages.common.download_as_pdf') }}
                    </a>
                @endcan

                {{-- <a href="{{ route('credit-note.view-as-customer', $creditNote->id) }}"
                    class="dropdown-item text-content-wrap" data-toggle="tooltip" data-placement="bottom"
                    title="{{ __('messages.credit_note.view_credit_note_as_customer') }}"
                    data-delay='{"show":"500", "hide":"50"}'>
                    {{ __('messages.credit_note.view_credit_note_as_customer') }}</a> --}}
                @if (
                    $creditNote->payment_status != \App\Models\CreditNote::PAYMENT_STATUS_DRAFT &&
                        $creditNote->payment_status != \App\Models\CreditNote::PAYMENT_STATUS_OPEN &&
                        $creditNote->payment_status != \App\Models\CreditNote::PAYMENT_STATUS_VOID &&
                        $creditNote->payment_status != \App\Models\CreditNote::PAYMENT_STATUS_CLOSED)
                    <a class="dropdown-item text-content-wrap" href="#" id="markAsDraft" data-status="0"
                        data-toggle="tooltip" data-placement="bottom"
                        title="{{ __('messages.credit_note.mark_as_draft') }}"
                        data-delay='{"show":"500", "hide":"50"}'>{{ __('messages.credit_note.mark_as_draft') }}</a>
                @endif
                @if (
                    $creditNote->payment_status != \App\Models\CreditNote::PAYMENT_STATUS_OPEN &&
                        $creditNote->payment_status != \App\Models\CreditNote::PAYMENT_STATUS_DRAFT)
                    <a class="dropdown-item text-content-wrap" href="#" id="markAsOpen" data-status="1"
                        data-toggle="tooltip" data-placement="bottom"
                        title="{{ __('messages.credit_note.mark_as_open') }}"
                        data-delay='{"show":"500", "hide":"50"}'>{{ __('messages.credit_note.mark_as_open') }}</a>
                @endif
                @if (
                    $creditNote->payment_status != \App\Models\CreditNote::PAYMENT_STATUS_VOID &&
                        $creditNote->payment_status != \App\Models\CreditNote::PAYMENT_STATUS_DRAFT)
                    <a class="dropdown-item text-content-wrap" href="#" id="markAsVoid" data-status="2"
                        data-toggle="tooltip" data-placement="bottom"
                        title="{{ __('messages.credit_note.mark_as_void') }}"
                        data-delay='{"show":"500", "hide":"50"}'>{{ __('messages.credit_note.mark_as_void') }}</a>
                @endif
                @if (
                    $creditNote->payment_status != \App\Models\CreditNote::PAYMENT_STATUS_CLOSED &&
                        $creditNote->payment_status != \App\Models\CreditNote::PAYMENT_STATUS_DRAFT)
                    <a class="dropdown-item text-content-wrap" href="#" id="markAsClosed" data-status="3"
                        data-toggle="tooltip" data-placement="bottom"
                        title="{{ __('messages.credit_note.mark_as_closed') }}"
                        data-delay='{"show":"500", "hide":"50"}'>{{ __('messages.credit_note.mark_as_closed') }}</a>
                @endif
            </div>
        </div>
    </div>
</div>
<hr>
<div class="tab-content" id="myTabContent2">
    <div class="tab-pane fade show active" id="creditNoteDetails" role="tabpanel"
        aria-labelledby="creditNoteDetails-tab">
        <div class="row">
            <div class="form-group col-md-4 col-12">
                {{ Form::label('hsn_tax', __('messages.branches.name')) }}<br>
                {{ $creditNote->invoice->branch?->name ?? __('messages.common.n/a') }}
            </div>
            <div class="form-group col-md-4 col-12">
                {{ Form::label('title', __('messages.credit_note.title')) }}
                <p>{{ html_entity_decode($creditNote->title) }}</p>
            </div>
            <div class="form-group col-md-4 col-12">
                {{ Form::label('title', __('messages.credit_note.invoice_id')) }}
                <p>{{ $creditNote->invoice_id ?? '' }}</p>
            </div>
            <div class="form-group col-md-4 col-12">
                {{ Form::label('credit_note_number', __('messages.credit_note.credit_note_number')) }}
                <p>{{ $creditNote->credit_note_number }}</p>
            </div>
            <div class="form-group col-md-4 col-12">
                {{ Form::label('customer', __('messages.invoice.customer')) }}
                <p><a href="{{ url('admin/customers/' . $creditNote->customer->id) }}"
                        class="anchor-underline">{{ html_entity_decode($creditNote->customer->company_name) }}</a></p>
            </div>
            <div class="form-group col-md-4 col-12">
                {{ Form::label('credit_note_number', __('messages.customer.vendor_code')) }}
                <p>{{ $creditNote->vendor_code }}</p>
            </div>
            <div class="form-group col-md-4 col-12">
                {{ Form::label('invoice_date', __('messages.credit_note.credit_note_date')) }}
                <p>{{ Carbon\Carbon::parse($creditNote->credit_note_date)->translatedFormat('jS M, Y H:i A') }}</p>
            </div>
            <div class="form-group col-md-4 col-12">
                {{ Form::label('currency', __('messages.invoice.currency')) }}
                <p>{{ isset($creditNote->currency) ? $creditNote->getCurrencyText($creditNote->currency) : __('messages.common.n/a') }}
                </p>
            </div>
            {{-- <div class="form-group col-md-4 col-12">
                {{ Form::label('discount_type', __('messages.invoice.discount_type') ) }}
                <p>{{ isset($creditNote->discount_type) ? $creditNote->getDiscountTypeText($creditNote->discount_type) : __('messages.common.n/a') }}
                </p>
            </div> --}}
            <div class="form-group col-md-4 col-12">
                {{ Form::label('reference', __('messages.credit_note.reference')) }}
                <p>{{ !empty($creditNote->reference) ? html_entity_decode($creditNote->reference) : __('messages.common.n/a') }}
                </p>
            </div>
            <div class="form-group col-md-4 col-12">
                {{ Form::label('title', __('messages.invoice.project')) }}
                <p>{{ $creditNote->invoice ? $creditNote->invoice->project->project_name ?? '' : '' }}</p>
            </div>
            <div class="form-group col-md-4 col-12">
                {{ Form::label('title', __('messages.project.project_location')) }}
                <p>{{ $creditNote->invoice ? $creditNote->invoice->project->project_location ?? '' : '' }}</p>
            </div>
            <div class="form-group col-md-4 col-12">
                {{ Form::label('po_number', __('messages.project.po_number')) }}
                <p>{{ $creditNote->invoice ? $creditNote->invoice->project->po_number ?? '' : '' }}</p>
            </div>

            <div class="form-group col-md-4 col-12">
                {{ Form::label('created_at', __('messages.common.created_on')) }}
                <p><span data-toggle="tooltip" data-placement="right"
                        title="{{ Carbon\Carbon::parse($creditNote->created_at)->translatedFormat('jS M, Y') }}">{{ $creditNote->created_at->diffForHumans() }}</span>
                </p>
            </div>
            <div class="form-group col-md-4 col-12">
                {{ Form::label('updated_at', __('messages.common.last_updated')) }}
                <p><span data-toggle="tooltip" data-placement="right"
                        title="{{ Carbon\Carbon::parse($creditNote->updated_at)->translatedFormat('jS M, Y') }}">{{ $creditNote->updated_at->diffForHumans() }}</span>
                </p>
            </div>
            <div class="col-12">
                <div class="row">
                    <div class="form-group col-12">
                        {{ Form::label('admin_text', 'Remarks') }}
                        <br>{!! !empty($creditNote->admin_text) ? html_entity_decode($creditNote->admin_text) : __('messages.common.n/a') !!}
                    </div>
                </div>
            </div>


            {{-- <div class="col-12">
                <div class="row">
                    @foreach ($creditNote->creditNoteAddresses as $address)
                        @if ($address->type == 1)
                            <div class="form-group col-md-4 col-12">
                                {{ Form::label('bill_to', __('messages.invoice.bill_to')) }}
                                <div>{{ html_entity_decode($address->street) }},</div>
                                <div>{{ $address->city }},</div>
                                <div>{{ $address->state }},</div>
                                <div>{{ $address->country }},</div>
                                <div>{{ $address->zip_code }}</div>
                            </div>
                        @else
                            <div class="form-group col-md-4 col-12">
                                {{ Form::label('bill_to', __('messages.invoice.ship_to')) }}
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
            <table
                class="table table-responsive-sm table-responsive-md table-responsive-lg table-responsive-xl table-bordered">
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
                    @foreach ($creditNote->salesItems as $index => $item)
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
                            {{-- <td style="text-align: right;">
                                    {{ $item->discount ?? 0 }} <!-- Discount -->
                                </td> --}}
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




            <div class="col-lg-12">
                <div class="row">
                    <table class="table table-bordered text-center">
                        <thead>
                            <tr>
                                <th>Subtotal</th>
                                <th>Total Discount
                                    {{-- {{ isset($estimate->discount_type) ? ($estimate->discount_type == 0 ? '%' : '$') : '' }} --}}
                                </th>
                                <th>Total Taxable</th>
                                <th>Total Vat</th>
                                <th class="round-off-column ">Round Off</th>
                                <th>Net Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="text-center">{{ number_format($subtotal ?? 0, 2) }}</td>
                                <td class="text-center">{{ number_format($creditNote->discount ?? 0, 2) }}</td>
                                <td class="text-center">{{ number_format($totalTaxable ?? 0, 2) }}</td>
                                <td class="text-center">{{ number_format($totalVat ?? 0, 2) }}</td>
                                <td class="text-center round-off-column ">{{ number_format($creditNote->adjustment ?? 0, 2) }}</td>
                                <td class="text-center">{{ number_format($creditNote->total_amount ?? 0, 2) }}</td>
                            </tr>
                        </tbody>
                    </table>

                </div>
                <div class="row justify-content-between mt-3 ml-2">

                    <div class="col-md-12 p-0 m-0">
                        <strong> Amount In words</strong><br>
                        {{ ucfirst($words) }} Only <br><br><br>
                        <div class=" p-0 m-0">
                            <div style="width: 100%;background:lightblue;color:black;height:40px;border-radius:5px;">
                                <label for="terms_dropdown"
                                    style="font-weight: bold;line-height:40px;padding-left:10px;"> Terms &
                                    Conditions</label>
                            </div>
                            <div class="mt-3">
                                <table class="table table-bordered">

                                    <tbody>
                                        <!-- Loop through the terms and display them -->
                                        @foreach ($creditNote->invoice?->project?->terms as $index => $estimateTerm)
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
            {{-- <div class="col-12">
                <div class="row">
                    <div class="form-group col-lg-6 col-md-12 col-sm-12">
                        {{ Form::label('client_note', __('messages.invoice.client_note')) }}
                        <br>{!! !empty($creditNote->client_note) ? html_entity_decode($creditNote->client_note) : __('messages.common.n/a') !!}
                    </div>
                    <div class="form-group col-lg-6 col-md-12 col-sm-12">
                        {{ Form::label('terms_conditions', __('messages.invoice.terms_conditions')) }}
                        <br>{!! !empty($creditNote->term_conditions)
                            ? html_entity_decode($creditNote->term_conditions)
                            : __('messages.common.n/a') !!}
                    </div>
                </div>
            </div> --}}
        </div>
    </div>
</div>
