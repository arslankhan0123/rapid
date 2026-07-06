<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\PurchaseOrder;


class PurchaseOrderTerm extends Model
{
    use HasFactory;

    protected $table = 'purchase_order_terms';

    // Define the fillable fields
    protected $fillable = [
        'estimate_id',
        'description',
        'terms_id',
    ];

    // Define relationships if necessary
    public function estimate()
    {
        return $this->belongsTo(PurchaseOrder::class, 'estimate_id');
    }

    public function term()
    {
        return $this->belongsTo(Term::class, 'terms_id');
    }
}
