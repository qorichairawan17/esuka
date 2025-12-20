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
                        <div class="card-body">
                            <div class="alert bg-soft-warning fw-medium" role="alert">
                                <i class="uil uil-info-circle fs-5 align-middle me-1"></i> Harap perhatikan pengisian data
                                dengan teliti dan valid, Apabila ada kekeliruan maka akan ditolak oleh petugas verifikasi
                                !
                            </div>
                            @php
                                $isEditMode = isset($suratKuasa);
                                $formAction = $isEditMode ? route('surat-kuasa.update', ['id' => Crypt::encrypt($suratKuasa->id)]) : route('surat-kuasa.store');
                            @endphp
                            <form id="form-pendaftaran-surat-kuasa" action="{{ $formAction }}" method="post" enctype="multipart/form-data">
                                @method($isEditMode ? 'POST' : 'POST')
                                @csrf

                                <div class="form-group mb-3">
                                    <label for="idDaftar" class="form-label">
                                        ID Pendaftaran <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" readonly class="form-control" id="idDaftar" name="idDaftar" placeholder="ID Pendaftaran" value="{{ $idDaftar }}" required>
                                    <div class="invalid-feedback" id="idDaftar-error"></div>
                                </div>
                                <div class="form-group mb-3">
                                    <label for="perihal" class="form-label">
                                        Perihal <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" id="perihal" name="perihal" placeholder="Perihal Surat Kuasa" value="{{ old('perihal', $suratKuasa->perihal ?? '') }}"
                                        required>
                                    <div class="invalid-feedback" id="perihal-error"></div>
                                </div>
                                <div class="form-group mb-3">
                                    <label for="jenisSurat" class="form-label">
                                        Jenis Surat Kuasa <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select" id="jenisSurat" name="jenisSurat" required>
                                        <option selected disabled>Pilih Jenis Surat Kuasa</option>
                                        @foreach (\App\Enum\JenisSuratEnum::cases() as $jenis)
                                            <option value="{{ $jenis->value }}" @if (old('jenisSurat', $suratKuasa->jenis_surat ?? '') == $jenis->value) selected @endif>{{ $jenis->name }}</option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback" id="jenisSurat-error"></div>
                                    <!-- Hidden input to pass the 'klasifikasi' to the controller -->
                                    <input type="hidden" readonly class="form-control" name="klasifikasi" value="{{ session()->get('klasifikasi') }}">
                                </div>
                                @if (session()->get('klasifikasi') == \App\Enum\SuratKuasaEnum::Advokat->value)
                                    @include('admin.surat-kuasa.form-upload-advokat', ['isEditMode' => $isEditMode, 'suratKuasa' => $suratKuasa ?? null])
                                @elseif (session()->get('klasifikasi') == \App\Enum\SuratKuasaEnum::NonAdvokat->value)
                                    @include('admin.surat-kuasa.form-upload-non-advokat', ['isEditMode' => $isEditMode, 'suratKuasa' => $suratKuasa ?? null])
                                @endif
                                <div class="border-top pt-3">
                                    <label>Pemberi Kuasa <span class="text-danger">*</span></label>
                                    <div class="table-responsive">
                                        <table class="table table-hover table-bordered" id="pemberi-kuasa-table">
                                            <thead>
                                                <tr>
                                                    <td>#</td>
                                                    <td>Nama</td>
                                                    <td>NIK</td>
                                                    <td>Pekerjaan</td>
                                                    <td>Alamat</td>
                                                    <td>Aksi</td>
                                                </tr>
                                            </thead>
                                            <tbody id="pemberi-kuasa-table-body">
                                            </tbody>
                                        </table>
                                    </div>

                                    <button type="button" data-bs-toggle="modal" data-bs-target="#pemberi-surat-kuasa" class="btn btn-sm btn-primary mb-3">
                                        Tambah Pemberi Kuasa
                                    </button>
                                </div>

                                <div class="border-top pt-3">
                                    <label>Penerima Kuasa <span class="text-danger">*</span></label>
                                    <div class="table-responsive">
                                        <table class="table table-hover table-bordered" id="penerima-kuasa-table">
                                            <thead>
                                                <tr>
                                                    <td>#</td>
                                                    <td>Nama</td>
                                                    <td>NIK</td>
                                                    <td>Pekerjaan</td>
                                                    <td>Alamat</td>
                                                    <td>Aksi</td>
                                                </tr>
                                            </thead>
                                            <tbody id="penerima-kuasa-table-body">
                                            </tbody>
                                        </table>
                                    </div>

                                    <button type="button" data-bs-toggle="modal" data-bs-target="#penerima-surat-kuasa" class="btn btn-sm btn-primary mb-3">
                                        Tambah Penerima Kuasa
                                    </button>
                                </div>

                                <div class="mt-3 border-top pt-3">
                                    <a href="{{ route('surat-kuasa.index') }}" class="btn  btn-secondary btn-sm">
                                        <i class="ti ti-arrow-left"></i> Kembali
                                    </a>
                                    <button type="submit" id="btn-submit-pendaftaran" class="btn btn-success btn-sm">
                                        {{ $isEditMode ? 'Update Pendaftaran' : 'Ajukan Pendaftaran' }} <i class="ti ti-arrow-right"></i>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div><!--end container-->

        @include('admin.layout.content-footer')
        <!-- End -->
        @include('admin.surat-kuasa.component.modal-pihak-pemberi-kuasa')
        @include('admin.surat-kuasa.component.modal-pihak-penerima-kuasa')
    </main>
    <!--End page-content" -->
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            @php
                $pihakPemberi = isset($suratKuasa) ? $suratKuasa->pihak()->where('jenis', \App\Enum\PihakSuratKuasaEnum::Pemberi->value)->get()->toJson() : '[]';
                $pihakPenerima = isset($suratKuasa) ? $suratKuasa->pihak()->where('jenis', \App\Enum\PihakSuratKuasaEnum::Penerima->value)->get()->toJson() : '[]';
            @endphp

            // --- Configuration ---
            const isEditMode = {{ isset($suratKuasa) ? 'true' : 'false' }};
            const registrationId = '{{ $idDaftar }}';
            const pemberiStorageKey = `pemberiKuasaList_${registrationId}`;
            const penerimaStorageKey = `penerimaKuasaList_${registrationId}`;
            const isNotUser = {{ Auth::user()->role !== \App\Enum\RoleEnum::User->value ? 'true' : 'false' }};

            // --- State Management ---
            function initializePartyList(storageKey, initialData) {
                // On edit mode, prioritize data from server.
                if (isEditMode) {
                    const storedData = localStorage.getItem(storageKey);
                    // If there's data in localStorage (e.g., user edited then reloaded), use it. Otherwise, use server data.
                    return storedData ? JSON.parse(storedData) : initialData;
                }
                // On add mode, always try to get from localStorage first.
                const storedData = localStorage.getItem(storageKey);
                if (storedData) return JSON.parse(storedData);

                return initialData;
            }

            let pemberiKuasaList = initializePartyList(pemberiStorageKey, {!! $pihakPemberi !!});
            let penerimaKuasaList = initializePartyList(penerimaStorageKey, {!! $pihakPenerima !!});

            // Initial render on page load
            renderTable(pemberiKuasaList, 'pemberi-kuasa-table-body');
            renderTable(penerimaKuasaList, 'penerima-kuasa-table-body');

            // Helper to save to localStorage
            function saveToStorage(key, list) {
                localStorage.setItem(key, JSON.stringify(list));
            }

            // --- JS Sensor Function ---
            function sensor(data, visibleChars = 4) {
                if (!isNotUser || data === null || typeof data === 'undefined') {
                    return data;
                }
                const dataStr = String(data);
                return dataStr.substring(0, visibleChars) + '*'.repeat(Math.max(0, dataStr.length - visibleChars));
            }

            // --- Helper function to render table rows ---
            function renderTable(list, tableBodyId) {
                const tableBody = $(`#${tableBodyId}`);
                tableBody.empty();
                list.forEach((item, index) => {
                    const row = `
                        <tr data-index="${index}">
                            <td>${index + 1}</td>
                            <td>${item.nama}</td>
                            <td>${sensor(item.nik)}</td>
                            <td>${item.pekerjaan}</td>
                            <td>${sensor(item.alamat, 10)}</td>
                            <td>
                                <button type="button" class="btn btn-danger btn-sm btn-pills btn-delete-pihak" data-list="${tableBodyId.includes('pemberi') ? 'pemberi' : 'penerima'}">
                                    <i class="ti ti-trash"></i>
                                </button>
                            </td>
                        </tr>
                    `;
                    tableBody.append(row);
                });
            }

            // --- Logic for adding "Pemberi Kuasa" ---
            $('#form-pemberi-kuasa').on('submit', function(e) {
                e.preventDefault();
                const newPemberi = {
                    nama: $('#pemberi_nama').val(),
                    nik: $('#pemberi_nik').val(),
                    pekerjaan: $('#pemberi_pekerjaan').val(),
                    alamat: $('#pemberi_alamat').val(),
                };
                pemberiKuasaList.push(newPemberi); // Add to array
                saveToStorage(pemberiStorageKey, pemberiKuasaList); // Save to localStorage
                renderTable(pemberiKuasaList, 'pemberi-kuasa-table-body');
                $(this)[0].reset();
                $('#pemberi-surat-kuasa').modal('hide');
                Toast.fire({
                    icon: 'success',
                    title: 'Pemberi kuasa berhasil ditambahkan.'
                });
            });

            // --- Logic for adding "Penerima Kuasa" ---
            $('#form-penerima-kuasa').on('submit', function(e) {
                e.preventDefault();
                const newPenerima = {
                    nama: $('#penerima_nama').val(),
                    nik: $('#penerima_nik').val(),
                    pekerjaan: $('#penerima_pekerjaan').val(),
                    alamat: $('#penerima_alamat').val(),
                };
                penerimaKuasaList.push(newPenerima); // Add to array
                saveToStorage(penerimaStorageKey, penerimaKuasaList); // Save to localStorage
                renderTable(penerimaKuasaList, 'penerima-kuasa-table-body');
                $(this)[0].reset();
                $('#penerima-surat-kuasa').modal('hide');
                Toast.fire({
                    icon: 'success',
                    title: 'Penerima kuasa berhasil ditambahkan.'
                });
            });

            // --- Logic for deleting a party ---
            $(document).on('click', '.btn-delete-pihak', function() {
                const listType = $(this).data('list');
                const rowIndex = $(this).closest('tr').data('index');

                if (listType === 'pemberi') {
                    pemberiKuasaList.splice(rowIndex, 1);
                    saveToStorage(pemberiStorageKey, pemberiKuasaList);
                    renderTable(pemberiKuasaList, 'pemberi-kuasa-table-body');
                } else {
                    penerimaKuasaList.splice(rowIndex, 1);
                    saveToStorage(penerimaStorageKey, penerimaKuasaList);
                    renderTable(penerimaKuasaList, 'penerima-kuasa-table-body');
                }
                Toast.fire({
                    icon: 'info',
                    title: 'Pihak berhasil dihapus.'
                });
            });

            // --- Main form submission logic ---
            $('#form-pendaftaran-surat-kuasa').on('submit', function(e) {
                e.preventDefault();

                // Client-side validation for parties
                if (pemberiKuasaList.length === 0 || penerimaKuasaList.length === 0) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Data Tidak Lengkap',
                        text: 'Kamu harus menambahkan minimal satu Pihak Pemberi Kuasa dan satu Pihak Penerima Kuasa.',
                    });
                    return; // Stop form submission
                }

                // Clear previous validation errors
                $('.form-control, .form-select').removeClass('is-invalid');
                $('.invalid-feedback').text('');

                const form = this;
                const formData = new FormData(form);

                // Append party lists as JSON strings
                formData.append('pemberi_kuasa', JSON.stringify(pemberiKuasaList));
                formData.append('penerima_kuasa', JSON.stringify(penerimaKuasaList));

                const button = $('#btn-submit-pendaftaran');
                const originalButtonText = button.html();
                button.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Memproses...').prop('disabled', true);

                $.ajax({
                    url: $(form).attr('action'),
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: response.message,
                            timer: 2000,
                            showConfirmButton: false,
                            timerProgressBar: true
                        }).then(() => {
                            // Clear localStorage on successful submission
                            localStorage.removeItem(pemberiStorageKey);
                            localStorage.removeItem(penerimaStorageKey);
                            if (isEditMode) {
                                window.location.href = "{{ route('surat-kuasa.index') }}";
                            } else {
                                // Buat URL dasar dengan placeholder, lalu ganti dengan ID dari response.
                                let url = "{{ route('surat-kuasa.pembayaran', ['id' => 'ID_PLACEHOLDER']) }}";
                                window.location.href = url.replace('ID_PLACEHOLDER', response.id);
                            }
                        });
                    },
                    error: function(xhr) {
                        button.html(originalButtonText).prop('disabled', false);
                        const errors = xhr.responseJSON.errors;

                        if (xhr.status === 422) {
                            // Handle validation errors
                            let errorMessages = [];
                            $.each(errors, function(key, value) {
                                // Handle nested errors for parties (e.g., 'pemberi_kuasa.0.nama')
                                if (key.includes('.')) {
                                    if (key.startsWith('pemberi_kuasa')) {
                                        errorMessages.push(`Pemberi Kuasa: ${value[0]}`);
                                    } else if (key.startsWith('penerima_kuasa')) {
                                        errorMessages.push(`Penerima Kuasa: ${value[0]}`);
                                    }
                                } else {
                                    // Handle regular form fields
                                    const el = $(`#${key}`);
                                    el.addClass('is-invalid'); // Add red border to the input
                                    // Find the specific error div by ID and set the text
                                    $(`#${key}-error`).text(value[0]);
                                    errorMessages.push(value[0]);
                                }
                            });

                            // Special check for party lists being empty
                            if (errors.pemberi_kuasa && !Array.isArray(errors.pemberi_kuasa)) {
                                errorMessages.push(errors.pemberi_kuasa[0]);
                            }
                            if (errors.penerima_kuasa && !Array.isArray(errors.penerima_kuasa)) {
                                errorMessages.push(errors.penerima_kuasa[0]);
                            }

                            Swal.fire({
                                icon: 'error',
                                title: 'Oops... Terjadi Kesalahan',
                                html: 'Silakan periksa kembali isian Anda.<br><ul class="text-start mt-2">' + errorMessages.map(e => `<li>${e}</li>`).join('') +
                                    '</ul>',
                            });

                        } else {
                            // Handle other server errors
                            Swal.fire({
                                icon: 'error',
                                title: 'Terjadi Kesalahan Server',
                                text: xhr.responseJSON.message || 'Tidak dapat memproses permintaan Anda.',
                            });
                        }
                    }
                });
            });

            // Simple Toast for notifications
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            });

            // Clear localStorage when user navigates away from the form page
            window.addEventListener('beforeunload', function(e) {
                if (!isEditMode) { // Only clear for 'add' mode to prevent data loss on accidental refresh
                    localStorage.removeItem(pemberiStorageKey);
                    localStorage.removeItem(penerimaStorageKey);
                }
            });
        });
    </script>
@endpush
