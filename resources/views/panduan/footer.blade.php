<!-- javascript -->
<script src="{{ asset('admin/assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('admin/assets/libs/feather-icons/feather.min.js') }}"></script>
<script src="{{ asset('admin/assets/libs/simplebar/simplebar.min.js') }}"></script>
<!-- Main Js -->
<script src="{{ asset('admin/assets/js/plugins.init.js') }}"></script>
<script src="{{ asset('admin/assets/js/app.js') }}"></script>
<script src="{{ asset('assets/js/jquery-3.7.0.min.js') }}"></script>
<script src="{{ asset('assets/libs/jquery-ui-1.14.1/external/jquery/jquery.js') }}"></script>
<script src="{{ asset('assets/libs/jquery-ui-1.14.1/jquery-ui.js') }}"></script>
<script src="{{ asset('assets/libs/sweetalert2/dist/sweetalert2.all.min.js') }}"></script>
<script src="{{ asset('admin/assets/plugins/DataTables/datatables.min.js') }}"></script>
@foreach (['success', 'error', 'warning'] as $msg)
    @if (session()->has($msg))
        <script>
            Swal.fire({
                position: 'center',
                icon: '{{ $msg }}',
                title: '{{ $msg == 'success' ? 'Notifikasi' : ($msg == 'error' ? 'Oops...' : 'Informasi...') }}',
                text: '{{ session()->get($msg) }}',
                customClass: {
                    popup: 'rounded-4',
                    confirmButton: 'btn btn-soft-primary px-4'
                },
                buttonsStyling: false
            })
        </script>
    @endif
@endforeach
@stack('scripts')
<script>
    // Hide preloader when page is loaded
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(function() {
            const preloader = document.getElementById('preloader');
            if (preloader) {
                preloader.style.opacity = '0';
                preloader.style.transition = 'opacity 0.5s ease';
                setTimeout(function() {
                    preloader.style.display = 'none';
                }, 500);
            }
        }, 500);
    });

    // Search Topics Function
    function searchTopics() {
        const input = document.getElementById('s');
        if (!input) return;

        const filter = input.value.toLowerCase().trim();
        const menu = document.getElementById('panduan-menu');
        if (!menu) return;

        // Get all menu items
        const allLinks = menu.querySelectorAll('.sidebar a');
        const dropdowns = menu.querySelectorAll('.sidebar-dropdown');

        // If search is empty, show everything
        if (filter === '') {
            allLinks.forEach(link => {
                link.closest('li').style.display = '';
            });
            dropdowns.forEach(dropdown => {
                dropdown.style.display = '';
            });
            return;
        }

        // Search through dropdowns and their submenus
        dropdowns.forEach(dropdown => {
            const mainLink = dropdown.querySelector(':scope > a');
            const submenuItems = dropdown.querySelectorAll('.sidebar-submenu li');
            let hasVisibleChild = false;

            submenuItems.forEach(item => {
                const link = item.querySelector('a');
                if (link) {
                    const text = link.textContent.toLowerCase();
                    if (text.includes(filter)) {
                        item.style.display = '';
                        hasVisibleChild = true;
                    } else {
                        item.style.display = 'none';
                    }
                }
            });

            // Check if main category matches
            const mainText = mainLink ? mainLink.textContent.toLowerCase() : '';
            if (mainText.includes(filter)) {
                hasVisibleChild = true;
                submenuItems.forEach(item => item.style.display = '');
            }

            dropdown.style.display = hasVisibleChild ? '' : 'none';
        });

        // Search through standalone menu items
        const standaloneItems = menu.querySelectorAll(':scope > li.sidebar');
        standaloneItems.forEach(item => {
            const link = item.querySelector('a');
            if (link) {
                const text = link.textContent.toLowerCase();
                item.style.display = text.includes(filter) ? '' : 'none';
            }
        });
    }

    // Add smooth scrolling
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
</script>
</body>

</html>
