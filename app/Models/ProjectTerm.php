<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectTerm extends Model
{
    use HasFactory;

    protected $table = 'project_terms';

    // Define the fillable fields
    protected $fillable = [
        'project_id',
        'description',
        'terms_id',
    ];

    // Define relationships if necessary
    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function term()
    {
        return $this->belongsTo(Term::class, 'terms_id');
    }
}
