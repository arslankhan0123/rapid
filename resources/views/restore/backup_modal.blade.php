<div id="importModalRestore" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Import Database </h5>
                <div id="progressContainer" style="display: none;padding-top:6px;padding-left:10px;">
                    <div class="progress">
                        <div id="uploadProgressBar" class="progress-bar" role="progressbar" style="width: 0%;">
                            0%
                        </div>
                    </div>
                </div>
                <button type="button" class="close" aria-label="Close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                {{ Form::open(['route' => 'restore.upload', 'method' => 'POST', 'id' => 'restoreForm', 'enctype' => 'multipart/form-data']) }}
                <div class="form-group">
                    {{ Form::label('restoreFile', 'Choose a file to restore (ZIP only):') }} <span
                        class="required">*</span>
                    {{ Form::file('restore_file', ['class' => 'form-control', 'id' => 'restoreFile', 'required']) }}
                </div>

                <div class="row justify-content-end mr-1">
                    {{ Form::button('Submit', ['type' => 'submit', 'id' => 'submitBtn', 'class' => 'btn btn-primary']) }}
                </div>
                {{ Form::close() }}

            </div>
        </div>
    </div>
</div>
