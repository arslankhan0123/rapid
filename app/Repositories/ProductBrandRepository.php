<?php

namespace App\Repositories;

use App\Models\ProductBrand;
use Illuminate\Support\Arr;

class ProductBrandRepository extends BaseRepository
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
        return ProductBrand::class;
    }
    public function create($input)
    {
        return ProductBrand::create(Arr::only($input, ['title','description']));
    }
}
