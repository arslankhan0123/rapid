@extends('layouts.app')
@section('title')
    {{ __('messages.lead.lead_convert_customer_chart') }}
@endsection
@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{ __('messages.lead.lead_convert_customer_chart') }}</h1>
            <div class="section-header-breadcrumb">
                <div class="card-header-action">
                    <a href="{{ route('leads.index') }}"
                       class="btn btn-primary form-btn float-right-mobile">{{ __('messages.lead.lead_list') }}
                    </a>
                </div>
            </div>
        </div>
        <div class="section-body">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-12 col-lg-12 col-md-12 col-sm-12">
                            <h6 class="contract-summary-heading mb-5">{{ __('messages.lead.lead_convert_customer') }}</h6>
                            <canvas id="leadConvertChart" width="400" height="150"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
@section('scripts')
    <script>
        let leads = JSON.parse('@json($leads)');
        let currentMonthDates = JSON.parse('@json($currentMonthDates)');
    </script>
    <script src="{{ mix('assets/js/chart/Chart.min.js') }}"></script>
    <script src="{{ mix('assets/js/leads/lead-convert-customer-chart.js') }}"></script>
@endsection
