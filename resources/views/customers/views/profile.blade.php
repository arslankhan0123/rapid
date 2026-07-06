@extends('customers.show')
@section('section')
    <ul class="nav nav-tabs mb-3" id="customer" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="customerDetails" data-toggle="tab" href="#cDetails" role="tab" aria-controls="home"
                aria-selected="true">{{ __('messages.customer.customer_details') }}</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="#addressDetails" data-toggle="tab" href="#aDetails" role="tab"
                aria-controls="profile" aria-selected="false">{{ __('messages.customer.address_details') }}</a>
        </li>
    </ul>
    <div class="tab-content" id="myTabContent2">
        <div class="tab-pane fade show active" id="cDetails" role="tabpanel" aria-labelledby="customerDetails">
            <div class="row">

                <div class="form-group col-md-2">
                    {{ Form::label('company_name', __('messages.customer.code')) }}
                    <p>{{ html_entity_decode($customer->code) }}</p>
                </div>
                <div class="form-group col-md-2">
                    {{ Form::label('company_name', __('messages.customer.company_name')) }}
                    <p>{{ html_entity_decode($customer->company_name) }}</p>
                </div>
                <div class="form-group col-md-3">
                    {{ Form::label('customer_arabic_name', 'Customer Arabic Name') }}
                    <p>{{ html_entity_decode($customer->customer_arabic_name ?? '') }}</p>
                </div>
                <div class="form-group col-md-2">
                    {{ Form::label('company_name', __('messages.customer.short_name')) }}
                    <p>{{ html_entity_decode($customer->short_name ?? '') }}</p>
                </div>
                <div class="form-group col-md-2">
                    {{ Form::label('company_name', __('messages.customer.inactive')) }}
                    <p>{{ html_entity_decode($customer->inactive ? 'Yes' : 'False') }}</p>
                </div>
                <div class="form-group col-md-2">
                    {{ Form::label('vat_number', __('messages.customer.vat_number')) }}
                    <p>{{ !empty($customer->vat_number) ? $customer->vat_number : __('messages.common.n/a') }}</p>
                </div>
                <div class="form-group col-md-2">
                    {{ Form::label('vat_number', __('messages.customer.vendor_code')) }}
                    <p>{{ !empty($customer->vendor_code) ? $customer->vendor_code : __('messages.common.n/a') }}</p>
                </div>

                <div class="form-group col-md-2 col-sm-12">
                    {{ Form::label('payment_modes', __('messages.customer.payment_mode')) }}
                    <div class="input-group">
                        @if (isset($customer) && $customer->customerPayment)
                            @php
                                $paymentModeNames = $customer->customerPayment->pluck('payment.name')->implode(', ');
                            @endphp
                            <p>{{ $paymentModeNames }}</p>
                        @else
                            <p>{{ __('No payment modes selected') }}</p>
                        @endif
                    </div>
                </div>
                <div class="form-group col-md-2">
                    {{ Form::label('currency', __('messages.customer.currency')) }}
                    <p>{{ isset($customer->currency) ? $customer->currency : __('messages.common.n/a') }}</p>
                </div>


                <div class="form-group col-md-2">
                    {{ Form::label('phone', __('messages.customer.phone')) }}
                    <p>{{ isset($customer->phone) ? $customer->phone : __('messages.common.n/a') }}</p>
                </div>
                <div class="form-group col-md-2">
                    {{ Form::label('fax', __('messages.customer.fax')) }}
                    <p>{{ isset($customer->fax) ? $customer->fax : __('messages.common.n/a') }}</p>
                </div>
                <div class="form-group col-md-2">
                    {{ Form::label('mobile', __('messages.customer.mobile')) }}
                    <p>{{ isset($customer->mobile) ? $customer->mobile : __('messages.common.n/a') }}</p>
                </div>
                <div class="form-group col-md-2">
                    {{ Form::label('whatsapp', __('messages.customer.whatsapp')) }}
                    <p>{{ isset($customer->whatsapp) ? $customer->whatsapp : __('messages.common.n/a') }}</p>
                </div>


                <div class="form-group col-md-2">
                    {{ Form::label('country', __('messages.customer.country')) }}
                    <p>{{ !empty($customer->country) ? $customer->country : __('messages.common.n/a') }}</p>
                </div>

                <div class="form-group col-md-2">
                    {{ Form::label('state', __('messages.customer.state')) }}
                    @if (isset($billingAddress['state']))
                        <p id="billingState">{{ $states->find($billingAddress['state'])->name ?? 'No state selected' }}</p>
                    @else
                        <p id="billingState">{{ __('messages.common.n/a') }}</p>
                    @endif
                </div>
                <div class="form-group col-md-2">
                    {{ Form::label('city', __('messages.customer.city')) }}
                    <p>{{ !empty($billingAddress['city']) ? $billingAddress['city'] : __('messages.common.n/a') }}</p>
                </div>
                {{-- <div class="form-group col-md-3">
                    {{ Form::label('street', __('messages.customer.street')) }}
                    <p>{{ !empty($billingAddress['street']) ? $billingAddress['street'] : __('messages.common.n/a') }}</p>
                </div> --}}
                <div class="form-group col-md-2">
                    {{ Form::label('zip', __('messages.customer.zip_code')) }}
                    <p>{{ !empty($billingAddress['zip']) ? $billingAddress['zip'] : __('messages.common.n/a') }}</p>
                </div>
                <div class="form-group col-md-2">
                    {{ Form::label('address', __('messages.customer.address')) }}
                    <p>{{ isset($customer->address) ? $customer->address : __('messages.common.n/a') }}</p>
                </div>
                <div class="form-group col-md-2">
                    {{ Form::label('email', __('messages.customer.email')) }}
                    <p>{{ isset($customer->email) ? $customer->mobile : __('messages.common.n/a') }}</p>
                </div>
                <div class="form-group col-md-2">
                    {{ Form::label('website', __('messages.customer.website')) }}
                    <p>
                        @if (!empty($customer->website))
                            <a href="{{ $customer->website }}" class="anchor-underline">{{ $customer->website }}</a>
                        @else
                            {{ __('messages.common.n/a') }}
                        @endif
                    </p>
                </div>

                <div class="form-group col-md-2">
                    {{ Form::label('default_language', __('messages.customer.default_language')) }}
                    <p>{{ isset($customer->default_language) ? $customer->default_language : __('messages.common.n/a') }}
                    </p>
                </div>
                <div class="form-group col-md-2">
                    {{ Form::label('bank', __('messages.customer.bank')) }}
                    <p>{{ $customer->bank->name ?? 'N/A' }}</p>
                </div>

                <div class="form-group col-md-2">
                    {{ Form::label('location_url', __('messages.customer.location_url')) }}
                    <p>{{ isset($customer->location_url) ? $customer->location_url : __('messages.common.n/a') }}</p>
                </div>
                <div class="form-group col-md-2">
                    {{ Form::label('location_url', 'Opening Balance') }}
                    <p>{{ isset($customer->opening_balance) ? $customer->opening_balance : 0 }}</p>
                </div>
                <div class="form-group col-md-12">
                    {{ Form::label('currency', __('messages.customer.customer_logo')) }} <br>
                    @if (isset($customer) && $customer->customer_logo)
                        <img id="preview" src="{{ asset('uploads/customer/' . $customer->customer_logo) }}"
                            alt="Customer Logo Preview"
                            style="width: 200px; height: 200px; border-radius: 5%; object-fit: cover;">
                    @endif
                </div>
                {{-- <div class="form-group col-md-3">
                    {{ Form::label('groups', __('messages.customer.groups').':') }}
                    <p>
                        @forelse($customerGroups as $customerGroup)
                            <span class="badge border border-secondary mb-1">{{ html_entity_decode($customerGroup) }}</span>
                        @empty
                            {{ __('messages.common.n/a') }}
                        @endforelse
                    </p>
                </div> --}}
                <div class="form-group col-md-3">
                    {{ Form::label('created_at', __('messages.common.created_on')) }}
                    <p><span data-toggle="tooltip" data-placement="right"
                            title="{{ \Carbon\Carbon::parse($customer->created_at)->translatedFormat('jS M, Y') }}">{{ $customer->created_at->diffForHumans() }}</span>
                    </p>
                </div>


                <div class="form-group col-md-3">
                    {{ Form::label('updated_at', __('messages.common.last_updated')) }}
                    <p><span data-toggle="tooltip" data-placement="right"
                            title="{{ \Carbon\Carbon::parse($customer->updated_at)->translatedFormat('jS M, Y') }}">{{ $customer->updated_at->diffForHumans() }}</span>
                    </p>
                </div>
            </div>
        </div>
        <div class="tab-pane fade" id="aDetails" role="tabpanel" aria-labelledby="addressDetails">
            <div class="row">
                <div class="col-md-6 col-sm-12">
                    <div class="card my-0">
                        <div class="card-header pl-2">
                            <h4 class="text-black-50 font-weight-bold">{{ __('messages.customer.billing_address') }}</h4>
                            @if (empty($billingAddress->street))
                                <a href="#" data-toggle="modal" data-target="#addModal"
                                    class="mr-3 addressModalIcon"><i class="fa fa-edit"></i></a>
                            @endif
                        </div>
                    </div>
                    @if (!empty($billingAddress))
                        <div class="form-group col-sm-12">
                            {{ Form::label('street', __('messages.customer.street')) }}
                            <p>{{ !empty($billingAddress->street) ? $billingAddress->street : __('messages.common.n/a') }}
                            </p>
                        </div>
                        <div class="form-group col-sm-12">
                            {{ Form::label('city', __('messages.customer.city')) }}
                            <p>{{ !empty($billingAddress->city) ? $billingAddress->city : __('messages.common.n/a') }}</p>
                        </div>
                        <div class="form-group col-sm-12">
                            {{ Form::label('zip', __('messages.customer.zip_code')) }}
                            <p>{{ !empty($billingAddress->zip) ? $billingAddress->zip : __('messages.common.n/a') }}</p>
                        </div>
                        <div class="form-group col-sm-12">
                            {{ Form::label('state', __('messages.customer.state')) }}
                            <p>{{ !empty($billingAddress->state) ? $billingAddress->state : __('messages.common.n/a') }}
                            </p>
                        </div>
                        <div class="form-group col-sm-12">
                            {{ Form::label('country', __('messages.customer.country')) }}
                            <p>{{ isset($billingAddress->country) ? $billingAddress->country : __('messages.common.n/a') }}
                            </p>
                        </div>
                    @else
                        <div class="address-control col-sm-12 text-center">
                            <p class="font-weight-bold">{{ __('messages.customer.billing_address_details_not_available') }}
                            </p>
                        </div>
                    @endif
                </div>
                <div class="col-md-6 col-sm-12">
                    <div class="card my-0">
                        <div class="card-header pl-2">
                            <h4 class="text-black-50 font-weight-bold">{{ __('messages.customer.shipping_address') }}</h4>
                        </div>
                    </div>
                    @if (!empty($shippingAddress))
                        <div class="form-group col-sm-12">
                            {{ Form::label('street', __('messages.customer.street')) }}
                            <p>{{ !empty($shippingAddress->street) ? $shippingAddress->street : __('messages.common.n/a') }}
                            </p>
                        </div>
                        <div class="form-group col-sm-12">
                            {{ Form::label('city', __('messages.customer.city')) }}
                            <p>{{ !empty($shippingAddress->city) ? $shippingAddress->city : __('messages.common.n/a') }}
                            </p>
                        </div>
                        <div class="form-group col-sm-12">
                            {{ Form::label('zip', __('messages.customer.zip_code')) }}
                            <p>{{ !empty($shippingAddress->zip) ? $shippingAddress->zip : __('messages.common.n/a') }}</p>
                        </div>
                        <div class="form-group col-sm-12">
                            {{ Form::label('state', __('messages.customer.state')) }}
                            <p>{{ !empty($shippingAddress->state) ? $shippingAddress->state : __('messages.common.n/a') }}
                            </p>
                        </div>
                        <div class="form-group col-sm-12">
                            {{ Form::label('country', __('messages.customer.country')) }}
                            <p>{{ isset($shippingAddress->country) ? $shippingAddress->country : __('messages.common.n/a') }}
                            </p>
                        </div>
                    @else
                        <div class="address-control col-sm-12 text-center">
                            <p class="font-weight-bold">
                                {{ __('messages.customer.shipping_address_details_not_available') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="modal-body employeeForm">
        <div
            style="background:#6777ef;width:100%;border-radius:2px; color:white;height:50px;padding:10px 0px 10px 0px;margin-bottom:10px;">
            <h4 class="text-center">{{ __('messages.employees.manage_documents') }}</h4>
        </div>
        <div class="row " style="padding:0px 16px 0px 16px;">
            <table class="table table-bordered table-responsive">
                <thead>
                    <tr>
                        <th style="width: 35%;">{{ __('messages.employees.documents') }}</th>
                        <th style="width: 35%;">{{ __('messages.employees.files') }}</th>
                        <th style="width: 35%;">{{ __('messages.employees.expiry_date') }}</th>
                        <th style="width: 10%;">{{ __('messages.common.action') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($customer?->documents as $document)
                        <tr>
                            <td>{{ $document->name }}</td>
                            <td>{{ $document->file }}</td>
                            <td>{{ $document->expiry_date ? \Carbon\Carbon::parse($document->expiry_date)->format('d/m/Y') : 'N/A' }}
                            </td>
                            <td>
                                <a href="{{ asset('uploads/public/customer_docs/' . basename($document->file)) }}"
                                    class="btn btn-outline-primary" target="_blank" title="View PDF">
                                    <i class="fas fa-eye"></i>

                                </a>

                            </td>
                        </tr>
                    @endforeach

                </tbody>
            </table>

        </div>
    </div>
    @include('customers.address-modal')
@endsection
