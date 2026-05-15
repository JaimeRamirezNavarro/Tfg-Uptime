<?php

namespace App\Traits;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

trait SendsWhatsAppAlerts
{
    /**
     * Enviar mensaje por WhatsApp usando CallMeBot API.
     * @param string $message
     */
    public function sendWhatsAppMessage(string $message)
    {
        $phone = config('services.callmebot.phone');
        $apikey = config('services.callmebot.apikey');

        if (!$phone || !$apikey) {
            Log::warning('CallMeBot config missing. Cannot send WhatsApp alert.');
            return;
        }

        try {
            $url = "https://api.callmebot.com/whatsapp.php";
            
            $response = Http::timeout(5)->get($url, [
                'phone' => $phone,
                'text' => $message,
                'apikey' => $apikey
            ]);

            if ($response->successful()) {
                Log::info("WhatsApp alert sent successfully.");
            } else {
                Log::error("Failed to send WhatsApp alert: " . $response->body());
            }
        } catch (\Exception $e) {
            Log::error("WhatsApp exception: " . $e->getMessage());
        }
    }
}
