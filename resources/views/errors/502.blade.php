@extends('errors.layout.index', ['title' => '502 Bad Gateway'])
@section('title', '502 Bad Gateway')
@section('content')
    @include('errors.layout.content', ['code' => '502 Bad Gateway', 'message' => 'Server menerima respons yang tidak valid. Silakan coba lagi nanti.'])
@endsection
