<?php

namespace App\Domain\Users\Repositories;

use App\Domain\Users\Models\User;

class UserRepository
{
    public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }
}
