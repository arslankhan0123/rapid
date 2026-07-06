<!-- Modal -->
<div id="importModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Import</h5>
                <button type="button" class="close" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            {{ Form::open(['id' => 'importAttendances', 'enctype' => 'multipart/form-data']) }}
            <div class="modal-body">
                <div class="row justify-content-end">
                    <div class="form-group col-sm-6 col-md-7">
                        <a href="{{ asset('uploads/public/sample_csv/' . basename('sample.csv')) }}" target="_blank" class="btn text-primary">
                          {{ __('messages.attendances.download_csv')}}
                        </a>
                    </div>
                </div>


                <div class="row">
                    <div class="form-group col-sm-12">
                        {{ Form::label('csv_file', __('messages.attendances.upload_csv') . ':') }}<span
                                    class="required">*</span>
                        {{ Form::file('csv_file', ['class' => 'form-control', 'required']) }}
                    </div>

                </div>
                <div class="text-right">
                    {{ Form::button(__('messages.common.submit'), ['type' => 'submit', 'class' => 'btn btn-primary', 'id' => 'btnImport', 'data-loading-text' => "<span class='spinner-border spinner-border-sm'></span> Processing..."]) }}
                    <button type="button" id="btnCancel" class="btn btn-light ml-1"
                        data-dismiss="modal">Cancel</button>
                </div>
            </div>
            {{ Form::close() }}

        </div>
    </div>
</div>
