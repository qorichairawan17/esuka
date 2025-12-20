@extends('admin.layout.body')
@section('title', $title)
@section('content')
    <!-- Start Page Content -->
    <main class="page-content bg-light">

        @include('admin.component.top-header')

        <div class="container-fluid">
            <div class="layout-specing">

                @include('admin.component.breadcumb')

                <h5 class="text-primary">Hallo, {{ Auth::user()->name }}</h5>

                <div class="row mt-3">
                    <div class="col-sm-12 col-md-12 col-lg-4">
                        <div class="card features feature-primary rounded shadow mb-3">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="icon text-center rounded-pill">
                                        <i class="uil uil-users-alt fs-4 mb-0"></i>
                                    </div>
                                    <div class="flex-1 ms-3">
                                        <h6 class="mb-0 text-muted">Pengguna</h6>
                                        <p class="fs-5 text-dark fw-bold mb-0">{{ $userTotal }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-12 col-lg-4">
                        <div class="card features feature-primary rounded shadow mb-3">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="icon text-center rounded-pill">
                                        <i class="uil uil-file fs-4 mb-0"></i>
                                    </div>
                                    <div class="flex-1 ms-3">
                                        <h6 class="mb-0 text-muted">Surat Kuasa</h6>
                                        <p class="fs-5 text-dark fw-bold mb-0">{{ $suratKuasaTotal }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-12 col-lg-4">
                        <div class="card features feature-primary rounded shadow mb-3">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="icon text-center rounded-pill">
                                        <i class="uil uil-file fs-4 mb-0"></i>
                                    </div>
                                    <div class="flex-1 ms-3">
                                        <h6 class="mb-0 text-muted">Testimoni</h6>
                                        <p class="fs-5 text-dark fw-bold mb-0">{{ $testimoniTotal }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 mt-2">
                    <div class="d-flex justify-content-between p-2 shadow rounded-top bg-soft-primary">
                        <h6 class="fw-bold mb-0 text-dark">Pendaftaran Surat Kuasa</h6>
                        <a href="{{ route('surat-kuasa.index') }}" class="text-primary">Lihat <i class="uil uil-arrow-right align-middle"></i></a>
                    </div>
                    <div class="table-responsive shadow rounded-bottom simplebar-scrollable-x simplebar-scrollable-y" data-simplebar="init" style="height: 250px;">
                        <div class="simplebar-wrapper" style="margin: 0px;">
                            <div class="simplebar-height-auto-observer-wrapper">
                                <div class="simplebar-height-auto-observer"></div>
                            </div>
                            <div class="simplebar-mask">
                                <div class="simplebar-offset" style="right: 0px; bottom: 0px;">
                                    <div class="simplebar-content-wrapper" tabindex="0" role="region" aria-label="scrollable content" style="height: 100%; overflow: scroll;">
                                        <div class="simplebar-content" style="padding: 0px;">
                                            <table class="table table-center bg-white mb-0" style="font-size: 14px;">
                                                <thead>
                                                    <tr>
                                                        <th class="border-bottom p-3">#</th>
                                                        <th class="border-bottom p-3">ID Daftar</th>
                                                        <th class="border-bottom p-3">Tanggal</th>
                                                        <th class="border-bottom p-3" style="min-width: 220px;">Pemohon</th>
                                                        <th class="border-bottom p-3">Jenis</th>
                                                        <th class="border-bottom p-3">Tahapan</th>
                                                        <th class="border-bottom p-3" style="min-width: 100px;">Lihat</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @php
                                                        $no = 1;
                                                    @endphp
                                                    @foreach ($verifikasiSuratKuasa as $suratKuasa)
                                                        <tr>
                                                            <th class="p-3">{{ $no }}</th>
                                                            <th class="p-3">{{ $suratKuasa->id_daftar }}</th>
                                                            <td class="p-3">{{ \Carbon\Carbon::parse($suratKuasa->tanggal_daftar)->isoFormat('dddd, D MMMM Y') }}</td>
                                                            <td class="p-3">
                                                                {{ $suratKuasa->pemohon }}
                                                            </td>
                                                            <td class="p-3">
                                                                {{ $suratKuasa->jenis_surat }}
                                                            </td>
                                                            <td class="p-3">
                                                                {{ $suratKuasa->tahapan }}
                                                            </td>
                                                            <td class="p-3">
                                                                <a href="{{ route('surat-kuasa.detail', ['id' => Crypt::encrypt($suratKuasa->id)]) }}" class="btn btn-sm btn-soft-primary">
                                                                    <i class="ti ti-eye"></i>
                                                                </a>
                                                            </td>
                                                        </tr>
                                                        @php
                                                            $no++;
                                                        @endphp
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="simplebar-placeholder" style="width: 658px; height: 747px;"></div>
                        </div>
                        <div class="simplebar-track simplebar-horizontal" style="visibility: visible;">
                            <div class="simplebar-scrollbar" style="width: 577px; transform: translate3d(0px, 0px, 0px); display: block;"></div>
                        </div>
                        <div class="simplebar-track simplebar-vertical" style="visibility: visible;">
                            <div class="simplebar-scrollbar" style="height: 442px; transform: translate3d(0px, 0px, 0px); display: block;"></div>
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-lg-8 col-sm-12">
                        {{-- Grafik Chart --}}
                        <div class="card border-0 rounded shadow p-4 mb-3">
                            <h6 class="mb-0 mb-3 text-center">Grafik Pendaftaran Surat Kuasa</h6>
                            <div>
                                <canvas id="myChart"></canvas>
                            </div>
                        </div>

                        {{-- Audit Trail Terbaru --}}
                        <div class="card shadow">
                            <div class="card-body">
                                <h6>Audit Trail</h6>
                                <p class="text-muted m-0" style="text-align:justify;">
                                    {{ $lastAuditTrail->payload }}
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-sm-12">
                        <div class="d-flex flex-column mb-3">
                            <div class="card features feature-success rounded shadow">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="icon text-center rounded-pill">
                                            <i class="uil uil-file fs-4 mb-0"></i>
                                        </div>
                                        <div class="flex-1 ms-3">
                                            <h6 class="mb-0 text-muted">Surat Kuasa Disetujui</h6>
                                            <p class="fs-5 text-success fw-bold mb-0">
                                                {{ $statusSuratKuasa['disetujui'] }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex flex-column mb-3">
                            <div class="card features feature-danger rounded shadow">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="icon text-center rounded-pill">
                                            <i class="uil uil-file fs-4 mb-0"></i>
                                        </div>
                                        <div class="flex-1 ms-3">
                                            <h6 class="mb-0 text-muted">Surat Kuasa Ditolak</h6>
                                            <p class="fs-5 text-danger fw-bold mb-0">
                                                {{ $statusSuratKuasa['ditolak'] }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex flex-column mb-3">
                            <div class="card features feature-warning rounded shadow">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="icon text-center rounded-pill">
                                            <i class="uil uil-file fs-4 mb-0"></i>
                                        </div>
                                        <div class="flex-1 ms-3">
                                            <h6 class="mb-0 text-muted">Surat Kuasa Belum Bayar</h6>
                                            <p class="fs-5 text-warning fw-bold mb-0">
                                                {{ $tahapanSuratKuasa['pendaftaran'] }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex flex-column mb-3">
                            <div class="card features feature-primary rounded shadow">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="icon text-center rounded-pill">
                                            <i class="uil uil-file fs-4 mb-0"></i>
                                        </div>
                                        <div class="flex-1 ms-3">
                                            <h6 class="mb-0 text-muted">Surat Kuasa Sudah Bayar</h6>
                                            <p class="fs-5 text-primary fw-bold mb-0">
                                                {{ $tahapanSuratKuasa['pembayaran'] }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
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
    <script src="{{ asset('admin/assets/plugins/chartjs/dist/chart.umd.js') }}"></script>
    <script>
        const ctx = document.getElementById('myChart');
        ctx.style.width = '100%';

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September',
                    'Oktober', 'November', 'Desember'
                ],
                datasets: [{
                    label: 'Surat Kuasa Disetujui ({{ date('Y') }})',
                    data: @json($chartData),
                    borderWidth: 1
                }]
            },
            options: {
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
@endsection
