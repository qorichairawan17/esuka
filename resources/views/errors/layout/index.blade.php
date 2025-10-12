<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8" />
    <title>{{ $title ?? 'Error' }}</title>

    @include('miscellaneous.meta')

    <link href="{{ asset('admin/assets/css/bootstrap.min.css') }}" class="theme-opt" rel="stylesheet" type="text/css" />
    <link href="{{ asset('admin/assets/libs/@mdi/font/css/materialdesignicons.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('admin/assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('admin/assets/libs/@iconscout/unicons/css/line.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('admin/assets/css/style.min.css') }}" class="theme-opt" rel="stylesheet" type="text/css" />
    <link href="{{ asset('admin/assets/css/custom.css') }}" rel="stylesheet">
</head>

<body>

    @yield('content')

    <script src="{{ asset('admin/assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('admin/assets/libs/feather-icons/feather.min.js') }}"></script>
    <script src="{{ asset('admin/assets/js/plugins.init.js') }}"></script>
    <script src="{{ asset('admin/assets/js/app.js') }}"></script>
    @stack('scripts')
</body>

</html>
