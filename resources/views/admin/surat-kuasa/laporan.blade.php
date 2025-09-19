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
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="card-title mb-0">Laporan Pendaftaran Surat Kuasa</h6>
                                <div class="d-flex gap-2">
                                    <div class="widget-filter">
                                        <select class="form-select form-select-sm" id="statusFilter" aria-label="Filter Status">
                                            <option value="">Semua Status</option>
                                            <option value="{{ \App\Enum\StatusSuratKuasaEnum::Disetujui->value }}">Disetujui</option>
                                            <option value="{{ \App\Enum\StatusSuratKuasaEnum::Ditolak->value }}">Ditolak</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive" style="border-color: #f1f1f1;">
                                {{ $dataTable->table(['class' => 'table table-bordered table-hover', 'style' => 'font-size:15px;']) }}
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

            // Menambahkan event listener ke filter status
            statusFilter.addEventListener('change', function() {
                // Reload datatable ketika filter berubah
                window.LaravelDataTables['laporansuratkuasa-table'].ajax.reload();
            });
        });
    </script>
@endpush
