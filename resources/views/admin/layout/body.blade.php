@include('admin.layout.header')
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
    @include('admin.layout.sidebar')
    @yield('content')
</div>
<form action="{{ route('auth.logout') }}" method="POST" id="logout-form">
    @method('POST')
    @csrf
</form>
@include('admin.layout.footer')
