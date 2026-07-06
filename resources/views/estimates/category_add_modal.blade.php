<!-- Add Task Modal -->
    <div class="modal fade" id="addCategoryModal" tabindex="-1" role="dialog" aria-labelledby="addCategoryModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Category</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    {{ Form::open(['id' => 'addNewForm']) }}
    
                        <div class="alert alert-danger d-none" id="validationErrorsBox"></div>
                        <div class="row">
                             <div class="form-group col-sm-12">
                                {{ Form::label('title', __('messages.common.name') . ':') }}<span class="required">*</span>
                                {{ Form::text('name', null, ['class' => 'form-control', 'required', 'id' => 'productUnit_title', 'autocomplete' => 'off']) }}
                            </div>
                            <div class="form-group col-sm-12 mb-0">
                                {{ Form::label('description', __('messages.assets.category_description') . ':') }}
                                {{ Form::textarea('description', null, ['class' => 'form-control summernote-simple', 'id' => 'createDescription']) }}
                            </div>
                            
                        </div>
                        <div class="text-right mr-1">
                            {{ Form::button(__('messages.common.submit'), ['type' => 'submit', 'class' => 'btn btn-primary', 'id' => 'btnSave', 'data-loading-text' => "<span class='spinner-border spinner-border-sm'></span> Processing..."]) }}
    
                        </div>
    
                        {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
