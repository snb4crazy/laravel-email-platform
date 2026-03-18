<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MailMessageEvent extends Model
{
    public const TYPE_RECEIVED = 'received';

    public const TYPE_QUEUED = 'queued';

    public const TYPE_SENDING = 'sending';

    public const TYPE_SENT = 'sent';

    public const TYPE_DELIVERED = 'delivered';

    public const TYPE_BOUNCED = 'bounced';

    public const TYPE_COMPLAINED = 'complained';

    public const TYPE_OPENED = 'opened';

    public const TYPE_CLICKED = 'clicked';

    public const TYPE_UNSUBSCRIBED = 'unsubscribed';

    public const TYPE_FAILED = 'failed';

    public const TYPE_RETRIED = 'retried';

    public const TYPE_CANCELLED = 'cancelled';

    protected $fillable = [
        'mail_message_id',
        'type',
        'payload',
        'ip',
        'user_agent',
        'provider_event_id',
        'occurred_at',
    ];

    protected $casts = [
        'payload' => 'array',
        'occurred_at' => 'datetime',
    ];

    public function mailMessage(): BelongsTo
    {
        return $this->belongsTo(MailMessage::class);
    }
}
