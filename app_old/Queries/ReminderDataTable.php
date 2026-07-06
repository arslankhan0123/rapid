<?php

namespace App\Queries;

use App\Models\Reminder;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class ReminderDataTable
 */
class ReminderDataTable
{
    /**
     * @param  array  $input
     * @return Reminder|Builder
     */
    public function get($input = [])
    {
        /** @var Reminder $query */
        $query = Reminder::with('user', 'contact.user')->select('reminders.*')
            ->where('owner_id', $input['owner_id'])
            ->where('owner_type', Reminder::REMINDER_MODULES[$input['module_id']]);

        return $query->select('reminders.*');
    }
}
