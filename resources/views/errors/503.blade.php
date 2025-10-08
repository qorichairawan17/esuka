@extends('errors.layout.index', ['title' => '503 Service Unavailable'])
@section('title', '503 Service Unavailable')
@section('content')
    @include('errors.layout.content', ['code' => '503 Service Unavailable', 'message' => 'Layanan sedang tidak tersedia. Server sedang dalam pemeliharaan atau kelebihan beban.'])
@endsection
