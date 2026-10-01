<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\Unit;

class UpdateUnitSlugSeeder extends Seeder
{
    public function run()
    {
        $units = Unit::whereNull('slug')
                    ->orWhere('slug', '')
                    ->get();

        foreach ($units as $unit) {
            $unit->slug = Str::slug($unit->property_name);
            $unit->save();
        }
    }
}