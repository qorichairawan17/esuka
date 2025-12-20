@extends('panduan.body')
@section('title', $title)
@section('content')
    <!-- Start Page Content -->
    <main class="page-content bg-light">

        @include('panduan.top-header')

        <div class="container-fluid">
            <div class="layout-specing">

                <!-- Breadcrumb -->
                <nav aria-label="breadcrumb" class="mb-4">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('panduan.show') }}">Beranda</a>
                        </li>
                        @if (isset($breadcrumbs))
                            @foreach ($breadcrumbs as $crumb)
                                <li class="breadcrumb-item {{ $loop->last ? 'active' : '' }}">
                                    @if ($loop->last)
                                        {{ $crumb['title'] }}
                                    @else
                                        <a href="{{ $crumb['url'] }}">{{ $crumb['title'] }}</a>
                                    @endif
                                </li>
                            @endforeach
                        @else
                            <li class="breadcrumb-item active">{{ $title }}</li>
                        @endif
                    </ol>
                </nav>

                <!-- Main Content Card -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <!-- Article Content -->
                                <article class="panduan-content">
                                    {!! $content !!}
                                </article>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Navigation Between Articles -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <a href="{{ route('panduan.show') }}" class="btn btn-soft-warning btn-sm">
                                Kembali ke Beranda
                            </a>
                            <a href="https://wa.me/{{ $infoApp->kontak }}" target="_blank" class="btn btn-primary btn-sm">
                                Butuh Bantuan?
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div><!--end container-->

        @include('panduan.content-footer')
        <!-- End -->
    </main>
    <!--End page-content" -->
@endsection
