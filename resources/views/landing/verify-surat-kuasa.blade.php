@extends('landing.index')
@section('title', $title)
@section('content')

    <section class="bg-half-170 d-table w-100" style="background: #2F55D4;">
        <div class="container">
            <div class="row mt-5 justify-content-center">
                <div class="col-12">
                    <div class="title-heading text-center">
                        <h1 class="fw-bold title-dark" style="color: #FFFFFF;">Verifikasi Surat Kuasa</h1>
                        <p class="para-desc mx-auto mb-0" style="color: rgba(255, 255, 255, 0.7);">
                            Detail keabsahan pendaftaran surat kuasa pada sistem aplikasi {{ config('app.name') }}.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="position-relative">
        <div class="shape overflow-hidden" style="color: #FFFFFF;">
            <svg viewBox="0 0 2880 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M0 48H1437.5H2880V0H2160C1442.5 52 720 0 720 0H0V48Z" fill="currentColor"></path>
            </svg>
        </div>
    </div>

    <section class="section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10 col-md-12">
                    <div class="card shadow rounded border-0">
                        <div class="card-body">
                            <div class="text-center mb-4">
                                <i class="uil uil-check-circle text-success" style="font-size: 5rem;"></i>
                                <h4 class="mt-2">Dokumen Terverifikasi</h4>
                                <p class="text-muted">
                                    Pendaftaran surat kuasa ini telah disetujui dan terdaftar secara sah.
                                </p>
                            </div>

                            <div class="row border-bottom pb-3 mb-3">
                                <div class="col-md-6">
                                    <div class="d-flex mb-4">
                                        <i class="uil uil-key-skeleton-alt text-primary h4 me-3"></i>
                                        <div class="flex-1">
                                            <h6 class="mb-0">ID Pendaftaran</h6>
                                            <p class="text-muted mb-0">{{ $suratKuasa->pendaftaran->id_daftar }}</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="d-flex mb-4">
                                        <i class="uil uil-file-bookmark text-primary h4 me-3"></i>
                                        <div class="flex-1">
                                            <h6 class="mb-0">Nomor Surat Kuasa</h6>
                                            <p class="text-muted mb-0 fw-bold">{{ $suratKuasa->nomor_surat_kuasa }}</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="d-flex mb-4">
                                        <i class="uil uil-calendar-alt text-primary h4 me-3"></i>
                                        <div class="flex-1">
                                            <h6 class="mb-0">Tanggal Register</h6>
                                            <p class="text-muted mb-0">{{ \Carbon\Carbon::parse($suratKuasa->tanggal_register)->isoFormat('dddd, D MMMM Y') }}</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="d-flex mb-4">
                                        <i class="uil uil-user-check text-primary h4 me-3"></i>
                                        <div class="flex-1">
                                            <h6 class="mb-0">Disahkan oleh Panitera</h6>
                                            <p class="text-muted mb-0">{{ $suratKuasa->panitera->nama ?? 'N/A' }}</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="d-flex mb-4">
                                        <i class="uil uil-user-plus text-primary h4 me-3"></i>
                                        <div class="flex-1">
                                            <h6 class="mb-0">Didaftarkan oleh</h6>
                                            <p class="text-muted mb-0">{{ $suratKuasa->pendaftaran->user->name ?? 'N/A' }}</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="d-flex mb-4">
                                        <i class="uil uil-user-check text-primary h4 me-3"></i>
                                        <div class="flex-1">
                                            <h6 class="mb-0">Diverifikasi oleh Petugas</h6>
                                            <p class="text-muted mb-0">{{ $suratKuasa->approval->name ?? 'N/A' }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12 mb-3">
                                    <h5>Detail Pendaftaran</h5>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex mb-4">
                                        <i class="uil uil-info-circle text-primary h4 me-3"></i>
                                        <div class="flex-1">
                                            <h6 class="mb-0">Perihal</h6>
                                            <p class="text-muted mb-0">{{ $suratKuasa->pendaftaran->perihal }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex mb-4">
                                        <i class="uil uil-file-alt text-primary h4 me-3"></i>
                                        <div class="flex-1">
                                            <h6 class="mb-0">Jenis Surat Kuasa</h6>
                                            <p class="text-muted mb-0">{{ $suratKuasa->pendaftaran->jenis_surat }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex mb-4">
                                        <i class="uil uil-tag-alt text-primary h4 me-3"></i>
                                        <div class="flex-1">
                                            <h6 class="mb-0">Klasifikasi</h6>
                                            <p class="text-muted mb-0">Surat Kuasa {{ $suratKuasa->pendaftaran->klasifikasi }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex mb-4">
                                        <i class="uil uil-calendar-alt text-primary h4 me-3"></i>
                                        <div class="flex-1">
                                            <h6 class="mb-0">Tanggal Didaftarkan</h6>
                                            <p class="text-muted mb-0">{{ \Carbon\Carbon::parse($suratKuasa->pendaftaran->tanggal_daftar)->isoFormat('dddd, D MMMM Y') }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 mt-2">
                                    <div class="d-flex mb-4">
                                        <i class="uil uil-users-alt text-primary h4 me-3"></i>
                                        <div class="flex-1">
                                            <h6 class="mb-0">Para Pihak</h6>
                                            <p class="text-muted mb-0"><b>Pemberi Kuasa:</b> {{ $suratKuasa->pendaftaran->pihak->where('jenis', 'Pemberi')->pluck('nama')->join(', ') ?: 'N/A' }}</p>
                                            <p class="text-muted mb-0"><b>Penerima Kuasa:</b> {{ $suratKuasa->pendaftaran->pihak->where('jenis', 'Penerima')->pluck('nama')->join(', ') ?: 'N/A' }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
