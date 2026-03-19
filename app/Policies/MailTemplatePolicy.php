<?php

namespace App\Policies;

use App\Models\MailTemplate;
use App\Models\User;

class MailTemplatePolicy
{
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

    public function view(User $user, MailTemplate $mailTemplate): bool
    {
        return $user->id === $mailTemplate->tenant_id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, MailTemplate $mailTemplate): bool
    {
        return $user->id === $mailTemplate->tenant_id;
    }

    public function delete(User $user, MailTemplate $mailTemplate): bool
    {
        return $user->id === $mailTemplate->tenant_id;
    }
}
