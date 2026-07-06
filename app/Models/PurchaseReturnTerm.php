<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\PurchaseOrder;


class PurchaseReturnTerm extends Model
{
    use HasFactory;

    protected $table = 'purchase_return_terms';

    // Define the fillable fields
    protected $fillable = [
        'purchase_return_id',
        'description',
        'terms_id',
    ];

    // Define relationships if necessary
    public function estimate()
    {
        return $this->belongsTo(PurchaseReturn::class, 'purchase_return_id');
    }

    public function term()
    {
        return $this->belongsTo(Term::class, 'terms_id');
    }
}
