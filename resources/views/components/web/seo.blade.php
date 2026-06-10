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
