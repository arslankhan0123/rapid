<table class="table table-responsive-sm table-responsive-md table-responsive-lg table-striped table-bordered"
    id="documentsTable">
    <thead>
        <tr>
            <th scope="col">{{ __('messages.documents.document') }}</th>
            <th scope="col">{{ __('messages.documents.description') }}</th>
            @if (auth()->id() === 1)
                <th scope="col">{{ __('messages.documents.user') }}</th>
            @endif
            <th scope="col">{{ __('messages.documents.uploaded_at') }}</th>
            <th scope="col">{{ __('messages.common.action') }}</th>
        </tr>
    </thead>
    <tbody>
    </tbody>
</table>
