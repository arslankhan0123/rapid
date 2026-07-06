<?php

namespace App\Repositories;

use App\Models\LoggedUser;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class LoggedUserRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'users.first_name',
        'users.last_name',
        'users.email',
        'users.phone',
        'logged_users.ip_address',
        'logged_users.country',
        'logged_users.city',
        'logged_users.status'
    ];

    public function getFieldsSearchable()
    {
        return $this->fieldSearchable;
    }

    public function model()
    {
        return LoggedUser::class;
    }

    public function getOnlineUsers()
    {
        return LoggedUser::with('user')
            ->where('status', 'online')
            ->where('last_activity_at', '>', now()->subMinutes(5))
            ->orderBy('last_activity_at', 'desc')
            ->get();
    }

    public function getAllLoggedUsers($input = [])
    {
        $query = LoggedUser::with('user')
            ->join('users', 'logged_users.user_id', '=', 'users.id')
            ->select('logged_users.*', 'users.first_name', 'users.last_name', 'users.email', 'users.phone');

        if (!empty($input['search'])) {
            $search = $input['search'];
            $query->where(function ($q) use ($search) {
                $q->where('users.first_name', 'LIKE', "%$search%")
                    ->orWhere('users.last_name', 'LIKE', "%$search%")
                    ->orWhere('users.email', 'LIKE', "%$search%")
                    ->orWhere('users.phone', 'LIKE', "%$search%")
                    ->orWhere('logged_users.ip_address', 'LIKE', "%$search%")
                    ->orWhere('logged_users.country', 'LIKE', "%$search%")
                    ->orWhere('logged_users.city', 'LIKE', "%$search%");
            });
        }

        if (!empty($input['status'])) {
            $query->where('logged_users.status', $input['status']);
        }

        return $query->orderBy('logged_users.last_activity_at', 'desc');
    }

    public function getUsersForSelect()
    {
        return User::select('id', 'first_name', 'last_name')
            ->get()
            ->pluck('full_name', 'id');
    }
}
