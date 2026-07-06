<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CreditNoteTerms extends Model
{
    use HasFactory;


    protected $table = 'creditnote_terms';

    // Define the fillable fields
    protected $fillable = [
        'credit_note_id',
        'description',
        'terms_id',
    ];

    // Define relationships if necessary
    public function creditNote()
    {
        return $this->belongsTo(CreditNote::class, 'credit_note_id');
    }

    public function term()
    {
        return $this->belongsTo(Term::class, 'terms_id');
    }
}
