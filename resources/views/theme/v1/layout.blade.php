<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#37008a">
    <link rel="icon" href="{{ asset('/favicon.ico') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100..900;1,100..900&display=swap"
        rel="stylesheet">
    <link rel="preload" href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css" as="style"
        onload="this.onload=null;this.rel='stylesheet'">

    <noscript>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css">
    </noscript>

    @hasSection('meta')
        @yield('meta')
    @else
        @include('theme.v1.components.meta')
    @endif
    @include('theme.v1.components.json-ld')
    @stack('jsonld')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ asset('jquery/slick.css') }}">
    <link rel="stylesheet" href="{{ asset('jquery/slick-theme.css') }}">
    {!! data_get($site, 'magicbox.google.analytics_tracking_code') !!}
    {!! data_get($site, 'magicbox.inject.head') !!}
    @stack('styles')
    @yield('styles')
    <style>
        :root {
            --color-bg: #ffffff;
            --color-surface: #f7f8fb;
            --color-card: #ffffff;
            --color-border: #e5e7eb;
            --color-text: #0f172a;
            --color-muted: #475569;
            --color-primary: #370089;
            --color-primary-600: #270063;
            --color-primary-50: #5500d6;
            --color-secondary: #93C225;
            --color-success: #16a34a;
            --color-warning: #d97706;
            --color-danger: #dc2626;
            --color-info: #0284c7;
        }

        [data-theme="dark"] {
            --color-bg: #0b1220;
            --color-surface: #151c2c;
            --color-card: #0f172a;
            --color-border: #1f2937;
            --color-text: #e5e7eb;
            --color-muted: #94a3b8;
            --color-primary: #370089;
            --color-primary-600: #4600b0;
            --color-primary-50: #270063;
            --color-success: #22c55e;
            --color-warning: #f59e0b;
            --color-danger: #ef4444;
            --color-info: #38bdf8;
        }
    </style>
</head>
<body class="transition-colors duration-300">
    {!! data_get($site, 'magicbox.inject.body') !!}
    @include('theme.v1.components.header')
    @yield('content')
    @include('theme.v1.components.footer')
    @include('theme.v1.components.toast')
    <script src="{{ asset('jquery/jquery.js') }}"></script>
    <script src="{{ asset('jquery/slick.js') }}"></script>
    {!! data_get($site, 'magicbox.inject.footer') !!}
    @stack('scripts')
    @yield('scripts')
</body>