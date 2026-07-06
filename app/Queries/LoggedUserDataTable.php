<?php

namespace App\Queries;

use App\Models\LoggedUser;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;

class LoggedUserDataTable
{
    // public function get($input = [])
    // {
    //     $query = LoggedUser::with('user');

    //     if (!empty($input['search'])) {
    //         $search = $input['search'];
    //         $query->whereHas('user', function ($q) use ($search) {
    //             $q->where('first_name', 'LIKE', "%$search%")
    //                 ->orWhere('last_name', 'LIKE', "%$search%")
    //                 ->orWhere('email', 'LIKE', "%$search%")
    //                 ->orWhere('phone', 'LIKE', "%$search%");
    //         })->orWhere('ip_address', 'LIKE', "%$search%")
    //             ->orWhere('country', 'LIKE', "%$search%")
    //             ->orWhere('city', 'LIKE', "%$search%");
    //     }

    //     if (!empty($input['status'])) {
    //         $query->where('status', $input['status']);
    //     }

    //     return $query->orderBy('last_activity_at', 'desc');
    // }

    public function get($input = [])
    {
        $query = LoggedUser::with('user');

        // Global search
        if (!empty($input['search']['value'])) {
            $search = $input['search']['value'];
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($userQuery) use ($search) {
                    $userQuery->where('first_name', 'LIKE', "%$search%")
                        ->orWhere('last_name', 'LIKE', "%$search%")
                        ->orWhere('email', 'LIKE', "%$search%")
                        ->orWhere('phone', 'LIKE', "%$search%");
                })->orWhere('ip_address', 'LIKE', "%$search%")
                    ->orWhere('country', 'LIKE', "%$search%")
                    ->orWhere('city', 'LIKE', "%$search%")
                    ->orWhere('region', 'LIKE', "%$search%");
            });
        }

        // Column-specific search
        if (!empty($input['columns'])) {
            foreach ($input['columns'] as $column) {
                if ($column['searchable'] === 'true' && !empty($column['search']['value'])) {
                    $searchValue = $column['search']['value'];
                    $columnName = $column['data']; // Use 'data' instead of 'name'

                    if ($columnName === 'user.email') {
                        $query->whereHas('user', function ($q) use ($searchValue) {
                            $q->where('email', 'LIKE', "%$searchValue%");
                        });
                    } else if ($columnName === 'user.phone') {
                        $query->whereHas('user', function ($q) use ($searchValue) {
                            $q->where('phone', 'LIKE', "%$searchValue%");
                        });
                    } else if (!empty($columnName) && $columnName !== 'action') {
                        $query->where($columnName, 'LIKE', "%$searchValue%");
                    }
                }
            }
        }

        // Status filter
        if (!empty($input['status'])) {
            $query->where('status', $input['status']);
        }

        // User filter
        if (!empty($input['user_id'])) {
            $query->where('user_id', $input['user_id']);
        }

        // Handle ordering
        if (!empty($input['order'])) {
            $orderColumnIndex = $input['order'][0]['column'];
            $orderDirection = $input['order'][0]['dir'];
            $orderColumnName = $input['columns'][$orderColumnIndex]['data']; // Use 'data'

            // Handle related table ordering
            if (strpos($orderColumnName, 'user.') === 0) {
                $relationColumn = str_replace('user.', '', $orderColumnName);
                $query->join('users', 'logged_users.user_id', '=', 'users.id')
                    ->orderBy("users.$relationColumn", $orderDirection)
                    ->select('logged_users.*');
            } else if ($orderColumnName === 'full_name') {
                $query->join('users', 'logged_users.user_id', '=', 'users.id')
                    ->orderByRaw("CONCAT(users.first_name, ' ', users.last_name) $orderDirection")
                    ->select('logged_users.*');
            } else {
                $query->orderBy($orderColumnName, $orderDirection);
            }
        } else {
            $query->orderBy('last_activity_at', 'desc');
        }

        return $query;
    }
}
