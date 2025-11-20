<?php

namespace App\Domain\Users\Policies;

use App\Domain\Users\Models\User;

class UserPolicy
{
    public function manage(User $user): bool
    {
        return $user->hasRole('admin');
    }
}
