@extends('errors.layout.index', ['title' => '403 Forbidden'])
@section('title', '403 Forbidden')
@section('content')
    @include('errors.layout.content', ['code' => '403 Forbidden', 'message' => 'Akses ditolak. Kamu tidak memiliki izin untuk mengakses halaman ini.'])
@endsection
