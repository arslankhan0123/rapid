<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CertificateType extends Model
{
    use HasFactory;

    protected $table = 'certificate_types'; // Table name if it's different from the default plural form

    protected $fillable = ['name']; // Attributes you can mass assign
}
