<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class StuartService
{
    protected $client;
    protected $baseUrl = 'https://api.stuart.com';
    protected $accessToken;

    public function __construct()
    {
        $this->accessToken = $this->getAccessToken();

        $this->client = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->accessToken,
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ])->baseUrl($this->baseUrl);
    }

    protected function getAccessToken()
    {
        $clientId = config('services.stuart.client_id');
        $clientSecret = config('services.stuart.client_secret');

        $basicToken = base64_encode("$clientId:$clientSecret");

        $response = Http::withHeaders([
            'Authorization' => 'Basic ' . $basicToken,
            'Content-Type' => 'application/x-www-form-urlencoded',
            'Accept' => 'application/json',
        ])->asForm()->post($this->baseUrl . '/oauth/token', [
            'grant_type' => 'client_credentials',
            'scope' => 'api',
        ]);

        Log::info('Stuart OAuth Response', [
            'status' => $response->status(),
            'body' => $response->body(),
        ]);

        if ($response->failed()) {
            throw new \Exception('Failed to get Stuart Access Token: ' . $response->body());
        }

        return $response->json()['access_token'];
    }


    public function createJob(array $data)
    {
        $response = $this->client->post('/v2/jobs', [
            'job' => $data
        ]);

        Log::info('Stuart Create Job Response', [
            'status' => $response->status(),
            'body' => $response->body()
        ]);

        if ($response->failed()) {
            throw new \Exception('Stuart Job Creation Failed: ' . $response->body());
        }

        return $response->json();
    }

    public function getJob($jobId)
    {
        $url = $this->baseUrl . '/v2/jobs/' . $jobId; // Stuart API endpoint

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->accessToken,
            'Content-Type' => 'application/json',
        ])->get($url);

        if ($response->successful()) {
            return $response->json();
        } else {
            throw new \Exception('Stuart Job fetch failed: ' . $response->body());
        }
    }
}
