@extends('errors.layout.index', ['title' => '404 Not Found'])
@section('title', '404 Page Not Found')
@section('content')
    @include('errors.layout.content', ['code' => '404 Not Found', 'message' => 'Maaf, halaman yang Kamu cari tidak ditemukan.'])
@endsection
