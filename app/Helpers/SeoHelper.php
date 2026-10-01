<?php

namespace App\Helpers;

use Illuminate\Support\Str;

class SeoHelper
{
    public static function build(array $data = []): array
    {
        $metaTitle = $data['meta_title'] ?? 'Vserve Real Estate';
        $metaDescription = $data['description'] ?? 'Find the best real estate properties with Vserve Real Estate.';
        $keywords = $data['keywords'] ?? 'real estate, property, buy, rent';

        $pageType = $data['page_type'] ?? 'default'; // listing, property, project, default
        $slug = $data['slug'] ?? Str::slug($metaTitle);
        $country = $data['country'] ?? 'in';
        $canonical = $data['canonical'] ?? url()->current();

        $defaultOgImage = url("images/og/{$slug}.webp");
        $fallbackImage = url('images/og/default-logo.webp');

        $ogImage = $fallbackImage;

        if ($pageType === 'listing') {
            $ogImage = self::resolveListingImage($slug, $defaultOgImage, $fallbackImage);
        }

        if ($pageType === 'property' && ! empty($data['property_image'])) {
            $ogImage = self::absoluteUrl($data['property_image']);
        }

        if ($pageType === 'project' && ! empty($data['project_image'])) {
            $ogImage = self::absoluteUrl($data['project_image']);
        }

        if (empty($ogImage)) {
            $ogImage = $fallbackImage;
        }

        return [
            'meta_title' => $metaTitle,
            'description' => $metaDescription,
            'keywords' => $keywords,
            'og_title' => $metaTitle,
            'og_description' => $metaDescription,
            'og_image' => $ogImage,
            'og_image_alt' => $metaTitle,
            'og_type' => 'website',
            'og_url' => $canonical,
            'og_site_name' => 'Vserve Real Estate',
            'twitter_card' => 'summary_large_image',
            'twitter_title' => $metaTitle,
            'twitter_description' => $metaDescription,
            'twitter_image' => $ogImage,
            'canonical' => $canonical,
            'country' => $country,
        ];
    }

    protected static function resolveListingImage(string $slug, string $defaultOgImage, string $fallbackImage): string
    {
        $publicPath = public_path("images/og/{$slug}.webp");

        if (file_exists($publicPath)) {
            return $defaultOgImage;
        }

        return $fallbackImage;
    }

    protected static function absoluteUrl(string $path): string
    {
        if (Str::startsWith($path, ['http://', 'https://'])) {
            return $path;
        }

        return url($path);
    }
}
