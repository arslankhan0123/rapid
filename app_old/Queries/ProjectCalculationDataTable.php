<?php

namespace App\Queries;

use App\Models\ProjectsCalculation;
use Illuminate\Database\Eloquent\Builder;

class ProjectCalculationDataTable
{
    public function get($input = [])
    {
        $query = ProjectsCalculation::orderBy('created_at', 'desc')->get();
        return $query;
    }
}
