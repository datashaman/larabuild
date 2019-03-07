<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="utf-8">
        <meta http-equiv="x-ua-compatible" content="ie=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="">
        <meta name="og:title" content="">
        <meta name="og:type" content="website">
        <meta name="og:image" content="">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title></title>

        <link rel="icon" type="image/png" href="" sizes="32x32" />
        <link rel="icon" type="image/png" href="" sizes="16x16" />

        <link rel="stylesheet" href="/css/app.css">

        @if ($trackingId = config('services.googleAnalytics.trackingId'))
            <script async src="https://www.googletagmanager.com/gtag/js?id={{ $trackingId }}"></script>
            <script>
                window.dataLayer = window.dataLayer || [];
                function gtag(){dataLayer.push(arguments);}
                gtag('js', new Date());
                gtag('config', '{{ $trackingId }}');
            </script>
        @endif

        @yield('scripts_head')
    </head>

    <body>
        <div id="app">
            <app></app>
        </div>

        <script src="/js/app.js"></script>
    </body>

</html>
