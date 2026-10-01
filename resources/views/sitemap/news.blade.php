<?xml version="1.0" encoding="UTF-8"?>

<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">

@foreach($blogs as $blog)
<url>
<loc>{{ url($countrySlug.'/news/'.$blog->slug) }}</loc>
<lastmod>{{ optional($blog->updated_at)->toAtomString() }}</lastmod>
<changefreq>weekly</changefreq>
<priority>0.7</priority>
</url>
@endforeach

</urlset>