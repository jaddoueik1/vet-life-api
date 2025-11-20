<?php

namespace App\Domain\Appointments\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Domain\Patients\Models\Patient;
use App\Domain\Users\Models\User;

class Appointment extends Model
{
    protected $fillable = ['patient_id', 'scheduled_at', 'status', 'notes', 'assigned_vet_id'];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function vet(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_vet_id');
    }
}
