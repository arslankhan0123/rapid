<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Document extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'documents';

    protected $fillable = [
        'user_id',
        'document',
        'description'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getDocumentUrlAttribute()
    {
        return asset('storage/documents/' . $this->document);
    }

    public function getDocumentExtensionAttribute()
    {
        return pathinfo($this->document, PATHINFO_EXTENSION);
    }
}
