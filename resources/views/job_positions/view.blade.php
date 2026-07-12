@extends('layouts.app')
@section('title')
    Job Position Details
@endsection
@section('content')
    <section class="section">
        <div class="section-header item-align-right">
            <h1>Job Position Details</h1>
            <div class="section-header-breadcrumb float-right">
                <a href="{{ route('job-positions.index') }}" class="btn btn-primary form-btn">Back</a>
            </div>
        </div>
        <div class="section-body">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-6 mb-3">
                            <h6>Position Code</h6>
                            <p>{{ $jobPosition->position_code ?? 'N/A' }}</p>
                        </div>
                        <div class="col-sm-6 mb-3">
                            <h6>Position Title</h6>
                            <p>{{ $jobPosition->title }}</p>
                        </div>
                        <div class="col-sm-6 mb-3">
                            <h6>Job Category</h6>
                            <p>{{ $jobPosition->category ? $jobPosition->category->name : 'N/A' }}</p>
                        </div>
                        <div class="col-sm-6 mb-3">
                            <h6>Department</h6>
                            <p>{{ $jobPosition->department ? $jobPosition->department->name : 'N/A' }}</p>
                        </div>
                        <div class="col-sm-6 mb-3">
                            <h6>Employment Type</h6>
                            <p>{{ $jobPosition->employment_type ?? 'N/A' }}</p>
                        </div>
                        <div class="col-sm-6 mb-3">
                            <h6>Experience Required</h6>
                            <p>{{ $jobPosition->experience_required ?? 'N/A' }}</p>
                        </div>
                        <div class="col-sm-6 mb-3">
                            <h6>Minimum Salary</h6>
                            <p>{{ $jobPosition->min_salary ? number_format($jobPosition->min_salary, 2) : 'N/A' }}</p>
                        </div>
                        <div class="col-sm-6 mb-3">
                            <h6>Maximum Salary</h6>
                            <p>{{ $jobPosition->max_salary ? number_format($jobPosition->max_salary, 2) : 'N/A' }}</p>
                        </div>
                        <div class="col-sm-6 mb-3">
                            <h6>Status</h6>
                            <p>
                                @if($jobPosition->status == 'Open')
                                    <span class="badge badge-success">Open</span>
                                @elseif($jobPosition->status == 'Closed')
                                    <span class="badge badge-danger">Closed</span>
                                @else
                                    <span class="badge badge-secondary">Inactive</span>
                                @endif
                            </p>
                        </div>
                        <div class="col-sm-6 mb-3">
                            <h6>Required Skills</h6>
                            <p>
                                @forelse($jobPosition->skills as $skill)
                                    <span class="badge badge-info mr-1 mb-1">{{ $skill->name }}</span>
                                @empty
                                    <span class="text-muted">None</span>
                                @endforelse
                            </p>
                        </div>
                        <div class="col-sm-12 mb-3">
                            <h6>Job Description</h6>
                            <p>{!! $jobPosition->description ?? 'N/A' !!}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
