@extends('landing.index')
@section('title', $title)
@section('content')

    @push('styles')
        <style>
            /* Hero Section Styles */
            .about-hero {
                background: linear-gradient(135deg, #2F55D4 0%, #2F55D4 50%, #ffffff 100%);
                position: relative;
                overflow: hidden;
                min-height: 400px;
            }

            .about-hero::before {
                content: '';
                position: absolute;
                top: -50%;
                right: -50%;
                width: 100%;
                height: 200%;
                background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 60%);
                animation: float 6s ease-in-out infinite;
            }

            .about-hero::after {
                content: '';
                position: absolute;
                bottom: -50%;
                left: -50%;
                width: 100%;
                height: 200%;
                background: radial-gradient(circle, rgba(255, 255, 255, 0.08) 0%, transparent 60%);
                animation: float 8s ease-in-out infinite reverse;
            }

            @keyframes float {

                0%,
                100% {
                    transform: translateY(0) rotate(0deg);
                }

                50% {
                    transform: translateY(-20px) rotate(5deg);
                }
            }

            .hero-badge {
                background: rgba(255, 255, 255, 0.2);
                backdrop-filter: blur(10px);
                border: 1px solid rgba(255, 255, 255, 0.3);
                padding: 8px 20px;
                border-radius: 50px;
                display: inline-block;
                color: white;
                font-size: 0.9rem;
                margin-bottom: 1.5rem;
            }

            /* Glassmorphism Card */
            .glass-card {
                background: rgba(255, 255, 255, 0.95);
                backdrop-filter: blur(20px);
                border-radius: 24px;
                border: 1px solid rgba(255, 255, 255, 0.8);
                box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15);
                transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
                overflow: hidden;
            }

            .glass-card:hover {
                transform: translateY(-8px);
                box-shadow: 0 35px 60px -15px rgba(0, 0, 0, 0.2);
            }

            .glass-card-dark {
                background: linear-gradient(135deg, rgba(13, 110, 253, 0.1) 0%, rgba(13, 110, 253, 0.1) 100%);
                border: 1px solid rgba(13, 110, 253, 0.2);
            }

            /* Image Frame */
            .image-frame {
                position: relative;
                border-radius: 20px;
                overflow: hidden;
                box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
            }

            .image-frame::before {
                content: '';
                position: absolute;
                inset: 0;
                border-radius: 20px;
                padding: 3px;
                background: linear-gradient(135deg, #2F55D4, #2F55D4, #ffffff);
                -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
                -webkit-mask-composite: xor;
                mask-composite: exclude;
                z-index: 1;
            }

            .image-frame img {
                border-radius: 17px;
                transition: transform 0.5s ease;
            }

            .image-frame:hover img {
                transform: scale(1.03);
            }

            /* Feature Icons */
            .feature-icon {
                width: 60px;
                height: 60px;
                border-radius: 16px;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 1.5rem;
                background: linear-gradient(135deg, #2F55D4 0%, #2F55D4 100%);
                color: white;
                box-shadow: 0 10px 20px rgba(47, 85, 212, 0.3);
            }

            /* Timeline Styles */
            .timeline-section {
                position: relative;
            }

            .timeline-section::before {
                content: '';
                position: absolute;
                left: 50%;
                transform: translateX(-50%);
                width: 3px;
                height: 100%;
                background: linear-gradient(180deg, #2F55D4 0%, #2F55D4 50%, #ffffff 100%);
                border-radius: 2px;
            }

            @media (max-width: 768px) {
                .timeline-section::before {
                    left: 20px;
                }
            }

            .timeline-dot {
                width: 20px;
                height: 20px;
                background: linear-gradient(135deg, #2F55D4 0%, #2F55D4 100%);
                border-radius: 50%;
                position: absolute;
                left: 50%;
                transform: translateX(-50%);
                border: 4px solid white;
                box-shadow: 0 4px 15px rgba(13, 110, 253, 0.4);
                z-index: 2;
            }

            @media (max-width: 768px) {
                .timeline-dot {
                    left: 20px;
                }
            }

            /* Award Badge */
            .award-badge {
                background: linear-gradient(135deg, #e7f1ff 0%, #cfe2ff 100%);
                padding: 6px 16px;
                border-radius: 50px;
                display: inline-flex;
                align-items: center;
                gap: 8px;
                font-weight: 600;
                color: #0d6efd;
                font-size: 0.85rem;
            }

            /* Floating Decorations */
            .floating-shape {
                position: absolute;
                opacity: 0.1;
                z-index: 0;
            }

            .shape-1 {
                top: 10%;
                right: 10%;
                width: 100px;
                height: 100px;
                background: linear-gradient(135deg, #0d6efd, #0d6efd);
                border-radius: 30% 70% 70% 30% / 30% 30% 70% 70%;
                animation: morph 8s ease-in-out infinite;
            }

            .shape-2 {
                bottom: 20%;
                left: 5%;
                width: 80px;
                height: 80px;
                background: linear-gradient(135deg, #ffffff, #e7f1ff);
                border-radius: 60% 40% 30% 70% / 60% 30% 70% 40%;
                animation: morph 10s ease-in-out infinite reverse;
            }

            @keyframes morph {

                0%,
                100% {
                    border-radius: 30% 70% 70% 30% / 30% 30% 70% 70%;
                }

                50% {
                    border-radius: 60% 40% 30% 70% / 60% 30% 70% 40%;
                }
            }

            /* Quote Style */
            .quote-text {
                font-size: 1.1rem;
                font-style: italic;
                position: relative;
                padding-left: 30px;
            }

            .quote-text::before {
                content: '"';
                position: absolute;
                left: 0;
                top: -10px;
                font-size: 4rem;
                font-family: Georgia, serif;
                color: #2F55D4;
                opacity: 0.3;
                line-height: 1;
            }

            /* CTA Button */
            .btn-gradient {
                background: linear-gradient(135deg, #2F55D4 0%, #2F55D4 100%);
                border: none;
                color: white;
                padding: 14px 32px;
                border-radius: 50px;
                font-weight: 600;
                transition: all 0.3s ease;
                box-shadow: 0 10px 30px rgba(47, 85, 212, 0.4);
            }

            .btn-gradient:hover {
                transform: translateY(-3px);
                box-shadow: 0 15px 40px rgba(47, 85, 212, 0.5);
                color: white;
            }

            /* Values Grid */
            .value-card {
                padding: 30px;
                border-radius: 20px;
                background: white;
                border: 1px solid #eee;
                transition: all 0.3s ease;
            }

            .value-card:hover {
                border-color: #2F55D4;
                box-shadow: 0 15px 30px rgba(47, 85, 212, 0.1);
            }

            .value-card .icon {
                font-size: 2.5rem;
                margin-bottom: 1rem;
            }

            /* Link Style */
            .link-gradient {
                background: linear-gradient(135deg, #2F55D4 0%, #2F55D4 100%);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                background-clip: text;
                font-weight: 600;
                text-decoration: none;
                transition: all 0.3s ease;
            }

            .link-gradient:hover {
                opacity: 0.8;
            }
        </style>
    @endpush

    <!-- Hero Section -->
    <section class="about-hero d-flex align-items-center position-relative" style="padding: 120px 0 80px;">
        <div class="container position-relative" style="z-index: 2;">
            <div class="row justify-content-center text-center">
                <div class="col-lg-8 wow animate__animated animate__fadeInUp" data-wow-delay=".1s">
                    <h1 class="display-4 fw-bold text-white mb-4">
                        Inovasi Digital untuk <br>
                        <span style="background: linear-gradient(90deg, #ffffff, #e7f1ff); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">Pelayanan Publik</span>
                    </h1>
                    <p class="lead text-white opacity-75 mb-0" style="max-width: 600px; margin: 0 auto;">
                        Mendekatkan layanan peradilan kepada masyarakat melalui transformasi digital yang mudah, cepat, dan terjangkau.
                    </p>
                </div>
            </div>
        </div>

        <!-- Decorative Elements -->
        <div class="floating-shape shape-1"></div>
        <div class="floating-shape shape-2"></div>
    </section>

    <!-- About Content Section -->
    <section class="section position-relative" style="margin-top: -60px; z-index: 3;">
        <div class="container">
            <!-- Main About Card -->
            <div class="glass-card p-4 p-lg-5 wow animate__animated animate__fadeInUp" data-wow-delay=".2s">
                <div class="row align-items-center g-5">
                    <div class="col-lg-5">
                        <div class="image-frame">
                            <img src="{{ asset('assets/images/Piagam-Esuka.png') }}" class="img-fluid" alt="Piagam E-Suka">
                        </div>
                    </div>
                    <div class="col-lg-7">
                        <div class="ps-lg-4">
                            <span class="badge bg-primary bg-opacity-10 text-white rounded-pill px-3 py-2 mb-3">
                                <i class="uil uil-history me-1"></i> Latar Belakang
                            </span>
                            <h2 class="fw-bold mb-4" style="color: #1a1a2e;">
                                Transformasi Layanan <span class="text-primary">Surat Kuasa</span>
                            </h2>
                            <p class="text-muted mb-4" style="text-align: justify; line-height: 1.8;">
                                Sebagai salah satu lembaga peradilan yang menangani perkara yang banyak setiap tahunnya,
                                <strong>{{ config('app.author') }}</strong> memiliki beban administrasi cukup tinggi terutama dalam hal pelayanan
                                penerbitan legalisasi pendaftaran surat kuasa.
                            </p>
                            <p class="text-muted mb-4" style="text-align: justify; line-height: 1.8;">
                                Sesuai dengan asas <span class="badge bg-success bg-opacity-10 text-white">Mudah</span>
                                <span class="badge bg-info bg-opacity-10 text-white">Cepat</span>
                                <span class="badge bg-warning bg-opacity-10 text-white">Biaya Ringan</span>,
                                kami membuat terobosan inovasi yang memudahkan proses pendaftaran surat kuasa secara digital.
                            </p>

                            <div class="quote-text text-muted mb-4">
                                Dengan adanya inovasi ini, pendaftaran surat kuasa berbasis online melalui aplikasi
                                <strong class="text-primary">{{ config('app.name') }}</strong> dapat diakses dimana saja dan kapan saja.
                            </div>

                            <a href="{{ route('app.signin') }}" class="btn btn-gradient">
                                <i class="uil uil-rocket me-2"></i>Daftar Sekarang
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Values Section -->
    <section class="section bg-light position-relative overflow-hidden">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-12 text-center mb-5 wow animate__animated animate__fadeInUp" data-wow-delay=".1s">
                    <h2 class="fw-bold" style="color: #1a1a2e;">Prinsip Layanan</h2>
                    <p class="text-muted">Tiga pilar utama yang menjadi dasar pelayanan kami</p>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-lg-4 col-md-6 wow animate__animated animate__fadeInUp" data-wow-delay=".2s">
                    <div class="value-card text-center h-100">
                        <div class="icon">🚀</div>
                        <h5 class="fw-bold mb-3">Mudah</h5>
                        <p class="text-muted mb-0">Proses pendaftaran yang simpel dan user-friendly, dapat dilakukan oleh siapa saja tanpa memerlukan keahlian teknis khusus.</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 wow animate__animated animate__fadeInUp" data-wow-delay=".3s">
                    <div class="value-card text-center h-100">
                        <div class="icon">⚡</div>
                        <h5 class="fw-bold mb-3">Cepat</h5>
                        <p class="text-muted mb-0">Waktu pemrosesan yang efisien. Tidak perlu mengantri, cukup beberapa klik untuk menyelesaikan pendaftaran.</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 wow animate__animated animate__fadeInUp" data-wow-delay=".4s">
                    <div class="value-card text-center h-100">
                        <div class="icon">💰</div>
                        <h5 class="fw-bold mb-3">Biaya Ringan</h5>
                        <p class="text-muted mb-0">Tarif yang terjangkau sesuai dengan Peraturan Pemerintah. Transparan tanpa biaya tersembunyi.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Award Section -->
    <section class="section">
        <div class="container">
            <div class="row align-items-center g-5">
                <div class="col-lg-6 order-lg-2 wow animate__animated animate__fadeInRight" data-wow-delay=".2s">
                    <div class="image-frame">
                        <img src="{{ asset('assets/images/sk-penetapan.jpg') }}" class="img-fluid" alt="SK Penetapan Penghargaan">
                    </div>
                </div>
                <div class="col-lg-6 order-lg-1 wow animate__animated animate__fadeInLeft" data-wow-delay=".2s">
                    <div class="pe-lg-4">
                        <div class="award-badge mb-4">
                            <i class="uil uil-award"></i>
                            Penghargaan 2022
                        </div>
                        <h2 class="fw-bold mb-4" style="color: #1a1a2e;">
                            Raih Penghargaan dari <span class="text-primary">Direktorat Jenderal Badan Peradilan Umum</span>
                        </h2>
                        <p class="text-muted mb-4" style="text-align: justify; line-height: 1.8;">
                            <strong class="text-primary">{{ config('app.name') }}</strong> telah meraih penghargaan sebagai salah satu aplikasi terbaik kategori
                            <span class="badge bg-primary bg-opacity-40 text-white f-12">"Penerapan Aplikasi Pelayanan Publik"</span> pada tahun 2022.
                        </p>
                        <p class="text-muted mb-4" style="text-align: justify; line-height: 1.8;">
                            Penghargaan diberikan oleh <strong>YM Ketua Mahkamah Agung RI</strong> didampingi
                            <strong>Direktur Jenderal Badan Peradilan Umum, H. Bambang Myanto, SH, MH</strong>
                            kepada para pemenang Lomba bagi Satuan Kerja di Lingkungan Peradilan Umum Tahun 2022.
                        </p>

                        <div class="d-flex align-items-center gap-3 p-3 rounded-3"
                            style="background: linear-gradient(135deg, rgba(13, 110, 253, 0.05) 0%, rgba(13, 110, 253, 0.05) 100%); border: 1px solid rgba(13, 110, 253, 0.1);">
                            <div class="feature-icon flex-shrink-0">
                                <i class="uil uil-calendar-alt"></i>
                            </div>
                            <div>
                                <small class="text-muted d-block">Waktu & Tempat</small>
                                <strong>Senin, 12 Desember 2022</strong>
                                <br>
                                <small class="text-muted">Hotel Inna Malioboro, Yogyakarta</small>
                            </div>
                        </div>

                        <div class="mt-4">
                            <a href="https://badilum.mahkamahagung.go.id/berita/berita-kegiatan/3866-ketua-mahkamah-agung-berikan-penghargaan-pemenang-lomba-bagi-satuan-kerja-di-lingkungan-peradilan-umum-tahun-2022.html"
                                target="_blank" class="link-gradient d-inline-flex align-items-center gap-2">
                                <i class="uil uil-external-link-alt"></i>
                                Baca Berita Lengkap
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="section" style="background: linear-gradient(135deg, #202942 0%, #202942 100%); position: relative; overflow: hidden;">
        <div class="container position-relative" style="z-index: 2;">
            <div class="row justify-content-center">
                <div class="col-lg-8 text-center wow animate__animated animate__fadeInUp" data-wow-delay=".1s">
                    <h2 class="display-5 fw-bold text-white mb-4">
                        Siap Mendaftarkan Surat Kuasa?
                    </h2>
                    <p class="lead text-white opacity-75 mb-5">
                        Bergabunglah dengan ribuan pengguna yang sudah merasakan kemudahan layanan digital kami.
                    </p>
                    <div class="d-flex flex-wrap justify-content-center gap-3">
                        <a href="{{ route('app.signin') }}" class="btn btn-light btn-lg rounded-pill px-5 fw-semibold" style="box-shadow: 0 10px 30px rgba(0,0,0,0.2);">
                            <i class="uil uil-user-plus me-2"></i>Daftar Sekarang
                        </a>
                        <a href="{{ route('app.contact') }}" class="btn btn-primary btn-lg rounded-pill px-5">
                            <i class="uil uil-envelope me-2"></i>Hubungi Kami
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Background Decorations -->
        <div class="position-absolute top-0 start-0 w-100 h-100" style="z-index: 1;">
            <div class="position-absolute" style="top: 10%; left: 5%; width: 200px; height: 200px; background: rgba(255,255,255,0.1); border-radius: 50%; animation: float 6s ease-in-out infinite;">
            </div>
            <div class="position-absolute"
                style="bottom: 10%; right: 10%; width: 150px; height: 150px; background: rgba(255,255,255,0.08); border-radius: 50%; animation: float 8s ease-in-out infinite reverse;"></div>
            <div class="position-absolute" style="top: 50%; right: 20%; width: 100px; height: 100px; background: rgba(255,255,255,0.05); border-radius: 50%; animation: float 7s ease-in-out infinite;">
            </div>
        </div>
    </section>

@endsection
