<?php

namespace App\Queries;

use App\Models\Announcement;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class AnnouncementDataTable
 */
class AnnouncementDataTable
{
    /**
     * @param  array  $input
     * @return Announcement
     */
    public function get($input = [])
    {
        /** @var Announcement $query */
        $query = Announcement::query()->select('announcements.*')->latest();

        $query->when(isset($input['status']) && $input['status'] !== Announcement::STATUS_ARRAY,
            function (Builder $q) use ($input) {
                $q->where('status', $input['status']);
            });

        $query->when(isset($input['showToClient']) && $input['showToClient'] !== Announcement::SHOW_TO_CLIENT_ARRAY,
            function (Builder $q) use ($input) {
                $q->where('show_to_clients', $input['showToClient']);
            });

        return $query;
    }
}
