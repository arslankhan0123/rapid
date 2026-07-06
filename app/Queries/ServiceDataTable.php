<?php

namespace App\Queries;

use App\Models\Service;

/**
 * Class TagDataTable
 */
class ServiceDataTable
{
    /**
     * @return Service
     */
    public function get()
    {
        /** @var Service $query */
        $query = Service::query()->select('services.*')->latest();

        return $query;
    }
}
