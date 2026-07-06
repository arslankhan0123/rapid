<?php

namespace App\Repositories;

use App\Models\Document;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class DocumentRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'document',
        'description',
        'user_id'
    ];

    public function getFieldsSearchable()
    {
        return $this->fieldSearchable;
    }

    public function model()
    {
        return Document::class;
    }

    public function create($input)
    {
        $lastDocument = null;

        // Get the user IDs (array for multiple users or single user ID)
        $userIds = $input['user_id'] ?? [auth()->id()];

        // Ensure userIds is an array even if it's a single value
        if (!is_array($userIds)) {
            $userIds = [$userIds];
        }

        if (isset($input['document']) && is_array($input['document'])) {
            foreach ($input['document'] as $index => $file) {
                if ($file instanceof \Illuminate\Http\UploadedFile) {
                    $fileName = time() . '_' . $file->getClientOriginalName();

                    // Save file
                    $destinationPath = public_path('uploads/documents/');
                    $file->move($destinationPath, $fileName);

                    // Create document for each selected user
                    foreach ($userIds as $userId) {
                        $lastDocument = Document::create([
                            'user_id'     => $userId,
                            'document'    => $fileName,
                            'description' => $input['description'][$index] ?? null,
                        ]);
                    }
                }
            }
        }

        return $lastDocument; // returns the last created model
    }

    // public function create($input)
    // {
    //     $lastDocument = null;

    //     if (isset($input['document']) && is_array($input['document'])) {
    //         foreach ($input['document'] as $index => $file) {
    //             if ($file instanceof \Illuminate\Http\UploadedFile) {
    //                 $fileName = time() . '_' . $file->getClientOriginalName();

    //                 // Save file
    //                 $destinationPath = public_path('uploads/documents/');
    //                 $file->move($destinationPath, $fileName);

    //                 // Save in DB
    //                 $lastDocument = Document::create([
    //                     'user_id'     => $input['user_id'] ?? auth()->id(),
    //                     'document'    => $fileName,
    //                     'description' => $input['description'][$index] ?? null,
    //                 ]);
    //             }
    //         }
    //     }

    //     return $lastDocument; // 👈 returns the last created model
    // }



    public function update($input, $id)
    {
        $document = Document::findOrFail($id);

        // Handle file upload if new file is provided
        if (isset($input['document']) && $input['document']) {
            // Delete old file
            if ($document->document) {
                Storage::delete('public/documents/' . $document->document);
            }

            $file = $input['document'];
            $fileName = time() . '_' . $file->getClientOriginalName();

            // Ensure directory exists
            Storage::makeDirectory('public/documents');

            $file->storeAs('public/documents', $fileName);
            $input['document'] = $fileName;
        } else {
            unset($input['document']);
        }

        $document->update(Arr::only($input, ['document', 'description']));

        return $document;
    }

    public function delete($id)
    {
        $document = Document::findOrFail($id);

        // Delete file from storage
        if ($document->document) {
            Storage::delete('public/documents/' . $document->document);
        }

        return $document->delete();
    }

    public function getUsers()
    {
        return User::select('id', 'first_name', 'last_name')
            ->get()
            ->pluck('full_name', 'id');
    }
}
