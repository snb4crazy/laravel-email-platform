<?php

namespace App\Models;

use App\Enums\CaptchaProvider;
use App\Enums\SiteAuthMode;
use Database\Factories\SiteFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Site extends Model
{
    /** @use HasFactory<SiteFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'name',
        'domain',
        'notification_email',
        'public_key',
        'auth_mode',
        'captcha_provider',
        'captcha_site_key',
        'captcha_secret',
        'is_active',
        'metadata',
    ];

    protected $casts = [
        'auth_mode' => SiteAuthMode::class,
        'captcha_provider' => CaptchaProvider::class,
        'is_active' => 'boolean',
        'metadata' => 'array',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'tenant_id');
    }

    public function credentials(): HasMany
    {
        return $this->hasMany(SiteCredential::class);
    }

    public function mailMessages(): HasMany
    {
        return $this->hasMany(MailMessage::class);
    }
}
