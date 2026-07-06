<?php

namespace App\Queries;

use App\Models\ArticleGroup;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class TagDataTable
 */
class ArticleGroupDataTable
{
    /**
     * @return ArticleGroup|Builder
     */
    public function get()
    {
        /** @var ArticleGroup $query */
        $query = ArticleGroup::query()->select('article_groups.*')->withCount('articles')->latest();

        return $query;
    }
}
