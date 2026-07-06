<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class AddTicketPermissionInSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissionExist = Permission::whereName('contact_tickets')->exists();

        if (! $permissionExist) {
            Permission::create([
                'name' => 'contact_tickets',
                'type' => 'Contacts',
                'display_name' => 'Contact Tickets',
            ]);
        }
    }
}
