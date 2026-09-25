@php
    $pageTitle = filled($title ?? null) ? $title.' - '.config('app.name', 'Laravel') : config('app.name', 'Laravel');
    $pageDescription = Str::limit(trim(strip_tags($description ?? __('Find a shelter animal waiting for a home. Browse the dogs and cats of our partner shelters and adopt your new best friend.'))), 160);
    // Only the public portal pages opt in to indexing; the backoffice, auth and print pages stay out of search engines.
    $isIndexable = ($indexable ?? false) === true;
    // Pages whose content depends on a query string (e.g. a shared pet link) pass their own canonical URL.
    $pageUrl = $canonicalUrl ?? url()->current();
@endphp
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />

<title>
    {{ $pageTitle }}
</title>

<meta name="description" content="{{ $pageDescription }}">
<meta name="robots" content="{{ $isIndexable ? 'index, follow, max-image-preview:large' : 'noindex, nofollow' }}">
<meta name="theme-color" content="#fffbeb" media="(prefers-color-scheme: light)">
<meta name="theme-color" content="#0c0a09" media="(prefers-color-scheme: dark)">

@if ($isIndexable)
    <link rel="canonical" href="{{ $pageUrl }}">

    <meta property="og:type" content="website">
    <meta property="og:site_name" content="{{ config('app.name') }}">
    <meta property="og:title" content="{{ $pageTitle }}">
    <meta property="og:description" content="{{ $pageDescription }}">
    <meta property="og:url" content="{{ $pageUrl }}">
    <meta property="og:locale" content="{{ str_replace('-', '_', app()->getLocale()) }}">
    @if (filled($image ?? null))
        <meta property="og:image" content="{{ $image }}">
        <meta property="og:image:alt" content="{{ $title ?? config('app.name') }}">
    @endif

    <meta name="twitter:card" content="{{ filled($image ?? null) ? 'summary_large_image' : 'summary' }}">
    <meta name="twitter:title" content="{{ $pageTitle }}">
    <meta name="twitter:description" content="{{ $pageDescription }}">
    @if (filled($image ?? null))
        <meta name="twitter:image" content="{{ $image }}">
    @endif

    @if (filled($feedUrl ?? null))
        <link rel="alternate" type="application/rss+xml" title="{{ $pageTitle }}" href="{{ $feedUrl }}">
    @endif

    @if (filled($structuredData ?? null))
        <script type="application/ld+json">{!! json_encode($structuredData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!}</script>
    @endif
@endif

<link rel="icon" href="/favicon.ico" sizes="any">
<link rel="icon" href="/favicon.svg" type="image/svg+xml">
<link rel="apple-touch-icon" href="/apple-touch-icon.png">

@fonts

@vite(['resources/css/app.css', 'resources/js/app.js'])
@fluxAppearance
