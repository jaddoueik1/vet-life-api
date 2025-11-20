<?php

namespace App\Domain\Appointments\Services;

use App\Domain\Appointments\Models\Appointment;
use App\Domain\Appointments\Events\AppointmentCreated;
use Illuminate\Support\Facades\Event;

class AppointmentService
{
    public function create(array $data): Appointment
    {
        $appointment = Appointment::create($data);
        Event::dispatch(new AppointmentCreated($appointment->toArray()));
        return $appointment;
    }
}
