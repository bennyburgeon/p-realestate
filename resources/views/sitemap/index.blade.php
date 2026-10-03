@php echo '<?xml version="1.0" encoding="UTF-8"?>'."\n"; @endphp
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <url>
        <loc>{{ route('home') }}</loc>
        <changefreq>daily</changefreq>
    </url>
    <url>
        <loc>{{ route('properties.index') }}</loc>
        <changefreq>hourly</changefreq>
    </url>
@foreach ($properties as $property)
    <url>
        <loc>{{ route('properties.show', $property) }}</loc>
        <lastmod>{{ $property->updated_at->toAtomString() }}</lastmod>
        <changefreq>weekly</changefreq>
    </url>
@endforeach
</urlset>
