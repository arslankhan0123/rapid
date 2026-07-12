@extends('layouts.app')
@section('title')
    Job Category Details
@endsection
@section('content')
    <section class="section">
        <div class="section-header item-align-right">
            <h1>Job Category Details</h1>
            <div class="section-header-breadcrumb float-right">
                <a href="{{ route('job-categories.index') }}" class="btn btn-primary form-btn">Back</a>
            </div>
        </div>
        <div class="section-body">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-6 mb-3">
                            <h6>Category Code</h6>
                            <p>{{ $jobCategory->category_code ?? 'N/A' }}</p>
                        </div>
                        <div class="col-sm-6 mb-3">
                            <h6>Category Name</h6>
                            <p>{{ $jobCategory->name }}</p>
                        </div>
                        <div class="col-sm-6 mb-3">
                            <h6>Parent Category</h6>
                            <p>{{ $jobCategory->parent ? $jobCategory->parent->name : 'N/A' }}</p>
                        </div>
                        <div class="col-sm-6 mb-3">
                            <h6>Display Order</h6>
                            <p>{{ $jobCategory->display_order }}</p>
                        </div>
                        <div class="col-sm-6 mb-3">
                            <h6>Status</h6>
                            <p>{{ $jobCategory->is_active ? 'Active' : 'Inactive' }}</p>
                        </div>
                        <div class="col-sm-12 mb-3">
                            <h6>Description</h6>
                            <p>{!! $jobCategory->description ?? 'N/A' !!}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
