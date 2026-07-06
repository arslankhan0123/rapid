<div>
    <div class="row">
        <div class="col-md-12">
            <div wire:loading id="overlay-screen-lock">
                <div class="live-wire-infy-loader">
                    @include('loader')
                </div>
            </div>
        </div>
        <div class="col-lg-12 col-md-12">
            <div class="row mb-3 justify-content-end flex-wrap">
                <div>
                    <div class="selectgroup mr-3">
                        <input wire:model.debounce.100ms="search" type="search" autocomplete="off"
                               id="search" placeholder="{{ __('messages.common.search') }}"
                               class="form-control">
                    </div>
                </div>
            </div>
            @if(count($countries) > 0)
                <div class="content">
                    <div class="row position-relative">
                        @foreach($countries as $country)
                            <div class="col-12 col-sm-12 col-md-6 col-xl-4 mb-3">
                                <div class="hover-effect-country position-relative mb-4 country-card-hover-border">
                                    <div class="country-listing-details">
                                        <div class="d-flex country-listing-description">
                                            <div class="country-data">
                                                <h3 class="country-listing-title mb-1">
                                                    <a href="#"
                                                       class="text-dark text-decoration-none countries-listing-text show-btn"
                                                       data-id="{{ $country->id }}" data-toggle="tooltip"
                                                       title="{{ html_entity_decode($country->name) }}">
                                                        {{ Str::limit(html_entity_decode($country->name), 8, '...') }}
                                                    </a>
                                                </h3>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="country-action-btn">
                                        <a title="{{ __('messages.common.edit') }}"
                                           class="btn action-btn edit-btn country-edit"
                                           data-id="{{ $country->id }}"
                                           href="#">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                        <a title="{{ __('messages.common.delete') }}"
                                           class="btn action-btn delete-btn country-delete"
                                           data-id="{{ $country->id }}"
                                           href="#">
                                            <i class="fa fa-trash"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    @if($countries->count() > 0)
                        <div class="mt-0 mb-5 col-12">
                            <div class="row paginatorRow">
                                <div class="col-lg-2 col-md-6 col-sm-12 pt-2">
                                <span class="d-inline-flex">
                                    {{ __('messages.common.showing') }}
                                    <span class="font-weight-bold ml-1 mr-1">{{ $countries->firstItem() }}</span> -
                                    <span class="font-weight-bold ml-1 mr-1">{{ $countries->lastItem() }}</span> {{ __('messages.common.of') }}
                                    <span class="font-weight-bold ml-1">{{ $countries->total() }}</span>
                                </span>
                                </div>
                                <div class="col-lg-10 col-md-6 col-sm-12 d-flex justify-content-end">
                                    {{ $countries->links() }}
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            @else
                <div class="col-lg-12 col-md-12 d-flex justify-content-center">
                    @if(empty($search))
                        <p class="text-dark">{{ __('messages.country.no_country_available') }}</p>
                    @else
                        <p class="text-dark">{{ __('messages.country.no_country_found') }}</p>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>

