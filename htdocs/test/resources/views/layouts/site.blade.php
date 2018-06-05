<!DOCTYPE html>
<html lang="ru">
    
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="theme-color" content="black">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @if (auth()->check())
        <meta name="api-token" content="{{ auth()->user()->api_token }}">
    @endif
    <title>Expass</title>
    <!-- Подключаем стили -->
    <link rel="stylesheet" href="{{ asset('/css/site.css') }}" />
</head>

<body>
    <div id="app"></div>
    <!-- Подключение скриптов -->
    <!--[if lt IE 9]>
    <script src="{{ asset('/js/ie.js') }}"></script>
    <![endif]-->
    <!--Libs-->

    <script src="https://cdn.polyfill.io/v2/polyfill.min.js"></script>
    <script src="{{ asset('/js/manifest.js') }}"></script>
    <script src="{{ asset('/js/vendor.js') }}"></script>
    <script src="{{ asset("/js/{$role}.js") }}"></script>
</body>

</html>
