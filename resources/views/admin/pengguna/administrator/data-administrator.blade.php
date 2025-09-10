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
                        <div class="card-header d-flex flex-wrap align-items-center justify-content-between">
                            <h6 class="card-title mb-0">Data Administrator</h6>
                            <a href="{{ route('administrator.form', ['param' => 'add']) }}" class="btn btn-primary btn-sm">Tambah</a>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive" style="border-color: #f1f1f1;">
                                {!! $dataTable->table(['class' => 'table table-bordered', 'style' => 'width:100%;font-size:15px;']) !!}
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
                title: 'Apakah Anda yakin?',
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
                        window.LaravelDataTables['administrator-table'].ajax.reload();
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
