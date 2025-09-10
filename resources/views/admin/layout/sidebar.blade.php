 @php
     if (Auth::user()->role == \App\Enum\RoleEnum::Superadmin->value) {
         $urlDashboard = route('dashboard.admin');
     } else {
         $urlDashboard = route('dashboard.pengguna');
     }
 @endphp
 <!-- sidebar-wrapper -->
 <nav id="sidebar" class="sidebar-wrapper sidebar-light">
     <div class="sidebar-content" data-simplebar style="height: calc(100% - 60px);">
         <div class="sidebar-brand">
             <a href="{{ $urlDashboard }}">
                 <img src="{{ asset('icons/horizontal-e-suka.png') }}" height="50" class="logo-light-mode" alt="">
                 <img src="{{ asset('icons/horizontal-e-suka-white.png') }}" height="50" class="logo-dark-mode" alt="">
                 <span class="sidebar-colored">
                     <img src="{{ asset('icons/horizontal-e-suka-white.png') }}" height="50" alt="">
                 </span>
             </a>
         </div>

         <ul class="sidebar-menu">
             <li class="sidebar">
                 <a href="{{ $urlDashboard }}">
                     <i class="ti ti-home me-2"></i>Dashboard
                 </a>
             </li>
             <li class="sidebar-dropdown">
                 <a href="javascript:void(0)"><i class="ti ti-files me-2"></i>Surat Kuasa</a>
                 <div class="sidebar-submenu">
                     <ul>
                         <li><a href="{{ route('surat-kuasa.index') }}">Pendaftaran</a></li>
                         @if (Auth::user()->role == \App\Enum\RoleEnum::Superadmin->value)
                             <li><a href="">Laporan</a></li>
                         @endif
                     </ul>
                 </div>
             </li>
             @if (Auth::user()->role == \App\Enum\RoleEnum::Superadmin->value)
                 <li class="sidebar-dropdown">
                     <a href="javascript:void(0)"><i class="ti ti-browser me-2"></i>Pengguna</a>
                     <div class="sidebar-submenu">
                         <ul>
                             <li><a href="{{ route('panitera.index') }}">Panitera</a></li>
                             <li><a href="{{ route('administrator.index') }}">Administrator</a></li>
                             <li><a href="{{ route('advokat.index') }}">Advokat/Non Advokat</a></li>
                         </ul>
                     </div>
                 </li>
                 <li class="sidebar-dropdown">
                     <a href="javascript:void(0)"><i class="ti ti-settings me-2"></i>Pengaturan</a>
                     <div class="sidebar-submenu">
                         <ul>
                             <li><a href="{{ route('aplikasi.index') }}">Aplikasi</a></li>
                             <li><a href="{{ route('testimoni.index') }}">Testimoni</a></li>
                             <li><a href="{{ route('pembayaran.index') }}">Pembayaran & PNBP</a></li>
                             <li><a href="{{ route('pejabat-struktural.index') }}">Pejabat Struktural</a></li>
                         </ul>
                     </div>
                 </li>
             @endif
             <li class="sidebar">
                 <a href="{{ route('audit-trail.index') }}"><i class="ti ti-database me-2"></i>Audit Trail</a>
             </li>
             <li class="sidebar">
                 <a href="javascript:void(0)" class="text-danger"><i class="ti ti-arrow-right me-2"></i>Keluar</a>
             </li>

         </ul>
         <!-- sidebar-menu  -->
     </div>
     <!-- Sidebar Footer -->
     <ul class="sidebar-footer list-unstyled mb-0">
         <li class="list-inline-item mb-0">
             <a href="https://1.envato.market/landrick" target="_blank" class="btn btn-icon btn-soft-light">
                 <i class="ti ti-headphone-alt"></i>
             </a>
             <small class="text-muted fw-medium ms-1">
                 Layanan Bantuan
             </small>
         </li>
     </ul>
     <!-- Sidebar Footer -->
 </nav>
 <!-- sidebar-wrapper  -->
