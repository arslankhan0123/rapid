<?php

namespace App\Queries;

use App\Models\MasterAccount;

class MasterAccountDataTable
{
    // public function get($input = [])
    // {
    //     return MasterAccount::orderBy('account_type')
    //         ->orderBy('account_level')
    //         ->orderBy('name')
    //         ->get();
    // }

    public function get($input = [])
    {
        $query = MasterAccount::orderBy('created_at', 'desc')->get();
        return $query;
    }
}
