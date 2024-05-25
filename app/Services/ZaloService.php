<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class ZaloService
{
    protected $accessToken;
    protected $apiUrl;

    public function __construct()
    {
        $this->accessToken = config('zalo.access_token');
        $this->apiUrl = config('zalo.api_url');
    }

    public function sendMessage($phoneNumber, $message)
    {
        $response = Http::withHeaders([
            'access_token' => $this->accessToken,
        ])->post($this->apiUrl . '/message', [
            'recipient' => ['phone' => $phoneNumber],
            'message' => ['text' => $message],
        ]);

        return $response->json();
    }

    public function sendMessages(array $phoneNumbers, $message)
    {
        $results = [];
        foreach ($phoneNumbers as $phoneNumber) {
            $results[] = $this->sendMessage($phoneNumber, $message);
        }
        return $results;
    }
}
