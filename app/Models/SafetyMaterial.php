<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SafetyMaterial extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'safety_materials';

    protected $fillable = [
        'date',
        'employee_id',
        'category',
        'amount',
        'duration',
        'next_date',
        'note'
    ];

    protected $casts = [
        'date' => 'date',
        'next_date' => 'date',
        'amount' => 'decimal:2'
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function calculateNextDate()
    {
        $date = $this->date;

        switch ($this->duration) {
            case '1month':
                return $date->addMonth();
            case '2m':
                return $date->addMonths(2);
            case '3m':
                return $date->addMonths(3);
            case '6m':
                return $date->addMonths(6);
            case '9m':
                return $date->addMonths(9);
            case '1year':
                return $date->addYear();
            default:
                return $date->addMonth();
        }
    }
}
