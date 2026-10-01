<?php

namespace App\Services;

use App\Models\Country;
use App\Models\SeoMeta;

class SeoService
{
    public function get($pageType, $data = [])
    {
        $country_code = session('country_code') ?? null;
        $countries = Country::where('country_code', $country_code)->first();
        if (! $countries) {
            $countries = Country::where('country_code', 'AED')->first();
        } 
        $seo = SeoMeta::where('page_type', $pageType)->where('country_id', $countries->id)->first(); 
        if (!$seo) {
            return [
                'title' => '',
                'description' => '',
                'keywords' => ''
            ];
        }
         $cityValue = ($country_code == 'in') 
        ? ($data['city'] ?? '') 
        : ($data['location'] ?? '');

        // dd($data);
        
        $replace = [
            '{project_name}' => $data['project_name'] ?? '',
            '{Project Name}' => $data['project_name'] ?? '',

            '{city}' => $cityValue,
            '{City}' => $cityValue,

            '{developer}' => $data['developer'] ?? '',
            '{Developer}' => $data['developer'] ?? '',

            '{price}' => !empty($data['price']) ? priceShort($data['price']) : '',
            '{Price}' => !empty($data['price']) ? priceShort($data['price']) : '', 

            

            '{current_year}' => date('Y'),
        ];

        return [
            'title' => str_replace(array_keys($replace), array_values($replace), $seo->meta_title),
            'meta_title' => str_replace(array_keys($replace), array_values($replace), $seo->meta_title),
            'description' => str_replace(array_keys($replace), array_values($replace), $seo->meta_description),
            'keywords' => str_replace(array_keys($replace), array_values($replace), $seo->meta_keywords),


            //og titles
            'og_title' => str_replace(array_keys($replace), array_values($replace), $seo->og_title),
            'og_description' => str_replace(array_keys($replace), array_values($replace), $seo->og_description),
            'og_image' => $seo->og_image,
            'og_image_alt' => $seo->og_image_alt, 
        ];
    }
}