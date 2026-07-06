<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BackupSchedule extends Model
{
    use HasFactory;
    protected $fillable = ['schedule_name', 'frequency', 'last_backup_at','time','day'];

}
