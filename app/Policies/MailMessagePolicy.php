<?php

namespace App\Policies;

use App\Models\MailMessage;
use App\Models\User;

class MailMessagePolicy
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

    public function view(User $user, MailMessage $mailMessage): bool
    {
        return $user->id === $mailMessage->tenant_id;
    }
}
