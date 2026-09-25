{!! '<?xml version="1.0" encoding="UTF-8"?>' !!}
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
    <channel>
        <title>{{ $shelter->name }}</title>
        <link>{{ route('shelters.show', $shelter) }}</link>
        <description>{{ __(':name in :city: meet the animals waiting for adoption.', ['name' => $shelter->name, 'city' => $shelter->city]) }}</description>
        <language>{{ app()->getLocale() }}</language>
        <atom:link href="{{ route('shelters.feed', $shelter) }}" rel="self" type="application/rss+xml" />
        @foreach ($pets as $pet)
            @php
                $petUrl = route('shelters.show', ['shelter' => $shelter, 'animal' => $pet->id]);
                $mainImage = $pet->images->firstWhere('is_main', true) ?? $pet->images->first();
            @endphp
            <item>
                <title>{{ __(':name is looking for a family!', ['name' => $pet->name]) }}</title>
                <link>{{ $petUrl }}</link>
                <guid isPermaLink="true">{{ $petUrl }}</guid>
                <pubDate>{{ ($pet->checkin_date ?? $pet->created_at)->toRssString() }}</pubDate>
                <description>{{ $pet->shareCaption($petUrl) }}</description>
                @if ($mainImage)
                    <enclosure url="{{ url(Storage::url($mainImage->image_path)) }}" length="{{ Storage::exists($mainImage->image_path) ? Storage::size($mainImage->image_path) : 0 }}" type="{{ Storage::exists($mainImage->image_path) ? Storage::mimeType($mainImage->image_path) : 'image/jpeg' }}" />
                @endif
            </item>
        @endforeach
    </channel>
</rss>
