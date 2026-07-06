   <!-- Task Assign Modal -->
    <div class="modal fade" id="taskAssignModal" tabindex="-1" role="dialog" aria-labelledby="taskAssignModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="taskAssignModalLabel">{{ __('messages.task-assign.add') }}</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            @include('task_assign.partials.create') {{-- Move your existing Task Assign form here as a partial --}}
          </div>
        </div>
      </div>
    </div>
    
