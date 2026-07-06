<script id="announcementActionTemplate" type="text/x-jsrender">
    <a title="<?php echo __('messages.common.edit') ?>" class="edit-blog-btn mr-2 mt-1 edit-btn" href="#" data-id="{{:id}}" data-toggle="modal" data-target="#editModal">
    <i class="fas fa-edit card-edit-icon"></i></a>
    <a title="<?php echo __('messages.payment_mode.show') ?>" class="mt-1 mr-2 card-view-icon" href="{{:showUrl}}"><i class="fas fa-eye card-view-icon"></i></a>
    <a title="<?php echo __('messages.common.delete') ?>
    " class="delete-btn mt-1 delete-btn" href="javascript:void(0)" data-id="{{:id}}">
    <i class="fas fa-trash card-delete-icon"></i></a>



</script>
<script id="announcementShowToClientTemplate" type="text/x-jsrender">
    <label class="custom-switch pl-0" data-placement="bottom">
        <input type="checkbox" name="active" class="custom-switch-input"data-id="{{:id}}" 
            value="{{:showToClient}}" id="showToClient" data-class="active" {{:showToClient == 1 ? 'checked' : ''}}>
        <span class="custom-switch-indicator"></span>
    </label>


</script>
