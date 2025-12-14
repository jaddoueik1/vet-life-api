<?php

namespace App\Core\Services;

use Twilio\Rest\Client;
use Illuminate\Support\Facades\Log;
use App\Core\Config\ConfigService;

class WhatsAppService
{
    protected Client $client;
    protected string $from;
    protected ConfigService $config;

    public function __construct(ConfigService $config)
    {
        $sid = config('services.twilio.sid', env('TWILIO_SID'));
        $token = config('services.twilio.token', env('TWILIO_TOKEN'));
        $this->from = config('services.twilio.whatsapp_from', env('TWILIO_WHATSAPP_FROM'));
        $this->config = $config;

        try {
             $this->client = new Client($sid, $token);
        } catch (\Exception $e) {
            Log::error('Twilio Client initialization failed: ' . $e->getMessage());
        }
    }

    public function sendMessage(string $to, string $templateCode, array $variables = []): bool
    {
        if (!isset($this->client)) {
            Log::error('Twilio client is not initialized.');
            return false;
        }

        // 1. Get the template body from config
        $templateBody = $this->config->get("plugins.whatsapp_reminder.templates.{$templateCode}.body");

        if (!$templateBody) {
            Log::warning("WhatsApp template '{$templateCode}' not found in config.");
            return false;
        }

        // 2. Replace placeholders {1}, {2}, etc. with variables
        // Assuming 1-based index in template matching the variables array order (0-based)
        foreach ($variables as $index => $value) {
            $placeholder = '{' . ($index + 1) . '}';
            $templateBody = str_replace($placeholder, $value, $templateBody);
        }
        
        // Ensure "to" number has whatsapp: prefix
        if (!str_starts_with($to, 'whatsapp:')) {
            $to = 'whatsapp:' . $to;
        }
        
        // Ensure "from" number has whatsapp: prefix
        $from = $this->from;
        if (!str_starts_with($from, 'whatsapp:')) {
            $from = 'whatsapp:' . $from;
        }

        try {
            $message = $this->client->messages->create(
                $to,
                [
                    'from' => $from,
                    'body' => $templateBody
                ]
            );

            Log::info("Twilio WhatsApp message sent to {$to}", ['sid' => $message->sid]);
            return true;

        } catch (\Exception $e) {
            Log::error("Failed to send Twilio WhatsApp message: " . $e->getMessage());
            return false;
        }
    }
}
