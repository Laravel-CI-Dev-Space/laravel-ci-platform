@props([
    'title'       => null,
    'description' => null,
    'image'       => null,
    'canonical'   => null,
    'type'        => 'website',
    'robots'      => null,
])

@php
    $title       = $title ?: config('vitrine.default_title');
    $description = $description ?: config('vitrine.default_description');
    $image       = $image ?: asset(config('vitrine.og_image'));
    $canonical   = $canonical ?: url()->current();
    $siteName    = config('app.name', 'Laravel CI');
@endphp

<title>{{ $title }}</title>
<meta name="description" content="{{ $description }}" />
@if($robots)
  <meta name="robots" content="{{ $robots }}" />
@endif
<link rel="canonical" href="{{ $canonical }}" />

<meta property="og:type" content="{{ $type }}" />
<meta property="og:site_name" content="{{ $siteName }}" />
<meta property="og:title" content="{{ $title }}" />
<meta property="og:description" content="{{ $description }}" />
<meta property="og:url" content="{{ $canonical }}" />
<meta property="og:image" content="{{ $image }}" />

<meta name="twitter:card" content="summary_large_image" />
<meta name="twitter:title" content="{{ $title }}" />
<meta name="twitter:description" content="{{ $description }}" />
<meta name="twitter:image" content="{{ $image }}" />
