<?php

namespace App\Queries\Clients;

use App\Models\Announcement;

/**
 * Class AnnouncementDataTable
 */
class AnnouncementDataTable
{
    /**
     * @return Announcement
     */
    public function get()
    {
        /** @var Announcement $query */
        $query = Announcement::whereShowToClients(true)->select('announcements.*');

        return $query;
    }
}
