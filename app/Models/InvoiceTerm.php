<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceTerm extends Model
{
    use HasFactory;

    protected $table = 'invoice_terms';

    // Define the fillable fields
    protected $fillable = [
        'invoice_id',
        'description',
        'terms_id',
    ];

    // Define relationships if necessary
    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }

    public function term()
    {
        return $this->belongsTo(Term::class, 'terms_id');
    }
}
