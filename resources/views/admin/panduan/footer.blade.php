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
@if (session()->has('success'))
    <script>
        Swal.fire({
            position: 'center',
            icon: 'success',
            title: 'Notifikasi',
            text: '{{ session()->get('success') }}',
        })
    </script>
@elseif (session()->has('error'))
    <script>
        Swal.fire({
            position: 'center',
            icon: 'error',
            title: 'Oops...',
            text: '{{ session()->get('error') }}',
        })
    </script>
@elseif (session()->has('warning'))
    <script>
        Swal.fire({
            position: 'center',
            icon: 'warning',
            title: 'Informasi...',
            text: '{{ session()->get('warning') }}',
        })
    </script>
@endif
@stack('scripts')
<script>
    function searchTopics() {
        const input = document.getElementById('s');
        const filter = input.value.toLowerCase();

        const menu = document.getElementById('panduan-menu');
        const listItems = menu.getElementsByTagName('li');

        for (let i = 0; i < listItems.length; i++) {
            const a = listItems[i].getElementsByTagName('a')[0];
            if (a) {
                const txtValue = a.textContent || a.innerText;
                if (txtValue.toLowerCase().indexOf(filter) > -1) {
                    listItems[i].style.display = "";
                } else {
                    listItems[i].style.display = "none";
                }
            }
        }

        const dropdowns = menu.getElementsByClassName('sidebar-dropdown');
        for (let i = 0; i < dropdowns.length; i++) {
            const submenuItems = dropdowns[i].querySelectorAll('.sidebar-submenu li');
            let hasVisibleChild = false;
            submenuItems.forEach(item => {
                if (item.style.display !== 'none') {
                    hasVisibleChild = true;
                }
            });

            dropdowns[i].style.display = hasVisibleChild ? "" : (filter ? "none" : "");
        }
    }
</script>
</body>

</html>
