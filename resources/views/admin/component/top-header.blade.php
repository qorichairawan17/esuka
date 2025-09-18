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
                            <input type="text" class="form-control border rounded" name="s" id="s" placeholder="Cari Surat Kuasa...">
                            <input type="submit" id="searchsubmit" value="Search">
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <ul class="list-unstyled mb-0">
            <li class="list-inline-item mb-0">
                <div class="dropdown dropdown-primary">
                    <button type="button" class="btn btn-icon btn-soft-light dropdown-toggle p-0" id="notification-button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="ti ti-bell"></i>
                    </button>
                    @if (isset($unreadNotificationsCount) && $unreadNotificationsCount > 0)
                        <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle">
                            <span class="visually-hidden">Notifikasi Baru</span>
                        </span>
                    @endif
                    <div class="dropdown-menu dd-menu shadow rounded border-0 mt-3 p-0" data-simplebar style="height: 320px; width: 290px;">
                        <div class="d-flex align-items-center justify-content-between p-3 border-bottom">
                            <h6 class="mb-0 text-dark">Notifikasi</h6>
                            @if (isset($unreadNotificationsCount) && $unreadNotificationsCount > 0)
                                <span class="badge bg-soft-danger rounded-pill">{{ $unreadNotificationsCount }}</span>
                            @endif
                        </div>
                        <div class="p-3">
                            @if (isset($unreadNotifications) && $unreadNotifications->count() > 0)
                                @foreach ($unreadNotifications as $notification)
                                    <a href="{{ $notification->data['url'] }}" class="dropdown-item features feature-primary key-feature p-0 py-2 border-bottom">
                                        <div class="d-flex align-items-center">
                                            <div class="flex-1">
                                                <h6 class="mb-0 text-dark title">{{ $notification->data['title'] }}</h6>
                                                <small class="text-muted text-wrap">
                                                    {{ $notification->data['message'] }} <br>
                                                    {{ $notification->created_at->diffForHumans() }}
                                                </small>
                                            </div>
                                        </div>
                                    </a>
                                @endforeach
                            @else
                                <div class="text-center">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-1">
                                            <small class="text-muted">Tidak ada notifikasi baru.</small>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </li>
            <li class="list-inline-item mb-0 ms-1">
                <div class="dropdown dropdown-primary">
                    <button type="button" class="btn btn-soft-light dropdown-toggle p-0" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><img
                            src="{{ Auth::user()->profile->foto ? asset('storage/' . Auth::user()->profile->foto) : asset('assets/images/user/user-none.png') }}" class="avatar avatar-ex-small rounded"
                            alt=""></button>
                    <div class="dropdown-menu dd-menu dropdown-menu-end shadow border-0 mt-3 py-3" style="min-width: 200px;">
                        <a class="dropdown-item d-flex align-items-center text-dark pb-3" href="profile.html">
                            <img src="{{ Auth::user()->profile->foto ? asset('storage/' . Auth::user()->profile->foto) : asset('assets/images/user/user-none.png') }}"
                                class="avatar avatar-md-sm rounded-circle border shadow" alt="">
                            <div class="flex-1 ms-2">
                                <span class="d-block">{{ Auth::user()->name }}</span>
                                <small class="text-muted">{{ Auth::user()->email }}</small>
                            </div>
                        </a>
                        <a class="dropdown-item text-dark" href="{{ route('profile.index') }}">
                            <span class="mb-0 d-inline-block me-1">
                                <i class="ti ti-user"></i>
                            </span>
                            Profil
                        </a>
                        <a onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="dropdown-item text-danger" href="javascript:void(0)">
                            <span class="mb-0 d-inline-block me-1">
                                <i class="ti ti-arrow-right"></i>
                            </span>
                            Keluar
                        </a>
                    </div>
                </div>
            </li>
        </ul>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const notificationButton = document.getElementById('notification-button');
        if (notificationButton) {
            notificationButton.addEventListener('click', function() {
                const notificationBadge = document.querySelector('.bg-danger.rounded-circle');
                if (!notificationBadge) {
                    return; // Jangan lakukan apa-apa jika tidak ada notifikasi baru
                }

                // Mark notifications as read on the server
                fetch("{{ route('notifications.markAsRead') }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                }).then(response => {
                    if (response.ok) {
                        // Hapus indikator titik merah setelah notifikasi ditandai terbaca
                        setTimeout(() => notificationBadge.remove(), 1000);
                    }
                });
            });
        }
    });
</script>
<!-- Top Header -->
