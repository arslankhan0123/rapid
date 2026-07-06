<div>
    <div class="row">
        <div class="mt-0 mb-3 col-12 d-flex justify-content-end search-display-block">
            @if(!empty($customer))
                <div class="mt-2">
                    {{Form::select('status', $proposalStatus, $statusFilter, ['id' => 'proposalFilterStatus', 'class' => 'form-control','placeholder' =>__('messages.placeholder.select_status')]) }}
                </div>
            @endif
            <div class="p-2">
                <input wire:model.debounce.100ms="search" type="search" class="form-control" placeholder="{{ __('messages.common.search')}}"
                       id="search">
            </div>
        </div>
        <div class="col-md-12">
            <div wire:loading id="live-wire-screen-lock">
                <div class="live-wire-infy-loader">
                    @include('loader')
                </div>
            </div>
        </div>
        @if(empty($customer) && empty($lead))
            <div class="col-md-12">
                <div class="row justify-content-md-center text-center mb-4">
                    <div class="owl-carousel owl-theme">
                        <div class="item">
                            <div class="ticket-statistics mx-auto bg-danger">
                                <p>{{ $statusCount->open }}</p>
                            </div>
                            <h5 class="my-0 mt-1">{{ __('messages.proposal.open') }}</h5>
                        </div>
                        <div class="item">
                            <div class="ticket-statistics mx-auto bg-warning">
                                <p>{{ $statusCount->drafted }}</p>
                            </div>
                            <h5 class="my-0 mt-1">{{ __('messages.proposal.drafted') }}</h5>
                        </div>
                        <div class="item">
                            <div class="ticket-statistics mx-auto bg-info">
                                <p>{{ $statusCount->declined }}</p>
                            </div>
                            <h5 class="my-0 mt-1">{{ __('messages.proposal.declined') }}</h5>
                        </div>
                        <div class="item">
                            <div class="ticket-statistics mx-auto bg-success">
                                <p>{{ $statusCount->accepted }}</p>
                            </div>
                            <h5 class="my-0 mt-1">{{ __('messages.proposal.accepted') }}</h5>
                        </div>
                        <div class="item">
                            <div class="ticket-statistics mx-auto bg-primary">
                                <p>{{ $statusCount->revised }}</p>
                            </div>
                            <h5 class="my-0 mt-1">{{ __('messages.proposal.revised') }}</h5>
                        </div>
                    </div>
                </div>
            </div>
        @endif
        @forelse($proposals as $proposal)
            <div class="col-12 col-md-6 col-xl-3 col-xlg-4">
                <div class="livewire-card card card-{{ \App\Models\Proposal::STATUS_COLOR[$proposal->status] }} shadow mb-5 rounded hover-card  proposal-height-210">
                    <div class="card-header d-flex justify-content-between align-items-center itemCon p-3">
                        <div class="d-flex">
                            <a href="{{ url('admin/proposals', $proposal->id) }}"
                               class="d-flex flex-wrap text-decoration-none">
                                <h4 class="invoice-clients invoice_title pl-2">
                                    {{ Str::limit(html_entity_decode($proposal->title), 5, '...') }}
                                </h4>
                                (<small>{{ $proposal->proposal_number }}</small>)
                            </a>
                        </div>
                        <div class="proposal-action-btn d-none">
                            @if($proposal->status != \App\Models\Proposal::STATUS_DECLINED)
                                <a title="{{ __('messages.common.edit') }}"
                                   href="{{ route('proposals.edit',$proposal->id) }}"><i
                                            class="fa fa-edit text-warning"></i></a>
                            @endif
                            <a title="{{ __('messages.common.delete') }}"
                               class="text-danger action-btn delete-btn tickets-delete"
                               data-id="{{ $proposal->id }}" href="#">
                                <i class="fa fa-trash"></i>
                            </a>
                        </div>
                    </div>
                    <div class="card-body d-flex justify-content-between pt-1 px-3">
                        <div class="d-table w-100">
                            <div>
                                <span class="text-decoration-none" data-toggle="tooltip"
                                      title="{{ __('messages.task.related_to') }}"> {{ $proposal->getRelatedText($proposal->related_to) }}</span>
                            </div>
                            @if($proposal->owner_type == \App\Models\Lead::class)
                                <span data-toggle="tooltip"
                                      title="{{ __('messages.proposal.lead') }}"> {{ !empty($proposal->lead) ? html_entity_decode($proposal->lead->name) : '' }}</span>
                            @else
                                <span data-toggle="tooltip"
                                      title="{{ __('messages.common.customer') }}"> {{ !empty($proposal->customer) ? html_entity_decode($proposal->customer->company_name) : ''}}</span>
                            @endif
                            <span class="d-table-row w-100">
                                <big class="d-table-cell w-100 d-mobile-block">
                                    <i class="{{ getCurrencyClassFromIndex($proposal->currency) }}"></i> {{ number_format( $proposal->total_amount, 2) }}
                                </big>
                                <span class="mt-mobile-2 badge text-uppercase status-{{ \App\Models\Proposal::STATUS[$proposal->status] }}">
                                    {{  App\Models\Proposal::STATUS[$proposal->status] }}
                                </span>
                            </span>
                            @if(!empty($proposal->open_till))
                                <span class="d-table-row text-nowrap w-100 {{ now() > Carbon\Carbon::parse($proposal->open_till)  ? 'text-danger' : '' }}">
                            {{Carbon\Carbon::parse($proposal->open_till)->translatedFormat('jS M, Y')}}
                        </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="mt-0 mb-5 col-12 d-flex justify-content-center  mb-5 rounded">
                <div class="p-2">
                    @if(empty($search))
                        <p class="text-dark">{{ __('messages.proposal.no_proposal_available') }}</p>
                    @else
                        <p class="text-dark">{{ __('messages.proposal.no_proposal_found') }}</p>
                    @endif
                </div>
            </div>
        @endforelse
        @if($proposals->count() > 0)
            <div class="mt-0 mb-5 col-12">
                <div class="row paginatorRow">
                    <div class="col-lg-2 col-md-6 col-sm-12 pt-2">
                        <span class="d-inline-flex">
                            {{ __('messages.common.showing') }}
                            <span class="font-weight-bold ml-1 mr-1">{{ $proposals->firstItem() }}</span> - 
                            <span class="font-weight-bold ml-1 mr-1">{{ $proposals->lastItem() }}</span> {{ __('messages.common.of') }} 
                            <span class="font-weight-bold ml-1">{{ $proposals->total() }}</span>
                        </span>
                    </div>
                    <div class="col-lg-10 col-md-6 col-sm-12 d-flex justify-content-end">
                        {{ $proposals->links() }}
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
