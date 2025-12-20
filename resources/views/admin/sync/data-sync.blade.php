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
                            <h6 class="card-title mb-0 text-dark">Staging Synchronize Surat Kuasa</h6>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-lg-6 col-md-6 col-sm-12">
                                    <label for="sinkronisasi">Sinkronisasi Staging Database Surat Kuasa Lama</label>
                                    <select class="form-select form-select-sm w-100" id="sinkronisasi" aria-label="Sinkronisasi">
                                        <option value="">Pilih Sinkronisasi</option>
                                        <option value="{{ \App\Enum\SuratKuasaEnum::Advokat->value }}">Sinkronisasi Surat Kuasa Advokat</option>
                                        <option value="{{ \App\Enum\SuratKuasaEnum::NonAdvokat->value }}">Sinkronisasi Surat Kuasa Non Advokat</option>
                                    </select>
                                    <button type="button" class="btn btn-sm btn-soft-danger mt-2" id="deleteSinkronisasi">Hapus Data Sinkronisasi</button>
                                    <button type="button" class="btn btn-sm btn-success mt-2" id="migrasi">Migrasi Data Surat Kuasa</button>
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-12">
                                    <label for="klasifikasiFilter">Filter Pencarian</label>
                                    <select class="form-select form-select-sm w-100" id="klasifikasiFilter" aria-label="Filter Klasifikasi">
                                        <option value="">Semua Klasifikasi</option>
                                        <option value="{{ \App\Enum\SuratKuasaEnum::Advokat->value }}">Advokat</option>
                                        <option value="{{ \App\Enum\SuratKuasaEnum::NonAdvokat->value }}">Non Advokat</option>
                                    </select>
                                </div>
                            </div>

                            <div class="progress mt-2 mb-2" id="sync-progress-bar" style="display: none; height: 1.5rem;">
                                <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%">
                                    <small style="font-size:15px;">Sinkronisasi sedang berjalan, mohon tunggu...</small>
                                </div>
                            </div>

                            <div class="table-responsive">
                                {!! $dataTable->table(['class' => 'table table-bordered', 'style' => 'font-size:14px;']) !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div><!--end container-->

        @include('admin.layout.content-footer')
        <!-- End -->

        <!-- Detail Modal -->
        <div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="detailModalLabel">Detail Data Staging</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div id="detail-content">
                            {{-- Data akan dimuat di sini oleh JavaScript --}}
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>

    </main>
    <!--End page-content" -->
@endsection
@push('scripts')
    {!! $dataTable->scripts(attributes: ['type' => 'module']) !!}

    <script type="module">
        $(document).ready(function() {
            const table = window.LaravelDataTables['stagingsuratkuasa-table'];
            const progressBar = $('#sync-progress-bar');

            // 1. Handle Sinkronisasi
            $('#sinkronisasi').on('change', function() {
                const klasifikasi = $(this).val();
                if (!klasifikasi) return;

                Swal.fire({
                    title: 'Konfirmasi Sinkronisasi',
                    text: `Kamu yakin ingin memulai sinkronisasi untuk data ${klasifikasi}? Proses ini mungkin memakan waktu.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, Lanjutkan!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        progressBar.show();
                        $('#sinkronisasi, #deleteSinkronisasi, #migrasi').prop('disabled', true);

                        $.ajax({
                            url: '{{ route('sync.fetch-data') }}',
                            type: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}',
                                klasifikasi: klasifikasi
                            },
                            success: function(response) {
                                Swal.fire('Berhasil!', response.message, 'success');
                                table.ajax.reload();
                            },
                            error: function(xhr) {
                                const errorMsg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Terjadi kesalahan saat sinkronisasi.';
                                Swal.fire('Gagal!', errorMsg, 'error');
                            },
                            complete: function() {
                                progressBar.hide();
                                $('#sinkronisasi, #deleteSinkronisasi, #migrasi').prop('disabled', false);
                                $('#sinkronisasi').val('');
                            }
                        });
                    } else {
                        $(this).val('');
                    }
                });
            });

            // 2. Handle Hapus Data Sinkronisasi
            $('#deleteSinkronisasi').on('click', function() {
                Swal.fire({
                    title: 'Kamu Yakin?',
                    text: "Tindakan ini akan menghapus semua data dari tabel staging sinkronisasi. Data tidak dapat dipulihkan.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, Hapus Semua!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        progressBar.show();
                        $('#sinkronisasi, #deleteSinkronisasi, #migrasi').prop('disabled', true);

                        $.ajax({
                            url: '{{ route('sync.delete-data') }}',
                            type: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                Swal.fire('Berhasil!', response.message, 'success');
                                table.ajax.reload();
                            },
                            error: function(xhr) {
                                const errorMsg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Terjadi kesalahan saat menghapus data.';
                                Swal.fire('Gagal!', errorMsg, 'error');
                            },
                            complete: function() {
                                progressBar.hide();
                                $('#sinkronisasi, #deleteSinkronisasi, #migrasi').prop('disabled', false);
                            }
                        });
                    }
                });
            });

            // 3. Handle Migrasi Data Surat Kuasa
            $('#migrasi').on('click', function() {
                Swal.fire({
                    title: 'Konfirmasi Migrasi Data',
                    text: "Kamu yakin ingin memigrasi semua data dari tabel staging ke tabel utama? Proses ini mungkin memakan waktu dan tidak dapat dibatalkan.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, Migrasi Sekarang!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        progressBar.find('small').text('Migrasi sedang berjalan, mohon tunggu...');
                        progressBar.show();
                        $('#sinkronisasi, #deleteSinkronisasi, #migrasi').prop('disabled', true);

                        $.ajax({
                            url: '{{ route('sync.migrate') }}', // Memanggil route migrasi yang baru
                            type: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                Swal.fire('Berhasil!', response.message, 'success');
                                table.ajax.reload(); // Muat ulang tabel staging setelah migrasi
                            },
                            error: function(xhr) {
                                const errorMsg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Terjadi kesalahan saat migrasi data.';
                                Swal.fire('Gagal!', errorMsg, 'error');
                            },
                            complete: function() {
                                progressBar.hide();
                                $('#sinkronisasi, #deleteSinkronisasi, #migrasi').prop('disabled', false);
                            }
                        });
                    }
                });
            });

            // 2. Handle Filter
            $('#klasifikasiFilter').on('change', function() {
                const filterValue = $(this).val();
                table.column('klasifikasi:name').search(filterValue).draw();
            });

        });

        // 3. Handle Detail Modal
        window.showDetail = function(id) {
            const url = `{{ route('sync.show', ':id') }}`.replace(':id', id);
            const detailModal = new bootstrap.Modal(document.getElementById('detailModal'));

            fetch(url)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Data tidak ditemukan atau terjadi kesalahan server.');
                    }
                    return response.json();
                })
                .then(data => {
                    // Fungsi untuk mencoba parsing JSON
                    const tryParseJSON = (jsonString) => {
                        if (!jsonString || typeof jsonString !== 'string' || !jsonString.startsWith('[')) {
                            return null;
                        }
                        try {
                            const arr = JSON.parse(jsonString);
                            // Handle kasus di mana GROUP_CONCAT menghasilkan [null]
                            if (Array.isArray(arr) && arr.length === 1 && arr[0] === null) {
                                return [];
                            }
                            return arr;
                        } catch (e) {
                            return null;
                        }
                    };

                    // Menggabungkan data pihak menjadi array of objects
                    const pemberiList = (tryParseJSON(data.nama_pemberi) || []).map((nama, index) => ({
                        nama: nama,
                        nik: (tryParseJSON(data.nik_pemberi) || [])[index] || '-',
                        pekerjaan: (tryParseJSON(data.pekerjaan_pemberi) || [])[index] || '-',
                        alamat: (tryParseJSON(data.alamat_pemberi) || [])[index] || '-',
                    }));

                    const penerimaList = (tryParseJSON(data.nama_penerima) || []).map((nama, index) => ({
                        nama: nama,
                        nik: (tryParseJSON(data.nik_penerima) || [])[index] || '-',
                        pekerjaan: (tryParseJSON(data.pekerjaan_penerima) || [])[index] || '-',
                        alamat: (tryParseJSON(data.alamat_penerima) || [])[index] || '-',
                    }));

                    // Kunci-kunci yang berhubungan dengan pihak, untuk dilewati di loop utama
                    const partyKeys = new Set(['id_pemberi', 'nik_pemberi', 'nama_pemberi', 'pekerjaan_pemberi', 'alamat_pemberi', 'id_penerima', 'nik_penerima', 'nama_penerima',
                        'pekerjaan_penerima', 'alamat_penerima'
                    ]);

                    let contentHtml = '<table class="table table-bordered table-sm table-striped">';
                    for (const [key, value] of Object.entries(data)) {
                        // Lewati kunci pihak karena akan ditangani secara khusus
                        if (partyKeys.has(key)) continue;

                        const formattedKey = key.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());

                        let displayValue = value;
                        // Format tanggal untuk created_at dan updated_at
                        if ((key === 'created_at' || key === 'updated_at') && value) {
                            // Mengubah '2025-09-24T16:42:35.000000Z' menjadi '2025-09-24 16:42:35'
                            displayValue = value.replace('T', ' ').substring(0, 19);
                        }
                        contentHtml += `<tr><td class="fw-bold" style="width: 30%;">${formattedKey}</td><td>${displayValue !== null ? displayValue : '-'}</td></tr>`;
                    }

                    // Fungsi untuk membuat tabel pihak
                    const createPartyTable = (title, partyList) => {
                        if (partyList.length === 0) return '';
                        let tableHtml = `
                            <tr>
                                <td class="fw-bold" colspan="2">${title}</td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <table class="table table-sm table-hover mb-0">
                                        <thead><tr><th>#</th><th>Nama</th><th>NIK</th><th>Pekerjaan</th><th>Alamat</th></tr></thead>
                                        <tbody>`;
                        partyList.forEach((pihak, index) => {
                            tableHtml += `<tr><td>${index + 1}</td><td>${pihak.nama}</td><td>${pihak.nik}</td><td>${pihak.pekerjaan}</td><td>${pihak.alamat}</td></tr>`;
                        });
                        tableHtml += `</tbody></table></td></tr>`;
                        return tableHtml;
                    };

                    contentHtml += createPartyTable('Pihak Pemberi', pemberiList);
                    contentHtml += createPartyTable('Pihak Penerima', penerimaList);

                    contentHtml += '</table>';
                    $('#detail-content').html(contentHtml);
                    detailModal.show();
                })
                .catch(error => {
                    console.error('Error fetching detail:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal Memuat Data',
                        text: error.message,
                    });
                });
        }
    </script>
@endpush
