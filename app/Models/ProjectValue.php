<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProjectValue extends Model
{
    use HasFactory;

    protected $table = 'project_values';

    protected $fillable = [
        'project_calculation_id',
        'category',
        'amount',
        'unit',
        'quantity',
        'discount',
        'percentage',
        'gross_amount',
        'net_amount'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'percentage' => 'decimal:2',
        'quantity' => 'decimal:2',
        'discount' => 'decimal:2',
        'gross_amount' => 'decimal:2',
        'net_amount' => 'decimal:2'
    ];

    public function projectCalculation()
    {
        return $this->belongsTo(ProjectsCalculation::class, 'project_calculation_id');
    }
}
