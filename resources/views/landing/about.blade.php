@extends('landing.index')
@section('titlte', $title)
@section('content')

    <section class="section d-table w-100">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-5 col-md-5 mt-4 pt-2 mt-sm-0 pt-sm-0">
                    <div class="position-relative">
                        <img src="{{ asset('assets/images/laptop.png') }}" class="rounded img-fluid mx-auto d-block"
                            alt="">
                    </div>
                </div>

                <div class="col-lg-7 col-md-7 mt-4 pt-2 mt-sm-0 pt-sm-0">
                    <div class="section-title ms-lg-4">
                        <h4 class="title mb-4">Latar Belakang</h4>
                        <p class="text-muted" style="text-align:justify;">
                            Sebagai salah satu lembaga peradilan yang menangani perkara yang banyak setiap tahunnya,
                            {{ config('app.author') }} memiliki beban administrasi cukup tinggi terutama dalam hal pelayanan
                            penerbitan legalisasi pendaftaran surat kuasa.
                            Untuk itu sesuai dengan asas <span class="text-primary fw-bold">Mudah, Cepat, Biaya
                                Ringan</span>. {{ config('app.author') }} membuat sebuah teroboson inovasi yang memudahkan
                            dalam proses pendaftaran surat kuasa, yang mana dulunya pendaftaran melalui Meja Hukum pada
                            Pelayanan Terpadu Satu Pintu, namun kini cukup melalui aplikasi tanpa harus datang.
                            <br>
                            Dengan adanya inovasi ini dapat memberikan respon positif atas kemudahan pendaftaran surat kuasa
                            berbasis online melalui aplikasi {{ config('app.name') }}, Secara mudah, cepat, biaya ringan dan
                            dapat diakses dimana saja.
                        </p>
                        <a href="{{ route('app.signin') }}" class="btn btn-pills btn-primary mt-1">Daftar Sekarang
                            <i class="uil uil-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="container mt-100">
            <div class="row align-items-center">
                <div class="col-lg-7 col-md-7 mt-4 pt-2 mt-sm-0 pt-sm-0">
                    <div class="section-title ms-lg-4">
                        <h4 class="title mb-4">Raih Penghargaan Dari Direktorat Jenderal Badan Peradilan Umum </h4>
                        <p class="text-muted" style="text-align:justify;">
                            {{ config('app.name') }} telah meraih penghargaan sebagai salah satu aplikasi terbaik kategori
                            <span class="text-primary fw-bold">"Penerapan Aplikasi Pelayanan Publik"</span> pada tahun 2022.
                            Penghargaan diberikan oleh YM Ketua
                            Mahkamah Agung RI didampingi Direktur Jenderal Badan Peradilan Umum, H. Bambang Myanto, SH, MH
                            kepada para pemenang Lomba bagi Satuan Kerja di Lingkungan Peradilan Umum Tahun 2022 pada hari
                            Senin, 12 Desember 2022, bertempat di ballroom Hotel Inna Malioboro Yogyakarta.
                        </p>
                        <a title="Menuju Berita" target="_blank"
                            href="https://badilum.mahkamahagung.go.id/berita/berita-kegiatan/3866-ketua-mahkamah-agung-berikan-penghargaan-pemenang-lomba-bagi-satuan-kerja-di-lingkungan-peradilan-umum-tahun-2022.html">https://badilum.mahkamahagung.go.id/berita/berita-kegiatan/3866-ketua-mahkamah-agung-berikan-penghargaan-pemenang-lomba-bagi-satuan-kerja-di-lingkungan-peradilan-umum-tahun-2022.html
                        </a>
                    </div>
                </div>

                <div class="col-lg-5 col-md-5 mt-4 pt-2 mt-sm-0 pt-sm-0">
                    <div class="position-relative">
                        <img src="{{ asset('assets/images/sk-penetapan.jpg') }}" class="rounded img-fluid mx-auto d-block"
                            alt="">
                    </div>
                </div>

            </div>
        </div>
    </section>
    <!-- About End -->
@endsection
