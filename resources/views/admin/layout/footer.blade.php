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
</body>

</html>
