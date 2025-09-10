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

    <section class="bg-home d-flex align-items-center">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-7 col-md-12 col-sm-12 d-none d-lg-block">
                    <div class="me-lg-5">
                        <img onclick="window.location='{{ route('app.home') }}'" src="{{ asset('icons/horizontal-e-suka.png') }}" class="img-fluid d-block mx-auto" alt="logo">
                    </div>
                </div>
                <div class="col-lg-5 col-md-12 col-sm-12">
                    <div class="card login-page shadow rounded border-0">
                        <div class="card-body">
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
                                                <input type="text" class="form-control ps-5" placeholder="Qori...." id="namaDepan" name="namaDepan" required value="{{ old('namaDepan') }}"
                                                    autocomplete="given-name">
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
                                                <input type="text" class="form-control ps-5" placeholder="Chairawan...." id="namaBelakang" name="namaBelakang" required autocomplete="family-name"
                                                    value="{{ old('namaBelakang') }}">
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
                                                <input type="email" class="form-control ps-5" placeholder="qori@example.com" id="email" name="email" required value="{{ old('email') }}"
                                                    autocomplete="email">
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
                                                <input type="password" class="form-control ps-5" placeholder="**********" id="password" name="password" required autocomplete="new-password">
                                                <small class="text-danger mt-2" id="passwordError"></small>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-lg-12 mb-0">
                                        <p class="forgot-pass mb-0"><a href="{{ route('auth.forgot-password') }}" class="text-dark fw-bold">Lupa Password ?</a></p>
                                        <div class="d-grid mt-3">
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
</body>

</html>
