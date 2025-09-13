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
                            <h6 class="card-title mb-0">Audit Trail</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                {{ $dataTable->table(['class' => 'table table-bordered', 'style' => 'width:100%;font-size:15px;']) }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div><!--end container-->

        <!-- Modal Detail -->
        <div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="detailModalLabel">Detail Audit Trail</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <table class="table table-borderless">
                            <tbody>
                                <tr>
                                    <td style="width: 150px;"><strong>Pengguna</strong></td>
                                    <td style="width: 10px;">:</td>
                                    <td id="detail-user"></td>
                                </tr>
                                <tr>
                                    <td><strong>Aksi</strong></td>
                                    <td>:</td>
                                    <td id="detail-payload"></td>
                                </tr>
                                <tr>
                                    <td><strong>Tanggal</strong></td>
                                    <td>:</td>
                                    <td id="detail-created_at"></td>
                                </tr>
                                <tr>
                                    <td><strong>IP Address</strong></td>
                                    <td>:</td>
                                    <td id="detail-ip_address"></td>
                                </tr>
                                {{-- Hanya tampilkan URL dan Method jika user bukan 'Pengguna' biasa --}}
                                @if (auth()->user()->role !== \App\Enum\RoleEnum::User->value)
                                    <tr id="row-url">
                                        <td><strong>URL</strong></td>
                                        <td>:</td>
                                        <td id="detail-url"></td>
                                    </tr>
                                    <tr id="row-method">
                                        <td><strong>Method</strong></td>
                                        <td>:</td>
                                        <td id="detail-method"></td>
                                    </tr>
                                @endif
                                <tr>
                                    <td class="align-top"><strong>User Agent</strong></td>
                                    <td class="align-top">:</td>
                                    <td id="detail-user_agent" style="word-break: break-all;"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>

        @include('admin.layout.content-footer')
        <!-- End -->
    </main>
    <!--End page-content" -->
@endsection

@push('scripts')
    {{ $dataTable->scripts(attributes: ['type' => 'module']) }}

    <script type="text/javascript">
        function showDetail(id) {
            const detailModal = new bootstrap.Modal(document.getElementById('detailModal'));

            fetch(`{{ route('audit-trail.index') }}/${id}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Data tidak ditemukan');
                    }
                    return response.json();
                })
                .then(data => {
                    document.getElementById('detail-user').innerText = data.user ? data.user.name : 'Sistem/Tidak Diketahui';
                    document.getElementById('detail-payload').innerText = data.payload || '-';
                    document.getElementById('detail-created_at').innerText = new Date(data.created_at).toLocaleString('id-ID');
                    document.getElementById('detail-ip_address').innerText = data.ip_address || '-';
                    document.getElementById('detail-user_agent').innerText = data.user_agent || '-';

                    @if (auth()->user()->role !== \App\Enum\RoleEnum::User->value)
                        const detailUrlElement = document.getElementById('detail-url');
                        const detailMethodElement = document.getElementById('detail-method');
                        if (detailUrlElement && detailMethodElement) {
                            detailUrlElement.innerText = data.url || '-';
                            detailMethodElement.innerText = data.method || '-';
                        }
                    @endif
                    detailModal.show();
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal Memuat Data',
                        text: 'Terjadi kesalahan saat mengambil detail log. Silakan coba lagi nanti.',
                    });
                });
        }
    </script>
@endpush
