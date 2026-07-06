<?php

namespace App\Repositories;

use App\Models\ProductGroup;

/**
 * Class ProductGroupRepository
 *
 * @version October 11, 2021, 9:56 am UTC
 */
class ProductGroupRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'name',
        'description',
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
        return ProductGroup::class;
    }
}
