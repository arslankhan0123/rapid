<?php

namespace App\Repositories;

use App\Models\Size;
use Illuminate\Support\Arr;


class ProductSizeRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'title',
        'description'
    ];
    public function getFieldsSearchable()
    {
        return $this->fieldSearchable;
    }

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Size::class;
    }
    public function create($input)
    {
        return Size::create(Arr::only($input, ['title','description']));
    }
}
