<nav id="sidebar" class="sidebar-wrapper sidebar-light">
    <div class="sidebar-content" data-simplebar style="height: calc(100% - 80px);">
        <div class="sidebar-brand">
            <a href="{{ route('panduan.show') }}" class="d-flex align-items-center text-decoration-none">
                <img src="{{ asset('icons/horizontal-e-suka.png') }}" height="45" class="logo-light-mode" alt="Logo">
                <img src="{{ asset('icons/horizontal-e-suka-white.png') }}" height="45" class="logo-dark-mode" alt="Logo">
            </a>
        </div>

        <ul class="sidebar-menu" id="panduan-menu">
            <!-- Home -->
            <li class="sidebar">
                <a href="{{ route('panduan.show') }}">Beranda</a>
            </li>

            <!-- Section Label -->
            <li class="section-label">Panduan</li>

            <!-- Panduan Akun -->
            <li class="sidebar-dropdown">
                <a href="javascript:void(0)">Panduan Akun</a>
                <div class="sidebar-submenu">
                    <ul>
                        <li><a href="{{ route('panduan.show', 'akun/daftar-dengan-email') }}">Daftar Dengan Email</a></li>
                        <li><a href="{{ route('panduan.show', 'akun/daftar-dengan-google') }}">Daftar Dengan Google</a></li>
                        <li><a href="{{ route('panduan.show', 'akun/lupa-password') }}">Lupa Password</a></li>
                        <li><a href="{{ route('panduan.show', 'akun/menghapus-akun') }}">Menghapus Akun</a></li>
                    </ul>
                </div>
            </li>

            <!-- Panduan Surat Kuasa -->
            <li class="sidebar-dropdown">
                <a href="javascript:void(0)">Panduan Surat Kuasa</a>
                <div class="sidebar-submenu">
                    <ul>
                        <li><a href="{{ route('panduan.show', 'surat-kuasa/pengajuan') }}">Pengajuan Surat Kuasa</a></li>
                        <li><a href="{{ route('panduan.show', 'surat-kuasa/pembayaran') }}">Pembayaran Surat Kuasa</a></li>
                        <li><a href="{{ route('panduan.show', 'surat-kuasa/perbaikan') }}">Perbaikan Surat Kuasa</a></li>
                        <li><a href="{{ route('panduan.show', 'surat-kuasa/download-barcode') }}">Download Barcode</a></li>
                    </ul>
                </div>
            </li>
        </ul>
    </div>
</nav>
