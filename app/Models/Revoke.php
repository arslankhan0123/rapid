<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Revoke extends Model
{
    use HasFactory;

    protected $fillable = [
        'termination_id',
        'employee_id',
        'reason',
        'date',
        'status'
    ];

    public function termination()
    {
        return $this->belongsTo(Termination::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
