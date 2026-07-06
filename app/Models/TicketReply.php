<?php

namespace App\Models;

use Barryvdh\LaravelIdeHelper\Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\Models\TicketReply
 *
 * @property int $id
 * @property int $ticket_id
 * @property int $user_id
 * @property string $reply
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static Builder|TicketReply newModelQuery()
 * @method static Builder|TicketReply newQuery()
 * @method static Builder|TicketReply query()
 * @method static Builder|TicketReply whereCreatedAt($value)
 * @method static Builder|TicketReply whereId($value)
 * @method static Builder|TicketReply whereReply($value)
 * @method static Builder|TicketReply whereTicketId($value)
 * @method static Builder|TicketReply whereUpdatedAt($value)
 * @method static Builder|TicketReply whereUserId($value)
 * @mixin Eloquent
 */
class TicketReply extends Model
{
    /**
     * @var string
     */
    protected $table = 'ticket_replies';

    /**
     * @var string[]
     */
    protected $fillable = ['ticket_id', 'user_id', 'reply'];

    /**
     * @var string[]
     */
    protected $casts = [
        'ticket_id' => 'integer',
        'user_id' => 'integer',
        'reply' => 'string',
    ];
}
