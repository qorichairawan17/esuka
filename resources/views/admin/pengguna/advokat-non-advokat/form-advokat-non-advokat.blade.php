@extends('admin.layout.body')
@section('title', $title)
@section('content')
    <!-- Start Page Content -->
    <main class="page-content bg-light">

        @include('admin.component.top-header')

        <div class="container-fluid">
            <div class="layout-specing">

                @include('admin.component.breadcumb')

                <div class="mt-4">
                    <div class="card shadow">
                        <div class="card-header d-flex flex-wrap align-items-center justify-content-between">
                            <h6 class="card-title mb-0">{{ $pageTitle }}</h6>
                            <a href="{{ route('panitera.index') }}" class="btn btn-sm btn-secondary">Kembali</a>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('advokat.store') }}" method="POST" id="advokat-form" enctype="multipart/form-data">
                                @csrf
                                @if (isset($user))
                                    <input type="hidden" name="id" value="{{ Crypt::encrypt($user->id) }}">
                                @endif
                                <div class="form-group mb-3">
                                    <label for="nama">
                                        Nama <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" name="nama" class="form-control" id="nama" placeholder="Ahmad Naufal" required value="{{ old('nama', $user->name ?? '') }}">
                                    <div class="invalid-feedback"></div>
                                </div>
                                <div class="form-group mb-3">
                                    <label for="email">
                                        Email <span class="text-danger">*</span>
                                    </label>
                                    <input type="email" name="email" class="form-control" id="email" placeholder="naufal@example.com" required value="{{ old('email', $user->email ?? '') }}">
                                    <div class="invalid-feedback"></div>
                                </div>
                                <div class="form-group mb-3">
                                    <label for="password">
                                        Password
                                        @if (!isset($user))
                                            <span class="text-danger">*</span>
                                        @else
                                            <small class="text-muted">(Kosongkan jika tidak ingin diubah)</small>
                                        @endif
                                    </label>
                                    <input type="password" name="password" class="form-control" id="password" placeholder="***********" {{ isset($user) ? '' : 'required' }}>
                                    <div class="invalid-feedback"></div>
                                </div>
                                <div class="form-group mb-3">
                                    <label for="kontak">
                                        Kontak <span class="text-danger">*</span>
                                    </label>
                                    <input type="number" name="kontak" class="form-control" id="kontak" placeholder="6288088776767" required value="{{ old('kontak', $user->profile->kontak ?? '') }}">
                                    <div class="invalid-feedback"></div>
                                </div>
                                <div class="form-group mb-3">
                                    <label for="foto">
                                        Foto
                                        @if (!isset($user))
                                            <span class="text-danger">*</span>
                                        @else
                                            <small class="text-muted">(Kosongkan jika tidak ingin diubah)</small>
                                        @endif
                                    </label>
                                    <input type="file" name="foto" class="form-control" id="foto">
                                    @if (isset($user) && $user->profile && $user->profile->foto)
                                        <img src="{{ asset('storage/' . $user->profile->foto) }}" alt="Foto Profil" class="img-thumbnail mt-2" style="max-width: 150px;">
                                    @endif
                                    <div class="invalid-feedback"></div>
                                </div>
                                <div class="form-group mb-3">
                                    <label for="aktif">
                                        Status Akun <span class="text-danger">*</span>
                                    </label>
                                    <select name="aktif" class="form-control" id="aktif" required>
                                        <option selected disabled>Pilih Status</option>
                                        <option value="0" {{ old('aktif', $user->block ?? '') == '0' ? 'selected' : '' }}>Aktif</option>
                                        <option value="1" {{ old('aktif', $user->block ?? '') == '1' ? 'selected' : '' }}>Diblokir</option>
                                    </select>
                                    <div class="invalid-feedback"></div>
                                </div>
                                <button type="submit" class="btn btn-primary btn-sm" id="submit-button">Simpan</button>
                                <button type="reset" class="btn btn-warning btn-sm">Reset</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div><!--end container-->

        @include('admin.layout.content-footer')
        <!-- End -->
    </main>
    <!--End page-content" -->
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('advokat-form');
            const submitButton = document.getElementById('submit-button');
            const originalButtonHtml = submitButton.innerHTML;

            form.addEventListener('submit', async function(e) {
                e.preventDefault();

                // Disable button and show spinner
                submitButton.disabled = true;
                submitButton.innerHTML = `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menyimpan...`;

                // Clear previous errors
                document.querySelectorAll('.form-control').forEach(el => el.classList.remove('is-invalid'));
                document.querySelectorAll('.invalid-feedback').forEach(el => el.textContent = '');

                const formData = new FormData(form);
                const url = form.getAttribute('action');
                const method = form.getAttribute('method');
                let isSuccess = false;

                try {
                    const response = await fetch(url, {
                        method: method,
                        body: formData,
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                        }
                    });

                    const result = await response.json();

                    if (!response.ok) {
                        if (response.status === 422) { // Validation error
                            const errors = result.errors;
                            for (const key in errors) {
                                if (Object.hasOwnProperty.call(errors, key)) {
                                    const element = document.getElementById(key);
                                    if (element) {
                                        element.classList.add('is-invalid');
                                        element.nextElementSibling.textContent = errors[key][0];
                                    }
                                }
                            }
                            Swal.fire('Gagal!', 'Silakan periksa kembali isian Anda.', 'error');
                        } else {
                            Swal.fire('Error!', result.message || 'Terjadi kesalahan pada server.', 'error');
                        }
                        return;
                    }

                    // Handle success
                    isSuccess = true;
                    await Swal.fire({
                        title: 'Berhasil!',
                        text: result.message,
                        icon: 'success',
                        confirmButtonText: 'OK'
                    });

                    window.location.href = "{{ route('advokat.index') }}";

                } catch (error) {
                    console.error('Fetch Error:', error);
                    Swal.fire('Error!', 'Tidak dapat terhubung ke server.', 'error');
                } finally {
                    if (!isSuccess) {
                        submitButton.disabled = false;
                        submitButton.innerHTML = originalButtonHtml;
                    }
                }
            });
        });
    </script>
@endpush
