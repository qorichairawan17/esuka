@include('panduan.header')
<!-- Loader -->
<div id="preloader">
    <div id="status">
        <div class="spinner">
            <div class="double-bounce1"></div>
            <div class="double-bounce2"></div>
        </div>
    </div>
</div>
<!-- Loader -->
<div class="page-wrapper toggled">
    @include('panduan.sidebar')
    @yield('content')
</div>
@include('panduan.footer')
