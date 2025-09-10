<header id="topnav" class="defaultscroll sticky">
    <div class="container">
        <a class="logo" href="index.html">
            <img src="{{ asset('icons/horizontal-e-suka.png') }}" height="45" class="logo-light-mode" alt="">
            <img src="{{ asset('icons/horizontal-e-suka-white.png') }}" height="45" class="logo-dark-mode" alt="">
        </a>

        <div class="menu-extras">
            <div class="menu-item">
                <a class="navbar-toggle" id="isToggle" onclick="toggleMenu()">
                    <div class="lines">
                        <span></span>
                        <span></span>
                        <span></span>
                    </div>
                </a>
            </div>
        </div>

        <ul class="buy-button list-inline mb-0">
            <li class="list-inline-item ps-1 mb-0">
                @if (Auth::check())
                    <a href="{{ route('dashboard.pengguna') }}" class="btn btn-pills btn-primary">
                        Dashboard <i data-feather="arrow-right"></i>
                    </a>
                @else
                    <a href="{{ route('app.signin') }}" class="btn btn-pills btn-soft-primary">
                        Login <i data-feather="log-in" class="fea icon-sm"> </i>
                    </a>
                @endif

            </li>
        </ul>

        <div id="navigation">
            <ul class="navigation-menu" style="float: left;">
                <li><a href="{{ route('app.home') }}" class="sub-menu-item active">Beranda</a></li>
                <li><a href="{{ route('app.about') }}" class="has-submenu parent-parent-menu-item">Tentang</a></li>
                <li><a href="{{ route('app.contact') }}" class="has-submenu parent-parent-menu-item">Kontak</a></li>

                <li class="has-submenu parent-menu-item">
                    <a href="javascript:void(0)">Surat Kuasa</a><span class="menu-arrow"></span>
                    <ul class="submenu">
                        <li><a href="documentation.html" class="sub-menu-item">Documentation</a></li>
                        <li><a href="changelog.html" class="sub-menu-item">Changelog</a></li>
                        <li><a href="widget.html" class="sub-menu-item">Widget</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</header>
