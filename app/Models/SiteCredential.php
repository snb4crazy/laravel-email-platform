<?php

namespace App\Models;

use App\Enums\CredentialType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class SiteCredential extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'site_id',
        'name',
        'credential_type',
        'key_id',
        'secret_hash',
        'is_active',
        'last_used_at',
        'expires_at',
        'metadata',
    ];

    protected $casts = [
        'credential_type' => CredentialType::class,
        'is_active' => 'boolean',
        'last_used_at' => 'datetime',
        'expires_at' => 'datetime',
        'metadata' => 'array',
    ];

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }
}
