<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <style media="screen">
        html,body{
            background:rgb(48, 65, 86);
        }
        .title{
            text-align:center;
            margin:50px 0;
            color: #fff;
        }
    </style>
    @yield('styles')
</head>
<body>
    <div id="app" class="{{ route_class() }}-page">
        <div class="title">
            <h2>文章管理系统</h2>
        </div>
        @yield('content')
    </div>
    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}"></script>
    @yield('scripts')
</body>
</html>
