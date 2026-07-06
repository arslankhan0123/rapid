@extends('layouts.app')
@section('title')
    {{ __('messages.contract.contract_summary') }}
@endsection
@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{ __('messages.contract.contract_summary') }}</h1>
            <div class="section-header-breadcrumb">
                <div class="card-header-action">
                    <a href="{{ route('contracts.index') }}"
                       class="btn btn-primary form-btn float-right-mobile">{{ __('messages.contract.contract_list') }}
                    </a>
                </div>
            </div>
        </div>
        <div class="section-body">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-12 col-lg-12 col-md-12 col-sm-12">
                            <div class="row">
                                <div class="col-lg-6 col-md-12 col-sm-12 mb-3">
                                    <h6 class="contract-summary-heading">{{ __('messages.contract.contracts_by_type') }}</h6>
                                    <canvas id="contractBarChart" width="400" height="400"></canvas>
                                </div>
                                <div class="col-lg-6 col-md-12 col-sm-12">
                                    <h6 class="contract-summary-heading">{{ __('messages.contract.contracts_value_by_type') }}</h6>
                                    <canvas id="contractLineChart" width="400" height="400"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
@section('page_scripts')
    <script>
        let contractTypeData = JSON.parse('@json($contractForTypes)');
        let contractValueData = JSON.parse('@json($contractForValues)');
    </script>
    <script src="{{ mix('assets/js/chart/Chart.min.js') }}"></script>
    <script src="{{ mix('assets/js/contracts/contract-summary.js') }}"></script>
@endsection
