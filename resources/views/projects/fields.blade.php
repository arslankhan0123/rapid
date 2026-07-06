<div class="col-lg-12">

    <div class="row">


        {{-- <div class="form-group col-sm-12 col-md-4">
            {{ Form::label('', __('messages.branches.name')) }}
            {{ Form::select('branch_id', $usersBranches ?? [], $project->branch_id ?? null, ['class' => 'form-control select2']) }}
        </div> --}}
        <div class="form-group col-sm-12 col-md-4">
            {{ Form::label('', __('messages.branches.name')) }}
            {{ Form::select('branch_id', $usersBranches ?? [], $project->branch_id ?? null, [
                'class' => 'form-control select2',
                'placeholder' => 'Select Branch',
            ]) }}
        </div>

        <div class="form-group col-md-4">
            {{ Form::label('customer_id', __('messages.project.customer')) }}<span class="required">*</span>
            <div class="input-group ">
                {{ Form::select('customer_id', [], isset($project->customer_id) ? $project->customer_id : $customerId, ['class' => 'form-control', 'required', 'id' => 'customerSelectBox', 'autocomplete' => 'off', 'required', 'placeholder' => __('messages.placeholder.select_customer')]) }}

                <div class="input-group-append ">
                    <button type="button" class="btn btn-outline-primary" id="btnAddCustomer" style="width:50px;">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
            </div>
        </div>

        <div class="form-group col-md-4">
            {{ Form::label('', __('messages.customer.code')) }}<span class="required">*</span>
            {{ Form::text('customer_code', $project->customer->code ?? null, ['class' => 'form-control', 'id' => 'customer_code', 'required', 'autocomplete' => 'off', 'disabled' => true]) }}
        </div>
        <div class="form-group col-md-4">
            {{ Form::label('', __('messages.customer.vendor_code')) }}<span class="required">*</span>
            {{ Form::text('vendor_code', $project->customer->vendor_code ?? null, ['class' => 'form-control', 'id' => 'vendor_code', 'required', 'autocomplete' => 'off', 'disabled' => true]) }}
        </div>
        <div class="form-group  col-sm-12 col-md-4">
            {{ Form::label('status', __('messages.project.status')) }}<span class="required">*</span>
            {{ Form::select('status', $data['status'], isset($project->status) ? $project->status : null, ['class' => 'form-control', 'id' => 'statusSelectBox', 'required', 'placeholder' => __('messages.placeholder.select_status')]) }}
        </div>
        <div class="form-group col-md-4">
            {{ Form::label('project_name', __('messages.project.project_name')) }}<span class="required">*</span>
            {{ Form::text('project_name', isset($project->project_name) ? $project->project_name : null, ['class' => 'form-control', 'required', 'autocomplete' => 'off']) }}
        </div>

        <div class="form-group col-md-4">
            {{ Form::label('project_code', __('messages.project.project_code')) }}
            {{ Form::text('project_code', isset($project->project_code) ? $project->project_code : null, ['class' => 'form-control', 'required', 'autocomplete' => 'off', 'maxlength' => 10]) }}
        </div>
        {{-- <div class="form-group col-sm-6">
            {{ Form::label('members', __('messages.employees.name') ) }}<span class="required">*</span>
            {{ Form::select('members[]', $data['members'], isset($project->members) ? $project->members->pluck('user_id') : null, ['class' => 'form-control', 'id' => 'membersSelectBox', 'required', 'autocomplete' => 'off', 'multiple' => 'multiple']) }}
        </div> --}}

        <div class="form-group  col-md-4">
            {{ Form::label('billing_type', __('messages.project.billing_type')) }}<span class="required">*</span>
            {{ Form::select('billing_type', $data['billingTypes'], isset($project->billing_type) ? $project->billing_type : null, ['class' => 'form-control', 'id' => 'billingTypeSelectBox', 'required', 'placeholder' => __('messages.placeholder.select_billing_type')]) }}
        </div>


        <div class="form-group col-md-4">
            {{ Form::label('start_date', __('messages.project.start_date')) }}<span class="required">*</span>
            <div class="input-group">
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                </div>

                {{ Form::text('start_date', isset($project->start_date) ? date('Y-m-d H:i:s', strtotime($project->start_date)) : null, ['class' => 'form-control', 'required', 'id' => 'startDate', 'autocomplete' => 'off', 'required']) }}
            </div>
        </div>
        <div class="form-group col-md-4">
            {{ Form::label('start_date', __('messages.project.project_location')) }}
            {{ Form::text('project_location', $project->project_location ?? null, ['class' => 'form-control', 'autocomplete' => 'off']) }}
        </div>
        <div class="form-group col-md-4">
            {{ Form::label('start_date', __('messages.project.po_number')) }}
            {{ Form::text('po_number', $project->po_number ?? null, ['class' => 'form-control', 'autocomplete' => 'off']) }}
        </div>

        <div class="form-group col-sm-12 mb-0">
            {{ Form::label('description', __('messages.common.description')) }}
            {{ Form::textarea('description', isset($project->description) ? $project->description : null, ['class' => 'form-control summernote-simple', 'id' => 'projectDescription']) }}
        </div>


    </div>
</div>
