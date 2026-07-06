<?php

namespace App\Queries;

use App\Models\Announcement;
use App\Models\Language;

/**
 * Class TranslatorManagerDataTable
 */
class TranslatorManagerDataTable
{
    /**
     * @return Announcement
     */
    public function get()
    {
        /** @var Announcement $query */
        $query = Language::query()->latest();

        return $query;
    }
}
