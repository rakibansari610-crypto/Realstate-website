<?php

use App\Models\Country;

//    if (!function_exists('priceShort')) {
//        function priceShort($number)
//        {
//            if ($number >= 1000000000) {
//                return round($number / 1000000000, 2) . 'B';
//            }
//            if ($number >= 1000000) {
//                return round($number / 1000000, 2) . 'M';
//            }
//            if ($number >= 1000) {
//                 return round($number / 1000, 2) . 'K';
//             }
//            return number_format($number);
//        }
//    }

if (! function_exists('resolveSitemapCountry')) {
    function resolveSitemapCountry(string $slug): ?Country
    {
        $slug = strtolower(trim($slug));

        return match ($slug) {
            'in' => Country::query()
                ->where(function ($q) {
                    $q->whereRaw('LOWER(country_code) = ?', ['in'])
                        ->orWhereRaw('LOWER(country_name) = ?', ['india'])
                        ->orWhereRaw('LOWER(country_iso) = ?', ['india']);
                })
                ->first(),

            'ae', 'uae' => Country::query()
                ->where(function ($q) {
                    $q->whereRaw('LOWER(country_name) = ?', ['united arab emirates'])
                        ->orWhereRaw('LOWER(country_iso) = ?', ['uae']);
                })
                ->first(),

            default => null,
        };
    }
}
if (! function_exists('priceShort')) {
    function priceShort($number)
    {
        $country = session('country_code');
        if ($country == 'in') {

            if ($number >= 10000000) {
                return round($number / 10000000, 2).' Crore';
            }

            if ($number >= 100000) {
                return round($number / 100000, 2).' Lakh';
            }

            if ($number >= 1000) {
                return round($number / 1000, 2).' K';
            }

            return number_format($number);
        }

        if ($number >= 1000000000) {
            return round($number / 1000000000, 2).'B';
        }

        if ($number >= 1000000) {
            return round($number / 1000000, 2).'M';
        }
        if ($number >= 1000) {
            return round($number / 1000, 2).'K';
        }

        return number_format($number);
    }
}
