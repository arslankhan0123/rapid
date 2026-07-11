<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobRecruiter extends Model
{
    use HasFactory;

    public $table = 'job_recruiters';

    public $fillable = [
        'recruiter_name',
    ];

    protected $casts = [
        'id' => 'integer',
        'recruiter_name' => 'string',
    ];
}
