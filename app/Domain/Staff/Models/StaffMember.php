<?php

namespace App\Domain\Staff\Models;

use Illuminate\Database\Eloquent\Model;

class StaffMember extends Model
{
    public const ROLE_VETERINARIAN = 'veterinarian';
    public const ROLE_STAFF = 'staff';

    protected $fillable = [
        'name',
        'email',
        'phone',
        'role',
        'specialization',
        'position',
    ];
}
