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
                            Pembayaran & PNBP (Penerimaan Negara Bukan Pajak)
                        </h6>
                    </div>
                    <div class="card-body">
                        <form id="pembayaranForm" action="{{ route('pembayaran.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="form-group mb-3">
                                <label for="namaBank">
                                    Bank Rekening <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="namaBank" class="form-control" id="namaBank" placeholder="Bank BTN" value="{{ old('namaBank', $pembayaran->nama_bank ?? '') }}" required>
                                <div class="invalid-feedback" id="namaBank-error"></div>
                            </div>
                            <div class="form-group mb-3">
                                <label for="nomorRekening">
                                    Nomor Rekening <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="nomorRekening" class="form-control" id="nomorRekening" placeholder="0000-0000-0000-0000"
                                    value="{{ old('nomorRekening', $pembayaran->nomor_rekening ?? '') }}" required>
                                <div class="invalid-feedback" id="nomorRekening-error"></div>
                            </div>
                            <div class="form-group mb-3">
                                <label for="logoBank">
                                    Logo Bank
                                    @if (empty($pembayaran->logo_bank))
                                        <span class="text-danger">*</span>
                                    @endif
                                </label>
                                <input type="file" name="logoBank" class="form-control" id="logoBank" accept="image/png, image/jpeg, image/gif">
                                <div class="invalid-feedback" id="logoBank-error"></div>
                                @if (!empty($pembayaran->logo_bank))
                                    <img src="{{ asset('storage/' . $pembayaran->logo_bank) }}" class="img-fluid img-thumbnail mt-3" alt="Logo Bank" style="max-width: 150px;">
                                @endif
                            </div>
                            <div class="form-group mb-3">
                                <label for="qris">
                                    QRIS <sub>(Opsional)</sub>
                                </label>
                                <input type="file" name="qris" class="form-control" id="qris" accept="image/png, image/jpeg, image/gif">
                                <div class="invalid-feedback" id="qris-error"></div>
                                @if (!empty($pembayaran->qris))
                                    <img src="{{ asset('storage/' . $pembayaran->qris) }}" class="img-fluid img-thumbnail mt-3" alt="QRIS" style="max-width: 150px;">
                                @endif
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
        document.getElementById('pembayaranForm').addEventListener('submit', async function(e) {
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
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content'),
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
                            timer: 2000,
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
