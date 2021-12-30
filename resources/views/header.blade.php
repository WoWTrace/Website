<?php
if (Breadcrumbs::has()) {
    $position    = 1;
    $breadcrumbs = [];
    foreach (Breadcrumbs::current() as $breadcrumb) {
        $breadcrumbs[] = [
            '@type'    => 'ListItem',
            'position' => $position++,
            'name'     => $breadcrumb->title(),
            'item'     => $breadcrumb->url() ?? '',
        ];
    }
}
?>

@push('head')
    <!-- Primary Meta Tags -->
    <meta name="description"
          content="WoWTrace shows data from World of Warcraft in a clear form and help with data mining.">
    <meta name='robots' content='index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1'/>
    <meta name="format-detection" content="telephone=no">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{env('APP_URL', 'localhost')}}">
    <meta property="og:title"
          content="@yield('title', config('app.name')) @hasSection('title') - {{ config('app.name') }} @endif">
    <meta property="og:description"
          content="WoWTrace shows data from World of Warcraft in a clear form and help with data mining.">
    <meta property="og:image" content="/resources/image/logo.png">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{{env('APP_URL', 'localhost')}}">
    <meta property="twitter:title"
          content="@yield('title', config('app.name')) @hasSection('title') - {{ config('app.name') }} @endif">
    <meta property="twitter:description"
          content="WoWTrace shows data from World of Warcraft in a clear form and help with data mining.">
    <meta property="twitter:image" content="/resources/image/logo.png">

    <!-- Favicon -->
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="manifest" href="/site.webmanifest">
    <link rel="mask-icon" href="/safari-pinned-tab.svg" color="#cfa03f">
    <meta name="apple-mobile-web-app-title" content="WoWTrace">
    <meta name="application-name" content="WoWTrace">
    <meta name="msapplication-TileColor" content="#2b5797">
    <meta name="theme-color" content="#cccccc">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-touch-fullscreen" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">

    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "ResearchProject",
      "name": "WoWTrace",
      "url": "{{env('APP_URL', 'localhost')}}",
      "logo": "{{env('APP_URL', 'localhost')}}resources/image/logo.png",
      "sameAs": "https://github.com/Luzifix/wowtrace"
    }
    </script>
    @if(Breadcrumbs::has())
        <script type="application/ld+json">
        {
          "@context": "https://schema.org/",
          "@type": "BreadcrumbList",
          "itemListElement": {!! json_encode($breadcrumbs, JSON_UNESCAPED_SLASHES) !!}
            }

        </script>
    @endif
@endpush

<div class="h2 fw-light d-flex align-items-center al">
    <div id="website-logo"></div>

    <p class="ms-3 my-0 d-none d-sm-block">
        WoWTrace<br>
        <small class="align-top opacity">A datamining website</small>
    </p>
</div>
