<?php

namespace App\Domain\Inventory\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $fillable = ['name', 'email', 'phone'];
}
