<div class="welcome-section mb-4">
    <h2>Selamat Datang di Pusat Panduan</h2>
    <p>Temukan panduan lengkap untuk membantu Kamu menggunakan aplikasi {{ config('app.name') }} dengan mudah dan efektif.</p>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-6 col-lg-3">
        <a href="{{ route('panduan.show', 'akun/daftar-dengan-email') }}" class="text-decoration-none">
            <div class="guide-card">
                <h5>Daftar Akun</h5>
                <p>Pelajari cara mendaftar akun baru dengan email atau Google</p>
            </div>
        </a>
    </div>
    <div class="col-md-6 col-lg-3">
        <a href="{{ route('panduan.show', 'surat-kuasa/pengajuan') }}" class="text-decoration-none">
            <div class="guide-card">
                <h5>Pengajuan Surat</h5>
                <p>Panduan lengkap mengajukan surat kuasa secara online</p>
            </div>
        </a>
    </div>
    <div class="col-md-6 col-lg-3">
        <a href="{{ route('panduan.show', 'surat-kuasa/pembayaran') }}" class="text-decoration-none">
            <div class="guide-card">
                <h5>Pembayaran</h5>
                <p>Cara melakukan pembayaran surat kuasa dengan mudah</p>
            </div>
        </a>
    </div>
    <div class="col-md-6 col-lg-3">
        <a href="{{ route('panduan.show', 'surat-kuasa/download-barcode') }}" class="text-decoration-none">
            <div class="guide-card">
                <h5>Download Barcode</h5>
                <p>Unduh dan cetak barcode surat kuasa Anda</p>
            </div>
        </a>
    </div>
</div>

<div class="card border-0">
    <div class="card-body">
        <h4 class="mb-4">Informasi Aplikasi</h4>
        <div class="row g-3">
            <div class="col-md-6">
                <div class="info-box">
                    <div class="info-box-content">
                        <h6>Nama Aplikasi</h6>
                        <p>{{ config('app.name') }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="info-box">
                    <div class="info-box-content">
                        <h6>Versi</h6>
                        <p>{{ config('app.version') }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="info-box">
                    <div class="info-box-content">
                        <h6>Deskripsi</h6>
                        <p>{{ config('app.description') }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="info-box">
                    <div class="info-box-content">
                        <h6>Rilis Panduan</h6>
                        <p>{{ \Carbon\Carbon::parse('2025-12-25')->isoFormat('dddd, D MMMM Y') }}</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="mt-4 pt-3 border-top">
            <a class="btn btn-primary btn-sm" href="{{ route('app.signin') }}">Akses Aplikasi</a>
        </div>
    </div>
</div>
