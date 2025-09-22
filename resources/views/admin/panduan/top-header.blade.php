<!-- Top Header -->
<div class="top-header">
    <div class="header-bar d-flex justify-content-between">
        <div class="d-flex align-items-center">
            <a href="#" class="logo-icon me-3">
                <img src="{{ asset('icons/android-icon-192x192.png') }}" height="30" class="small" alt="logo">
                <span class="big">
                    <img src="{{ asset('icons/horizontal-e-suka.png') }}" height="50" class="logo-light-mode" alt="logo">
                    <img src="{{ asset('icons/horizontal-e-suka-white.png') }}" height="50" class="logo-dark-mode" alt="logo">
                </span>
            </a>
            <a id="close-sidebar" class="btn btn-icon btn-soft-light" href="javascript:void(0)">
                <i class="ti ti-menu-2"></i>
            </a>
            <div class="search-bar p-0 d-none d-md-block ms-2">
                <div id="search" class="menu-search mb-0">
                    <form role="search" method="get" id="searchform" class="searchform">
                        <div>
                            <input type="text" class="form-control border rounded" name="s" id="s" placeholder="Cari Topik...">
                            <input type="submit" id="searchsubmit" value="Search">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Top Header -->
