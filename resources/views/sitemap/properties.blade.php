<?xml version="1.0" encoding="UTF-8"?>

<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">

@foreach($properties as $property)
<url>
    <loc>{{ url($countrySlug.'/properties/'.$property->slug) }}</loc>
    <lastmod>{{ $property->updated_at->toAtomString() }}</lastmod>
    <changefreq>weekly</changefreq>
    <priority>0.8</priority>
</url>
@endforeach

</urlset>