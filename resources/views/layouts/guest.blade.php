<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        {{-- SEO: Title --}}
        <title>@hasSection('seo_title') @yield('seo_title') @else {{ config('app.name', 'Retunly') }} — Automate Your Returns Workflow @endif</title>

        {{-- SEO: Meta Description --}}
        <meta name="description" content="@hasSection('seo_description') @yield('seo_description') @else Retunly is the AI-powered returns management platform for e-commerce. Automate photo verification, fraud detection, and customer replies. @endif">

        {{-- SEO: Canonical URL --}}
        <link rel="canonical" href="@hasSection('seo_canonical') @yield('seo_canonical') @else {{ url()->current() }} @endif">

        {{-- SEO: Robots --}}
        <meta name="robots" content="@hasSection('seo_robots') @yield('seo_robots') @else index, follow @endif">

        {{-- SEO: Open Graph --}}
        <meta property="og:type" content="website">
        <meta property="og:site_name" content="Retunly">
        <meta property="og:title" content="@hasSection('seo_title') @yield('seo_title') @else {{ config('app.name', 'Retunly') }} — Automate Your Returns Workflow @endif">
        <meta property="og:description" content="@hasSection('seo_description') @yield('seo_description') @else Retunly is the AI-powered returns management platform for e-commerce. Automate photo verification, fraud detection, and customer replies. @endif">
        <meta property="og:url" content="{{ url()->current() }}">
        <meta property="og:locale" content="{{ app()->getLocale() === 'id' ? 'id_ID' : 'en_US' }}">
        @hasSection('seo_image')
            <meta property="og:image" content="@yield('seo_image')">
        @else
            <meta property="og:image" content="{{ asset('images/og-retunly.png') }}">
        @endif

        {{-- SEO: Twitter Card --}}
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:title" content="@hasSection('seo_title') @yield('seo_title') @else {{ config('app.name', 'Retunly') }} — Automate Your Returns Workflow @endif">
        <meta name="twitter:description" content="@hasSection('seo_description') @yield('seo_description') @else Retunly is the AI-powered returns management platform for e-commerce. @endif">
        @hasSection('seo_image')
            <meta name="twitter:image" content="@yield('seo_image')">
        @else
            <meta name="twitter:image" content="{{ asset('images/og-retunly.png') }}">
        @endif

        {{-- SEO: JSON-LD Structured Data --}}
        @stack('json_ld')

        {{-- Tipografi: Bricolage Grotesque (display), IBM Plex Sans (isi), IBM Plex Mono (data) --}}
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=bricolage-grotesque:500,600,700|ibm-plex-mono:400,500,600|ibm-plex-sans:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-ink antialiased">
        {{ $slot }}
    </body>
</html>
