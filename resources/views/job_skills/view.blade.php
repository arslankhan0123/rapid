@extends('layouts.app')
@section('title')
    Job Skill Details
@endsection
@section('content')
    <section class="section">
        <div class="section-header item-align-right">
            <h1>Job Skill Details</h1>
            <div class="section-header-breadcrumb float-right">
                <a href="{{ route('job-skills.index') }}" class="btn btn-primary form-btn">Back</a>
            </div>
        </div>
        <div class="section-body">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-6 mb-3">
                            <h6>Skill Code</h6>
                            <p>{{ $jobSkill->skill_code ?? 'N/A' }}</p>
                        </div>
                        <div class="col-sm-6 mb-3">
                            <h6>Skill Name</h6>
                            <p>{{ $jobSkill->name }}</p>
                        </div>
                        <div class="col-sm-6 mb-3">
                            <h6>Skill Category</h6>
                            <p>{{ $jobSkill->category ? $jobSkill->category->name : 'N/A' }}</p>
                        </div>
                        <div class="col-sm-6 mb-3">
                            <h6>Skill Level</h6>
                            <p>{{ $jobSkill->level ?? 'N/A' }}</p>
                        </div>
                        <div class="col-sm-6 mb-3">
                            <h6>Status</h6>
                            <p>{{ $jobSkill->is_active ? 'Active' : 'Inactive' }}</p>
                        </div>
                        <div class="col-sm-12 mb-3">
                            <h6>Description</h6>
                            <p>{!! $jobSkill->description ?? 'N/A' !!}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
