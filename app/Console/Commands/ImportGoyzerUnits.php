<?php

namespace App\Console\Commands;

use App\Models\Unit;
use App\Models\UnitFitting;
use App\Models\UnitImage;
use App\Services\GoyzerService;
use Illuminate\Console\Command;

class ImportGoyzerUnits extends Command
{
    protected $signature = 'goyzer:import {--type=all : sale|rent|all}';

    protected $description = 'Import sale and rent listings from Goyzer into units table';

    public function handle(GoyzerService $goyzer): int
    {
        $type = strtolower((string) $this->option('type'));

        if ($type === 'sale' || $type === 'all') {
            $this->importSales($goyzer);
        }

        if ($type === 'rent' || $type === 'all') {
            $this->importRent($goyzer);
        }

        $this->info('Import completed successfully.');

        return self::SUCCESS;
    }

    protected function importSales(GoyzerService $goyzer): void
    {
        $this->info('Fetching sale listings...');
        $rows = $goyzer->fetchSalesListings();

        $count = 0;

        foreach ($rows as $row) {
            $data = $goyzer->mapToUnitData($row, 'sale');

            if (empty($data['code']) && empty($data['ref_no'])) {
                continue;
            }

            $unit = Unit::updateOrCreate(
                ['code' => $data['code'] ?: $data['ref_no']],
                $data
            );

            $this->syncUnitImages($unit, $row, $goyzer);
            $this->syncUnitFittings($unit, $row, $goyzer);
            $count++;
        }

        $this->info("Imported/updated {$count} sale listings.");
    }

    protected function importRent(GoyzerService $goyzer): void
    {
        $this->info('Fetching rent listings...');
        $rows = $goyzer->fetchRentListings();

        $count = 0;

        foreach ($rows as $row) {
            $data = $goyzer->mapToUnitData($row, 'rent');

            if (empty($data['code']) && empty($data['ref_no'])) {
                continue;
            }

            $unit = Unit::updateOrCreate(
                ['code' => $data['code'] ?: $data['ref_no']],
                $data
            );

            $this->syncUnitImages($unit, $row, $goyzer);

            $count++;
        }

        $this->info("Imported/updated {$count} rent listings.");
    }

    protected function syncUnitImages(Unit $unit, array $row, GoyzerService $goyzer): void
    {
        $imageRows = $goyzer->extractImageRows($row, $unit->code);
        $hashes = [];

        foreach ($imageRows as $imageRow) {
            $hash = sha1($imageRow['image_url']);
            $hashes[] = $hash;

            UnitImage::updateOrCreate(
                [
                    'unit_id' => $unit->id,
                    'image_hash' => $hash,
                ],
                [
                    'source' => $imageRow['source'],
                    'source_unit_code' => $imageRow['source_unit_code'],
                    'image_type' => $imageRow['image_type'],
                    'title' => $imageRow['title'],
                    'is_marketing_image' => $imageRow['is_marketing_image'],
                    'image_remarks' => $imageRow['image_remarks'],
                    'image_url' => $imageRow['image_url'],
                    'sort_order' => $imageRow['sort_order'],
                ]
            );
        }

        UnitImage::where('unit_id', $unit->id)
            ->when(! empty($hashes), fn ($q) => $q->whereNotIn('image_hash', $hashes))
            ->delete();
    }

    protected function syncUnitFittings(Unit $unit, array $row, GoyzerService $goyzer): void
    {
        $fittings = $goyzer->extractFittings($row, $unit->code);

        $ids = [];

        foreach ($fittings as $fit) {
            if (empty($fit['source_fixture_id'])) {
                continue;
            }

            $ids[] = $fit['source_fixture_id'];

            UnitFitting::updateOrCreate(
                [
                    'unit_id' => $unit->id,
                    'source_fixture_id' => $fit['source_fixture_id'],
                ],
                [
                    'source' => $fit['source'],
                    'title' => $fit['title'],
                    'name' => $fit['name'],
                ]
            );
        }

        // delete removed ones
        UnitFitting::where('unit_id', $unit->id)
            ->when(! empty($ids), fn ($q) => $q->whereNotIn('source_fixture_id', $ids))
            ->delete();
    }
}
