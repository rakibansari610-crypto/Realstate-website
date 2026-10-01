<?xml version="1.0" encoding="UTF-8"?>

<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">

@foreach($urls as $url)
<url>
<loc>{{ $url }}</loc>
<changefreq>weekly</changefreq>
<priority>0.8</priority>
</url>
@endforeach




{{-- PROJECTS --}}
@foreach($projects as $project)
@foreach($countries as $country)
<url>
<loc>{{ url($country.'/projects/'.$project->slug) }}</loc>
<lastmod>{{ $project->updated_at->toAtomString() }}</lastmod>
<changefreq>weekly</changefreq>
<priority>0.9</priority>
</url>
@endforeach
@endforeach

{{-- BLOGS --}}
@foreach($blogs as $blog)
@foreach($countries as $country)
<url>
<loc>{{ url($country.'/news/'.$blog->slug) }}</loc>
<lastmod>{{ $blog->updated_at->toAtomString() }}</lastmod>
<changefreq>weekly</changefreq>
<priority>0.7</priority>
</url>
@endforeach
@endforeach

</urlset>