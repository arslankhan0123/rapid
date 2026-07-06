<?php

namespace App\Queries;

use App\Models\SafetyMaterial;
use Illuminate\Database\Eloquent\Builder;

class SafetyMaterialDataTable
{
    // public function get($input = [])
    // {
    //     $query = SafetyMaterial::with(['employee'])->select('safety_materials.*');

    //     if (!empty($input['search'])) {
    //         $search = $input['search'];
    //         $query->where(function ($q) use ($search) {
    //             $q->where('category', 'LIKE', "%$search%")
    //                 ->orWhere('amount', 'LIKE', "%$search%")
    //                 ->orWhere('duration', 'LIKE', "%$search%")
    //                 ->orWhereHas('employee', function ($q) use ($search) {
    //                     $q->where('name', 'LIKE', "%$search%");
    //                 });
    //         });
    //     }

    //     return $query->orderBy('date', 'desc');
    // }

    public function get($input = [])
    {
        $query = SafetyMaterial::with('employee')->select('safety_materials.*');

        // Filter by employee
        if (!empty($input['employee_id'])) {
            $query->where('employee_id', $input['employee_id']);
        }

        // Global search
        if (!empty($input['search']['value'])) {
            $search = $input['search']['value'];
            $query->where(function ($q) use ($search) {
                $q->where('category', 'LIKE', "%$search%")
                    ->orWhere('amount', 'LIKE', "%$search%")
                    ->orWhere('duration', 'LIKE', "%$search%")
                    ->orWhereHas('employee', fn($q) => $q->where('name', 'LIKE', "%$search%"));
            });
        }

        return $query->orderBy('date', 'desc')->get();
    }
}
