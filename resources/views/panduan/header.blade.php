<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8" />
    <title>{{ $title ?? '' }}</title>

    @include('miscellaneous.meta')
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Css -->
    <link href="{{ asset('admin/assets/libs/simplebar/simplebar.min.css') }}" rel="stylesheet">
    <!-- Bootstrap Css -->
    <link href="{{ asset('admin/assets/css/bootstrap.min.css') }}" class="theme-opt" rel="stylesheet" type="text/css" />
    <!-- Icons Css -->
    <link href="{{ asset('admin/assets/libs/@mdi/font/css/materialdesignicons.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('admin/assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('admin/assets/libs/@iconscout/unicons/css/line.css') }}" type="text/css" rel="stylesheet" />
    <!-- Style Css-->
    <link href="{{ asset('admin/assets/css/style.min.css') }}" class="theme-opt" rel="stylesheet" type="text/css" />
    <link href="{{ asset('admin/assets/css/additional-style.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/libs/jquery-ui-1.14.1/jquery-ui.css') }}" rel="stylesheet">
    <link href="{{ asset('admin/assets/plugins/DataTables/datatables.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/libs/sweetalert2/dist/sweetalert2.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/panduan.css') }}" rel="stylesheet">
</head>

<body>
