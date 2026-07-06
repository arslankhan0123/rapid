<div id="addressModal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg" style="margin-top:12%;">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header bg-primary text-white" style="width: 100%;padding-bottom:8px;">
                <h5 class="modal-title">Supplier Address</h5>
                <button type="button" aria-label="Close" class="close p-2" style="font-size: 30px;"
                    data-dismiss="modal">×</button>
            </div>

            <div class="modal-body">

                <div class="container-fluid">

                    <div class="row ">
                        <div class="form-group  col-md-6 col-sm-6">
                            {{ Form::label('estimate_number', 'Contact person') }}
                            <input type="text" class="form-control contact_person" disabled>
                        </div>
                        <div class="form-group  col-md-3 col-sm-6">
                            {{ Form::label('estimate_number', 'WhatsApp Number') }}
                            <input type="text" class="form-control whatsapp" disabled>
                        </div>
                        <div class="form-group  col-md-3 col-sm-6">
                            {{ Form::label('estimate_number', 'Mobile Number') }}
                            <input type="text" class="form-control phone" disabled>
                        </div>
                        <div class="form-group  col-md-6 col-sm-6">
                            {{ Form::label('estimate_number', 'Email Address') }}
                            <input type="text" class="form-control email" disabled>
                        </div>


                        <div class="form-group  col-md-3 col-sm-6">
                            {{ Form::label('estimate_number', 'Country') }}
                            <input type="text" class="form-control country" disabled>
                        </div>
                        <div class="form-group  col-md-3 col-sm-6">
                            {{ Form::label('estimate_number', 'State') }}
                            <input type="text" class="form-control state" disabled>
                        </div>
                        <div class="form-group  col-md-3 col-sm-6">
                            {{ Form::label('estimate_number', 'City') }}
                            <input type="text" class="form-control city" disabled>
                        </div>
                        <div class="form-group  col-md-3 col-sm-6">
                            {{ Form::label('estimate_number', 'P.O.Box') }}
                            <input type="text" class="form-control po_box" disabled>
                        </div>
                        <div class="form-group  col-md-6 col-sm-6">
                            {{ Form::label('estimate_number', 'Mailing Address') }}
                            <input type="text" class="form-control mailing_address" disabled>
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
