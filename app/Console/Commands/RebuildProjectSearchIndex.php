<?php

namespace App\Console\Commands;

use App\Models\ProjectSearchIndex;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RebuildProjectSearchIndex extends Command
{
    protected $signature = 'ai:rebuild-project-index {--project_id=}';

    protected $description = 'Build/refresh project_search_indexes from projects + payment plans + map points + amenities.';

    public function handle(): int
    {
        $projectId = $this->option('project_id');

        $base = DB::table('projects')->select([
            'id', 'name', 'slug', 'city', 'location', 'status', 'min_price', 'max_price', 'price_currency', 'completion_date',
            'short_description', 'building_description',
        ]);

        if ($projectId) {
            $base->where('id', $projectId);
        }

        $count = 0;

        $base->orderBy('id')->chunk(200, function ($projects) use (&$count) {
            foreach ($projects as $p) {
                // Pick best payment plan (lowest sort_order)
                $plan = DB::table('payment_plans')
                    ->where('project_id', $p->id)
                    ->orderBy('sort_order')
                    ->first();

                $hasPostHandover = false;
                $monthsAfter = null;
                $bookingMin = null;
                $planRatio = null;
                $planSummary = null;

                if ($plan) {
                    $monthsAfter = $plan->months_after_handover ?? null;
                    $hasPostHandover = (bool) ($monthsAfter && $monthsAfter > 0);

                    $steps = DB::table('payment_plan_steps')
                        ->where('payment_plan_id', $plan->id)
                        ->orderBy('sort_order')
                        ->get(['name', 'percentage', 'stage_type']);

                    // booking min: detect booking/down steps
                    $bookingCandidates = $steps->filter(function ($s) {
                        $n = strtolower((string) $s->name);
                        $t = strtolower((string) $s->stage_type);

                        return str_contains($n, 'booking')
                            || str_contains($n, 'down')
                            || str_contains($n, 'eoi')
                            || str_contains($t, 'booking')
                            || str_contains($t, 'down');
                    })->pluck('percentage')->filter(fn ($v) => $v !== null);

                    $bookingMin = $bookingCandidates->isNotEmpty() ? (float) $bookingCandidates->min() : null;

                    // Plan summary text
                    $planSummary = $steps->map(function ($s) {
                        $pct = $s->percentage !== null
                            ? rtrim(rtrim(number_format((float) $s->percentage, 2, '.', ''), '0'), '.').'%'
                            : '';
                        $nm = trim((string) $s->name);

                        return trim($nm.($pct ? " ({$pct})" : ''));
                    })->filter()->implode(' • ');

                    // Rough ratio guess: top 2 percentages (handy for “60/40” queries)
                    $sorted = $steps->pluck('percentage')->filter()->sortDesc()->values();
                    if ($sorted->count() >= 2) {
                        $a = (int) round((float) $sorted[0]);
                        $b = (int) round((float) $sorted[1]);
                        if ($a >= 10 && $b >= 10 && $a + $b <= 100) {
                            $planRatio = "{$a}/{$b}";
                        }
                    }
                }

                // Map points summary (Metro/Beach/Mall etc)
                $mapPoints = DB::table('project_map_points')
                    ->where('project_id', $p->id)
                    ->orderBy('sort_order')
                    ->limit(8)
                    ->get(['name', 'time', 'distance']);

                $mapSummary = $mapPoints->map(function ($m) {
                    $parts = [trim((string) $m->name)];
                    if (! is_null($m->time)) {
                        $parts[] = $m->time.' min';
                    }
                    if (! is_null($m->distance)) {
                        $parts[] = rtrim(rtrim(number_format((float) $m->distance, 2, '.', ''), '0'), '.').' km';
                    }

                    return implode(' • ', $parts);
                })->implode(' | ');

                // Amenities text
                $amenities = DB::table('amenity_project')
                    ->join('amenities', 'amenities.id', '=', 'amenity_project.amenity_id')
                    ->where('amenity_project.project_id', $p->id)
                    ->pluck('amenities.name')
                    ->filter();

                $amenitiesText = $amenities->implode(', ');

                $searchText = implode("\n", array_filter([
                    $p->name,
                    $p->city,
                    $p->location,
                    $p->status,
                    $planSummary ? "Payment Plan: {$planSummary}" : null,
                    $mapSummary ? "Nearby: {$mapSummary}" : null,
                    $amenitiesText ? "Amenities: {$amenitiesText}" : null,
                    $p->short_description ?? null,
                    $p->building_description ?? null,
                ]));

                ProjectSearchIndex::updateOrCreate(
                    ['project_id' => $p->id],
                    [
                        'name' => $p->name,
                        'slug' => $p->slug,
                        'city' => $p->city,
                        'location' => $p->location,
                        'status' => $p->status,
                        'min_price' => $p->min_price,
                        'max_price' => $p->max_price,
                        'currency' => $p->price_currency,
                        'completion_date' => $p->completion_date,
                        'has_post_handover' => $hasPostHandover,
                        'booking_percent_min' => $bookingMin,
                        'months_after_handover' => $monthsAfter,
                        'plan_ratio' => $planRatio,
                        'payment_plan_summary' => $planSummary,
                        'map_points_summary' => $mapSummary,
                        'search_text' => $searchText,
                        'indexed_at' => now(),
                    ]
                );

                $count++;
            }
        });

        $this->info("Indexed {$count} projects.");

        return self::SUCCESS;
    }
}
