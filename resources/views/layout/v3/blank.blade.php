<!doctype html>
<html lang="{{ __('config.html_language') }}">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex, nofollow, noarchive, noodp, NoImageIndex, noydir">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="mobile-web-app-capable" content="yes">
    <!--
    (BLANK)
    If the base href URL begins with "http://" but you are sure it should start with "https://",
    please visit the following page: https://bit.ly/FF3-broken-base-href
    -->
    <base href="{{ route('index', null, true) }}/">
    <title>@yield('status') - {{ $title ?? 'Firefly III'  }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes"/>
    <meta name="theme-color" content="#ffffff" media="(prefers-color-scheme: light)"/>
    <meta name="theme-color" content="#161b22" media="(prefers-color-scheme: dark)"/>
    <meta name="color-scheme" content="light dark">
    @vite(['sass/app.scss'])
    <x-layout.fav-icons/>
    <script nonce="{{ $JS_NONCE ?? '' }}">
        (() => {
            'use strict'
            document.documentElement.setAttribute('data-bs-theme', (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light'))
        })()
    </script>
</head>
<body class="bg-body-tertiary">
<main class="ff-status-page">
    <div class="ff-status-card">
        <a href="{{ route('index', null, true) }}" class="ff-auth-brand">
            <img src="images/logo-session.png" width="40" height="40" alt="" aria-hidden="true"/>
            <span>Firefly III</span>
        </a>
        <p class="ff-status-code">@yield('status_code')</p>
        <h1>@yield('status')</h1>
        <h2>@yield('sub_title')</h2>
        <div class="ff-status-body">
            @yield('content')
        </div>
    </div>
</main>
@vite(['js/pages/blank.js'])
@yield('scripts')
</body>
</html>
