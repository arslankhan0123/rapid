<div>
    <div class="row">
        <div class="mt-0 mb-3 col-12 d-flex justify-content-end search-display-block">
            @if (!empty($customer))
                <div class="mt-2">
                    {{ Form::select('payment_status', $invoiceStatus, $statusFilter, ['id' => 'invoicePaymentStatus', 'class' => 'form-control', 'placeholder' => __('messages.placeholder.select_status')]) }}
                </div>
            @endif
            <div class="p-2">
                <input wire:model.debounce.100ms="search" type="search" class="form-control"
                    placeholder="{{ __('messages.common.search') }}" id="search">
            </div>
        </div>
        <div class="col-md-12">
            <div wire:loading id="live-wire-screen-lock">
                <div class="live-wire-infy-loader">
                    @include('loader')
                </div>
            </div>
        </div>
        @if (empty($customer))
            <div class="col-md-12">
                <div class="row justify-content-md-center text-center mb-4">
                    <div class="owl-carousel owl-theme">
                        <div class="item">
                            <div class="ticket-statistics mx-auto bg-danger">
                                <p>{{ $statusCount->cancelled }}</p>
                            </div>
                            <h5 class="my-0 mt-1">{{ __('messages.invoice.cancelled') }}</h5>
                        </div>
                        <div class="item">
                            <div class="ticket-statistics mx-auto bg-warning">
                                <p>{{ $statusCount->drafted }}</p>
                            </div>
                            <h5 class="my-0 mt-1">{{ __('messages.invoice.drafted') }}</h5>
                        </div>
                        <div class="item">
                            <div class="ticket-statistics mx-auto bg-primary">
                                <p>{{ $statusCount->unpaid }}</p>
                            </div>
                            <h5 class="my-0 mt-1">{{ __('messages.invoice.unpaid') }}</h5>
                        </div>
                        <div class="item">
                            <div class="ticket-statistics mx-auto bg-info">
                                <p>{{ $statusCount->partially_paid }}</p>
                            </div>
                            <h5 class="my-0 mt-1">{{ __('messages.invoice.partially_paid') }}</h5>
                        </div>
                        <div class="item">
                            <div class="ticket-statistics mx-auto bg-success">
                                <p>{{ $statusCount->paid }}</p>
                            </div>
                            <h5 class="my-0 mt-1">{{ __('messages.invoice.paid') }}</h5>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @php
            $hasDraft = $invoices->contains('payment_status', \App\Models\Invoice::STATUS_DRAFT);
            $dividerRendered = false;
        @endphp

        @forelse($invoices as $invoice)
            @if($invoice->payment_status != \App\Models\Invoice::STATUS_DRAFT && $hasDraft && !$dividerRendered)
                <div class="col-12 my-3">
                    <div class="d-flex align-items-center">
                        <span class="text-muted font-weight-bold mr-3" style="font-size: 0.85rem; letter-spacing: 1px; text-transform: uppercase;">Invoices</span>
                        <div class="flex-grow-1" style="height: 2px; background: linear-gradient(to right, #e3e6f0, transparent);"></div>
                    </div>
                </div>
                @php $dividerRendered = true; @endphp
            @endif
            <div class="col-12 col-md-6 col-xl-3 col-xlg-4 mb-4">
                <div
                    class="card card-{{ \App\Models\Invoice::STATUS_COLOR[$invoice->payment_status] }} shadow rounded h-100">
                    {{-- Card Header --}}
                    {{-- <div class="card-header d-flex justify-content-between align-items-center p-2">
                        <a href="{{ url('admin/invoices', $invoice->id) }}" class="text-decoration-none">
                            <h6 class="text-primary mb-0 font-weight-bold">
                                #{{ $invoice->invoice_number }}
                            </h6>
                        </a>
                        <div class="invoice-action-btn">
                            @if ($invoice->payment_status == \App\Models\Invoice::STATUS_DRAFT || $invoice->payment_status == \App\Models\Invoice::STATUS_UNPAID)
                                @can('update_invoices')
                                    <a title="{{ __('messages.common.edit') }}"
                                        href="{{ route('invoices.edit', $invoice->id) }}">
                                        <i class="fa fa-edit text-warning"></i>
                                    </a>
                                @endcan
                            @endif
                        </div>
                    </div> --}}

                    <div class="card-header d-flex justify-content-between align-items-center p-2">
                        <a href="{{ url('admin/invoices', $invoice->id) }}" class="text-decoration-none">
                            <h6 class="text-primary mb-0 font-weight-bold">
                                #{{ $invoice->invoice_number }}
                            </h6>
                        </a>
                        <div class="invoice-action-btn">
                            {{-- Download PDF Button --}}
                            @can('export_invoices')
                                <a title="{{ __('messages.common.download_as_pdf') }}"
                                    href="{{ route('invoice.pdf', ['invoice' => $invoice->id]) }}" class="mr-2">
                                    <i class="fa fa-download text-primary"></i>
                                </a>
                            @endcan

                            {{-- Edit Button --}}
                            @if (
                                $invoice->payment_status == \App\Models\Invoice::STATUS_DRAFT ||
                                    $invoice->payment_status == \App\Models\Invoice::STATUS_UNPAID)
                                @can('update_invoices')
                                    <a title="{{ __('messages.common.edit') }}"
                                        href="{{ route('invoices.edit', $invoice->id) }}">
                                        <i class="fa fa-edit text-warning"></i>
                                    </a>
                                @endcan
                            @endif
                        </div>
                    </div>

                    {{-- Card Body --}}
                    <div class="card-body p-2">
                        {{-- Customer --}}
                        <div class="mb-2">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-building text-muted mr-2 fa-sm"></i>
                                <span class="text-dark small font-weight-medium">
                                    {{ html_entity_decode(Str::limit($invoice->customer->company_name, 20)) }}
                                </span>
                            </div>
                        </div>

                        @if ($invoice->project)
                            <div class="mb-2">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-project-diagram text-muted mr-2 fa-sm"></i>
                                    <span class="text-dark small font-weight-medium">
                                        {{ html_entity_decode(Str::limit($invoice->project->project_name, 30)) }}
                                    </span>
                                </div>
                            </div>
                        @endif

                        {{-- Amount --}}
                        <div class="mb-2">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted small">Amount</span>
                                <span class="font-weight-bold text-primary">
                                    {{ number_format($invoice->total_amount, 2) }}
                                </span>
                            </div>
                        </div>

                        {{-- Status --}}
                        <div class="mb-2">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted small">Status</span>
                                <span
                                    class="badge badge-{{ \App\Models\Invoice::STATUS_COLOR[$invoice->payment_status] }}">
                                    {{ App\Models\Invoice::PAYMENT_STATUS[$invoice->payment_status] }}
                                </span>
                            </div>
                        </div>

                        {{-- Dates Section --}}
                        <div class="border-top pt-2">
                            {{-- Invoice Date --}}
                            <div class="mb-1">
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted">Invoice Date</small>
                                    <small class="text-dark font-weight-medium">
                                        {{ Carbon\Carbon::parse($invoice->invoice_date)->format('M j, Y') }}
                                    </small>
                                </div>
                            </div>

                            {{-- Due Date --}}
                            @if (!empty($invoice->due_date))
                                <div class="mb-1">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <small class="text-muted">Due Date</small>
                                        <small
                                            class="{{ now() > Carbon\Carbon::parse($invoice->due_date) ? 'text-danger font-weight-bold' : 'text-dark' }}">
                                            {{ Carbon\Carbon::parse($invoice->due_date)->format('M j, Y') }}
                                        </small>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Card Footer --}}
                    <div class="card-footer bg-transparent py-1 px-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">
                                Created: {{ Carbon\Carbon::parse($invoice->created_at)->format('M j') }}
                            </small>
                            @can('delete_invoices')
                                @if (
                                    $invoice->payment_status != \App\Models\Invoice::STATUS_PAID &&
                                        $invoice->payment_status != \App\Models\Invoice::STATUS_PARTIALLY_PAID)
                                    <a title="{{ __('messages.common.delete') }}" class="text-danger action-btn delete-btn"
                                        data-id="{{ $invoice->id }}" href="#">
                                        <i class="fa fa-trash fa-sm"></i>
                                    </a>
                                @endif
                            @endcan
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="mt-0 mb-5 col-12 d-flex justify-content-center mb-5 rounded">
                <div class="p-2">
                    @if (empty($search))
                        <p class="text-dark">{{ __('messages.invoice.no_invoice_available') }}</p>
                    @else
                        <p class="text-dark">{{ __('messages.invoice.no_invoice_found') }}</p>
                    @endif
                </div>
            </div>
        @endforelse

        @if ($invoices->count() > 0)
            <div class="mt-0 mb-5 col-12">
                <div class="row paginatorRow">
                    <div class="col-lg-2 col-md-6 col-sm-12 pt-2">
                        <span class="d-inline-flex">
                            {{ __('messages.common.showing') }}
                            <span class="font-weight-bold ml-1 mr-1">{{ $invoices->firstItem() }}</span> -
                            <span class="font-weight-bold ml-1 mr-1">{{ $invoices->lastItem() }}</span>
                            {{ __('messages.common.of') }}
                            <span class="font-weight-bold ml-1">{{ $invoices->total() }}</span>
                        </span>
                    </div>
                    <div class="col-lg-10 col-md-6 col-sm-12 d-flex justify-content-end">
                        {{ $invoices->links() }}
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
