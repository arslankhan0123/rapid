<ul class="nav nav-tabs mb-3" role="tablist">
    <li class="nav-item">
        <a href="{{ route('estimates.show',['estimate' => $estimate->id, 'group' => 'estimate_details']) }}"
           class="nav-link {{ (isset($groupName) && $groupName == 'estimate_details' || !isset($groupName)) ? 'active' : ''}}">
           Opening Stock Details
        </a>
    </li>
    <li class="nav-item d-none">
        <a href="{{ route('estimates.show',['estimate' => $estimate->id, 'group' => 'tasks']) }}"
           class="nav-link {{ (isset($groupName) && $groupName == 'tasks') ? 'active' : ''}}">
            {{ __('messages.tasks') }}
        </a>
    </li>
</ul>
<br>
@yield('section')
