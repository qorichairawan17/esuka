@extends('errors.layout.index', ['title' => '504 Gateway Timeout'])
@section('title', '504 Gateway Timeout')
@section('content')
    @include('errors.layout.content', ['code' => '504 Gateway Timeout', 'message' => 'Server tidak menerima respons tepat waktu dari server upstream. Silakan coba lagi nanti.'])
@endsection
