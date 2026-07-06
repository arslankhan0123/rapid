<div id="viewModal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('messages.attendances.view') }}</h5>
                <button type="button" aria-label="Close" class="close" data-dismiss="modal">×</button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="form-group col-sm-12 col-md-6">
                        <strong>{{ Form::label('title', __('messages.attendances.iqama_no')) }}</strong>
                        <p style="color: #555;" id="viewIqamaNo"></p>
                    </div>
                    <div class="form-group col-sm-12 col-md-6">
                        <strong>{{ Form::label('title', __('messages.attendances.employee')) }}</strong>
                        <p style="color: #555;" id="viewEmployeeName"></p>
                    </div>
                    <div class="form-group col-sm-12 col-md-6">
                        <strong>{{ Form::label('title', __('messages.designations.name')) }}</strong>
                        <p style="color: #555;" id="viewDesignationName"></p>
                    </div>
                    <div class="form-group col-sm-12 col-md-6">
                        <strong>{{ Form::label('title', __('messages.department.departments')) }}</strong>
                        <p style="color: #555;" id="viewDepartment"></p>
                    </div>
                    <div class="form-group col-sm-12 col-md-6">
                        <strong>{{ Form::label('title', __('messages.department.sub_departments')) }}</strong>
                        <p style="color: #555;" id="viewSubDepartment"></p>
                    </div>

                    <div class="form-group col-sm-6 col-md-6">
                        <strong>{{ Form::label('description', __('messages.attendances.total_hours')) }}</strong>
                        <div style="color: #555;"><span id="viewTotalHours"></span></div>
                    </div>
                    <div class="form-group col-sm-6 col-md-6">
                        <strong>{{ Form::label('description', __('messages.attendances.date')) }}</strong>
                        <div style="color: #555;"><span id="viewDate"></span></div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
