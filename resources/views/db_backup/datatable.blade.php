<!-- DataTable Section -->
<table id="backupTable" class="table table-striped table-bordered">
    <thead>
        <tr>
            <th>Sl</th>
            <th>File Name</th>
            <th>Created By</th>
            <th>Created At</th>
            <th style="width:100px;text-align:end;">Action</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($backups as $index => $backup)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $backup['file_name'] }}</td>
                <td>{{ $backup['user_name'] }}</td>
                <td>{{ $backup['created_at'] }}</td>
                <td style="width:100px;text-align:end;">
                    @can('delete_backup')
                        <button class="btn btn-warning btn-sm delete-backup" data-id="{{ $backup['id'] }}">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    @endcan
                    @can('download_backup')
                        <a href="{{ route('backup.download', $backup['file_name']) }}" class="btn btn-success btn-sm">
                            <i class="fas fa-download"></i>
                        </a>
                    @endcan
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
