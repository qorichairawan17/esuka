@extends('errors.layout.index', ['title' => '429 Too Many Requests'])
@section('title', '429 Too Many Requests')
@section('content')
    @include('errors.layout.content', ['code' => '429 Too Many Requests', 'message' => 'Terlalu banyak permintaan. Silakan tunggu beberapa saat sebelum mencoba lagi.'])
@endsection
