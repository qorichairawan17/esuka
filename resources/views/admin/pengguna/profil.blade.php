@extends('admin.layout.body')
@section('title', $title)
@section('content')
    <!-- Start Page Content -->
    <main class="page-content bg-light">

        @include('admin.component.top-header')

        <div class="container-fluid">
            <div class="layout-specing">

                @include('admin.component.breadcumb')

                <div class="row mt-4">
                    <div class="row">
                        <div class="col-lg-4">
                            <div class="card shadow mb-3">
                                <div class="card-body text-center">
                                    <img class="avatar float-md-left avatar-medium rounded-circle shadow me-md-4"
                                        src="{{ $user->profile->foto ? asset('storage/' . $user->profile->foto) : asset('assets/images/user/user-none.png') }}" alt="profile">
                                    <h6 class="mt-3 mb-0">{{ $user->name }}</h6>
                                    <p class="m-0" style="font-size: 12px;">Terdaftar pada : {{ \Carbon\Carbon::parse($user->created_at)->isoFormat('DD MMMM YYYY') }}</p>
                                    <button class="btn btn-sm btn-success w-100 mt-2" data-bs-toggle="modal" data-bs-target="#uploadFoto">
                                        Ubah Foto
                                    </button>

                                    @if (Auth::user()->role == \App\Enum\RoleEnum::User->value)
                                        <p class="m-0 mt-2 border-top pt-2" style="font-size: 13px;">
                                            Hapus akun beserta seluruh informasi yang tersimpan di aplikasi
                                        </p>
                                        <button class="btn btn-sm btn-soft-danger w-100 mt-2" id="btn-delete-account">
                                            Hapus Akun
                                        </button>
                                    @endif

                                </div>
                            </div>

                            <div class="card shadow mb-3">
                                <div class="card-body">
                                    <div class="flex flex-wrap flex-row gap-3">
                                        <h6>Tautkan Akun Dengan Google</h6>
                                        @if (Auth::user()->google_id != null)
                                            <form id="unlink-google-form" action="{{ route('google.unlink') }}" method="POST" class="w-100">
                                                @csrf
                                                <button type="submit" class="btn btn-success btn-sm w-100">
                                                    <i class="uil uil-google"></i> Akun Tertaut
                                                </button>
                                                <small class="text-muted d-block mt-1">Klik untuk memutuskan tautan.</small>
                                            </form>
                                        @else
                                            <a href="{{ route('google.redirect', ['action' => 'link']) }}" class="btn btn-danger btn-sm w-100"><i class="uil uil-google"></i> Tautkan dengan Google</a>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="card shadow mb-3">
                                <div class="card-body">
                                    <div class="flex flex-wrap flex-row gap-3">
                                        <h6>Layanan Kontak Informasi</h6>
                                        <ul class="list-unstyled ms-auto">
                                            <li><i class="uil uil-phone me-1"></i> {{ $infoApp->kontak }}</li>
                                            <li><i class="uil uil-envelope me-1"></i> {{ $infoApp->email }}</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <div class="card shadow mb-3">
                                <div class="card-body">
                                    <div class="flex flex-wrap flex-row gap-3">
                                        <h6>Panduan Penggunaan</h6>
                                        <a href="{{ route('panduan.show') }}" target="_blank"><i class="uil uil-external-link-alt"></i> Klik Disini</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-8">
                            <div class="card shadow">
                                <div class="card-body">
                                    @if ($user->profile_status == 1)
                                        <div class="alert bg-soft-success fw-medium" role="alert" style="text-align:justify;">
                                            <i class="uil uil-info-circle fs-5 align-middle me-1"></i>
                                            Profil Kamu telah lengkap, kamu dapat melakukan pengajuan surat kuasa.
                                        </div>
                                    @else
                                        <div class="alert bg-soft-danger fw-medium" role="alert" style="text-align:justify;">
                                            <i class="uil uil-info-circle fs-5 align-middle me-1"></i>
                                            Profil Kamu belum lengkap, silahkan lengkapi terlebih dahulu untuk melanjutkan proses pengajuan surat kuasa.
                                        </div>
                                    @endif
                                    <ul class="nav nav-pills mb-3 gap-3" id="pills-tab" role="tablist">
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link active" id="pills-profil-tab" data-bs-toggle="pill" data-bs-target="#pills-profil" type="button" role="tab"
                                                aria-controls="pills-profil" aria-selected="true">Profil Saya</button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link" id="pills-password-tab" data-bs-toggle="pill" data-bs-target="#pills-password" type="button" role="tab"
                                                aria-controls="pills-password" aria-selected="false">Password</button>
                                        </li>
                                    </ul>
                                    <div class="tab-content" id="pills-tabContent">
                                        <div class="tab-pane fade show active" id="pills-profil" role="tabpanel" aria-labelledby="pills-profil-tab" tabindex="0">
                                            <form id="updateProfileForm" method="post" action="{{ route('profile.update') }}">
                                                @csrf
                                                <div class="form-group">
                                                    <div class="row">
                                                        <div class="col-md-6 col-sm-12 mb-3">
                                                            <label for="namaDepan">
                                                                Nama Depan <span class="text-danger">*</span>
                                                            </label>
                                                            <input type="text" name="namaDepan" class="form-control" id="namaDepan" required value="{{ $user->profile->nama_depan ?? '' }}"
                                                                placeholder="Qori">
                                                            <div class="invalid-feedback" id="namaDepan-error"></div>
                                                        </div>
                                                        <div class="col-md-6 col-sm-12 mb-3">
                                                            <label for="namaBelakang">
                                                                Nama Belakang
                                                            </label>
                                                            <input type="text" name="namaBelakang" class="form-control" id="namaBelakang" value="{{ $user->profile->nama_belakang ?? '' }}"
                                                                placeholder="Chairawan">
                                                            <div class="invalid-feedback" id="namaBelakang-error"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group mb-3">
                                                    <label for="email">
                                                        Email <span class="text-danger">*</span>
                                                    </label>
                                                    <input type="email" name="email" class="form-control" id="email" required value="{{ $user->email ?? '' }}"
                                                        placeholder="qori@example.com">
                                                    <div class="invalid-feedback" id="email-error"></div>
                                                </div>
                                                <div class="form-group mb-3">
                                                    <label for="kontak">
                                                        Kontak Hp/Telepon <span class="text-danger">*</span>
                                                    </label>
                                                    <input type="number" name="kontak" class="form-control" id="kontak" required value="{{ $user->profile->kontak ?? '' }}"
                                                        placeholder="0812341448">
                                                    <div class="invalid-feedback" id="kontak-error"></div>
                                                </div>
                                                <div class="form-group mb-3">
                                                    <label for="tanggalLahir">
                                                        Tanggal Lahir <span class="text-danger">*</span>
                                                    </label>
                                                    <input type="text" id="tanggalLahir" name="tanggalLahir" class="form-control" required
                                                        value="{{ $user->profile->tanggal_lahir ? \Carbon\Carbon::parse($user->profile->tanggal_lahir)->format('d-m-Y') : '' }}"
                                                        placeholder="00-00-0000">
                                                    <div class="invalid-feedback" id="tanggalLahir-error"></div>
                                                </div>
                                                <div class="form-group mb-3">
                                                    <label>
                                                        Jenis Kelamin <span class="text-danger">*</span>
                                                    </label>
                                                    <div class="d-flex flex-wrap flex-row gap-3">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="radio" name="jenisKelamin" id="lakiLaki" value="Laki-Laki"
                                                                {{ ($user->profile->jenis_kelamin ?? null) == 'Laki-Laki' ? 'checked' : '' }}>
                                                            <label class="form-check-label" for="lakiLaki">
                                                                Laki-laki
                                                            </label>
                                                        </div>
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="radio" name="jenisKelamin" id="perempuan" value="Perempuan"
                                                                {{ ($user->profile->jenis_kelamin ?? null) == 'Perempuan' ? 'checked' : '' }}>
                                                            <label class="form-check-label" for="perempuan">
                                                                Perempuan
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="invalid-feedback d-block" id="jenisKelamin-error"></div>
                                                </div>
                                                <div class="form-group mb-3">
                                                    <label for="alamat">
                                                        Alamat <span class="text-danger">*</span>
                                                    </label>
                                                    <textarea name="alamat" id="alamat" class="form-control" required placeholder="Jalan Jenderal Sudirman 58 Lubuk Pakam">{{ $user->profile->alamat ?? '' }}</textarea>
                                                    <div class="invalid-feedback" id="alamat-error"></div>
                                                </div>
                                                <button class="btn btn-sm btn-primary" type="submit" id="btn-save-profile">Simpan</button>
                                            </form>
                                        </div>
                                        <div class="tab-pane fade" id="pills-password" role="tabpanel" aria-labelledby="pills-password-tab" tabindex="0">
                                            <div class="alert bg-soft-warning fw-medium" role="alert">
                                                <span class="fw-bold">
                                                    <i class="uil uil-info-circle fs-5 align-middle me-1"></i>
                                                    Perhatian !
                                                </span>
                                                <p style="text-align: justify;">
                                                    Gantilah password akun kamu secara berkala, untuk meningkatkan keamanan
                                                    akun kamu.
                                                    <b>
                                                        Jangan memberikan akses akun kamu kepada siapapun, untuk menghindari
                                                        penyalahgunaan otoritas dan sumber daya pada sistem aplikasi ini!
                                                    </b>
                                                </p>
                                            </div>
                                            <form id="updatePasswordForm" method="post" action="{{ route('profile.updatePassword') }}">
                                                @csrf
                                                <div class="form-group mb-3">
                                                    <label for="passwordLama">
                                                        Password Lama <span class="text-danger">*</span>
                                                    </label>
                                                    <input type="password" name="passwordLama" class="form-control" id="passwordLama" required placeholder="********">
                                                    <div class="invalid-feedback" id="passwordLama-error"></div>
                                                </div>

                                                <div class="form-group mb-3">
                                                    <label for="passwordBaru">
                                                        Password Baru <span class="text-danger">*</span>
                                                    </label>
                                                    <input type="password" name="passwordBaru" class="form-control" id="passwordBaru" required placeholder="********">
                                                    <div class="invalid-feedback" id="passwordBaru-error"></div>
                                                </div>
                                                <div class="form-group mb-3">
                                                    <label for="passwordBaru_confirmation">
                                                        Konfirmasi Password Baru <span class="text-danger">*</span>
                                                    </label>
                                                    <input type="password" name="passwordBaru_confirmation" class="form-control" id="passwordBaru_confirmation" required placeholder="********">
                                                </div>
                                                <button class="btn btn-sm btn-primary" type="submit" id="btn-save-password">
                                                    Simpan
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal Content Start -->
                <div class="modal fade" id="uploadFoto" tabindex="-1" aria-labelledby="uploadFoto-title" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content rounded shadow border-0">
                            <div class="modal-header border-bottom">
                                <h5 class="modal-title" id="uploadFoto-title">Unggah Foto</h5>
                                <button type="button" class="btn btn-icon btn-close" data-bs-dismiss="modal" id="close-modal">
                                    <i class="uil uil-times fs-4 text-dark"></i>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="form-group">
                                    <input type="file" name="foto" class="form-control" id="foto" required accept="image/png, image/jpeg, image/jpg">
                                    <div class="invalid-feedback" id="foto-error"></div>
                                    <small class="text-muted">* Tipe file: .jpg, .jpeg, .png. Maks 2MB.</small>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-primary btn-sm" id="btn-save-photo">Simpan</button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Modal Content End -->
            </div>
        </div><!--end container-->

        @include('admin.layout.content-footer')
        <!-- End -->
    </main>
    <!--End page-content" -->
@endsection
@push('scripts')
    <script>
        $("#tanggalLahir").datepicker({
            dateFormat: 'dd-mm-yy',
            autoClose: true
        });

        $('#uploadFoto').on('hidden.bs.modal', function() {
            // Reset file input and remove validation errors when modal is closed
            const fotoInput = $('#foto');
            fotoInput.val(''); // Clear the file input
            fotoInput.removeClass('is-invalid');
            $('#foto-error').text('');
        });

        $(document).ready(function() {
            $('#btn-save-photo').on('click', function() {
                const button = $(this);
                const originalButtonText = button.html();
                const fotoInput = $('#foto');
                const foto = $('#foto')[0].files[0];
                const errorDiv = $('#foto-error');

                // Clear previous errors
                fotoInput.removeClass('is-invalid');
                errorDiv.text('');

                if (!foto) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Silahkan pilih foto terlebih dahulu!',
                    });
                    return;
                }

                button.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menyimpan...').prop('disabled', true);

                const formData = new FormData();
                formData.append('foto', foto);

                fetch("{{ route('profile.updatePhoto') }}", {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                        },
                        body: formData
                    })
                    .then(response => response.json().then(data => ({
                        status: response.status,
                        body: data
                    })))
                    .then(({
                        status,
                        body
                    }) => {
                        if (status === 422) {
                            if (body.errors && body.errors.foto) {
                                fotoInput.addClass('is-invalid');
                                errorDiv.text(body.errors.foto[0]);
                            }
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal Unggah',
                                text: body.message || 'Periksa kembali file Anda.',
                            });
                        } else if (status >= 200 && status < 300) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: body.message,
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Terjadi Kesalahan',
                                text: body.message || 'Tidak dapat memproses permintaan Anda.',
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Terjadi kesalahan jaringan. Silakan coba lagi.',
                        });
                    })
                    .finally(() => {
                        // Re-enable button if the request failed
                        const swalIcon = Swal.getIcon();
                        if (!swalIcon || swalIcon !== 'success') {
                            button.html(originalButtonText).prop('disabled', false);
                        }
                    });
            });

            $('#updatePasswordForm').on('submit', function(e) {
                e.preventDefault();

                // Clear previous errors
                $('.form-control').removeClass('is-invalid');
                $('.invalid-feedback').text('');

                const form = $(this);
                const formData = new FormData(this);
                const button = $('#btn-save-password');
                const originalButtonText = button.html();

                button.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menyimpan...').prop('disabled', true);

                fetch(form.attr('action'), {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                        },
                        body: formData
                    })
                    .then(response => response.json().then(data => ({
                        status: response.status,
                        body: data
                    })))
                    .then(({
                        status,
                        body
                    }) => {
                        if (status === 422) {
                            $.each(body.errors, function(key, value) {
                                $('#' + key).addClass('is-invalid');
                                $('#' + key + '-error').text(value[0]);
                            });
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: 'Silakan periksa kembali isian Anda.',
                            });
                        } else if (status >= 200 && status < 300) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: body.message,
                            });
                            form[0].reset();
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Terjadi Kesalahan',
                                text: body.message || 'Tidak dapat memproses permintaan Anda.',
                            });
                        }
                    }).catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Terjadi kesalahan jaringan. Silakan coba lagi.',
                        });
                    }).finally(() => {
                        button.html(originalButtonText).prop('disabled', false);
                    });
            });

            $('#updateProfileForm').on('submit', function(e) {
                e.preventDefault();

                // Clear previous errors
                $('#updateProfileForm .form-control').removeClass('is-invalid');
                $('#updateProfileForm .invalid-feedback').text('');

                const form = $(this);
                const formData = new FormData(this);
                const button = $('#btn-save-profile');
                const originalButtonText = button.html();

                button.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menyimpan...').prop('disabled', true);

                fetch(form.attr('action'), {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                        },
                        body: formData
                    })
                    .then(response => response.json().then(data => ({
                        status: response.status,
                        body: data
                    })))
                    .then(({
                        status,
                        body
                    }) => {
                        if (status === 422) {
                            $.each(body.errors, function(key, value) {
                                const el = $('#' + key);
                                el.addClass('is-invalid');
                                $('#' + key + '-error').text(value[0]);
                            });
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: 'Silakan periksa kembali isian Anda.',
                            });
                        } else if (status >= 200 && status < 300) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: body.message,
                                timer: 2000,
                                showConfirmButton: false,
                                timerProgressBar: true
                            }).then(() => {
                                window.location.reload(); // Reload page to show new data
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Terjadi Kesalahan',
                                text: body.message || 'Tidak dapat memproses permintaan Anda.',
                            });
                        }
                    }).catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Terjadi kesalahan jaringan. Silakan coba lagi.',
                        });
                    }).finally(() => {
                        button.html(originalButtonText).prop('disabled', false);
                    });
            });

            $('#btn-delete-account').on('click', function(e) {
                e.preventDefault();

                Swal.fire({
                    title: 'Apakah Anda Yakin?',
                    html: `
                        <p class="text-danger fw-bold">Tindakan ini tidak dapat diurungkan!</p>
                        <p>Seluruh data Anda, termasuk riwayat pendaftaran surat kuasa dan file terkait, akan dihapus secara permanen.</p>
                        <p class="mt-3">Untuk melanjutkan, ketik "<b class="text-danger">hapus akun saya</b>" di bawah ini:</p>
                        <input type="text" id="confirmation-text" class="form-control" placeholder="hapus akun saya">
                    `,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, Hapus Akun Saya!',
                    cancelButtonText: 'Batal',
                    preConfirm: () => {
                        const confirmationText = Swal.getPopup().querySelector('#confirmation-text').value;
                        if (confirmationText.toLowerCase() !== 'hapus akun saya') {
                            Swal.showValidationMessage(`Silakan ketik "hapus akun saya" untuk konfirmasi.`);
                            return false;
                        }
                        return true;
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Show loading state
                        Swal.fire({
                            title: 'Menghapus Akun...',
                            text: 'Mohon tunggu sebentar, proses ini akan menghapus semua data Anda.',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        fetch("{{ route('profile.destroy') }}", {
                                method: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Accept': 'application/json',
                                }
                            })
                            .then(response => response.json().then(data => ({
                                status: response.status,
                                body: data
                            })))
                            .then(({
                                status,
                                body
                            }) => {
                                if (status === 200) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Berhasil!',
                                        text: body.message,
                                    }).then(() => {
                                        // Redirect to login page after deletion
                                        window.location.href = "{{ route('app.signin') }}";
                                    });
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Gagal!',
                                        text: body.message || 'Terjadi kesalahan saat menghapus akun.',
                                    });
                                }
                            })
                    }
                });
            });

            $('#unlink-google-form').on('submit', function(e) {
                e.preventDefault();
                const form = this;

                Swal.fire({
                    title: 'Putuskan Tautan Akun Google?',
                    text: "Anda masih bisa login menggunakan email dan password Anda. Anda dapat menautkannya kembali kapan saja.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, putuskan!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Memproses...',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });
                        // Submit the form to the backend
                        form.submit();
                    }
                })
            });

            // Display success/error messages from session
            @if (session('success'))
                Swal.fire('Berhasil!', '{{ session('success') }}', 'success');
            @endif

            @if (session('error'))
                Swal.fire('Gagal!', '{{ session('error') }}', 'error');
            @endif
        });
    </script>
@endpush
