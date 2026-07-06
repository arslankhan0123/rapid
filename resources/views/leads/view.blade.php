@extends('layouts.app')
@section('title')
    {{ __('messages.lead.view') }}
@endsection
@section('page_css')
    <link href="{{ asset('assets/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ asset('assets/css/bs4-summernote/summernote-bs4.css') }}">
    <link href="{{ asset('css/bootstrap-datetimepicker.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ asset('assets/css/int-tel/css/intlTelInput.css') }}">
    <style>
        .text-dark {
            font-weight: bold !important;
        }
    </style>
@endsection
@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{ __('messages.lead.view') }}</h1>
            <div class="section-header-breadcrumb">
                <a href="{{ url()->previous() }}"
                    class="btn btn-primary form-btn float-right-mobile">{{ __('messages.lead.list') }}</a>
            </div>
        </div>
        <div class="section-body">
            @include('layouts.errors')
            <div class="card">
                <div class="card-body">

                    <div class="row">
                        <div class="col-md-3 col-sm-6">
                            <p><span class="text-dark">{{ __('messages.lead.client_name') }}</span><br>
                                <span class="itemTxt">{{ $lead->name }}</span>
                            </p>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <p><span class="text-dark">{{ __('messages.lead.product_group') }}</span><br>
                                <span class="itemTxt">{{ $lead->productGroup->name }}</span>
                            </p>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <p><span class="text-dark">{{ __('messages.lead.product') }}</span><br>
                                <span class="itemTxt">{{ $lead->product->title }}</span>
                            </p>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <p><span class="text-dark">{{ __('messages.lead.budget') }}</span><br>
                                <span class="itemTxt">{{ $lead->budget }}</span>
                            </p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3 col-sm-6">
                            <p><span class="text-dark">{{ __('messages.lead.priority') }}</span><br>
                                <span class="itemTxt">{{ $lead->priority }}</span>
                            </p>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <p><span class="text-dark">{{ __('messages.lead.start_date') }}</span><br>
                                <span
                                    class="itemTxt">{{ \Carbon\Carbon::parse($lead->start_date)->format('d M Y') }}</span>
                            </p>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <p><span class="text-dark">{{ __('messages.lead.assignee') }}</span><br>
                                <span class="itemTxt">{{ $lead->assignedTo->first_name ?? null }}
                                    {{ $lead->assignedTo->last_name ?? null }}</span>
                            </p>
                        </div>
                        <div class="col-md-3 col-sm-12">
                            <p><span class="text-dark">{{ __('messages.lead.contact') }}</span><br>
                                <span class="itemTxt">{{ $lead->contact }}</span>
                            </p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3 col-sm-6">
                            <p><span class="text-dark">{{ __('messages.contact.position') }}</span><br>
                                <span class="itemTxt">{{ $lead->position }}</span>
                            </p>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <p><span class="text-dark">{{ __('messages.lead.source') }}</span><br>
                                <span class="itemTxt">{{ $lead->leadSource->name }}</span>
                            </p>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <p><span class="text-dark">{{ __('messages.lead.employees') }}</span><br>
                                <span class="itemTxt">{{ $lead->employees }}</span>
                            </p>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <p><span class="text-dark">{{ __('messages.lead.branches') }}</span><br>
                                <span class="itemTxt">{{ $lead->branches }}</span>
                            </p>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <p><span class="text-dark">{{ __('messages.lead.business') }}</span><br>
                                <span class="itemTxt">{{ $lead->business ?? null }}</span>
                            </p>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <p><span class="text-dark">{{ __('messages.lead.automation') }}</span><br>
                                <span class="itemTxt">{{ $lead->automation ? 'Yes' : 'No' }}</span>
                            </p>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <p><span class="text-dark">{{ __('messages.common.status') }}</span><br>
                                <span class="itemTxt">{{ $lead->leadStatus->name }}</span>
                            </p>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <p><span class="text-dark">{{ __('messages.customer.default_language') }}</span><br>
                                <span class="itemTxt">{{ $lead->language_name }}</span>
                            </p>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <p><span class="text-dark">{{ __('messages.lead.mobile') }}</span><br>
                                <span class="itemTxt">{{ $lead->mobile }}</span>
                            </p>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <p><span class="text-dark">{{ __('messages.lead.whatsapp') }}</span><br>
                                <span class="itemTxt">{{ $lead->whatsapp }}</span>
                            </p>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <p><span class="text-dark">{{ __('messages.lead.phone') }}</span><br>
                                <span class="itemTxt">{{ $lead->phone }}</span>
                            </p>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <p><span class="text-dark">{{ __('messages.lead.fax') }}</span><br>
                                <span class="itemTxt">{{ $lead->fax }}</span>
                            </p>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <p><span class="text-dark">{{ __('messages.lead.email') }}</span><br>
                                <span class="itemTxt">{{ $lead->email }}</span>
                            </p>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <p><span class="text-dark">{{ __('messages.lead.website') }}</span><br>
                                <span class="itemTxt">{{ $lead->website }}</span>
                            </p>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <p><span class="text-dark">{{ __('messages.customer.country') }}</span><br>
                                <span class="itemTxt">{{ $lead->leadCountry->name }}</span>
                            </p>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <p><span class="text-dark">{{ __('messages.lead.state') }}</span><br>
                                <span class="itemTxt">{{ $lead->state->name }}</span>
                            </p>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <p><span class="text-dark">{{ __('messages.lead.city') }}</span><br>
                                <span class="itemTxt">{{ $lead->city->name }}</span>
                            </p>
                        </div>
                        <div class="col-md-3 col-sm-6 ">
                            <p><span class="text-dark">{{ __('messages.lead.area') }}</span><br>
                                <span class="itemTxt">{{ $lead->area->name }}</span>
                            </p>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <p><span class="text-dark">{{ __('messages.lead.facebook') }}</span><br>
                                <span class="itemTxt">{{ $lead->facebook }}</span>
                            </p>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <p><span class="text-dark">{{ __('messages.lead.instagram') }}</span><br>
                                <span class="itemTxt">{{ $lead->instagram }}</span>
                            </p>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <p><span class="text-dark">{{ __('messages.lead.linkedin') }}</span><br>
                                <span class="itemTxt">{{ $lead->linkedin }}</span>
                            </p>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <p><span class="text-dark">{{ __('messages.lead.location') }}</span><br>
                                <span class="itemTxt">{{ $lead->location }}</span>
                            </p>
                        </div>

                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <p><span class="text-dark">{{ __('messages.lead.notes') }}</span><br>
                            </p>
                            {!! $lead->description !!}
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </section>
    @include('tags.common_tag_modal')
    @include('lead_sources.add_modal')
    @include('leads.lead_status_modal')
@endsection
@section('page_scripts')
    <script src="{{ asset('assets/js/select2.min.js') }}"></script>
    <script src="{{ mix('assets/js/bs4-summernote/summernote-bs4.js') }}"></script>
    <script src="{{ asset('js/bootstrap-datetimepicker.min.js') }}"></script>
    <script src="{{ asset('assets/js/int-tel/js/intlTelInput.min.js') }}"></script>
    <script src="{{ asset('assets/js/int-tel/js/utils.min.js') }}"></script>
@endsection
@section('scripts')
    <script>
        let isEdit = true;
        let createCustomerUrl = '{{ route('leads.contactAsPerCustomer') }}';
    </script>
    <script src="{{ mix('assets/js/custom/input-price-format.js') }}"></script>
@endsection
