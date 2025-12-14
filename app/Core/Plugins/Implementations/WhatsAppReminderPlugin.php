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

    protected function handleAppointmentCreated(AppointmentCreated $event): void
    {
        // 1. Check if the event is enabled in config
        $rules = $this->config->get('plugins.whatsapp_reminder.reminder_rules', []);
        $rule = collect($rules)->firstWhere('event', 'APPOINTMENT_CREATED');

        if (!$rule) {
            return;
        }

        // 2. Extract data
        $payload = $event->payload;
        $patientId = $payload['patient_id'] ?? null;
        
        if (!$patientId) {
            Log::warning('WhatsAppReminderPlugin: No patient_id found in payload', $payload);
            return;
        }

        $patient = \App\Domain\Patients\Models\Patient::with('owner')->find($patientId);
        
        if (!$patient || !$patient->owner) {
            Log::warning('WhatsAppReminderPlugin: Patient or Owner not found', ['patient_id' => $patientId]);
            return;
        }

        $phoneNumber = $patient->owner->phone;
        
        if (!$phoneNumber) {
            Log::warning('WhatsAppReminderPlugin: No phone number for owner', ['owner_id' => $patient->owner->id]);
            return;
        }

        $scheduledAt = $payload['scheduled_at'] ?? 'now';
        try {
            $dateObj = \Carbon\Carbon::parse($scheduledAt);
            $date = $dateObj->format('M d, Y'); // e.g., Dec 25, 2023
            $time = $dateObj->format('h:i A');  // e.g., 10:00 AM
        } catch (\Exception $e) {
            $date = $scheduledAt;
            $time = '';
        }

        $templateCode = $rule['template_code'];
        // Template: "Hello {1}, your appointment is confirmed for {2} at {3}."
        $variables = [
            $patient->owner->name,
            $date,
            $time,
        ];

        // 3. Send message using WhatsAppService
        $service = app(\App\Core\Services\WhatsAppService::class);
        $service->sendMessage($phoneNumber, $templateCode, $variables);
    }
}
