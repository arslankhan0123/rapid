<!-- DataTable Section -->
<table id="restoreDataTables" class="table table-striped table-bordered">
    <thead>
        <tr>
            <th>Sl</th>
            <th>File Name</th>
            <th>backup by</th>
            <th>backup at</th>
            <th>restored by</th>
            <th>restored at</th>
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
                <td>{{ $backup['restored_by'] }}</td>
                <td>{{ $backup['restored_at'] }}</td>
                <td style="width:100px;text-align:end;">

                        @can('restore')
                            <button class="btn btn-warning btn-sm restore-backup" data-file="{{ $backup['file_name'] }}"
                                onclick="showRestoreModal('{{ $backup['file_name'] }}', '{{ $backup['id'] }}')">
                                <i class="fas fa-undo-alt"></i>
                            </button>
                        @endcan


                </td>
            </tr>
        @endforeach
    </tbody>
</table>
