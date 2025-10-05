 <!-- sidebar-wrapper -->
 <nav id="sidebar" class="sidebar-wrapper sidebar-light">
     <div class="sidebar-content" data-simplebar style="height: calc(100% - 60px);">
         <div class="sidebar-brand">
             <a href="javascript:void(0);">
                 <img src="{{ asset('icons/horizontal-e-suka.png') }}" height="50" class="logo-light-mode" alt="">
                 <img src="{{ asset('icons/horizontal-e-suka-white.png') }}" height="50" class="logo-dark-mode" alt="">
                 <span class="sidebar-colored">
                     <img src="{{ asset('icons/horizontal-e-suka-white.png') }}" height="50" alt="">
                 </span>
             </a>
         </div>
         <ul class="sidebar-menu">
             <li class="sidebar">
                 <a href="{{ route('panduan.show') }}">
                     <i class="ti ti-home me-2"></i>Home
                 </a>
             </li>
             <li class="sidebar-dropdown">
                 <a href="javascript:void(0)"><i class="ti ti-user me-2"></i>Panduan Akun</a>
                 <div class="sidebar-submenu">
                     <ul>
                         <li><a href="{{ route('panduan.show', 'akun/daftar-dengan-email') }}">Daftar Dengan Email</a></li>
                         <li><a href="{{ route('panduan.show', 'akun/daftar-dengan-google') }}">Daftar Dengan Google</a></li>
                         <li><a href="{{ route('panduan.show', 'akun/lupa-password') }}">Lupa Password</a></li>
                         <li><a href="{{ route('panduan.show', 'akun/menghapus-akun') }}">Menghapus Akun</a></li>
                         <li><a href="{{ route('panduan.show', 'akun/tautkan-google') }}">Tautkan Akun Dengan Google</a></li>
                     </ul>
                 </div>
             </li>
             <li class="sidebar-dropdown">
                 <a href="javascript:void(0)"><i class="ti ti-files me-2"></i>Panduan Surat Kuasa</a>
                 <div class="sidebar-submenu">
                     <ul>
                         <li><a href="{{ route('panduan.show', 'surat-kuasa/pengajuan') }}">Pengajuan Surat Kuasa</a></li>
                         <li><a href="{{ route('panduan.show', 'surat-kuasa/pembayaran') }}">Pembayaran Surat Kuasa</a></li>
                         <li><a href="{{ route('panduan.show', 'surat-kuasa/perbaikan') }}">Perbaikan Surat Kuasa</a></li>
                         <li><a href="{{ route('panduan.show', 'surat-kuasa/verifikasi') }}">Verifikasi Surat Kuasa</a></li>
                     </ul>
                 </div>
             </li>
         </ul>
         <!-- sidebar-menu  -->
     </div>
     <!-- Sidebar Footer -->
     <ul class="sidebar-footer list-unstyled mb-0">
         <li class="list-inline-item mb-0">
             <a href="https://wa.me/{{ $infoApp->kontak }}" target="_blank" class="btn btn-icon btn-soft-light">
                 <i class="fa fa-phone-alt"></i>
             </a>
             <small class="text-muted fw-medium ms-1">
                 Layanan Bantuan
             </small>
         </li>
     </ul>
     <!-- Sidebar Footer -->
 </nav>
 <!-- sidebar-wrapper  -->
