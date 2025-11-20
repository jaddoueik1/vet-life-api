<?php

namespace App\Domain\Patients\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Owner extends Model
{
    protected $fillable = ['name', 'email', 'phone', 'address'];

    public function patients(): HasMany
    {
        return $this->hasMany(Patient::class);
    }
}
