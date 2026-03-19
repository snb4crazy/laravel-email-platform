<?php

namespace App\Policies;

use App\Models\Site;
use App\Models\User;

class SitePolicy
{
    /** Admins bypass all checks. */
    public function before(User $user): ?bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return null;
    }

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Site $site): bool
    {
        return $user->id === $site->tenant_id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Site $site): bool
    {
        return $user->id === $site->tenant_id;
    }

    public function delete(User $user, Site $site): bool
    {
        return $user->id === $site->tenant_id;
    }
}
