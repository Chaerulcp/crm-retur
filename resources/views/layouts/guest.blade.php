@php
    $seoTitle    = $__env->hasSection('seo_title')
        ? $__env->yieldContent('seo_title')
        : config('app.name', 'Retunly') . ' — Automate Your Returns Workflow';

    $seoDesc     = $__env->hasSection('seo_description')
        ? $__env->yieldContent('seo_description')
        : 'Retunly is the AI-powered returns management platform for e-commerce. Automate photo verification, fraud detection, and customer replies.';

    $seoCanonical = $__env->hasSection('seo_canonical')
        ? $__env->yieldContent('seo_canonical')
        : url()->current();

    $seoRobots   = $__env->hasSection('seo_robots')
        ? $__env->yieldContent('seo_robots')
        : 'index, follow';

    $seoImage    = $__env->hasSection('seo_image')
        ? $__env->yieldContent('seo_image')
        : asset('images/og-retunly.png');
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        {{-- SEO: Title --}}
        <title>{{ $seoTitle }}</title>

        {{-- SEO: Meta Description --}}
        <meta name="description" content="{{ $seoDesc }}">

        {{-- SEO: Canonical URL --}}
        <link rel="canonical" href="{{ $seoCanonical }}">

        {{-- SEO: Robots --}}
        <meta name="robots" content="{{ $seoRobots }}">

        {{-- SEO: Open Graph --}}
        <meta property="og:type" content="website">
        <meta property="og:site_name" content="Retunly">
        <meta property="og:title" content="{{ $seoTitle }}">
        <meta property="og:description" content="{{ $seoDesc }}">
        <meta property="og:url" content="{{ url()->current() }}">
        <meta property="og:locale" content="{{ app()->getLocale() === 'id' ? 'id_ID' : 'en_US' }}">
        <meta property="og:image" content="{{ $seoImage }}">

        {{-- SEO: Twitter Card --}}
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:title" content="{{ $seoTitle }}">
        <meta name="twitter:description" content="{{ $seoDesc }}">
        <meta name="twitter:image" content="{{ $seoImage }}">

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
