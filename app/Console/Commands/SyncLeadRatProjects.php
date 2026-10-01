<?php

namespace App\Console\Commands;

use App\Models\Project;
use App\Services\LeadRatService;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class SyncLeadRatProjects extends Command
{
    protected $signature = 'leadrat:sync-projects';

    protected $description = 'Fetch projects from LeadRat CRM and sync them into the local projects table';

    // Fixed values based on this application's setup
    protected int $indiaCountryId = 6;        // Country: IN
    protected int $leadratDeveloperId = 1078; // Placeholder "LeadRat Import" developer

    public function handle(LeadRatService $leadrat)
    {
        $this->info('Starting LeadRat projects sync...');

        $page = 1;
        $pageSize = 50;
        $totalSynced = 0;
        $totalCreated = 0;
        $totalUpdated = 0;

        do {
            $this->info("Fetching page {$page}...");

            try {
                $data = $leadrat->getAllProjects([
                    'PageNumber' => $page,
                    'PageSize'   => $pageSize,
                ]);
            } catch (\Exception $e) {
                $this->error('Failed to fetch from LeadRat: ' . $e->getMessage());

                return 1;
            }

            $items = $data['items'] ?? [];
            $totalCount = $data['totalCount'] ?? 0;

            if (empty($items)) {
                break;
            }

            foreach ($items as $item) {
                $leadratId = $item['id'] ?? null;
                if (! $leadratId) {
                    continue;
                }

                $name = $item['name'] ?? 'Untitled Project';

                $address = $item['address'] ?? null;
                $city = is_array($address) ? ($address['city'] ?? null) : null;
                $locality = is_array($address) ? ($address['locality'] ?? null) : null;
                $locationText = is_array($address)
                    ? trim(($address['locality'] ?? '') . ' ' . ($address['city'] ?? ''))
                    : null;

                $minPrice = $item['minimumPrice'] ?? null;
                $maxPrice = $item['maximumPrice'] ?? null;

                $coverImage = $item['imageUrls'][0] ?? null;

                $description = $item['description'] ?? null;

                // Check if this record already exists (by leadrat_id)
                $existing = Project::where('leadrat_id', $leadratId)->first();

                $payload = [
                    'leadrat_id'         => $leadratId,
                    'source'             => 'leadrat',
                    'name'               => $name,
                    'developer_id'       => $this->leadratDeveloperId,
                    'country_id'         => $this->indiaCountryId,
                    'city'               => $city,
                    'locality'           => $locality,
                    'location'           => $locationText ?: null,
                    'min_price'          => $minPrice,
                    'max_price'          => $maxPrice,
                    'cover_image_path'   => $coverImage,
                    'building_description' => $description,
                    'short_description'  => $description ? Str::limit($description, 250) : null,
                    'sale_status'        => null, // null passes the existing "!= out_of_stock OR null" filter
                    'active'             => 1,
                ];

                if ($existing) {
                    $existing->update($payload);
                    $totalUpdated++;
                } else {
                    $payload['slug'] = $this->generateUniqueSlug($name, $leadratId);
                    $payload['is_auto_slug'] = 1;
                    Project::create($payload);
                    $totalCreated++;
                }

                $totalSynced++;
            }

            $page++;
        } while (($page - 1) * $pageSize < $totalCount);

        $this->info("Sync complete. Total processed: {$totalSynced} (Created: {$totalCreated}, Updated: {$totalUpdated})");

        return 0;
    }

    protected function generateUniqueSlug(string $name, string $leadratId): string
    {
        $base = Str::slug($name);
        $suffix = substr(preg_replace('/[^a-zA-Z0-9]/', '', $leadratId), 0, 6);
        $slug = "{$base}-{$suffix}";

        $i = 1;
        $original = $slug;
        while (Project::where('slug', $slug)->exists()) {
            $slug = "{$original}-{$i}";
            $i++;
        }

        return $slug;
    }
}