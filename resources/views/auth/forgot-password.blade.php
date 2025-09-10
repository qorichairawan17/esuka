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

    <div class="back-to-home">
        <a href="{{ route('app.signin') }}" class="back-button btn btn-icon btn-primary"><i data-feather="arrow-left" class="icons"></i></a>
    </div>

    <section class="bg-home bg-circle-gradiant d-flex align-items-center">
        <div class="bg-overlay bg-overlay-white"></div>
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-5 col-md-8">
                    <div class="card shadow rounded border-0">
                        <div class="card-body">
                            @if (isset($token))
                                <h4 class="card-title text-center">Reset Password Baru</h4>
                                <form class="login-form mt-4" id="reset-password-form">
                                    @csrf
                                    <input type="hidden" name="token" value="{{ $token }}">
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <p class="text-muted">Masukkan password baru Anda. Password harus terdiri dari minimal 8 karakter.</p>
                                            <div class="mb-3">
                                                <label class="form-label" for="email">Email <span class="text-danger">*</span></label>
                                                <div class="form-icon position-relative">
                                                    <i data-feather="mail" class="fea icon-sm icons"></i>
                                                    <input type="email" class="form-control ps-5" name="email" id="email" value="{{ $email ?? old('email') }}" readonly>
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label" for="password">Password Baru <span class="text-danger">*</span></label>
                                                <div class="form-icon position-relative">
                                                    <i data-feather="key" class="fea icon-sm icons"></i>
                                                    <input type="password" class="form-control ps-5" placeholder="Password Baru" name="password" id="password" required>
                                                    <small class="text-danger mt-2" id="passwordError"></small>
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label" for="password_confirmation">Konfirmasi Password <span class="text-danger">*</span></label>
                                                <div class="form-icon position-relative">
                                                    <i data-feather="key" class="fea icon-sm icons"></i>
                                                    <input type="password" class="form-control ps-5" placeholder="Konfirmasi Password" name="password_confirmation" id="password_confirmation" required>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-12">
                                            <div class="d-grid">
                                                <button type="submit" class="btn btn-primary">Simpan Password Baru</button>
                                            </div>
                                        </div>
                                        <div class="mx-auto">
                                            <p class="mb-0 mt-3"><small class="text-dark me-2">Ingat password Kamu?</small> <a href="{{ route('app.signin') }}" class="text-dark fw-bold">Masuk</a></p>
                                        </div>
                                    </div>
                                </form>
                            @else
                                <h4 class="card-title text-center">Lupa Password</h4>
                                <form class="login-form mt-4" id="send-link-form">
                                    @csrf
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <p class="text-muted" style="text-align: center; font-size: 14px;">
                                                Silakan masukkan alamat email Kamu. Kamu akan menerima tautan untuk membuat Password baru melalui email.
                                            </p>
                                            <div class="mb-3">
                                                <label class="form-label" for="email">Email <span class="text-danger">*</span></label>
                                                <div class="form-icon position-relative">
                                                    <i data-feather="mail" class="fea icon-sm icons"></i>
                                                    <input type="email" class="form-control ps-5" placeholder="Masukkan Alamat Email Kamu" name="email" id="email" required>
                                                    <small class="text-danger mt-2" id="emailError"></small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-12">
                                            <div class="d-grid">
                                                <button type="submit" class="btn btn-primary">Kirim Tautan Reset</button>
                                            </div>
                                        </div>
                                        <div class="mx-auto">
                                            <p class="mb-0 mt-3"><small class="text-dark me-2">Ingat password Kamu?</small> <a href="{{ route('app.signin') }}" class="text-dark fw-bold">Masuk</a>
                                            </p>
                                        </div>
                                    </div>
                                </form>
                            @endif
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
    @if (session()->has('error'))
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: '{{ session('error') }}',
                showConfirmButton: true,
            });
        </script>
    @endif
    @include('auth.scripts.handleResetPassword')
</body>

</html>
