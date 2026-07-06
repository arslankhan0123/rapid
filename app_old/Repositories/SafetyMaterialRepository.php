<?php

namespace App\Repositories;

use App\Models\SafetyMaterial;
use App\Models\Employee;
use Illuminate\Support\Arr;

class SafetyMaterialRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'date',
        'category',
        'amount',
        'duration',
        'next_date',
        'employee.name',
        'note', // added
    ];

    public function getFieldsSearchable()
    {
        return $this->fieldSearchable;
    }

    public function model()
    {
        return SafetyMaterial::class;
    }

    public function create($input)
    {
        // Calculate next date based on duration
        $safetyMaterial = new SafetyMaterial();
        $safetyMaterial->date = $input['date'];
        $safetyMaterial->duration = $input['duration'];
        $safetyMaterial->next_date = $safetyMaterial->calculateNextDate();

        return SafetyMaterial::create(Arr::only($input, [
            'date',
            'employee_id',
            'category',
            'amount',
            'duration',
            'next_date',
            'note' // added
        ]));
    }

    public function update($input, $id)
    {
        $safetyMaterial = SafetyMaterial::findOrFail($id);

        // Recalculate next date if date or duration changed
        if ($input['date'] != $safetyMaterial->date || $input['duration'] != $safetyMaterial->duration) {
            $temp = new SafetyMaterial();
            $temp->date = $input['date'];
            $temp->duration = $input['duration'];
            $input['next_date'] = $temp->calculateNextDate();
        }

        $safetyMaterial->update(Arr::only($input, [
            'date',
            'employee_id',
            'category',
            'amount',
            'duration',
            'next_date',
            'note' // added
        ]));

        return $safetyMaterial;
    }

    public function getEmployees()
    {
        return Employee::pluck('name', 'id');
    }

    public function getDurationOptions()
    {
        return [
            '1month' => '1 Month',
            '2m' => '2 Months',
            '3m' => '3 Months',
            '6m' => '6 Months',
            '9m' => '9 Months',
            '1year' => '1 Year'
        ];
    }
}
