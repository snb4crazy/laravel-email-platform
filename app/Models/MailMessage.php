<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class MailMessage extends Model
{
    use SoftDeletes;

    // Source constants — match SendMailJob::TYPE_*
    public const SOURCE_WEB     = 'web';
    public const SOURCE_WEBHOOK = 'webhook';

    // Status lifecycle constants
    public const STATUS_RECEIVED  = 'received';
    public const STATUS_QUEUED    = 'queued';
    public const STATUS_SENDING   = 'sending';
    public const STATUS_SENT      = 'sent';
    public const STATUS_DELIVERED = 'delivered';
    public const STATUS_FAILED    = 'failed';
    public const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'tenant_id',
        'source',
        'from_name',
        'from_email',
        'to_name',
        'to_email',
        'reply_to',
        'subject',
        'body_text',
        'body_html',
        'file_url',
        'status',
        'mailer',
        'provider_message_id',
        'is_spam',
        'spam_reported_at',
        'ip',
        'user_agent',
        'metadata',
    ];

    protected $casts = [
        'is_spam'          => 'boolean',
        'spam_reported_at' => 'datetime',
        'metadata'         => 'array',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'tenant_id');
    }

    public function events(): HasMany
    {
        return $this->hasMany(MailMessageEvent::class);
    }

    public function recordEvent(string $type, array $payload = []): MailMessageEvent
    {
        /** @var MailMessageEvent */
        return $this->events()->create([
            'type'        => $type,
            'payload'     => $payload ?: null,
            'occurred_at' => now(),
        ]);
    }
}
