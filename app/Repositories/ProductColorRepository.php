<?php

namespace App\Repositories;

use App\Models\ProductColor;
use App\Models\Size;
use Illuminate\Support\Arr;


class ProductColorRepository extends BaseRepository
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
        return ProductColor::class;
    }
    public function create($input)
    {
        return ProductColor::create(Arr::only($input, ['title','description']));
    }
}
