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
                            <a href="{{ route('panitera.index') }}" class="btn btn-sm btn-secondary">
                                <i class="fa-solid fa-arrow-left"></i>
                                Kembali
                            </a>
                        </div>
                        <div class="card-body">
                            {{-- Store and Update handled by one controller function --}}
                            <form action="{{ route('panitera.store') }}" method="POST" id="panitera-form">
                                @csrf
                                {{-- This hidden id will be used to check if it's an update or store action --}}
                                @if (isset($panitera))
                                    <input type="hidden" name="id" value="{{ Crypt::encrypt($panitera->id) }}">
                                @endif

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="nip">
                                                NIP <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" name="nip" class="form-control" id="nip" placeholder="Contoh: 199001012020011001" required
                                                value="{{ $panitera->nip ?? old('nip') }}">
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="nama">
                                                Nama <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" name="nama" class="form-control" id="nama" placeholder="Contoh: Qori Chairawan" required
                                                value="{{ $panitera->nama ?? old('nama') }}">
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="jabatan">
                                                Jabatan <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" name="jabatan" class="form-control" id="jabatan" placeholder="Contoh: Panitera" required
                                                value="{{ $panitera->jabatan ?? old('jabatan') }}">
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="status">
                                                Status <span class="text-danger">*</span>
                                            </label>
                                            <select name="status" class="form-control" id="status" required>
                                                <option selected disabled>Pilih Status</option>
                                                @foreach (\App\Enum\StatusPaniteraEnum::cases() as $status)
                                                    <option value="{{ $status->value }}" {{ isset($panitera) && $panitera->status == $status->value ? 'selected' : '' }}>
                                                        {{ $status->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="aktif">
                                                Aktif <span class="text-danger">*</span>
                                            </label>
                                            <select name="aktif" class="form-control" id="aktif" required>
                                                <option selected disabled>Pilih Aktif</option>
                                                <option value="1" {{ isset($panitera) && $panitera->aktif == 1 ? 'selected' : '' }}>
                                                    Ya
                                                </option>
                                                <option value="0" {{ isset($panitera) && $panitera->aktif == 0 ? 'selected' : '' }}>
                                                    Tidak
                                                </option>
                                            </select>
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-sm btn-primary" id="submit-button">Simpan</button>
                                <button type="reset" class="btn btn-sm btn-warning">Reset</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @include('admin.layout.content-footer')
    </main>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('panitera-form');
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
                                        let feedback = element.nextElementSibling;
                                        while (feedback && !feedback.classList.contains('invalid-feedback')) {
                                            feedback = feedback.nextElementSibling;
                                        }
                                        if (feedback) {
                                            feedback.textContent = errors[key][0];
                                        }
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
                        confirmButtonColor: '#3085d6',
                        confirmButtonText: 'OK'
                    });

                    window.location.href = "{{ route('panitera.index') }}";

                } catch (error) {
                    console.error('Fetch Error:', error);
                    Swal.fire('Error!', 'Tidak dapat terhubung ke server.', 'error');
                } finally {
                    // Re-enable button if the process was not successful (and no redirect is happening)
                    if (!isSuccess) {
                        submitButton.disabled = false;
                        submitButton.innerHTML = originalButtonHtml;
                    }
                }
            });
        });
    </script>
@endpush
