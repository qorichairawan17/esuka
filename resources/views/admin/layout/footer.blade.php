<script src="{{ asset('admin/assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('admin/assets/libs/feather-icons/feather.min.js') }}"></script>
<script src="{{ asset('admin/assets/libs/simplebar/simplebar.min.js') }}"></script>
<script src="{{ asset('admin/assets/js/plugins.init.js') }}"></script>
<script src="{{ asset('admin/assets/js/app.js') }}"></script>
<script src="{{ asset('assets/js/jquery-3.7.0.min.js') }}"></script>
<script src="{{ asset('assets/libs/jquery-ui-1.14.1/external/jquery/jquery.js') }}"></script>
<script src="{{ asset('assets/libs/jquery-ui-1.14.1/jquery-ui.js') }}"></script>
<script src="{{ asset('assets/libs/sweetalert2/dist/sweetalert2.all.min.js') }}"></script>
<script src="{{ asset('admin/assets/plugins/DataTables/datatables.min.js') }}"></script>
@php
    $type = collect(['success', 'error', 'warning'])->first(fn($k) => session()->has($k));
    $titles = ['success' => 'Notifikasi', 'error' => 'Oops...', 'warning' => 'Informasi...'];
@endphp

@if ($type)
    <script>
        Swal.fire({
            position: 'center',
            icon: @json($type),
            title: @json($titles[$type]),
            text: @json(session($type)),
        });
    </script>
@endif
@stack('scripts')
</body>

</html>
