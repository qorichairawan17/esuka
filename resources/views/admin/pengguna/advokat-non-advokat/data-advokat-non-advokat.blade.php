@extends('admin.layout.body')
@section('title', $title)
@section('content')
    <!-- Start Page Content -->
    <main class="page-content bg-light">

        @include('admin.component.top-header')

        <div class="container-fluid">
            <div class="layout-specing">

                @include('admin.component.breadcumb')

                <div class="mt-4">
                    <div class="card shadow">
                        <div class="card-header d-flex flex-wrap align-items-center justify-content-between bg-soft-primary">
                            <h6 class="card-title mb-0 text-dark">Data Advokat/Non Advokat</h6>
                            <a href="{{ route('advokat.form', ['param' => 'add']) }}" class="btn btn-primary btn-sm">Tambah</a>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                {!! $dataTable->table(['class' => 'table table-bordered table-hover', 'style' => 'width:100%;font-size:14px;']) !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div><!--end container-->

        @include('admin.layout.content-footer')
        <!-- End -->
    </main>
    <!--End page-content" -->
@endsection
@push('scripts')
    {!! $dataTable->scripts(attributes: ['type' => 'module']) !!}
    <script type="text/javascript">
        window.deleteData = async function(url) {
            const result = await Swal.fire({
                title: 'Apakah Kamu yakin?',
                text: "Data yang akan dihapus tidak dapat dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            });

            if (result.isConfirmed) {
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                try {
                    const response = await fetch(url, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Content-Type': 'application/json'
                        }
                    });
                    const data = await response.json();
                    if (data.success) {
                        await Swal.fire('Berhasil!', data.message, 'success');
                        window.LaravelDataTables['advokat-table'].ajax.reload();
                    } else {
                        Swal.fire('Gagal!', data.message || 'Terjadi kesalahan saat menghapus data.', 'error');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    Swal.fire('Error!', 'Terjadi kesalahan.', 'error');
                }
            }
        }
    </script>
@endpush
