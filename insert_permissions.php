<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$permissions = ['view_locations', 'create_locations', 'update_locations', 'delete_locations', 'manage_locations'];

$role = \Spatie\Permission\Models\Role::findByName('staff_member');
if ($role) {
    $role->givePermissionTo($permissions);
    echo "Permissions assigned to staff_member role.\n";
}
