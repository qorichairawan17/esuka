@extends('errors.layout.index', ['title' => '419 Page Expired'])
@section('title', '419 Page Expired')
@section('content')
    @include('errors.layout.content', ['code' => '419 Page Expired', 'message' => 'Halaman telah kadaluarsa. Silakan muat ulang halaman dan coba lagi.'])
@endsection
