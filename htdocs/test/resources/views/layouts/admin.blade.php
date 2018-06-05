<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="api-token" content="{{ auth()->user()->api_token }}">

        <title>Expass - Admin</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">
    
        <!-- Styles -->
        <link href="{{ mix('/css/admin.css') }}" rel="stylesheet">
    </head>
    <body>
        <div id="app">
        </div>
        <script src="https://cdn.polyfill.io/v2/polyfill.min.js"></script>
        <script src="{{ mix('/js/manifest.js') }}"></script>
        <script src="{{ mix('/js/vendor.js') }}"></script>
        <script src="{{ mix('/js/' . auth()->user()->role . '.js') }}"></script>
    </body>
</html>
