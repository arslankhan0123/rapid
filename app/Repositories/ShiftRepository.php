<?php

namespace App\Repositories;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use App\Models\Shift;

/**
 * Class CustomerRepository
 *
 * @version April 3, 2020, 6:37 am UTC
 */
class ShiftRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'name',
        'description',
        'shift_start_time',
        'shift_end_time',
        'lunch_start_time',
        'lunch_end_time',
        'color'
    ];

    /**
     * Return searchable fields
     *
     * @return array
     */
    public function getFieldsSearchable()
    {
        return $this->fieldSearchable;
    }

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Shift::class;
    }

    public function create($input)
    {

        return Shift::create(Arr::only($input, ['name','description', 'shift_start_time','shift_end_time', 'lunch_start_time', 'lunch_end_time','color']));
    }
}
