@extends('errors.layout.index', ['title' => '400 Bad Request'])
@section('title', '400 Bad Request')
@section('content')
    @include('errors.layout.content', ['code' => '400 Bad Request', 'message' => 'Permintaan tidak dapat dipahami oleh server karena sintaks yang salah.'])
@endsection
