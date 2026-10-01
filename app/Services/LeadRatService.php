<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class LeadRatService
{
    protected $baseUrl;
    protected $tenant;
    protected $apiKey;
    protected $secretKey;

    public function __construct()
    {
        $this->baseUrl   = config('services.leadrat.base_url', 'https://connect.leadrat.com');
        $this->tenant    = config('services.leadrat.tenant');
        $this->apiKey    = config('services.leadrat.api_key');
        $this->secretKey = config('services.leadrat.secret_key');
    }

    /**
     * Get a valid access token (cached, since it expires in 60 minutes)
     */
    public function getAccessToken()
    {
        return Cache::remember('leadrat_access_token', 55 * 60, function () {
            $response = Http::withHeaders([
                'tenant' => $this->tenant,
                'Content-Type' => 'application/json',
            ])->post("{$this->baseUrl}/api/v1/authentication/token", [
                'apiKey'    => $this->apiKey,
                'secretKey' => $this->secretKey,
            ]);

            if ($response->failed()) {
                Log::error('LeadRat Token Generation Failed', [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);
                throw new \Exception('Failed to generate LeadRat access token: ' . $response->body());
            }

            return $response->json('data.accessToken');
        });
    }

    /**
     * Fetch all projects from LeadRat CRM
     *
     * @param array $filters Optional query filters (Search, PageNumber, PageSize, etc.)
     */
    public function getAllProjects(array $filters = [])
    {
        $token = $this->getAccessToken();

        $defaultParams = [
            'PageNumber' => 1,
            'PageSize'   => 50,
        ];

        $params = array_merge($defaultParams, $filters);

        $response = Http::withToken($token)
            ->acceptJson()
            ->get("{$this->baseUrl}/api/v1/project/all", $params);

        if ($response->status() === 401) {
            // Token might have expired earlier than cache TTL — clear and retry once
            Cache::forget('leadrat_access_token');
            $token = $this->getAccessToken();

            $response = Http::withToken($token)
                ->acceptJson()
                ->get("{$this->baseUrl}/api/v1/project/all", $params);
        }

        if ($response->failed()) {
            Log::error('LeadRat Get Projects Failed', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);
            throw new \Exception('Failed to fetch projects from LeadRat: ' . $response->body());
        }

        // NOTE: This endpoint returns items/itemsCount/totalCount directly
        // at the top level of the JSON response (no "data" wrapper).
        return $response->json();
    }

    /**
     * Fetch a single project by ID
     */
    public function getProjectById(string $id)
    {
        $token = $this->getAccessToken();

        $response = Http::withToken($token)
            ->acceptJson()
            ->get("{$this->baseUrl}/api/v1/project/{$id}");

        if ($response->failed()) {
            throw new \Exception('Failed to fetch project: ' . $response->body());
        }

        return $response->json('data');
    }
}