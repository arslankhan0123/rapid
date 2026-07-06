<script id="categoryActionTemplate" type="text/x-jsrender">

    <div style="width:100px;">
   <a title="<?php echo __('messages.common.delete') ?>" class="btn btn-danger action-btn has-icon delete-btn" data-id="{{:id}}" href="#">
        <i class="fa fa-trash"></i>
   </a>
      <a title="<?php echo __('messages.common.edit') ?>" class="btn btn-warning action-btn has-icon edit-btn" data-id="{{:id}}" href="#">
        <i class="fa fa-edit"></i>
   </a>
   <a title="<?php echo __('messages.common.view') ?>" class="btn btn-info action-btn has-icon view-btn" data-id="{{:id}}">
        <i class="fa fa-eye"></i>
   </a>
 </div>

</script>
