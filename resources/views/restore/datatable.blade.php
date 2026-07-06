<div class="section-body">
    <div class="card">
        <div class="card-body">



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
                                @can('download_backup')
                                    <a href="{{ route('backup.download', $backup['id']) }}" class="btn btn-success btn-sm">
                                        <i class="fas fa-download"></i>
                                    </a>
                                @endcan
                                @can('delete_backup')
                                    <button class="btn btn-danger btn-sm delete-backup" data-id="{{ $backup['id'] }}">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                @endcan
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
