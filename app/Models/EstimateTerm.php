<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstimateTerm extends Model
{
    use HasFactory;

    protected $table = 'estimate_terms';

    // Define the fillable fields
    protected $fillable = [
        'estimate_id',
        'description',
        'terms_id',
    ];

    // Define relationships if necessary
    public function estimate()
    {
        return $this->belongsTo(Estimate::class, 'estimate_id');
    }

    public function term()
    {
        return $this->belongsTo(Term::class, 'terms_id');
    }
}
