<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectCommission extends Model
{
    protected $table = 'project_commissions';

    protected $fillable = [
        'projects_calculation_id',
        'commission_amount',
        'project_months',
        'per_month'
    ];

    public function projectCalculation()
    {
        return $this->belongsTo(ProjectsCalculation::class, 'projects_calculation_id');
    }
}