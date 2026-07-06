<?php

namespace App\Models;

use Barryvdh\LaravelIdeHelper\Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\Models\ContactEmailNotification
 *
 * @property int $id
 * @property int $contact_id
 * @property int $email_notification_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static Builder|ContactEmailNotification newModelQuery()
 * @method static Builder|ContactEmailNotification newQuery()
 * @method static Builder|ContactEmailNotification query()
 * @method static Builder|ContactEmailNotification whereContactId($value)
 * @method static Builder|ContactEmailNotification whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ContactEmailNotification
 *     whereEmailNotificationId($value)
 * @method static Builder|ContactEmailNotification whereId($value)
 * @method static Builder|ContactEmailNotification whereUpdatedAt($value)
 * @mixin Eloquent
 */
class ContactEmailNotification extends Model
{
    /**
     * @var string
     */
    protected $table = 'contact_email_notifications';

    /**
     * @var string[]
     */
    protected $fillable = [
        'contact_id',
        'email_notification_id',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'contact_id' => 'integer',
        'email_notification_id' => 'integer',
    ];
}
