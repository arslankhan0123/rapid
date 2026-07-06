<div id="documentModal" class="modal fade" role="dialog">
    <div class="modal-dialog  modal-lg" style="margin-top:12%;">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header bg-primary text-white" style="width: 100%;padding-bottom:8px;">
                <h5 class="modal-title">Document Information</h5>
                <button type="button" aria-label="Close" class="close p-2" style="font-size: 30px;" data-dismiss="modal">×</button>
            </div>

            <div class="modal-body">

                <div class="container-fluid">

                    <div class="row ">
                        <div class="form-group col-md-6 col-sm-6">
                            {{ Form::label('estimate_number', 'Document Number') }}
                            <input type="text" class="form-control" value="{{$nextNumber}}"  disabled>
                        </div>
                        <div class="form-group col-md-6 col-sm-6">
                            {{ Form::label('estimate_number', 'Document Date') }}
                            <input type="text" class="form-control" value="{{ date('Y-m-d') }}" disabled>
                        </div>
                        <div class="form-group col-md-6 col-sm-6">
                            {{ Form::label('estimate_number', 'Document Time') }}
                            <input type="text" class="form-control" value="{{ date('h:i a') }}" disabled>
                        </div>

                        <div class="form-group col-md-6 col-sm-6">
                            {{ Form::label('estimate_number', 'Document Type') }}
                            <input type="text" class="form-control" disabled value="Purchase Order">
                        </div>

                    </div>
                    <br>
                </div>
                <div class="text-right mr-3">
                    {{-- <button type="button" id="btnCancel" class="btn btn-primary btn-light text-white ml-1"
                        style="line-height: 30px;" data-dismiss="modal">Close</button> --}}
                    {{-- {{ Form::button(__('messages.common.submit'), ['type' => 'submit', 'class' => 'btn btn-primary', 'style' => 'line-height:30px', 'id' => 'createNewItem', 'data-loading-text' => "<span class='spinner-border spinner-border-sm'></span> Processing..."]) }} --}}

                </div>
            </div>

        </div>
    </div>
</div>
