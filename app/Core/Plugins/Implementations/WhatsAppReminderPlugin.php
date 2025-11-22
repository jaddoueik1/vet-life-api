<?php

namespace App\Core\Plugins\Implementations;

use App\Core\Plugins\BasePlugin;
use App\Domain\Appointments\Events\AppointmentCreated;
use App\Domain\Visits\Events\VisitCompleted;
use App\Domain\Appointments\Models\Appointment;
use App\Jobs\SendAppointmentWhatsAppReminder;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Event;

class WhatsAppReminderPlugin extends BasePlugin
{
    public function getName(): string
    {
        return 'whatsapp_reminder';
    }

    public function boot(): void
    {
        if (!$this->isEnabled()) {
            return;
        }

        Event::listen(AppointmentCreated::class, function (AppointmentCreated $event) {
            $appointmentId = $event->payload['id'] ?? null;

            if (!$appointmentId) {
                Log::warning('Appointment ID missing from event payload, cannot schedule reminder', $event->payload);
                return;
            }

            $appointment = Appointment::with('patient.owner')->find($appointmentId);

            if (!$appointment || !$appointment->patient || !$appointment->patient->owner) {
                Log::warning('Appointment reminder skipped due to missing patient or owner', [
                    'appointment_id' => $appointmentId,
                ]);
                return;
            }

            $timezone = $this->config->get('deployment.timezone', config('app.timezone', 'UTC'));
            $scheduledAt = Carbon::parse($appointment->scheduled_at, $timezone);
            $sendAt = $scheduledAt->clone()->subDay();
            $messageTemplate = $this->config->get(
                'plugins.whatsapp_reminder.messages.appointment_before',
                'Hi {owner_name}, this is a reminder from {clinic_name} about {patient_name}\'s appointment on {scheduled_at}.'
            );

            $dispatchAt = $sendAt->isPast() ? now($timezone) : $sendAt;

            SendAppointmentWhatsAppReminder::dispatch($appointment->id, $messageTemplate)
                ->delay($dispatchAt);

            Log::info('Scheduled WhatsApp reminder', [
                'appointment_id' => $appointment->id,
                'owner_phone' => $appointment->patient->owner->phone,
                'send_at' => $dispatchAt->toDateTimeString(),
            ]);
        });

        Event::listen(VisitCompleted::class, function (VisitCompleted $event) {
            Log::info('Sending post visit WhatsApp message', $event->payload);
        });
    }
}
