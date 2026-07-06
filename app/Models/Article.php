<?php

namespace App\Models;

use Barryvdh\LaravelIdeHelper\Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

/**
 * Class Article
 *
 * @version April 6, 2020, 8:30 am UTC
 *
 * @property int $id
 * @property string subject
 * @property int group_id
 * @property bool|null internal_article
 * @property bool|null $disabled
 * @property string|null $description
 * @property string|null $image
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read ArticleGroup $articleGroup
 *
 * @method static Builder|Article newModelQuery()
 * @method static Builder|Article newQuery()
 * @method static Builder|Article query()
 * @method static Builder|Article whereCreatedAt($value)
 * @method static Builder|Article whereDescription($value)
 * @method static Builder|Article whereImage($value)
 * @method static Builder|Article whereDisabled($value)
 * @method static Builder|Article whereGroupId($value)
 * @method static Builder|Article whereId($value)
 * @method static Builder|Article whereInternalArticle($value)
 * @method static Builder|Article whereSubject($value)
 * @method static Builder|Article whereUpdatedAt($value)
 *
 * @property-read int|null $media_count
 * @mixin Eloquent
 *
 * @property-read bool|string $article_attachment
 */
class Article extends Model implements HasMedia
{
    use InteractsWithMedia;

    const INTERNAL_ARTICLE_ARR = [
        1 => 'Active',
        0 => 'Deactive',
    ];

    const DISABLED_ARTICLE_ARR = [
        1 => 'Active',
        0 => 'Deactive',
    ];

    public const COLLECTION_ARTICLE_PICTURES = 'article';

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'subject' => 'required|unique:articles,subject',
        'group_id' => 'required',
        'image' => 'nullable|mimes:jpeg,jpg,png|max:2000',
    ];

    /**
     * @var string[]
     */
    public static $messages = [
        'image.max' => 'Image size should not more than 2MB.',
    ];

    /**
     * @var string
     */
    protected $table = 'articles';

    /**
     * @var string[]
     */
    protected $fillable = [
        'subject',
        'group_id',
        'internal_article',
        'disabled',
        'description',
        'image',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'subject' => 'string',
        'group_id' => 'integer',
        'internal_article' => 'boolean',
        'disabled' => 'boolean',
        'description' => 'string',
        'image' => 'string',
    ];

    /**
     * @var array
     */
    protected $appends = ['article_attachment'];

    /**
     * @return bool|string
     */
    public function getArticleAttachmentAttribute()
    {
        $media = $this->getMedia(self::COLLECTION_ARTICLE_PICTURES)->first();
        if (! empty($media)) {
            return $media->getFullUrl();
        }

        return false;
    }

    /**
     * @return BelongsTo
     */
    public function articleGroup(): BelongsTo
    {
        return $this->belongsTo(ArticleGroup::class, 'group_id');
    }

    /**
     * @return string
     */
    public function getImageUrl(): string
    {
        $media = $this->getMedia(self::COLLECTION_ARTICLE_PICTURES)->first();
        if (! empty($media)) {
            return $media->getFullUrl();
        }

        return getArticleDefaultImage();
    }
}
