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
                        <div class="card-header bg-soft-primary">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="card-title mb-0 text-dark">Laporan Pendaftaran Surat Kuasa</h6>
                                <div class="d-flex gap-2">
                                    <div class="widget-filter">
                                        <select class="form-select form-select-sm" id="tahunFilter" aria-label="Filter Tahun">
                                            <option value="">Semua Tahun</option>
                                            @php
                                                $currentYear = date('Y');
                                                $startYear = 2020; // Tahun awal data
                                            @endphp
                                            @for ($year = $currentYear; $year >= $startYear; $year--)
                                                <option value="{{ $year }}">{{ $year }}</option>
                                            @endfor
                                        </select>
                                    </div>
                                    <div class="widget-filter">
                                        <select class="form-select form-select-sm" id="statusFilter" aria-label="Filter Status">
                                            <option value="">Semua Status</option>
                                            <option value="{{ \App\Enum\StatusSuratKuasaEnum::Disetujui->value }}">Disetujui</option>
                                            <option value="{{ \App\Enum\StatusSuratKuasaEnum::Ditolak->value }}">Ditolak</option>
                                        </select>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-primary" id="btnPrintPdf" title="Cetak PDF">
                                        <i class="uil uil-print"></i> Cetak PDF
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                {{ $dataTable->table(['class' => 'table table-bordered table-hover', 'style' => 'font-size:14px;']) }}
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
    {{ $dataTable->scripts(attributes: ['type' => 'module']) }}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const statusFilter = document.getElementById('statusFilter');
            const tahunFilter = document.getElementById('tahunFilter');
            const btnPrintPdf = document.getElementById('btnPrintPdf');

            // Menambahkan event listener ke filter status
            statusFilter.addEventListener('change', function() {
                // Reload datatable ketika filter berubah
                window.LaravelDataTables['laporansuratkuasa-table'].ajax.reload();
            });

            // Menambahkan event listener ke filter tahun
            tahunFilter.addEventListener('change', function() {
                // Reload datatable ketika filter berubah
                window.LaravelDataTables['laporansuratkuasa-table'].ajax.reload();
            });

            // Event listener untuk tombol print PDF
            btnPrintPdf.addEventListener('click', function() {
                const status = statusFilter.value;
                const tahun = tahunFilter.value;

                // Build URL with query parameters
                let url = '{{ route('surat-kuasa.laporan.export-pdf') }}';
                const params = new URLSearchParams();

                if (status) {
                    params.append('status', status);
                }
                if (tahun) {
                    params.append('tahun', tahun);
                }

                // Add params to URL if any
                if (params.toString()) {
                    url += '?' + params.toString();
                }

                // Open URL in new window to download PDF
                window.open(url, '_blank');
            });
        });
    </script>
@endpush
