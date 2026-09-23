{!! '<?xml version="1.0" encoding="UTF-8"?>' !!}
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <url>
        <loc>{{ route('home') }}</loc>
        <changefreq>daily</changefreq>
        <priority>1.0</priority>
    </url>
    <url>
        <loc>{{ route('shelters') }}</loc>
        <changefreq>weekly</changefreq>
        <priority>0.8</priority>
    </url>
    @foreach ($shelters as $shelter)
        <url>
            <loc>{{ route('shelters.show', $shelter) }}</loc>
            @if ($shelter->updated_at)
                <lastmod>{{ $shelter->updated_at->toAtomString() }}</lastmod>
            @endif
            <changefreq>daily</changefreq>
            <priority>0.7</priority>
        </url>
    @endforeach
    <url>
        <loc>{{ route('privacy-policy') }}</loc>
        <changefreq>yearly</changefreq>
        <priority>0.2</priority>
    </url>
</urlset>
