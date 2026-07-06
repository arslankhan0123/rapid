<div class="row">
    <div class="mt-0 mb-3 col-12 d-flex justify-content-end search-display-block">
        @if (!empty($customer))
            <div class="mt-2">
                {{ Form::select('status', $projectStatusArr, $statusFilter, ['id' => 'filterStatus', 'class' => 'form-control', 'placeholder' => __('messages.placeholder.select_status')]) }}
            </div>
        @endif
        <div class="p-2">
            <input wire:model.debounce.100ms="search" type="search" class=" form-control"
                placeholder="{{ __('messages.common.search') }}" id="search">
        </div>
    </div>
    <div class="col-md-12">
        <div wire:loading id="live-wire-screen-lock">
            <div class="live-wire-infy-loader">
                @include('loader')
            </div>
        </div>
    </div>


    {{-- <div class="col-md-12">
        <div class="row justify-content-md-center text-center mb-4">
            <div class="owl-carousel owl-theme">
                <div class="item">
                    <div class="ticket-statistics mx-auto bg-danger">
                        <p>{{ $statusCount['not_started'] }}</p>
                    </div>
                    <h5 class="my-0 mt-1">{{ __('messages.status.not_started') }}</h5>
                </div>
                <div class="item">
                    <div class="ticket-statistics mx-auto bg-primary">
                        <p>{{ $statusCount['in_progress'] }}</p>
                    </div>
                    <h5 class="my-0 mt-1">{{ __('messages.status.in_progress') }}</h5>
                </div>
                <div class="item">
                    <div class="ticket-statistics mx-auto bg-warning">
                        <p>{{ $statusCount['on_hold'] }}</p>
                    </div>
                    <h5 class="my-0 mt-1">{{ __('messages.status.on_hold') }}</h5>
                </div>
                <div class="item">
                    <div class="ticket-statistics mx-auto bg-info">
                        <p>{{ $statusCount['cancelled'] }}</p>
                    </div>
                    <h5 class="my-0 mt-1">{{ __('messages.status.cancelled') }}</h5>
                </div>
                <div class="item">
                    <div class="ticket-statistics mx-auto bg-success">
                        <p>{{ $statusCount['finished'] }}</p>
                    </div>
                    <h5 class="my-0 mt-1">{{ __('messages.status.finished') }}</h5>
                </div>
            </div>
        </div>
    </div> --}}

    @forelse($projects as $project)
        <div class="col-12 col-md-6 col-lg-6 col-xl-4 extra-large">
            <div
                class="livewire-card card card-{{ \App\Models\Project::CARD_COLOR[$project->status] }} shadow mb-5 rounded project-card-height">
                <div class="card-header d-flex justify-content-between align-items-center pt-2 pr-3 pb-3 pl-3">

                    <div class="row" style="width: 100%;">
                        <div class="col-md-9">
                            <a href="{{ route('projects.show', [$project->id, '']) }}" class="text-decoration-none ">
                                <h4 class="text-primary card-report-name">
                                    {{ Str::limit(html_entity_decode($project->project_name), 30, '...') }}{{ '(' . $project->project_code . ')' }}
                                </h4>
                            </a>
                        </div>

                        <div class="col-md-3 ">
                            <div class="row">
                                <div class="col-md-1">
                                    @can('update_projects')
                                        <a href="{{ route('projects.edit', $project->id) }}" class=" edit-btn">
                                            <i class="fas fa-edit mr-2 card-edit-icon"></i>
                                            {{-- {{ __('messages.common.edit') }} --}}
                                        </a>
                                    @endcan
                                </div>
                                <div class="col-md-1">
                                    @can('delete_projects')
                                        <a href="#" class=" delete-btn" data-id="{{ $project->id }}">
                                            <i class="fas fa-trash mr-2 card-delete-icon"></i>
                                            {{-- {{ __('messages.common.delete') }} --}}
                                        </a>
                                    @endcan
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- <a class="dropdown dropdown-list-toggle">
                        <a href="#" data-toggle="dropdown"
                            class="notification-toggle action-dropdown d-none position-xs-bottom">
                            <i class="fas fa-ellipsis-v action-toggle-mr"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right">
                            <div class="dropdown-list-content dropdown-list-icons">
                                @if ($project->status != \App\Models\Project::STATUS_CANCELLED)
                                @endif

                            </div>
                        </div>
                    </a> --}}

                </div>
                <div class="card-body pt-0 pl-3">
                    <div class="float-left">
                        <span class="projectStatistics" data-toggle="tooltip"
                            title="{{ __('messages.project.billing_type') }}">
                            {{ \App\Models\Project::BILLING_TYPES[$project->billing_type] }}
                        </span>
                    </div>
                    <div class="float-right project-card-status mt-1">
                        <span
                            class="badge {{ \App\Models\Project::STATUS_BADGE[$project->status] }} text-uppercase projectStatus">{{ \App\Models\Project::STATUS[$project->status] }}</span>
                    </div>
                    <br>
                    <div class="float-left mt-2">
                        <span class="mr-1" data-toggle="tooltip" title="{{ __('messages.project.customer') }}">
                            {{ html_entity_decode($project->customer?->company_name ?? '') }}

                        </span>
                    </div>
                </div>
                {{-- <div class="card-body d-flex justify-content-between align-items-center pt-0 pl-3 pb-2">
                    <div class="d-inline-block project-avatar-margin">
                        @foreach ($project->members as $counter => $member)
                            @if ($counter < 5)
                                <a href="{{ url('admin/members/'.$member->user->id) }}"><img
                                            class="projectUserAvatar p-0 mb-1"
                                            src="{{ $member->user->image_url }}"
                                            title="{{ html_entity_decode($member->user->full_name) }}"></a>
                            @elseif($counter == (count($project->members) - 1))
                                <span class="project_remaining_user"><b> + {{ (count($project->members) - 5) }}</b></span>
                            @endif
                        @endforeach
                    </div>
                </div> --}}
            </div>
        </div>
    @empty
        <div class="mt-0 mb-5 col-12 d-flex justify-content-center mb-5 rounded">
            <div class="p-2">
                @if (empty($search))
                    <p class="text-dark">{{ __('messages.project.no_project_available') . '.' }}</p>
                @else
                    <p class="text-dark">{{ __('messages.project.no_project_found') . '.' }}</p>
                @endif
            </div>
        </div>
    @endforelse
    @if ($projects->count() > 0)
        <div class="mt-0 mb-5 col-12">
            <div class="row paginatorRow">
                <div class="col-lg-2 col-md-6 col-sm-12 pt-2">
                    <span class="d-inline-flex">
                        {{ __('messages.common.showing') }}
                        <span class="font-weight-bold ml-1 mr-1">{{ $projects->firstItem() }}</span> -
                        <span class="font-weight-bold ml-1 mr-1">{{ $projects->lastItem() }}</span>
                        {{ __('messages.common.of') }}
                        <span class="font-weight-bold ml-1">{{ $projects->total() }}</span>
                    </span>
                </div>
                <div class="col-lg-10 col-md-6 col-sm-12 d-flex justify-content-end">
                    {{ $projects->links() }}
                </div>
            </div>
        </div>
    @endif
</div>
