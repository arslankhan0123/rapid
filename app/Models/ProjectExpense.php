<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProjectExpense extends Model
{
    use HasFactory;

    protected $table = 'project_expenses';

    protected $fillable = [
        'project_calculation_id',
        'name',
        'amount',
        'percentage',
        'unit',
        'quantity',
        'total_amount'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'percentage' => 'decimal:2',
        'quantity' => 'decimal:2',
        'total_amount' => 'decimal:2'
    ];

    public function projectCalculation()
    {
        return $this->belongsTo(ProjectsCalculation::class, 'project_calculation_id');
    }
}
