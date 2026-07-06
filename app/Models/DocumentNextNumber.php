<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class DocumentNextNumber extends Model
{
    use HasFactory;
    protected $table = 'document_next_number';

    // Allow mass assignment for the 'type' and 'number' columns
    protected $fillable = ['type', 'number'];

    /**
     * Get the next number for the given document type.
     *
     * @param string $type
     * @return int
     */
    public static function getNextNumber(string $type): int
    {
        $document = self::where('type', $type)->first();
        return $document ? $document->number : 0;
    }

    public static function initializeNumber(string $type, int $startNumber = 150): int
    {
        $document = self::firstOrCreate(
            ['type' => $type],
            ['number' => $startNumber]
        );

        if ($document->number < $startNumber) {
            $document->number = $startNumber;
            $document->save();
        }

        return $document->number;
    }


    /**
     * Update the number for a given type.
     *
     * @param string $type
     * @return void
     */
    public static function updateNumber(string $type): void
    {
        $document = self::firstOrCreate(
            ['type' => $type],
            ['number' => 0] // Default starting number if no record exists
        );

        $document->increment('number');
    }
}