<?php

namespace App\Domain\Users\Services;

use App\Domain\Users\Models\User;

class UserService
{
    public function create(array $data): User
    {
        return User::create($data);
    }
}
