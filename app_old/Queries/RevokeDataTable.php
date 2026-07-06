<?php


namespace App\Queries;

use App\Models\Revoke;
use Illuminate\Database\Eloquent\Builder;


class RevokeDataTable
{
    public function get($input = [])
    {
        $query = Revoke::with(['employee', 'termination', 'employee.branch'])
            ->orderBy('created_at', 'desc')
            ->get();

        return $query;
    }
}
