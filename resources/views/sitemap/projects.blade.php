<?xml version="1.0" encoding="UTF-8"?>

<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">

@foreach($projects as $project)
<url>
    <loc>{{ url($countrySlug.'/projects/'.$project->slug) }}</loc>
    <lastmod>{{ $project->updated_at->toAtomString() }}</lastmod>
    <changefreq>weekly</changefreq>
    <priority>0.9</priority>
</url>
@endforeach

</urlset>