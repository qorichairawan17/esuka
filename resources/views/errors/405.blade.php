@extends('errors.layout.index', ['title' => '405 Method Not Allowed'])
@section('title', '405 Method Not Allowed')
@section('content')
    @include('errors.layout.content', ['code' => '405 Method Not Allowed', 'message' => 'Metode permintaan tidak diizinkan untuk halaman ini.'])
@endsection
