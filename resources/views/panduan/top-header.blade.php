<!-- Top Header -->
<div class="top-header">
    <div class="header-bar">
        <!-- Left: Toggle + Search -->
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('panduan.show') }}" class="logo-icon d-lg-none">
                <img src="{{ asset('icons/android-icon-192x192.png') }}" height="32" class="rounded" alt="logo">
            </a>
            <a id="close-sidebar" class="btn btn-icon btn-soft-light" href="javascript:void(0)">☰</a>
            <div class="search-bar d-none d-md-block">
                <input type="text" class="form-control" autocomplete="off" name="s" id="s" placeholder="Cari topik panduan..." onkeyup="searchTopics()">
            </div>
        </div>
        <!-- Right: Button -->
        <div class="d-flex align-items-center">
            <a href="{{ route('app.signin') }}" class="btn btn-primary btn-sm">Masuk Aplikasi</a>
        </div>
    </div>
</div>
<!-- Mobile Search -->
<div class="d-md-none px-3 py-2 bg-white border-bottom">
    <input type="text" class="form-control form-control-sm w-100" autocomplete="off" placeholder="Cari topik panduan..." onkeyup="searchTopics()">
</div>
