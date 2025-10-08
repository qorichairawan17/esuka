@extends('errors.layout.index', ['title' => '408 Request Timeout'])
@section('title', '408 Request Timeout')
@section('content')
    @include('errors.layout.content', ['code' => '408 Request Timeout', 'message' => 'Permintaan Anda melebihi batas waktu. Silakan coba lagi.'])
@endsection
