<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="google-site-verification" content="Sp9E55tVkNph_ttvggLD52MY-ACeGfeivQbmWp7CWfo">
    <meta name="description" content="@yield('meta_description')">
    <meta name="theme-color" content="#524641">
    <meta name="apple-mobile-web-app-title" content="PCB">

    <meta property="og:url" content="https://projectcitybuild.com">
    <meta property="og:title" content="@yield('meta_title', 'Project City Build')">
    <meta property="og:description" content="@yield('meta_description')">
    <meta property="og:site_name" content="Project City Build">
    <meta property="og:type" content="website">
    <meta property="og:locale" content="en_US">

    <meta name="twitter:card" content="@yield('meta_description')">
    <meta name="twitter:site" content="@PCB_Minecraft">


    <title>@yield('title', 'Project City Build')</title>

    <link rel="stylesheet" href="{{ mix('assets/css/app-v2.css') }}">
    <link rel="icon" type="type/x-icon" href="{{ asset('assets/favicon.ico') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="https://i.imgur.com/g1OfIGT.png"/>
    <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css" />

    <script defer src="https://use.fontawesome.com/releases/v5.10.2/js/brands.js"></script>
    <script defer src="https://use.fontawesome.com/releases/v5.10.2/js/solid.js"></script>
    <script defer src="https://use.fontawesome.com/releases/v5.10.2/js/fontawesome.js"></script>

    <script defer src="{{ mix('assets/js/manifest.js') }}"></script>
    <script defer src="{{ mix('assets/js/vendor.js') }}"></script>
    <script defer src="{{ mix('assets/js/app.js') }}"></script>

    @stack('head')

    @env(['staging', 'production'])
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-2747125-5"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag() {
            dataLayer.push(arguments);
        }
        gtag('js', new Date());
        gtag('config', 'UA-2747125-5');
    </script>
    @endenv
</head>
<body>

<x-navbar />

<div id="app">
    @yield('body')
</div>

@stack('end')

</body>
</html>
