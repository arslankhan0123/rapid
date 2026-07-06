<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectInsurance extends Model
{
    protected $table = 'project_insurances';

    protected $fillable = [
        'projects_calculation_id',
        'insurance_amount',
        'project_months',
        'per_month'
    ];

    public function projectCalculation()
    {
        return $this->belongsTo(ProjectsCalculation::class, 'projects_calculation_id');
    }
}