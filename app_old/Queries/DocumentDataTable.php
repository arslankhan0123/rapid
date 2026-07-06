<?php

namespace App\Queries;

use App\Models\Document;
use Illuminate\Database\Eloquent\Builder;

class DocumentDataTable
{
    public function get($input = [])
    {
        $query = Document::with(['user']);

        // If not admin, only show user's own documents
        if (auth()->id() !== 1) {
            $query->where('user_id', auth()->id());
        }

        // Filter by user if provided
        if (!empty($input['user_id'])) {
            $query->where('user_id', $input['user_id']);
        }

        return $query->orderBy('created_at', 'desc');
    }
}