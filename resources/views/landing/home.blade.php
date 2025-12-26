@extends('landing.index')
@section('title', $title)
@section('content')
    <section class="bg-home pb-5 pb-sm-0 d-flex align-items-center bg-linear-gradient-primary">
        <div class="container">
            <div class="row mt-5 align-items-center">
                <div class="col-md-6">
                    <div class="title-heading me-lg-4 wow animate__animated animate__fadeInUp" data-wow-delay=".1s">
                        <h1 class="heading fw-bold mb-3">{{ config('app.name') }}<br>
                            <span class="text-primary" style="font-size: 2rem;">Mudah, Cepat, Biaya Ringan</span>
                        </h1>
                        <p class="para-desc text-muted" style="text-align:justify;">
                            Tidak perlu antri, Kamu dapat mendaftarkan surat
                            kuasa secara elektronik kapanpun dan dimanapun.
                            Proses mudah, hemat waktu, biaya ringan.
                        </p>
                        <a href="{{ route('app.signin') }}" class="btn btn-pills btn-primary">
                            Akses Sekarang <i data-feather="arrow-right"></i>
                        </a>
                        <p class="text-muted mb-0 mt-3">
                            Developed by <a href="https://pn-lubukpakam.go.id" target="_blank" class="text-primary">{{ $infoApp->pengadilan_negeri }}
                            </a>
                        </p>
                    </div>
                </div>

                <div class="col-md-6 mt-4 pt-2 mt-sm-0 pt-sm-0">
                    <div class="position-relative ms-lg-5">
                        <div class="bg-half-260 overflow-hidden rounded-md shadow-md jarallax" data-jarallax data-speed="0.5" style="background: url('{{ asset('assets/images/model.jpg') }}');">
                            <div class="py-lg-5 py-md-0 py-5"></div>
                        </div>

                        <div class="modern-saas-absolute-left wow animate__animated animate__fadeInUp" data-wow-delay=".3s">
                            <div class="card">
                                <div class="features feature-primary d-flex justify-content-between align-items-center rounded shadow p-3">
                                    <div class="d-flex align-items-center">
                                        <div class="icon bg-soft-primary text-center rounded-pill">
                                            <i class="uil uil-file fs-4 mb-0"></i>
                                        </div>
                                        <div class="flex-1 ms-3">
                                            <h6 class="mb-0 text-muted">Surat Kuasa</h6>
                                            <p class="fs-5 text-dark fw-bold mb-0">
                                                <span class="counter-value" data-target="{{ $totalSuratKuasa }}">{{ $totalSuratKuasa }}</span>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="modern-saas-absolute-right wow animate__animated animate__fadeInUp" data-wow-delay=".5s">
                            <div class="card rounded shadow">
                                <div class="p-3">
                                    <h5>Pengguna Terdaftar</h5>

                                    <div class="progress-box mt-2">
                                        <h6 class="title fw-normal text-muted">Terdaftar</h6>
                                        <div class="progress">
                                            <div class="progress-bar position-relative bg-primary" style="width:84%;">
                                                <div class="progress-value d-block text-muted h6 mt-1">{{ $totalUser }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="position-absolute top-0 start-0 translate-middle z-index-m-1">
                            <img src="{{ asset('assets/images/shapes/dots.svg') }}" class="avatar avatar-xl-large" alt="">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="pt-5 d-none d-lg-block">
        <div class="container ">
            <div class="row justify-content-center">
                <div class="col-lg-4 col-md-12 col-6 text-center py-4">
                    <h5 class="fw-bold wow animate__animated animate__fadeInUp" data-wow-delay=".1s" style="color:#8C98A4;">
                        Mahkamah Agung
                    </h5>
                </div>

                <div class="col-lg-4 col-md-12 col-6 text-center py-4">
                    <h5 class="fw-bold wow animate__animated animate__fadeInUp" data-wow-delay=".3s" style="color:#8C98A4;">
                        Direktorat Jenderal Badan Peradilan Umum
                    </h5>
                </div>

                <div class="col-lg-4 col-md-12 col-6 text-center py-4">
                    <h5 class="fw-bold wow animate__animated animate__fadeInUp" data-wow-delay=".5s" style="color:#8C98A4;">
                        {{ $infoApp->pengadilan_tinggi }}
                    </h5>
                </div>
            </div>
        </div>
    </section>

    <section class="section overflow-hidden">
        <div class="container pb-5 mb-md-5">
            <div class="row justify-content-center">
                <div class="col-12 text-center">
                    <div class="section-title mb-4 pb-2 wow animate__animated animate__fadeInUp" data-wow-delay=".1s">
                        <h4 class="title mb-0"><span class="fw-bold text-primary">{{ config('app.name') }}</span>
                        </h4>
                        <p class="text-muted para-desc mx-auto mb-0">
                            Layanan Pendaftaran Surat Kuasa Berbasis Online Dengan Aplikasi
                        </p>
                    </div>
                </div>
            </div>

            <div class="row align-items-center">
                <div class="col-lg-7 mt-4 pt-2">
                    <div class="saas-feature-shape-left position-relative me-lg-5 wow animate__animated animate__fadeInUp" data-wow-delay=".5s">
                        <img src="{{ asset('assets/images/dashboard.png') }}" class="img-fluid mx-auto d-block rounded shadow" alt="">
                    </div>
                </div>

                <div class="col-lg-5 mt-4 pt-2">
                    <div class="section-title wow animate__animated animate__fadeInUp" data-wow-delay=".7s">
                        <h4 class="title mb-4">Mudah, Cepat, Biaya Ringan</h4>
                        <p class="text-muted" style="text-align:justify;">
                            Tidak perlu antri untuk mendaftarkan surat kuasa pada {{ config('app.author') }}, kamu dapat
                            darimana saja mendaftarkan surat kuasa tanpa perlu datang Ke Pelayanan Terpadu Satu Pintu
                            (PTSP) {{ config('app.author') }}
                        </p>
                        <ul class="list-unstyled text-muted">
                            <li class="mb-1">
                                <span class="text-primary h5 me-2">
                                    <i class="uil uil-check-circle align-middle"></i>
                                </span>
                                Pendaftaran Surat Kuasa
                            </li>
                            <li class="mb-1">
                                <span class="text-primary h5 me-2">
                                    <i class="uil uil-check-circle align-middle"></i>
                                </span>
                                Pembayaran Dengan QRIS Dan Transfer Bank
                            </li>
                            <li class="mb-1">
                                <span class="text-primary h5 me-2">
                                    <i class="uil uil-check-circle align-middle"></i>
                                </span>
                                Verifikasi Pendaftaran Dan Pembayaran
                            </li>
                            <li class="mb-1">
                                <span class="text-primary h5 me-2">
                                    <i class="uil uil-check-circle align-middle"></i>
                                </span>
                                Cetak Bukti Barcode Pendaftaran
                            </li>
                        </ul>
                        <p class="text-muted" style="text-align:justify;">
                            Biaya Pendaftaran Per Surat Kuasa <span class="text-primary fw-bold">Rp10.000</span>
                            sesuai dengan Peraturan Pemerintah (PP)
                            Nomor 5 Tahun 2019 tentang Jenis dan Tarif atas Jenis Penerimaan
                            Negara Bukan Pajak yang Berlaku pada Mahkamah Agung dan Badan Peradilan yang Berada di
                            Bawahnya
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="container mt-100 mt-60">
            <div class="row wow animate__animated animate__fadeInUp">
                <div class="col-12 text-center">
                    <div class="section-title mb-4 pb-2">
                        <h4 class="title mb-4">Pejabat Pimpinan</h4>
                        <p class="text-muted para-desc mx-auto mb-0">
                            Profil Pejabat Pimpinan pada
                            <span class="fw-bold text-primary">{{ config('app.author') }}</span>
                        </p>
                    </div>
                </div>
            </div>

            <div class="row wow animate__animated animate__fadeInUp">
                <div class="col-lg-3 col-md-6 mt-4 pt-2">
                    <div class="card team team-primary text-center bg-transparent border-0">
                        <div class="card-body p-0">
                            <div class="position-relative d-flex justify-content-center">
                                <div class="pejabat-foto-wrapper" style="width: 180px; height: 180px; border-radius: 50%; overflow: hidden; box-shadow: 0 5px 15px rgba(0,0,0,0.15);">
                                    <img src="{{ $pejabatStruktural ? asset('storage/' . $pejabatStruktural->foto_ketua) : asset('assets/images/user/user-none.png') }}"
                                        style="width: 100%; height: 100%; object-fit: cover; object-position: center top;" alt="Foto Ketua">
                                </div>
                            </div>
                            <div class="content pt-3 pb-3">
                                <h5 class="mb-0">
                                    <a href="javascript:void(0)" class="name text-dark">
                                        {{ $pejabatStruktural->ketua ?? '' }}
                                    </a>
                                </h5>
                                <small class="designation text-muted">Ketua</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 mt-4 pt-2">
                    <div class="card team team-primary text-center bg-transparent border-0">
                        <div class="card-body p-0">
                            <div class="position-relative d-flex justify-content-center">
                                <div class="pejabat-foto-wrapper" style="width: 180px; height: 180px; border-radius: 50%; overflow: hidden; box-shadow: 0 5px 15px rgba(0,0,0,0.15);">
                                    <img src="{{ $pejabatStruktural ? asset('storage/' . $pejabatStruktural->foto_wakil_ketua) : asset('assets/images/user/user-none.png') }}"
                                        style="width: 100%; height: 100%; object-fit: cover; object-position: center top;" alt="Foto Wakil Ketua">
                                </div>
                            </div>
                            <div class="content pt-3 pb-3">
                                <h5 class="mb-0">
                                    <a href="javascript:void(0)" class="name text-dark">
                                        {{ $pejabatStruktural->wakil_ketua ?? '' }}
                                    </a>
                                </h5>
                                <small class="designation text-muted">Wakil Ketua</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 mt-4 pt-2">
                    <div class="card team team-primary text-center bg-transparent border-0">
                        <div class="card-body p-0">
                            <div class="position-relative d-flex justify-content-center">
                                <div class="pejabat-foto-wrapper" style="width: 180px; height: 180px; border-radius: 50%; overflow: hidden; box-shadow: 0 5px 15px rgba(0,0,0,0.15);">
                                    <img src="{{ $pejabatStruktural ? asset('storage/' . $pejabatStruktural->foto_panitera) : asset('assets/images/user/user-none.png') }}"
                                        style="width: 100%; height: 100%; object-fit: cover; object-position: center top;" alt="Foto Panitera">
                                </div>
                            </div>
                            <div class="content pt-3 pb-3">
                                <h5 class="mb-0">
                                    <a href="javascript:void(0)" class="name text-dark">
                                        {{ $pejabatStruktural->panitera ?? '' }}
                                    </a>
                                </h5>
                                <small class="designation text-muted">Panitera</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 mt-4 pt-2">
                    <div class="card team team-primary text-center bg-transparent border-0">
                        <div class="card-body p-0">
                            <div class="position-relative d-flex justify-content-center">
                                <div class="pejabat-foto-wrapper" style="width: 180px; height: 180px; border-radius: 50%; overflow: hidden; box-shadow: 0 5px 15px rgba(0,0,0,0.15);">
                                    <img src="{{ $pejabatStruktural ? asset('storage/' . $pejabatStruktural->foto_sekretaris) : asset('assets/images/user/user-none.png') }}"
                                        style="width: 100%; height: 100%; object-fit: cover; object-position: center top;" alt="Foto Sekretaris">
                                </div>
                            </div>
                            <div class="content pt-3 pb-3">
                                <h5 class="mb-0">
                                    <a href="javascript:void(0)" class="name text-dark">
                                        {{ $pejabatStruktural->sekretaris ?? '' }}
                                    </a>
                                </h5>
                                <small class="designation text-muted">Sekretaris</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if (!empty($testimoni) && count($testimoni) > 0)
            <div class="container mt-100 mt-60">
                <div class="row justify-content-center">
                    <div class="col-12">
                        <div class="section-title text-center mb-4 pb-2 wow animate__animated animate__fadeInUp" data-wow-delay=".1s">
                            <h4 class="title mb-4">Testimoni Pengguna</h4>
                            <p class="text-muted para-desc mb-0 mx-auto">
                                Apa pendapat mereka sebagai pengguna yang telah mendaftarkan surat kuasa secara elektronik
                                melalui Aplikasi <span class="text-primary fw-bold">{{ config('app.name') }}</span>
                            </p>
                        </div>
                    </div>
                </div>

                <div class="row justify-content-center">
                    <div class="col-lg-12 mt-4">
                        <div class="tiny-three-item">

                            @foreach ($testimoni as $item)
                                <div class="tiny-slide wow animate__animated animate__fadeInUp" data-wow-delay=".3s">
                                    <div class="d-flex client-testi m-1">
                                        @php
                                            $photoPath = $item->user->profile?->foto;
                                            $googleAvatar = $item->user->avatar;
                                            $imageSrc = $photoPath ? asset('storage/' . $photoPath) : ($googleAvatar ?: asset('assets/images/client/01.jpg'));
                                        @endphp
                                        <img src="{{ $imageSrc }}" class="avatar avatar-small client-image rounded shadow" alt="Foto testimoni dari {{ $item->user->name }}">
                                        <div class="card flex-1 content p-3 shadow rounded position-relative">
                                            <ul class="list-unstyled mb-0">
                                                @for ($i = 0; $i < $item->rating; $i++)
                                                    <li class="list-inline-item"><i class="mdi mdi-star text-warning"></i></li>
                                                @endfor
                                            </ul>
                                            <p class="text-muted mt-2">
                                                " {{ $item->testimoni }}"
                                            </p>
                                            <h6 class="text-primary">- {{ $item->user->name }}</h6>
                                        </div>
                                    </div>
                                </div>
                            @endforeach

                        </div>
                    </div>
                </div>
            </div>
        @endif

        <div class="container mt-100 mt-60">
            <div class="row mt-md-5 pt-md-3 mt-4 pt-2 mt-sm-0 pt-sm-0 justify-content-center">
                <div class="col-12 text-center">
                    <div class="section-title wow animate__animated animate__fadeInUp" data-wow-delay=".1s">
                        <div class="alert alert-light alert-pills text-dark" role="alert">
                            <span class="badge rounded-pill bg-success me-1">Layanan</span>
                            <span class="content"> 8 Jam Kerja/ 5 Hari Kerja</span>
                        </div>
                        <h4 class="title mb-2">Kritik, Saran, Pengaduan</h4>
                        <p class="text-muted para-desc mx-auto">
                            Sampaikan keluhan, kritik, saran ataupun pengaduan kamu pada email dibawah ini !
                        </p>
                        <div class="mt-4">
                            <a href="mailto:{{ $infoApp->email }}" class="btn btn-pills btn-soft-primary mb-3">
                                {{ $infoApp->email }} <i class="uil uil-envelope"></i>
                            </a>
                            <a href="mailto:media.pnpakam@gmail.com" class="btn btn-pills btn-soft-primary mb-3">
                                media.pnpakam@gmail.com <i class="uil uil-envelope"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection
