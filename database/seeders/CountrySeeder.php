<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Country;

class CountrySeeder extends Seeder
{
    public function run(): void
    {
        $countries = [
            [
                'country_name'     => 'India',
                'country_currency' => 'INR',
                'country_lang'     => 'Hindi, English',
                'country_code'     => 'IN',
                'country_iso'      => 'IND',
                'country_isd'      => '+91',
                'country_status'   => 1,
                'country_default'  => 1,
                'country_priority' => 1,
            ],
            [
                'country_name'     => 'United States',
                'country_currency' => 'USD',
                'country_lang'     => 'English',
                'country_code'     => 'US',
                'country_iso'      => 'USA',
                'country_isd'      => '+1',
                'country_status'   => 1,
                'country_default'  => 0,
                'country_priority' => 2,
            ],
            [
                'country_name'     => 'United Kingdom',
                'country_currency' => 'GBP',
                'country_lang'     => 'English',
                'country_code'     => 'UK',
                'country_iso'      => 'GBR',
                'country_isd'      => '+44',
                'country_status'   => 1,
                'country_default'  => 0,
                'country_priority' => 3,
            ],
            [
                'country_name'     => 'Canada',
                'country_currency' => 'CAD',
                'country_lang'     => 'English, French',
                'country_code'     => 'CA',
                'country_iso'      => 'CAN',
                'country_isd'      => '+1',
                'country_status'   => 1,
                'country_default'  => 0,
                'country_priority' => 4,
            ],
            [
                'country_name'     => 'Australia',
                'country_currency' => 'AUD',
                'country_lang'     => 'English',
                'country_code'     => 'AU',
                'country_iso'      => 'AUS',
                'country_isd'      => '+61',
                'country_status'   => 0,
                'country_default'  => 0,
                'country_priority' => 5,
            ],
        ];

        Country::insert($countries);
    }
}
