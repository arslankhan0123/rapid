@extends('projects.show')
@section('section')
    <div class="row">
        <div class="form-group col-md-4 col-12">
            {{ Form::label('vendor_code', __('messages.branches.name')) }}
            <p>{{ $project->branch?->name ?? '' }}</p>
        </div>
        <div class="form-group col-md-4 col-12">
            {{ Form::label('customer', __('messages.invoice.customer')) }}
            <p><a href="{{ url('admin/customers', $project->customer->id) }}"
                    class="anchor-underline">{{ html_entity_decode($project->customer->company_name) }}</a></p>
        </div>
        <div class="form-group col-md-4 col-12">
            {{ Form::label('customer', __('messages.customer.code')) }}
            <p><a href="{{ url('admin/customers', $project->customer->id) }}"
                    class="anchor-underline">{{ html_entity_decode($project->customer->code) }}</a></p>
        </div>
        <div class="form-group col-md-4 col-12">
            {{ Form::label('vendor_code', __('messages.customer.vendor_code')) }}
            <p>{{ $project->customer?->vendor_code ?? '' }}</p>
        </div>
        <div class="form-group col-md-4 col-12">
            {{ Form::label('project_name', __('messages.project.project_name')) }}
            <p>{{ html_entity_decode($project->project_name) }}</p>
        </div>
        <div class="form-group col-md-4 col-12">
            {{ Form::label('project_name', __('messages.project.project_code')) }}
            <p>{{ html_entity_decode($project->project_code ?? '') }}</p>
        </div>


        {{-- <div class="form-group col-md-4 col-12">
            {{ Form::label('contacts', __('messages.contacts') ) }}
            <p>
                @forelse($project->projectContacts as $contact)
                    <span class="badge border border-secondary mb-1">
                        <a href="{{ route('contacts.show', $contact->id) }}"
                            class="anchor-underline">{{ html_entity_decode($contact->user->full_name) }}</a>
                    </span>
                @empty
                    {{ __('messages.common.n/a') }}
                @endforelse
            </p>
        </div> --}}

        {{-- <div class="form-group col-md-4 col-12">
            {{ Form::label('progress', __('messages.project.progress').':') }}
            <p>{{ !empty($project->progress) ? $project->progress : '0' }}%</p>
        </div> --}}
        <div class="form-group col-md-4 col-12">
            {{ Form::label('billing_type', __('messages.project.billing_type')) }}
            <p>{{ $project->getBillingTypeText($project->billing_type) }}</p>
        </div>
        <div class="form-group col-md-4 col-12">
            {{ Form::label('status', __('messages.project.status')) }}
            <p>{{ $project->getStatusText($project->status) }}</p>
        </div>
        {{-- <div class="form-group col-md-4 col-12">
            {{ Form::label('estimated_hours', __('messages.project.estimated_hours') ) }}
            <p>{{ $project->estimated_hours }} {{ __('messages.invoice.hours') }}</p>
        </div> --}}
        <div class="form-group col-md-4 col-12">
            {{ Form::label('start_date', __('messages.project.start_date')) }}
            <p>{{ Carbon\Carbon::parse($project->start_date)->translatedFormat('jS M, Y') }}</p>
        </div>
        {{-- <div class="form-group col-md-4 col-12">
            {{ Form::label('deadline', __('messages.project.deadline') ) }}
            <p>{{ Carbon\Carbon::parse($project->deadline)->translatedFormat('jS M, Y') }}</p>
        </div> --}}
        {{-- <div class="form-group col-md-4 col-12">
            {{ Form::label('tags', __('messages.tags') ) }}
            <p>
                @forelse($project->tags as $tag)
                    <span class="badge border border-secondary mb-1">{{ html_entity_decode($tag->name) }} </span>
                @empty
                    {{ __('messages.common.n/a') }}
                @endforelse
            </p>
        </div> --}}


        <div class="form-group col-md-4 ">
            {{ Form::label('project_location', __('messages.project.project_location')) }}
            <br>
            {{ $project->project_location ?? __('messages.common.n/a') }}
        </div>
        <div class="form-group col-md-4 ">
            {{ Form::label('po_number', __('messages.project.po_number')) }}
            <br>
            {{ $project->po_number ?? __('messages.common.n/a') }}
        </div>
        <div class="form-group col-md-4 col-12">
            {{ Form::label('created_at', __('messages.common.created_on')) }}
            <p><span data-toggle="tooltip" data-placement="right"
                    title="{{ Carbon\Carbon::parse($project->created_at)->translatedFormat('jS M, Y') }}">{{ $project->created_at->diffForHumans() }}</span>
            </p>
        </div>
        <div class="form-group col-md-4 col-12">
            {{ Form::label('updated_at', __('messages.common.last_updated')) }}
            <p><span data-toggle="tooltip" data-placement="right"
                    title="{{ Carbon\Carbon::parse($project->updated_at)->translatedFormat('jS M, Y') }}">{{ $project->updated_at->diffForHumans() }}</span>
            </p>
        </div>
    </div>
    <div class="row">
        <div class="form-group col-md-4 col-12">
            {{ Form::label('description', __('messages.common.description')) }}
            <br>
            {!! !empty($project->description) ? html_entity_decode($project->description) : __('messages.common.n/a') !!}
        </div>
    </div>
    {{-- <div class="row">
        <div class="form-group col-md-12 col-12">
            <strong>Services Info</strong>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Item Code</th>
                        <th>Category Name</th>
                        <th>Service Name</th>
                        <th>Unit Price</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($project->services as $service)
                        <tr>
                            <td>{{ html_entity_decode($service['ref_no']) }}</td>
                            <td>{{ html_entity_decode($service['categories']['name'] ?? 'N/A') }}</td>
                            <td>{{ html_entity_decode($service->service->title ?? 'N/A') }}</td>
                            <td>{{ number_format($service['unit_price'], 2) }} SAR</td>
                            <!-- Format price to two decimal places -->
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4">{{ __('messages.common.n/a') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div> --}}

    <div class="row">
        <div class="form-group col-md-12 col-12">
            <strong>Services Info</strong>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Item Code</th>
                        <th>Category Name</th>
                        <th>Service Name</th>
                        <th>Unit Price</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($project->services as $service)
                        @php
                            // Find the service data from the services collection (like edit view)
                            $serviceData = $services->firstWhere('id', $service->service_id);
                            $categoryData = $categories[$service->category_id] ?? 'N/A';
                        @endphp
                        <tr>
                            <td>{{ $service->ref_no }}</td>
                            <td>{{ $categoryData }}</td>
                            <td>{{ $serviceData ? $serviceData->title : 'N/A' }}</td>
                            <td>{{ number_format($service->unit_price, 2) }} SAR</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4">{{ __('messages.common.n/a') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="row">
        {{-- {{dd($project->terms->toArray())}} --}}
        <div class="form-group col-md-12 col-12">
            <strong>Terms And Conditions</strong>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>SL</th> <!-- Serial Number Column -->
                        <th>Description</th> <!-- Description Column -->
                    </tr>
                </thead>
                <tbody>
                    @forelse($project->terms as $index => $term)
                        <!-- Iterate through the terms array -->
                        <tr>
                            <td>{{ $index + 1 }}</td> <!-- Serial number (1-based index) -->
                            <td>{{ html_entity_decode($term['description']) }}</td> <!-- Description -->
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2">{{ __('messages.common.n/a') }}</td> <!-- N/A message if no terms -->
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>
@endsection
