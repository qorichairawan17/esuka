@extends('errors.layout.index', ['title' => '401 Unauthorized'])
@section('title', '401 Unauthorized')
@section('content')
    @include('errors.layout.content', ['code' => '401 Unauthorized', 'message' => 'Anda tidak memiliki akses ke halaman ini. Silakan login terlebih dahulu.'])
@endsection
