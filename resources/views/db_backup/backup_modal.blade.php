<!-- Modal -->
<div id="importModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create Database Backup</h5>
                <button type="button" class="close" aria-label="Close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>
                        <input type="radio" name="backupType" value="manual" id="manualOption">
                        Manual
                    </label>
                    <label class="ml-3">
                        <input type="radio" name="backupType" value="automatic" id="automaticOption" checked>
                        Automatic
                    </label>
                </div>

                <!-- Manual Section (default visible) -->
                <div id="manualSection">
                    @can('create_backup')
                        <button id="createBackupBtn" class="btn btn-primary">
                            <span class="btn-text">Create Backup</span>
                            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                        </button>
                    @endcan
                </div>

                <!-- Automatic Section (hidden by default) -->
                <div id="automaticSection" style="display: none;">
                    <form action="{{ route('backup.schedule') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label for="backup_frequency">Backup Frequency</label><span class="required">*</span>
                                <select class="form-control" name="backup_frequency" id="backup_frequency" required>
                                    <option value="2"
                                        {{ isset($currentSchedule) && $currentSchedule->frequency == 2 ? 'selected' : '' }}>
                                        Daily</option>
                                    <option value="3"
                                        {{ isset($currentSchedule) && $currentSchedule->frequency == 3 ? 'selected' : '' }}>
                                        Weekly</option>
                                    <option value="4"
                                        {{ isset($currentSchedule) && $currentSchedule->frequency == 4 ? 'selected' : '' }}>
                                        Monthly</option>
                                </select>
                            </div>

                            <div class="form-group col-md-6">
                                <label for="backup_time">Time</label><span class="required">*</span>
                                {{-- <input type="time" class="form-control" name="time" id="backup_time"
                                    value="{{ $currentSchedule->time ?? '' }}" required> --}}
                                <input type="time" class="form-control" name="time" id="backup_time"
                                    step="1"
                                    value="{{ isset($currentSchedule) ? \Carbon\Carbon::parse($currentSchedule->time)->format('H:i:s') : '' }}"
                                    required>
                            </div>
                        </div>

                        <div class="row" id="day-selection" style="display: none;">
                            <div class="form-group col-md-12">
                                <label for="backup_day">Select Day</label><span class="required">*</span>
                                <select class="form-control" name="day" id="backup_day">
                                    <option value="Saturday"
                                        {{ isset($currentSchedule) && $currentSchedule->day == 'Saturday' ? 'selected' : '' }}>
                                        Saturday</option>
                                    <option value="Sunday"
                                        {{ isset($currentSchedule) && $currentSchedule->day == 'Sunday' ? 'selected' : '' }}>
                                        Sunday</option>
                                    <option value="Monday"
                                        {{ isset($currentSchedule) && $currentSchedule->day == 'Monday' ? 'selected' : '' }}>
                                        Monday</option>
                                    <option value="Tuesday"
                                        {{ isset($currentSchedule) && $currentSchedule->day == 'Tuesday' ? 'selected' : '' }}>
                                        Tuesday</option>
                                    <option value="Wednesday"
                                        {{ isset($currentSchedule) && $currentSchedule->day == 'Wednesday' ? 'selected' : '' }}>
                                        Wednesday</option>
                                    <option value="Thursday"
                                        {{ isset($currentSchedule) && $currentSchedule->day == 'Thursday' ? 'selected' : '' }}>
                                        Thursday</option>
                                    <option value="Friday"
                                        {{ isset($currentSchedule) && $currentSchedule->day == 'Friday' ? 'selected' : '' }}>
                                        Friday</option>
                                </select>
                            </div>
                        </div>
                        <div class="row justify-content-end mr-1">
                            <button type="submit" class="btn btn-primary">Set Schedule</button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
