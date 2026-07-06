<div class="container-fluid">
    <div class="row">
        <div class="form-group col-md-6 col-sm-12">
            <strong>{{ Form::label('company_name', __('messages.customer.company_name') ) }}</strong><span
                class="required">*</span>
            <p style="color: #555;">{{ $supplier->company_name }}</p>
        </div>
        <div class="form-group col-md-6 col-sm-12">
            <strong>{{ Form::label('vat_number', __('messages.customer.vat_number') ) }}</strong>
            <p style="color: #555;">{{ $supplier->vat_number }}</p>
        </div>
        <div class="form-group col-md-6 col-sm-12">
            <strong>{{ Form::label('website', __('messages.customer.website') ) }}</strong>
            <p style="color: #555;">{{ $supplier->website }}</p>
        </div>
        <div class="form-group col-md-6 col-sm-12">
            <strong>{{ Form::label('phone', __('messages.customer.phone') ) }}</strong><br>
            <p style="color: #555;">{{ $supplier->phone }}</p>
        </div>
        <div class="form-group col-md-6 col-sm-12">
            <strong>{{ Form::label('currency', __('messages.customer.currency') ) }}</strong>
            <p style="color: #555;">{{ $supplier->currency ? $data['currencies'][$supplier->currency] : '' }}</p>
        </div>
        <div class="form-group col-md-6 col-sm-12">
            <strong>{{ Form::label('country', __('messages.customer.country') ) }}</strong>
            <p style="color: #555;">{{ $supplier->country ? $data['countries'][$supplier->country] : '' }}</p>
        </div>
        <div class="form-group col-md-6 col-sm-12">
            <strong>{{ Form::label('default_language', __('messages.customer.default_language') ) }}</strong>
            <p style="color: #555;">
                {{ $supplier->default_language ? $data['languages'][$supplier->default_language] : '' }}</p>
        </div>
        {{-- <div class="form-group col-md-6 col-sm-12">
            <strong>{{ Form::label('groups', __('messages.customer.groups') ) }}</strong>
            <p style="color: #555;">
                @foreach ($supplierGroups as $group)
                    {{ $data['supplierGroups'][$group->group_id] }}{{ !$loop->last ? ', ' : '' }}
                @endforeach
            </p>
        </div> --}}
        <div class="form-group col-md-6 col-sm-12">
            <strong>{{ Form::label('street', __('messages.customer.street') ) }}</strong>
            <p style="color: #555;">{{ $supplier->street }}</p>
        </div>
        {{-- <div class="form-group col-md-6 col-sm-12">
            <strong>{{ Form::label('city', __('messages.customer.city') ) }}</strong>
            <p style="color: #555;">{{ $supplier->city }}</p>
        </div> --}}
        <div class="form-group col-md-6 col-sm-12">
            <strong>{{ Form::label('zip', __('messages.customer.zip_code') ) }}</strong>
            <p style="color: #555;">{{ $supplier->zip }}</p>
        </div>
        <div class="form-group col-md-6 col-sm-12">
            <strong>{{ Form::label('zip', 'Opening Balance' ) }}</strong>
            <p style="color: #555;">{{ $supplier->opening_balance ?? 0 }}</p>
        </div>
        {{-- <div class="form-group col-md-6 col-sm-12">
            <strong>{{ Form::label('state', __('messages.customer.state') ) }}</strong>
            <p style="color: #555;">{{ $supplier->state }}</p>
        </div> --}}


        <div
            style="background:#6777ef;width:100%;border-radius:2px; color:white;height:50px;padding:10px 0px 10px 0px;margin-bottom:10px;">
            <h4 class="text-center">{{ __('messages.employees.manage_documents') }}</h4>
        </div>
        <div class="row col" style="padding:0px 16px 0px 16px;">
            <table class="table table-bordered table-responsive">
                <thead>
                    <tr>
                        <th style="width: 35%;">{{ __('messages.employees.documents') }}</th>
                        <th style="width: 35%;">{{ __('messages.employees.files') }}</th>
                        <th style="width: 35%;">{{ __('messages.employees.expiry_date') }}</th>
                        <th style="width: 10%;">{{ __('messages.common.action') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($supplier->documents as $document)
                        <tr>
                            <td>{{ $document->name }}</td>
                            <td>{{ $document->file }}</td>
                            <td>{{ $document->expiry_date ? \Carbon\Carbon::parse($document->expiry_date)->format('d/m/Y') : 'N/A' }}
                            </td>
                            <td>
                                <a href="{{ asset('uploads/public/supplier_docs/' . basename($document->file)) }}"
                                    class="btn btn-outline-primary" target="_blank" title="View PDF">
                                    <i class="fas fa-eye"></i>

                                </a>

                            </td>
                        </tr>
                    @endforeach

                </tbody>
            </table>

        </div>

    </div>
</div>
