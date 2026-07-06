<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProjectCalculationPartner extends Model
{
    use HasFactory;

    protected $table = 'project_calculation_partners';

    protected $fillable = [
        'projects_calculation_id',
        'name',
        'amount',
        'percentage',
        'per_partner'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'percentage' => 'decimal:2',
        'per_partner' => 'decimal:2'
    ];

    public function project()
    {
        return $this->belongsTo(ProjectsCalculation::class, 'projects_calculation_id');
    }
}
