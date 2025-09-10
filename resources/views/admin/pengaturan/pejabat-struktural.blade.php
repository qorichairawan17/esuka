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
                            Profil Pejabat Struktural
                        </h6>
                    </div>
                    <div class="card-body">
                        <form id="pejabatForm" action="{{ route('pejabat-struktural.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                {{-- Ketua --}}
                                <div class="col-md-6 mb-3">
                                    <label for="ketua">
                                        Ketua <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" name="ketua" class="form-control" id="ketua" placeholder="Nama Ketua" value="{{ old('ketua', $pejabat->ketua ?? '') }}" required>
                                    <div class="invalid-feedback" id="ketua-error"></div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="foto_ketua">
                                        Foto Ketua
                                        @if (empty($pejabat->foto_ketua))
                                            <span class="text-danger">*</span>
                                        @endif
                                    </label>
                                    <input type="file" name="foto_ketua" class="form-control" id="foto_ketua" accept="image/png, image/jpeg, image/gif">
                                    <div class="invalid-feedback" id="foto_ketua-error"></div>
                                    @if (!empty($pejabat->foto_ketua))
                                        <img src="{{ asset('storage/' . $pejabat->foto_ketua) }}" class="img-fluid img-thumbnail mt-3" alt="Foto Ketua" style="max-width: 150px;">
                                    @endif
                                </div>

                                {{-- Wakil Ketua --}}
                                <div class="col-md-6 mb-3">
                                    <label for="wakil_ketua">
                                        Wakil Ketua <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" name="wakil_ketua" class="form-control" id="wakil_ketua" placeholder="Nama Wakil Ketua"
                                        value="{{ old('wakil_ketua', $pejabat->wakil_ketua ?? '') }}" required>
                                    <div class="invalid-feedback" id="wakil_ketua-error"></div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="foto_wakil_ketua">
                                        Foto Wakil Ketua
                                        @if (empty($pejabat->foto_wakil_ketua))
                                            <span class="text-danger">*</span>
                                        @endif
                                    </label>
                                    <input type="file" name="foto_wakil_ketua" class="form-control" id="foto_wakil_ketua" accept="image/png, image/jpeg, image/gif">
                                    <div class="invalid-feedback" id="foto_wakil_ketua-error"></div>
                                    @if (!empty($pejabat->foto_wakil_ketua))
                                        <img src="{{ asset('storage/' . $pejabat->foto_wakil_ketua) }}" class="img-fluid img-thumbnail mt-3" alt="Foto Wakil Ketua" style="max-width: 150px;">
                                    @endif
                                </div>

                                {{-- Panitera --}}
                                <div class="col-md-6 mb-3">
                                    <label for="panitera">
                                        Panitera <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" name="panitera" class="form-control" id="panitera" placeholder="Nama Panitera" value="{{ old('panitera', $pejabat->panitera ?? '') }}" required>
                                    <div class="invalid-feedback" id="panitera-error"></div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="foto_panitera">
                                        Foto Panitera
                                        @if (empty($pejabat->foto_panitera))
                                            <span class="text-danger">*</span>
                                        @endif
                                    </label>
                                    <input type="file" name="foto_panitera" class="form-control" id="foto_panitera" accept="image/png, image/jpeg, image/gif">
                                    <div class="invalid-feedback" id="foto_panitera-error"></div>
                                    @if (!empty($pejabat->foto_panitera))
                                        <img src="{{ asset('storage/' . $pejabat->foto_panitera) }}" class="img-fluid img-thumbnail mt-3" alt="Foto Panitera" style="max-width: 150px;">
                                    @endif
                                </div>

                                {{-- Sekretaris --}}
                                <div class="col-md-6 mb-3">
                                    <label for="sekretaris">
                                        Sekretaris <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" name="sekretaris" class="form-control" id="sekretaris" placeholder="Nama Sekretaris" value="{{ old('sekretaris', $pejabat->sekretaris ?? '') }}"
                                        required>
                                    <div class="invalid-feedback" id="sekretaris-error"></div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="foto_sekretaris">
                                        Foto Sekretaris
                                        @if (empty($pejabat->foto_sekretaris))
                                            <span class="text-danger">*</span>
                                        @endif
                                    </label>
                                    <input type="file" name="foto_sekretaris" class="form-control" id="foto_sekretaris" accept="image/png, image/jpeg, image/gif">
                                    <div class="invalid-feedback" id="foto_sekretaris-error"></div>
                                    @if (!empty($pejabat->foto_sekretaris))
                                        <img src="{{ asset('storage/' . $pejabat->foto_sekretaris) }}" class="img-fluid img-thumbnail mt-3" alt="Foto Sekretaris" style="max-width: 150px;">
                                    @endif
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
        <!-- End -->
    </main>

@endsection

@push('scripts')
    <script>
        document.getElementById('pejabatForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            const form = e.target;
            const formData = new FormData(form);
            const submitButton = document.getElementById('submitButton');
            const spinner = submitButton.querySelector('.spinner-border');

            // Clear previous errors
            form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
            form.querySelectorAll('.invalid-feedback').forEach(el => el.textContent = '');

            // Show spinner and disable button
            spinner.style.display = 'inline-block';
            submitButton.disabled = true;

            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                            .getAttribute('content'),
                        'Accept': 'application/json',
                    },
                });

                const data = await response.json();

                if (!response.ok) {
                    if (response.status === 422) {
                        // Handle validation errors
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
                        // Handle other server errors
                        throw new Error(data.message || 'Terjadi kesalahan pada server.');
                    }
                } else {
                    // Handle success
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: data.message,
                            timer: 2000,
                            showConfirmButton: false,
                            timerProgressBar: true,
                        }).then(() => {
                            window.location.reload(); // Reload page to show new data
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
                // Hide spinner and re-enable button
                spinner.style.display = 'none';
                submitButton.disabled = false;
            }
        });
    </script>
@endpush
