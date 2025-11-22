<?php

namespace App\Jobs;

use App\Core\Config\ConfigService;
use App\Core\Notifications\WhatsAppSender;
use App\Domain\Appointments\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendAppointmentWhatsAppReminder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(private int $appointmentId, private string $messageTemplate)
    {
    }

    public function handle(WhatsAppSender $sender, ConfigService $configService): void
    {
        $appointment = Appointment::with('patient.owner')->find($this->appointmentId);

        if (!$appointment) {
            Log::warning('Appointment reminder skipped, appointment not found', [
                'appointment_id' => $this->appointmentId,
            ]);
            return;
        }

        $patient = $appointment->patient;
        $owner = $patient?->owner;

        if (!$patient || !$owner || empty($owner->phone)) {
            Log::warning('Appointment reminder skipped, missing patient or owner contact', [
                'appointment_id' => $this->appointmentId,
            ]);
            return;
        }

        $timezone = $configService->get('deployment.timezone', config('app.timezone', 'UTC'));
        $scheduledAt = Carbon::parse($appointment->scheduled_at, $timezone);
        $formattedSchedule = $scheduledAt->format('Y-m-d H:i');

        $message = str_replace(
            ['{owner_name}', '{patient_name}', '{scheduled_at}', '{clinic_name}'],
            [
                $owner->name,
                $patient->name,
                $formattedSchedule,
                $configService->get('deployment.clinic_name', 'the clinic'),
            ],
            $this->messageTemplate
        );

        $sender->sendMessage($owner->phone, $message);
    }
}
