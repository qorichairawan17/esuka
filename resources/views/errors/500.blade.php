@extends('errors.layout.index', ['title' => '500 Internal Server Error'])
@section('title', '500 Internal Server Error')
@section('content')
    @include('errors.layout.content', ['code' => '500 Internal Server Error', 'message' => 'Terjadi kesalahan pada server. Silakan coba lagi nanti atau hubungi administrator.'])
@endsection
