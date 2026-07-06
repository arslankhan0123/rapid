<div class="container-fluid">
    <div class="row">
        <div class="form-group col-md-6 col-sm-12">
            <strong>{{ Form::label('company_name', __('messages.branches.company')) }}</strong>
            <p style="color: #555;">{{ $branch->company_name }}</p>
        </div>
        <div class="form-group col-md-6 col-sm-12">
            <strong>{{ Form::label('company_name', __('messages.branches.branch_name')) }}</strong>
            <p style="color: #555;">{{ $branch->name }}</p>
        </div>
        <div class="form-group col-md-6 col-sm-12">
            <strong>{{ Form::label('vat_number', __('messages.customer.vat_number')) }}</strong>
            <p style="color: #555;">{{ $branch->vat_number }}</p>
        </div>
        <div class="form-group col-md-6 col-sm-12">
            <strong>{{ Form::label('website', __('messages.customer.website')) }}</strong>
            <p style="color: #555;">{{ $branch->website }}</p>
        </div>
        <div class="form-group col-md-6 col-sm-12">
            <strong>{{ Form::label('phone', __('messages.customer.phone')) }}</strong>
            <p style="color: #555;">{{ $branch->phone }}</p>
        </div>
        <div class="form-group col-md-6 col-sm-12">
            <strong>{{ Form::label('currency', __('messages.customer.currency')) }}</strong>
            <p style="color: #555;">{{ $branch->currency->name }}</p>
        </div>
        <div class="form-group col-md-6 col-sm-12">
            <strong>{{ Form::label('country', __('messages.customer.country')) }}</strong>
            <p style="color: #555;">{{ $branch->country->name }}</p>
        </div>
        <div class="form-group col-md-6 col-sm-12">
            <strong>{{ Form::label('city', __('messages.customer.city')) }}</strong>
            <p style="color: #555;">{{ $branch->city }}</p>
        </div>
        <div class="form-group col-md-6 col-sm-12">
            <strong>{{ Form::label('state', __('messages.customer.state')) }}</strong>
            <p style="color: #555;">{{ $branch->state }}</p>
        </div>
        <div class="form-group col-md-6 col-sm-12">
            <strong>{{ Form::label('zip_code', __('messages.customer.zip_code')) }}</strong>
            <p style="color: #555;">{{ $branch->zip_code }}</p>
        </div>
        <div class="form-group col-md-6 col-sm-12">
            <strong>{{ Form::label('zip_code', __('messages.banks.name')) }}</strong>
            <p style="color: #555;">{{ $branch->bank?->name ?? '' }}</p>
        </div>


        <div class="form-group col-md-6 col-sm-12">
            <strong>{{ Form::label('zip_code', 'Print Format') }}</strong>
            <p style="color: #555;">{{ $printFormats[$branch->print_format] ?? 'N/A' }}</p>
        </div>


        <div class="form-group col-md-6 col-sm-12">
            <strong>{{ Form::label('address', __('messages.customer.address')) }}</strong>
            <div style="color: #555;"> {!! $branch->address !!}</div>
        </div>

        <div class="form-group col-md-6 col-sm-12">
            <strong>{{ Form::label('address', __('messages.customer.address')) }} Arabic</strong>
            <div style="color: #555;"> {!! $branch->address_ar !!}</div>
        </div>
        <div class="form-group col-md-6 col-sm-12">
            <strong>{{ Form::label('invoice_format', 'Invoice Format') }}</strong>
            <p style="color: #555;">{{ $invoiceFormats[$branch->invoice_format] ?? 'N/A' }}</p>
        </div>


    </div>
    <div class="modal-body employeeForm">
        <div
            style="background:#6777ef;width:100%;border-radius:2px; color:white;height:50px;padding:10px 0px 10px 0px;margin-bottom:10px;">
            <h4 class="text-center">{{ __('messages.employees.manage_documents') }}</h4>
        </div>
        <div class="row " style="padding:0px 16px 0px 16px;">
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
                    @foreach ($branch->documents as $document)
                        <tr>
                            <td>{{ $document->name }}</td>
                            <td>{{ $document->file }}</td>
                            <td>{{ $document->expiry_date ? \Carbon\Carbon::parse($document->expiry_date)->format('d/m/Y') : 'N/A' }}
                            </td>
                            <td>
                                <a href="{{ asset('uploads/public/branch_docs/' . basename($document->file)) }}"
                                    class="btn btn-primary" target="_blank" title="View PDF">
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
</div>
