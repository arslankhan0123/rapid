<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoggedUser extends Model
{
    protected $table = 'logged_users';

    protected $fillable = [
        'user_id',
        'session_id',
        'ip_address',
        'user_agent',
        'country',
        'city',
        'region',
        'timezone',
        'latitude',
        'longitude',
        'accuracy',
        'postal_code',
        'address',
        'location_source',
        'login_at',
        'last_activity_at',
        'status',
        'logout_at',
    ];

    protected $casts = [
        'login_at' => 'datetime',
        'last_activity_at' => 'datetime',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'logout_at' => 'datetime'
    ];

    // Add these accessors
    protected $appends = ['full_name', 'location'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isOnline(): bool
    {
        return $this->status === 'online' &&
            $this->last_activity_at->gt(now()->subMinutes(5));
    }

    public function getDurationAttribute(): string
    {
        return $this->login_at->diffForHumans();
    }

    public function getLastActivityAttribute(): string
    {
        return $this->last_activity_at->diffForHumans();
    }

    // Add this accessor for full_name
    public function getFullNameAttribute()
    {
        if ($this->user) {
            return $this->user->first_name . ' ' . $this->user->last_name;
        }

        return 'Unknown User';
    }

    // Add this accessor for location
    public function getLocationAttribute()
    {
        $locationParts = [];

        if (!empty($this->city)) {
            $locationParts[] = $this->city;
        }

        if (!empty($this->region)) {
            $locationParts[] = $this->region;
        }

        if (!empty($this->country)) {
            $locationParts[] = $this->country;
        }

        return implode(', ', $locationParts) ?: 'Unknown';
    }
}
