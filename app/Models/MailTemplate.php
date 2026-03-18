<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class MailTemplate extends Model
{
    use SoftDeletes;

    // Event type constants — one per email use-case
    public const EVENT_CONTACT_FORM    = 'contact_form';
    public const EVENT_WEBHOOK_CONTACT = 'webhook_contact';
    // TODO: expand as new email flows are added

    protected $fillable = [
        'tenant_id',
        'event_type',
        'name',
        'subject_template',
        'body_html',
        'body_text',
        'is_default',
        'is_active',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'is_active'  => 'boolean',
    ];

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'tenant_id');
    }

    // -------------------------------------------------------------------------
    // Scopes
    // -------------------------------------------------------------------------

    public function scopeForTenant(Builder $query, ?int $tenantId): Builder
    {
        return $query->where('tenant_id', $tenantId);
    }

    public function scopeForEvent(Builder $query, string $eventType): Builder
    {
        return $query->where('event_type', $eventType);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeDefault(Builder $query): Builder
    {
        return $query->where('is_default', true);
    }

    // -------------------------------------------------------------------------
    // Template rendering
    //
    // Uses {placeholder} syntax — intentionally NOT Blade syntax.
    // Safe to store in DB and preview without executing server-side code.
    // -------------------------------------------------------------------------

    public function renderSubject(array $vars = []): string
    {
        return $this->interpolate((string) $this->subject_template, $vars);
    }

    public function renderBodyHtml(array $vars = []): string
    {
        return $this->interpolate((string) $this->body_html, $vars);
    }

    public function renderBodyText(array $vars = []): string
    {
        return $this->interpolate((string) $this->body_text, $vars);
    }

    private function interpolate(string $template, array $vars): string
    {
        $search  = array_map(fn (string $key): string => '{' . $key . '}', array_keys($vars));
        $replace = array_values($vars);

        return str_replace($search, $replace, $template);
    }
}
