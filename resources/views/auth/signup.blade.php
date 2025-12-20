@include('auth.layouts.header')

<body>
    <div id="preloader">
        <div id="status">
            <div class="spinner">
                <div class="double-bounce1"></div>
                <div class="double-bounce2"></div>
            </div>
        </div>
    </div>

    <section class="d-flex align-items-center">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-lg-8 d-none d-lg-block" style="background-image: url('{{ asset('assets/images/model-1.jpeg') }}'); background-size: cover; background-position: center; height: 100vh;">
                </div>
                <div class="col-lg-4 col-md-12 col-sm-12">
                    <div class="card login-page rounded border-0">
                        <div class="card-body">
                            <img onclick="window.location='{{ route('app.home') }}'" src="{{ asset('icons/horizontal-e-suka.png') }}" class="img-fluid d-block mx-auto d-lg-none mb-4" alt="logo"
                                style="max-height: 50px; cursor: pointer;">
                            <h4 class="card-title text-center m-0">Daftar</h4>
                            <p class="text-center">{{ config('app.name') }}</p>
                            <form class="login-form mt-4" id="register-form">
                                @method('POST')
                                @csrf
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="mb-3">
                                            <label class="form-label" for="namaDepan">
                                                Nama Depan <span class="text-danger">*</span>
                                            </label>
                                            <div class="form-icon position-relative">
                                                <i data-feather="user" class="fea icon-sm icons"></i>
                                                <input type="text" class="form-control @error('namaDepan') is-invalid @enderror ps-5" placeholder="Qori...." id="namaDepan" name="namaDepan" required
                                                    value="{{ old('namaDepan') }}" autocomplete="given-name">
                                                <small class="text-danger mt-2" id="namaDepanError"></small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="mb-3">
                                            <label class="form-label" for="namaBelakang">
                                                Nama Belakang <span class="text-danger">*</span>
                                            </label>
                                            <div class="form-icon position-relative">
                                                <i data-feather="user" class="fea icon-sm icons"></i>
                                                <input type="text" class="form-control @error('namaBelakang') is-invalid @enderror ps-5" placeholder="Chairawan...." id="namaBelakang"
                                                    name="namaBelakang" required autocomplete="family-name" value="{{ old('namaBelakang') }}">
                                                <small class="text-danger mt-2" id="namaBelakangError"></small>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-lg-12">
                                        <div class="mb-3">
                                            <label class="form-label" for="email">
                                                Email <span class="text-danger">*</span>
                                            </label>
                                            <div class="form-icon position-relative">
                                                <i data-feather="user" class="fea icon-sm icons"></i>
                                                <input type="email" class="form-control @error('email') is-invalid @enderror ps-5" placeholder="qori@example.com" id="email" name="email"
                                                    required value="{{ old('email') }}" autocomplete="email">
                                                <small class="text-danger mt-2" id="emailError"></small>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-lg-12">
                                        <div class="mb-3">
                                            <label class="form-label" for="password">
                                                Password <span class="text-danger">*</span>
                                            </label>
                                            <div class="form-icon position-relative">
                                                <i data-feather="key" class="fea icon-sm icons"></i>
                                                <input type="password" class="form-control @error('password') is-invalid @enderror ps-5" placeholder="**********" id="password" name="password"
                                                    required autocomplete="new-password">
                                                <small class="text-danger mt-2" id="passwordError"></small>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-lg-12">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="privacy_policy" id="privacy_policy">
                                            <label class="form-check-label" for="privacy_policy">Saya telah membaca dan menyetujui
                                                <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#privacyPolicyModal" class="text-primary">
                                                    Kebijakan Privasi & Persyaratan Penggunaan
                                                </a>
                                            </label>
                                            <small class="text-danger mt-2 d-block" id="privacy_policyError"></small>
                                        </div>
                                        <p class="forgot-pass mb-0 m-0"><a href="{{ route('auth.forgot-password') }}" class="text-dark fw-bold">Lupa Password ?</a></p>
                                        <div class="d-grid mt-2">
                                            <button class="btn btn-primary" type="submit" id="register-button">Daftar</button>
                                        </div>
                                    </div>


                                    <div class="col-lg-12 mt-2 text-center">
                                        <h6>Daftar Dengan</h6>
                                        <div class="d-grid mt-2">
                                            <a href="{{ route('google.redirect', ['action' => 'register']) }}" class="btn btn-light">
                                                <i class="mdi mdi-google text-danger"></i> Google
                                            </a>
                                        </div>
                                    </div>

                                    <div class="col-12 text-center">
                                        <p class="mb-0 mt-3">
                                            <small class="text-dark me-2"> Sudah Punya Akun ?</small>
                                            <a href="{{ route('app.signin') }}" class="text-dark fw-bold">
                                                Login Disini !
                                            </a>
                                        </p>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Privacy Policy Modal -->
    <div class="modal fade" id="privacyPolicyModal" tabindex="-1" aria-labelledby="privacyPolicyModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="privacyPolicyModalLabel">Kebijakan Privasi dan Persyaratan Penggunaan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <h5>1. Pendahuluan</h5>
                    <p class="text-muted" style="text-align: justify;">
                        Kebijakan Privasi ini menjelaskan bagaimana Pengadilan Negeri Lubuk Pakam (“Kami”) melalui aplikasi Elektronik Surat Kuasa (“Aplikasi”) mengelola Data Pribadi pengguna. Kami
                        berkomitmen untuk menjaga kerahasiaan dan keamanan Data Pribadi sesuai UU No. 27 Tahun 2022 tentang Perlindungan Data Pribadi.
                        Dengan menggunakan Aplikasi ini, pengguna dianggap telah membaca, memahami, dan menyetujui Kebijakan Privasi ini.
                    </p>

                    <h5>2. Pengumpulan Informasi</h5>
                    <p class="text-muted">
                        Aplikasi dapat mengumpulkan data berikut:
                    <ul class="text-muted">
                        <li>Identitas diri: nama lengkap, NIK, alamat, nomor telepon, email.</li>
                        <li>Data hukum: nomor perkara, pihak berperkara, informasi terkait Surat Kuasa.</li>
                        <li>Dokumen pendukung: KTP, Surat Kuasa, dan dokumen hukum lainnya.</li>
                        <li>Data teknis: username, password, log aktivitas, alamat IP.</li>
                    </ul>
                    </p>

                    <h5>3. Penggunaan Informasi</h5>
                    <p class="text-muted">Informasi Kamu digunakan untuk:
                    <ul class="text-muted">
                        <li class="text-muted">Memproses pendaftaran surat kuasa Anda.</li>
                        <li class="text-muted">Mengirim notifikasi terkait status pendaftaran Anda.</li>
                        <li class="text-muted">Memverifikasi identitas pemberi dan penerima kuasa.</li>
                        <li class="text-muted">Pemenuhan kewajiban hukum dan administrasi peradilan</li>
                        <li class="text-muted">Menyediakan dukungan layanan pengguna.</li>
                    </ul>
                    </p>

                    <h5>4. Keamanan Data</h5>
                    <p class="text-muted" style="text-align: justify;">
                        Kami menerapkan langkah-langkah keamanan yang wajar untuk
                        melindungi informasi pribadi Kamu dari akses, penggunaan, atau pengungkapan yang
                        tidak sah. Namun, tidak ada metode transmisi melalui internet atau metode
                        penyimpanan elektronik yang 100% aman.
                    </p>

                    <h5>5. Hak Pengguna</h5>
                    <p class="text-muted">
                        Pengguna berhak untuk:
                    <ul class="text-muted">
                        <li>Mendapatkan informasi pemrosesan Data Pribadi.</li>
                        <li>Memperbaiki, memperbarui, atau menghapus Data Pribadi sesuai hukum.</li>
                        <li>Menarik persetujuan pemrosesan Data Pribadi.</li>
                        <li>Mengajukan keberatan atas pemrosesan tertentu.</li>
                    </ul>
                    </p>

                    <h5>6. Pengungkapan Data</h5>
                    <p class="text-muted">
                        Data Pribadi tidak akan diperjualbelikan kepada pihak manapun. Data hanya dapat dibuka apabila:
                    <ul class="text-muted">
                        <li> Diwajibkan oleh undang-undang. </li>
                        <li> Diminta oleh otoritas resmi berdasarkan prosedur hukum. </li>
                        <li> Dibutuhkan dalam penyelenggaraan tugas peradilan. </li>
                    </ul>
                    </p>

                    <h5>7. Perubahan Kebijakan Privasi</h5>
                    <p class="text-muted">
                        Kami dapat mengubah Kebijakan Privasi ini sewaktu-waktu. Setiap perubahan akan diumumkan melalui Aplikasi atau situs resmi Pengadilan Negeri Lubuk Pakam.
                    </p>

                    <h5>8. Persetujuan Pengguna</h5>
                    <p class="text-muted" style="text-align:justify;">
                        Dengan mencentang kotak persetujuan saat pendaftaran, Kamu setuju
                        dengan pengumpulan dan penggunaan informasi sesuai dengan kebijakan ini. Kamu juga
                        setuju untuk mematuhi semua persyaratan dan ketentuan yang berlaku.
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-sm btn-primary" id="agree-button">Saya Setuju</button>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/libs/feather-icons/feather.min.js') }}"></script>
    <script src="{{ asset('assets/libs/sweetalert2/dist/sweetalert2.all.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins.init.js') }}"></script>
    <script src="{{ asset('assets/js/app.js') }}"></script>
    @if (session()->has('success'))
        <script>
            Swal.fire({
                position: 'center',
                icon: 'success',
                title: 'Notifikasi',
                text: '{{ session()->get('success') }}',
                // timer: 1500
            })
        </script>
    @elseif (session()->has('error'))
        <script>
            Swal.fire({
                position: 'center',
                icon: 'error',
                title: 'Oops',
                text: '{{ session()->get('error') }}',
            })
        </script>
    @endif
    @include('auth.scripts.handleRegister')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const agreeButton = document.getElementById('agree-button');
            if (agreeButton) {
                agreeButton.addEventListener('click', function() {
                    document.getElementById('privacy_policy').checked = true;
                    bootstrap.Modal.getInstance(document.getElementById('privacyPolicyModal')).hide();
                });
            }
        });
    </script>
</body>

</html>
