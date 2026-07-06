<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Backup extends Model
{
    use HasFactory;


    protected $table = 'backups';
    protected $fillable = [
        'name',
        'user_id',
        'restored_by',
        'restored_at'
    ];
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function restoredBy()
    {
        return $this->belongsTo(User::class, 'restored_by');
    }
}
