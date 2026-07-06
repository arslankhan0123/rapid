 <div class="modal fade" tabindex="-1" role="dialog" id="viewModal">
     <div class="modal-dialog" role="document">
         <div class="modal-content">
             <div class="modal-header">
                 <h5 class="modal-title">{{ __('messages.expense_category.view_category') }}</h5>
                 <button type="button" aria-label="Close" class="close" data-dismiss="modal">×</button>
             </div>
             <div class="modal-body">
                 <strong> {{ Form::label('description', __('messages.common.name')) }}</strong>
                 <p id="viewName"></p>
                 <strong> {{ Form::label('description', __('messages.common.description')) }}</strong>
                 <p id="viewDescription"></p>
             </div>
             <div class="modal-footer">
                 <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
             </div>
         </div>
     </div>
 </div>
