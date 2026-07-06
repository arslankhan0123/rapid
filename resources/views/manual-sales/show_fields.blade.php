<ul class="nav nav-tabs mb-3" role="tablist">
    <li class="nav-item">
        <a href="{{ route('manual-sales.show',['invoice' => $invoice->id, 'group' => 'invoice_details']) }}"
           class="nav-link {{ (isset($groupName) && $groupName == 'invoice_details' || !isset($groupName)) ? 'active' : ''}}">
            {{ __('messages.invoice.invoice_details') }}
        </a>
    </li>

</ul>

@yield('section')
