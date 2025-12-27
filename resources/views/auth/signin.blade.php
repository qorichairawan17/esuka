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
                <div class="col-lg-8 d-none d-lg-block" style="background-image: url('{{ asset('assets/images/model-5.jpeg') }}'); background-size: cover; background-position: center; height: 100vh;">
                </div>
                <div class="col-lg-4 col-md-12 col-sm-12">
                    <div class="card login-page rounded border-0">
                        <div class="card-body">
                            <img onclick="window.location='{{ route('app.home') }}'" src="{{ asset('icons/horizontal-e-suka.png') }}" class="img-fluid d-block mx-auto d-lg-none mb-4" alt="logo"
                                style="max-height: 50px; cursor: pointer;">
                            <h4 class="card-title text-center m-0">Login</h4>
                            <p class="text-center">{{ config('app.name') }}</p>
                            <form class="login-form mt-4" id="login-form">
                                @method('POST')
                                @csrf
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="mb-3">
                                            <label class="form-label" for="email">
                                                Email <span class="text-danger">*</span>
                                            </label>
                                            <div class="form-icon position-relative">
                                                <i data-feather="user" class="fea icon-sm icons"></i>
                                                <input type="email" class="form-control @error('email') is-invalid @enderror ps-5" placeholder="Email" id="email" name="email" required
                                                    value="{{ old('email') }}">
                                                <div class="invalid-feedback mt-2" id="emailError"></div>
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
                                                <input type="password" class="form-control @error('password') is-invalid @enderror ps-5" placeholder="Password" id="password" name="password" required
                                                    value="{{ old('password') }}">
                                                <div class="invalid-feedback mt-2" id="passwordError"></div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-lg-12 mb-0">
                                        <div class="mb-3">
                                            <img title="Klik Untuk Refresh" class="img-fluid" src="{{ captcha_src('flat') }}" alt="captcha" id="captcha-img">
                                            <input class="form-control mt-2" type="text" name="captcha" id="captcha" placeholder="Masukkan kode captcha diatas" required>
                                            <div class="invalid-feedback mt-2" id="captchaError"></div>
                                        </div>
                                        <p class="forgot-pass mb-0">
                                            <a href="{{ route('auth.forgot-password') }}" class="text-dark fw-bold">
                                                Lupa Password ?
                                            </a>
                                        </p>
                                        <div class="d-grid mt-3">
                                            <button class="btn btn-primary" type="submit" id="login-button">Masuk</button>
                                        </div>
                                    </div>

                                    <div class="col-lg-12 mt-2 text-center">
                                        <h6>Masuk Dengan</h6>
                                        <div class="d-grid mt-2">
                                            <a href="{{ route('google.redirect', ['action' => 'login']) }}" class="btn btn-light"><i class="mdi mdi-google text-danger"></i> Google</a>
                                        </div>
                                    </div>

                                    <div class="col-12 text-center">
                                        <p class="mb-0 mt-3">
                                            <small class="text-dark me-2"> Belum Punya Akun ?</small>
                                            <a href="{{ route('app.signup') }}" class="text-dark fw-bold">
                                                Daftar Disini !
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
        <!-- Notification Modal -->
        <div class="modal fade" id="notifikasi" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content rounded shadow border-0">
                    <div class="modal-body py-5">
                        <div class="text-center">
                            <div class="icon d-flex align-items-center justify-content-center bg-soft-warning rounded-circle mx-auto" style="height: 95px; width:95px;">
                                <h1 class="mb-0"><i class="uil uil-user align-middle"></i></h1>
                            </div>
                            <div class="mt-4">
                                <h4>Aktivasi Ulang Akun</h4>
                                <p class="text-dark">
                                    Bagi pengguna yang sudah terdaftar di {{ config('app.name') }} versi lama, Silahkan aktivasi akun kembali dengan cara login dengan email sebelumnya dan melihat
                                    pesan masuk
                                    pada email untuk aktivasi akun kembali !
                                </p>
                                <button class="btn btn-primary btn-md" data-bs-dismiss="modal">Tutup</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script src="{{ asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/libs/feather-icons/feather.min.js') }}"></script>
    <script src="{{ asset('assets/libs/sweetalert2/dist/sweetalert2.all.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins.init.js') }}"></script>
    <script src="{{ asset('assets/js/app.js') }}"></script>
    @if (session()->has('success') || session()->has('error'))
        <script>
            Swal.fire({
                position: 'center',
                icon: '{{ session()->has('success') ? 'success' : 'error' }}',
                title: '{{ session()->has('success') ? 'Notifikasi' : 'Oops...' }}',
                text: '{{ session()->get('success') ?? session()->get('error') }}',
            })
        </script>
    @endif
    <script>
        window.addEventListener('load', function() {
            const modalElement = document.getElementById('notifikasi');
            const modal = new bootstrap.Modal(modalElement);
            modal.show();
        });
    </script>
    @include('auth.scripts.handleAuth')
</body>

</html>
