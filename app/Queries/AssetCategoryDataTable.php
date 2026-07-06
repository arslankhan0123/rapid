<?php

namespace App\Queries;


use App\Models\AssetCategory;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class TagDataTable
 */
class AssetCategoryDataTable
{
    /**
     * @param  array  $input
     * @return AssetCategory
     */
    public function get($input = [])
    {
        /** @var AssetCategory $query */
        $query = AssetCategory::all();
        return $query;
    }
}
