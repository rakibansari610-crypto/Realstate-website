<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class ReellyClient
{
    protected function client()
    {
        return Http::baseUrl(rtrim(config('services.reelly.base_url'), '/'))
            ->timeout(config('services.reelly.timeout', 20))
            ->acceptJson()
            ->withHeaders([
                'X-API-KEY'     => (string) config('services.reelly.api_key'),
                'X-ENVIRONMENT' => (string) config('services.reelly.environment'),
            ]);
    }
    public function getProjects(array $params = [])
    {
        return $this->client()
            ->get('/api/v2/clients/projects', $params)
            ->throw()
            ->json();
    }

    public function getProject(int $id)
    {
        return $this->client()
            ->get("/api/v2/clients/projects/{$id}")
            ->throw()
            ->json();
    }

    public function getProjectUnits(int $projectId, array $params = [])
    {
        return $this->client()
            ->get("/api/v2/clients/projects/{$projectId}/units", $params)
            ->throw()
            ->json();
    }
}
