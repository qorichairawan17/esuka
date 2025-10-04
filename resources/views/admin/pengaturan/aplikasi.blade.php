@extends('admin.layout.body')
@section('title', $title)
@section('content')
    <!-- Start Page Content -->
    <main class="page-content bg-light">

        @include('admin.component.top-header')

        <div class="container-fluid">
            <div class="layout-specing">

                @include('admin.component.breadcumb')

                <div class="card shadow mt-4">
                    <div class="card-header">
                        <h6 class="card-title mb-0">
                            Pengaturan Aplikasi
                        </h6>
                    </div>
                    <div class="card-body">
                        <form id="aplikasiForm" action="{{ route('aplikasi.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-lg-6 col-md-12 col-sm-12">
                                    <div class="form-group mb-3">
                                        <label for="pengadilanTinggi">
                                            Pengadilan Tingkat Banding <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" name="pengadilanTinggi" class="form-control" id="pengadilanTinggi" placeholder="Pengadilan Tinggi Medan"
                                            value="{{ old('pengadilanTinggi', $infoApp->pengadilan_tinggi ?? '') }}" required>
                                        <div class="invalid-feedback" id="pengadilanTinggi-error"></div>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="pengadilanNegeri">
                                            Pengadilan Tingkat Pertama <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" name="pengadilanNegeri" class="form-control" id="pengadilanNegeri" placeholder="Pengadilan Negeri Lubuk Pakam"
                                            value="{{ old('pengadilanNegeri', $infoApp->pengadilan_negeri ?? '') }}" required>
                                        <div class="invalid-feedback" id="pengadilanNegeri-error"></div>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="kodeDipa">
                                            Kode DIPA <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" name="kodeDipa" class="form-control" id="kodeDipa" placeholder="400395" value="{{ old('kodeDipa', $infoApp->kode_dipa ?? '') }}" required>
                                        <div class="invalid-feedback" id="kodeDipa-error"></div>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="kodeSuratKuasa">
                                            Kode Surat Kuasa <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" name="kodeSuratKuasa" class="form-control" id="kodeSuratKuasa" placeholder="400395"
                                            value="{{ old('kodeSuratKuasa', $infoApp->kode_surat_kuasa ?? '') }}" required>
                                        <div class="invalid-feedback" id="kodeSuratKuasa-error"></div>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="provinsi">
                                            Provinsi <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" name="provinsi" class="form-control" id="provinsi" placeholder="Sumatera Utara" value="{{ old('provinsi', $infoApp->provinsi ?? '') }}"
                                            required>
                                        <div class="invalid-feedback" id="provinsi-error"></div>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="kabupaten">
                                            Kabupaten/ Kota <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" name="kabupaten" class="form-control" id="kabupaten" placeholder="Deli Serdang" value="{{ old('kabupaten', $infoApp->kabupaten ?? '') }}"
                                            required>
                                        <div class="invalid-feedback" id="kabupaten-error"></div>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="kodePos">
                                            Kode Pos <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" name="kodePos" class="form-control" id="kodePos" placeholder="20512" value="{{ old('kodePos', $infoApp->kode_pos ?? '') }}" required>
                                        <div class="invalid-feedback" id="kodePos-error"></div>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="alamat">
                                            Alamat <span class="text-danger">*</span>
                                        </label>
                                        <textarea class="form-control" style="height: 128px" name="alamat" id="alamat" required placeholder="Jln. Jenderal Sudirman">{{ old('alamat', $infoApp->alamat ?? '') }}</textarea>
                                        <div class="invalid-feedback" id="alamat-error"></div>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-12 col-sm-12">
                                    <div class="form-group mb-3">
                                        <label for="website">
                                            Website <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" name="website" class="form-control" id="website" placeholder="https://website.go.id"
                                            value="{{ old('website', $infoApp->website ?? '') }}" required>
                                        <div class="invalid-feedback" id="website-error"></div>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="facebook">
                                            Facebook <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" name="facebook" class="form-control" id="facebook" placeholder="https://facebook.com/user"
                                            value="{{ old('facebook', $infoApp->facebook ?? '') }}" required>
                                        <div class="invalid-feedback" id="facebook-error"></div>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="instagram">
                                            Instagram <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" name="instagram" class="form-control" id="instagram" placeholder="https://instagram.com/user"
                                            value="{{ old('instagram', $infoApp->instagram ?? '') }}" required>
                                        <div class="invalid-feedback" id="instagram-error"></div>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="youtube">
                                            Youtube <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" name="youtube" class="form-control" id="youtube" placeholder="https://youtube.com/channel"
                                            value="{{ old('youtube', $infoApp->youtube ?? '') }}" required>
                                        <div class="invalid-feedback" id="youtube-error"></div>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="kontak">
                                            Kontak <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" name="kontak" class="form-control" id="kontak" placeholder="+6288088776767" value="{{ old('kontak', $infoApp->kontak ?? '') }}"
                                            required>
                                        <div class="invalid-feedback" id="kontak-error"></div>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="email">
                                            Email <span class="text-danger">*</span>
                                        </label>
                                        <input type="email" name="email" class="form-control" id="email" placeholder="email@example.com" value="{{ old('email', $infoApp->email ?? '') }}"
                                            required>
                                        <div class="invalid-feedback" id="email-error"></div>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="logo">
                                            Logo @if (empty($infoApp->logo))
                                                <span class="text-danger">*</span>
                                            @endif
                                        </label>
                                        <input type="file" name="logo" class="form-control" id="logo" accept="image/png, image/jpeg, image/gif">
                                        <div class="invalid-feedback" id="logo-error"></div>
                                        @if (!empty($infoApp->logo))
                                            <img src="{{ asset('storage/' . $infoApp->logo) }}" class="img-fluid img-thumbnail mt-3" alt="Logo" style="max-width: 150px;">
                                        @endif
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="maintenance">
                                            Mode Maintenance <span class="text-danger">*</span>
                                        </label>
                                        <div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="maintenance" id="maintenanceOn" value="1"
                                                    {{ old('maintenance', $infoApp->maintenance ?? '0') == '1' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="maintenanceOn">Aktif</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="maintenance" id="maintenanceOff" value="0"
                                                    {{ old('maintenance', $infoApp->maintenance ?? '0') == '0' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="maintenanceOff">Tidak Aktif</label>
                                            </div>
                                        </div>
                                        <div class="invalid-feedback" id="maintenance-error"></div>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" id="submitButton" class="btn btn-primary btn-sm">
                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true" style="display: none;"></span>
                                Simpan
                            </button>
                        </form>

                    </div>
                </div>

            </div>
        </div><!--end container-->

        @include('admin.layout.content-footer')
    </main>
@endsection

@push('scripts')
    <script>
        document.getElementById('aplikasiForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            const form = e.target;
            const formData = new FormData(form);
            const submitButton = document.getElementById('submitButton');
            const spinner = submitButton.querySelector('.spinner-border');

            form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
            form.querySelectorAll('.invalid-feedback').forEach(el => el.textContent = '');

            spinner.style.display = 'inline-block';
            submitButton.disabled = true;

            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                    },
                });

                const data = await response.json();

                if (!response.ok) {
                    if (response.status === 422) {
                        Object.keys(data.errors).forEach(key => {
                            const input = document.getElementById(key);
                            const errorDiv = document.getElementById(`${key}-error`);
                            if (input) {
                                input.classList.add('is-invalid');
                            }
                            if (errorDiv) {
                                errorDiv.textContent = data.errors[key][0];
                            }
                        });
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Harap periksa kembali isian form Anda.',
                        });
                    } else {
                        throw new Error(data.message || 'Terjadi kesalahan pada server.');
                    }
                } else {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: data.message,
                            timer: 1500,
                            showConfirmButton: false,
                            timerProgressBar: true,
                        }).then(() => {
                            window.location.reload();
                        });
                    }
                }
            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: error.message || 'Tidak dapat menyimpan data. Silakan coba lagi.',
                });
            } finally {
                spinner.style.display = 'none';
                submitButton.disabled = false;
            }
        });
    </script>
@endpush
