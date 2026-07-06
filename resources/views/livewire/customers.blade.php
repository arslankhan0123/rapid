<div>
    <div class="row">
        <div class="col-md-12">
            <div wire:loading id="overlay-screen-lock">
                <div class="live-wire-infy-loader">
                    @include('loader')
                </div>
            </div>
        </div>
    </div>
    <div class="row mb-3 justify-content-end flex-wrap">
        <div>
            <div class="selectgroup">
                <input wire:model="searchByCustomer" type="search" id="searchByCustomer"
                    placeholder="{{ __('messages.common.search') }}" autocomplete="off"
                    class="form-control customer-dashboard-ticket-search">
            </div>
        </div>
    </div>
    <div class="users-card">
        <div class="row">
            @forelse($customers as $customer)
                <div class="col-xl-4 col-md-6">
                    <div class="hover-effect-users position-relative mb-5 users-card-hover-border users-border">
                        <div class="users-listing-details">
                            <div
                                class="d-flex users-listing-description align-items-center justify-content-center flex-column">
                                <div class="pl-0 mb-2 users-avatar">
                                    @if (isset($customer) && $customer->customer_logo)
                                        <img id="preview"
                                            src="{{ asset('uploads/customer/' . $customer->customer_logo) }}"
                                            alt="Customer Logo Preview"
                                            style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover;">
                                    @else
                                        <img src="{{ asset('assets/icons/male.png') }}" alt="user-avatar-img"
                                            class="img-responsive users-avatar-img users-img mr-2">
                                    @endif
                                </div>
                                <div class="mb-auto w-100 users-data">
                                    <div class="d-flex justify-content-center align-items-center w-100">
                                        <div>
                                            <a href="{{ url('admin/customers', $customer->id) }}"
                                                class="users-listing-title text-decoration-none">{{ \Illuminate\Support\Str::limit(html_entity_decode($customer->company_name), 15, '...') }}{{ '(' . $customer->code . ')' }}</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex align-items-center justify-content-between assigned-user pt-0 pl-3 px-5">
                            <div>
                                <div class="text-center badge badge-primary font-weight-bold" data-toggle="tooltip"
                                    data-placement="top" title="{{ __('messages.customer.total_contact') }}">
                                    {{ $customer->contact_count }}</div>
                            </div>
                            <div>
                                <div class="text-center badge badge-success font-weight-bold" data-toggle="tooltip"
                                    data-placement="top" title="{{ __('messages.customer.total_project') }}">
                                    {{ $customer->project_count }}</div>
                            </div>
                        </div>
                        <div class="users-action-btn">
                            @can('update_customers')
                                <a title="{{ __('messages.common.edit') }}" class="action-btn edit-btn users-edit"
                                    href="{{ route('customers.edit', $customer->id) }}">
                                    <i class="fa fa-edit"></i>
                                </a>
                            @endcan
                            @can('delete_customers')
                                <a title="{{ __('messages.common.delete') }}"
                                    class="action-btn customer-delete-btn users-delete" data-id="{{ $customer->id }}"
                                    href="#">
                                    <i class="fa fa-trash"></i>
                                </a>
                            @endcan

                        </div>
                    </div>
                </div>
            @empty
                <div class="col-md-12 d-flex justify-content-center mt-3">
                    @if ($searchByCustomer == null || empty($searchByCustomer))
                        <p class="text-dark">{{ __('messages.customer.no_customer_available') }}</p>
                    @else
                        <p class="text-dark">{{ __('messages.customer.no_customer_found') }}</p>
                    @endif
                </div>
            @endforelse
        </div>
    </div>
    @if ($customers->count() > 0)
        <div class="mt-0 mb-5 col-12">
            <div class="row paginatorRow">
                <div class="col-lg-2 col-md-6 col-sm-12 pt-2">
                    <span class="d-inline-flex">
                        @if ($customers->total())
                            {{ __('messages.common.showing') }}
                            <span class="font-weight-bold ml-1 mr-1">{{ $customers->firstItem() }}</span> -
                            <span class="font-weight-bold ml-1 mr-1">{{ $customers->lastItem() }}</span>
                            {{ __('messages.common.of') }}
                            <span class="font-weight-bold ml-1">{{ $customers->total() }}</span>
                        @endif
                    </span>
                </div>
                <div class="col-lg-10 col-md-6 col-sm-12 d-flex justify-content-end">
                    {{ $customers->links() }}
                </div>
            </div>
        </div>
    @endif
</div>
