@extends('panduan.body')
@section('title', $title)
@section('content')
    <!-- Start Page Content -->
    <main class="page-content bg-light">

        @include('panduan.top-header')

        <div class="container-fluid">
            <div class="layout-specing">

                @include('admin.component.breadcumb')

                <div class="row mt-3">
                    <div class="col-12">
                        <div class="card shadow">
                            <div class="card-body">
                                {!! $content !!}
                            </div>
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
