<div>
    <div class="row">
        <div class="mt-0 mb-3 col-12 d-flex justify-content-end livewire-search">
            @if (!empty($customer))
                <div class="mt-2 mr-2 m-r-0">
                    {{ Form::select('payment_status', $creditNoteStatus, $filterStatus, ['id' => 'creditNoteStatus', 'class' => 'form-control', 'placeholder' => __('messages.placeholder.select_status')]) }}
                </div>
            @endif
            <div class="p-2 m-t-10">
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
                                <p>{{ $statusCount->open }}</p>
                            </div>
                            <h5 class="my-0 mt-1">{{ __('messages.credit_note.open') }}</h5>
                        </div>
                        <div class="item">
                            <div class="ticket-statistics mx-auto bg-warning">
                                <p>{{ $statusCount->drafted }}</p>
                            </div>
                            <h5 class="my-0 mt-1">{{ __('messages.credit_note.drafted') }}</h5>
                        </div>
                        <div class="item">
                            <div class="ticket-statistics mx-auto bg-primary">
                                <p>{{ $statusCount->void }}</p>
                            </div>
                            <h5 class="my-0 mt-1">{{ __('messages.credit_note.void') }}</h5>
                        </div>
                        <div class="item">
                            <div class="ticket-statistics mx-auto bg-success">
                                <p>{{ $statusCount->closed }}</p>
                            </div>
                            <h5 class="my-0 mt-1">{{ __('messages.credit_note.closed') }}</h5>
                        </div>
                    </div>
                </div>
            </div>
        @endif
        @forelse($creditNotes as $creditNote)
            <div class="col-12 col-md-6 col-xl-3 col-xlg-4">
                <div
                    class="credit-note-card card card-{{ \App\Models\CreditNote::STATUS_COLOR[$creditNote->payment_status] }} shadow mb-5 rounded hover-card card-height height-210">
                    <div
                        class="card-header d-flex justify-content-between align-items-center itemCon p-3 invoice-card-height">
                        <div class="d-flex">
                            <a href="{{ route('credit-notes.show', $creditNote->id) }}"
                                class="d-flex flex-wrap text-decoration-none credit-note-text">
                                <h4 class="text-primary invoice-clients invoice_title pl-2">
                                    {{ Str::limit(html_entity_decode($creditNote->title), 5, '...') }}
                                </h4>
                                (<small class="text-primary">{{ $creditNote->credit_note_number }}</small>)
                            </a>
                        </div>
                        <div class="credit-note-action d-none">
                            @if ($creditNote->payment_status !== \App\Models\CreditNote::PAYMENT_STATUS_CLOSED)
                                @can('update_credit_notes')
                                    <a title="{{ __('messages.common.edit') }}"
                                        href="{{ route('credit-notes.edit', $creditNote->id) }}"><i
                                            class="fa fa-edit text-warning"></i></a>
                                @endcan
                            @endif
                            @can('delete_credit_notes')
                                <a title="{{ __('messages.common.delete') }}"
                                    class="text-danger action-btn delete-btn tickets-delete"
                                    data-id="{{ $creditNote->id }}" href="#">
                                    <i class="fa fa-trash"></i>
                                </a>
                            @endcan

                        </div>
                    </div>
                    <div class="card-body d-flex justify-content-between pt-1 px-3">
                        <div class="d-table w-100">
                            <div>
                                <i class="fas fa-street-view"></i><span class="text-decoration-none">
                                    {{ !empty($creditNote->customer) ? html_entity_decode(Str::limit($creditNote->customer->company_name, 15)) : '' }}</span>
                            </div>
                            <span class="d-table-row w-100">
                                <big class="d-table-cell w-100 d-mobile-block">
                                    {{-- <i class="{{ getCurrencyClassFromIndex($creditNote->currency) }}"></i> {{ number_format( $creditNote->total_amount, 2) }} --}}
                                </big>
                                <span
                                    class="mt-mobile-2 badge badge-{{ \App\Models\CreditNote::STATUS_COLOR[$creditNote->payment_status] }} text-uppercase">
                                    {{ App\Models\CreditNote::PAYMENT_STATUS[$creditNote->payment_status] }}
                                </span>
                            </span>
                            <span class="d-table-row text-nowrap w-100">
                                {{ Carbon\Carbon::parse($creditNote->credit_note_date)->translatedFormat('jS M, Y') }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="mt-0 mb-5 col-12 d-flex justify-content-center  mb-5 rounded">
                <div class="p-2">
                    @if (empty($search))
                        <p class="text-dark">{{ __('messages.credit_note.no_credit_note_available') }}</p>
                    @else
                        <p class="text-dark">{{ __('messages.credit_note.no_credit_note_found') }}</p>
                    @endif
                </div>
            </div>
        @endforelse
        @if ($creditNotes->count())
            <div class="mt-0 mb-5 col-12">
                <div class="row paginatorRow">
                    <div class="col-lg-2 col-md-6 col-sm-12 pt-2">
                        <span class="d-inline-flex">
                            {{ __('messages.common.showing') }}
                            <span class="font-weight-bold ml-1 mr-1">{{ $creditNotes->firstItem() }}</span> -
                            <span class="font-weight-bold ml-1 mr-1">{{ $creditNotes->lastItem() }}</span>
                            {{ __('messages.common.of') }}
                            <span class="font-weight-bold ml-1">{{ $creditNotes->total() }}</span>
                        </span>
                    </div>
                    <div class="col-lg-10 col-md-6 col-sm-12 d-flex justify-content-end">
                        {{ $creditNotes->links() }}
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
