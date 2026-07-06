<!-- <script id="productActionTemplate" type="text/x-jsrender">
    <a title="<?php echo __('messages.common.edit') ?>
    " href="#" class="btn  btn-warning action-btn btn has-icon edit-btn" data-id="{{:id}}">
            <i class="fa fa-edit"></i>
   </a>
   <a title="<?php echo __('messages.common.delete') ?>
    " href="#" class="btn btn-danger action-btn has-icon delete-btn" data-id="{{:id}}">
            <i class="fa fa-trash"></i>
   </a>
</script> -->

<script id="productActionTemplate" type="text/x-jsrender">
    <div class="text-right"> <!-- Add this div to align the buttons -->
        <a title="<?php echo __('messages.common.delete') ?>" href="#" class="btn btn-danger action-btn has-icon delete-btn" data-id="{{:id}}">
            <i class="fa fa-trash"></i>
        </a>

        <a title="<?php echo __('messages.common.edit') ?>" href="#" class="btn btn-warning action-btn has-icon edit-btn" data-id="{{:id}}">
            <i class="fa fa-edit"></i>
        </a>
        <a title="<?php echo __('messages.common.view') ?>" href="#" class="btn btn-info action-btn has-icon view-btn" data-id="{{:id}}">
            <i class="fa fa-eye"></i>
        </a>
    </div>
</script>


<script id="productUnitActionTemplate" type="text/x-jsrender">
    <a title="<?php echo __('messages.common.delete') ?>
    " href="#" class="btn btn-danger action-btn has-icon delete-btn" data-id="{{:id}}" style="float:right;margin:2px;">
            <i class="fa fa-trash"></i>
   </a>
    <a title="<?php echo __('messages.common.edit') ?>
    " href="#" class="btn  btn-warning action-btn btn has-icon edit-btn" data-id="{{:id}}" style="float:right;margin:2px;">
            <i class="fa fa-edit"></i>
   </a>

</script>
