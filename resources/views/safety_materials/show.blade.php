@extends('layouts.app')
@section('title')
    {{ __('messages.safety_materials.view_safety_material') }}
@endsection
@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{ __('messages.safety_materials.view_safety_material') }}</h1>
            <div class="section-header-breadcrumb">
                <a href="{{ route('safety-materials.index') }}" class="btn btn-primary form-btn">
                    {{ __('messages.common.back') }}
                </a>
            </div>
        </div>
        <div class="section-body">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>{{ __('messages.safety_materials.date') }}:</label>
                                <p>{{ \Carbon\Carbon::parse($safetyMaterial->date)->format('d M, Y') }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>{{ __('messages.safety_materials.employee') }}:</label>
                                <p>{{ $safetyMaterial->employee->name }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>{{ __('messages.safety_materials.category') }}:</label>
                                <p>{{ $safetyMaterial->category }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>{{ __('messages.safety_materials.amount') }}:</label>
                                <p>{{ number_format($safetyMaterial->amount, 2) }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>{{ __('messages.safety_materials.duration') }}:</label>
                                <p>
                                    @php
                                        $durations = [
                                            '1month' => '1 Month',
                                            '2m' => '2 Months',
                                            '3m' => '3 Months',
                                            '6m' => '6 Months',
                                            '9m' => '9 Months',
                                            '1year' => '1 Year',
                                        ];
                                    @endphp
                                    {{ $durations[$safetyMaterial->duration] ?? $safetyMaterial->duration }}
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>{{ __('messages.safety_materials.next_date') }}:</label>
                                <p>{{ \Carbon\Carbon::parse($safetyMaterial->next_date)->format('d M, Y') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
