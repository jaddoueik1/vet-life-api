<?php

namespace App\Core\Notifications;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppSender
{
    public function sendMessage(string $phoneNumber, string $message): void
    {
        $token = config('services.whatsapp.token');
        $phoneNumberId = config('services.whatsapp.phone_number_id');
        $baseUrl = rtrim(config('services.whatsapp.base_url', 'https://graph.facebook.com/v19.0'), '/');

        if (!$token || !$phoneNumberId) {
            Log::warning('WhatsApp credentials missing, skipping message dispatch', [
                'phone_number_id' => $phoneNumberId,
                'token_present' => !empty($token),
            ]);

            return;
        }

        try {
            $response = Http::withToken($token)
                ->acceptJson()
                ->post("{$baseUrl}/{$phoneNumberId}/messages", [
                    'messaging_product' => 'whatsapp',
                    'to' => $phoneNumber,
                    'type' => 'text',
                    'text' => [
                        'preview_url' => false,
                        'body' => $message,
                    ],
                ]);

            if ($response->failed()) {
                Log::error('Failed to send WhatsApp message', [
                    'to' => $phoneNumber,
                    'status' => $response->status(),
                    'body' => $response->json(),
                ]);

                return;
            }

            Log::info('WhatsApp message sent', [
                'to' => $phoneNumber,
                'message_id' => $response->json('messages.0.id'),
            ]);
        } catch (\Throwable $e) {
            Log::error('WhatsApp API exception', [
                'to' => $phoneNumber,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
