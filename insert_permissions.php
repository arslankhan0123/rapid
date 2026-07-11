<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$permissions = [
    ['name' => 'view_job_sources', 'display_name' => 'View', 'type' => 'Job Sources'],
    ['name' => 'create_job_sources', 'display_name' => 'Add', 'type' => 'Job Sources'],
    ['name' => 'update_job_sources', 'display_name' => 'Edit', 'type' => 'Job Sources'],
    ['name' => 'delete_job_sources', 'display_name' => 'Delete', 'type' => 'Job Sources'],
    ['name' => 'manage_job_sources', 'display_name' => 'Manage', 'type' => 'Job Sources'],
];

foreach ($permissions as $perm) {
    \Spatie\Permission\Models\Permission::firstOrCreate(
        ['name' => $perm['name'], 'guard_name' => 'web'],
        ['display_name' => $perm['display_name'], 'type' => $perm['type']]
    );
}

$roleAdmin = \Spatie\Permission\Models\Role::findByName('admin');
if ($roleAdmin) {
    $roleAdmin->givePermissionTo(array_column($permissions, 'name'));
    echo "Permissions assigned to admin role.\n";
}

$roleStaff = \Spatie\Permission\Models\Role::findByName('staff_member');
if ($roleStaff) {
    $roleStaff->givePermissionTo(array_column($permissions, 'name'));
    echo "Permissions assigned to staff_member role.\n";
}
