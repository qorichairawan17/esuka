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
                        <div class="card-header d-flex flex-wrap align-items-center justify-content-between bg-soft-primary">
                            <h6 class="card-title mb-0 text-dark">Pembayaran Surat Kuasa ID : {{ $suratKuasa->id_daftar }}, Atas Nama {{ $suratKuasa->pemohon }}</h6>
                            <a href="{{ route('surat-kuasa.detail', ['id' => Crypt::encrypt($suratKuasa->id)]) }}" class="btn btn-sm btn-secondary">Kembali</a>
                        </div>
                        <div class="card-body">
                            <div class="alert bg-soft-warning fw-medium" role="alert">
                                <i class="uil uil-info-circle fs-5 align-middle me-1"></i>
                                Silahkan pilih metode pembayaran yang kamu ingin, kemudian lakukan pembayaran dengan nominal
                                yang tertera, selanjutnya upload bukti pembayaran kamu untuk dapat diverifikasi petugas !
                            </div>
                            <div class="row">
                                <div class="{{ $config->logo_bank ? 'col-lg-6' : 'col-lg-12' }} col-md-12 col-sm-12" id="transfer-col">
                                    <div id="pay-transfer" class="card card-payment text-center mb-3" data-payment-method="Transfer Bank" style="cursor: pointer;">
                                        <div class="card-body p-3">
                                            <h6>Transfer Via {{ $config->nama_bank }}</h6>
                                            @if ($config->logo_bank)
                                                <img class="img-fluid" style="width: 100px;" src="{{ asset('storage/' . $config->logo_bank) }}">
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                @if ($config->qris)
                                    <div class="col-lg-6 col-md-12 col-sm-12" id="qris-col">
                                        <div id="pay-qris" class="card card-payment text-center mb-3" data-payment-method="QRIS" style="cursor: pointer;">
                                            <div class="card-body p-3">
                                                <h6>Bayar Dengan QRIS</h6>
                                                <img class="img-fluid" style="width: 150px;" src="{{ asset('images/quick-response-code-indonesia-standard-qris-seeklogo.svg') }}">
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <div id="payment-details" class="mt-3 text-center" style="display: none;">
                                <h6>Silahkan Lakukan Pembayaran PNBP (Penerimaan Negara Bukan Pajak)</h6>
                                <h1 class="text-danger fw-bold">
                                    Rp. 10.000
                                </h1>
                                {{-- Metode Pembayaran Dengan Transfer Bank --}}
                                <div id="transfer-instructions" style="display: none;">
                                    <p>
                                        Buka Aplikasi Mobile Banking Kamu, Pilih Menu Transfer Kemudian Masukkan No Rekening :
                                        <span class="fw-bold">{{ $config->nomor_rekening }} - a/n {{ $config->nama_rekening }} ({{ $config->nama_bank }})</span>.
                                        Kemudian Unggah Bukti Pembayaran Dibawah !
                                    </p>
                                </div>

                                {{-- Metode Pembayaran Dengan QRIS --}}
                                <div id="qris-instructions" style="display: none;">
                                    <img class="img-fluid" src="{{ asset('storage/' . $config->qris) }}" width="250px;">
                                    <p class="mt-1">
                                        Buka Aplikasi Mobile Banking/Dompet Digital (Ovo/Dana/Gopay) Kamu, Pilih Menu QRIS
                                        Lalu Scanning QRCode Diatas Kemudian Unggah Bukti Pembayaran Dibawah !
                                    </p>
                                </div>

                                <form id="payment-form" class="mt-2" enctype="multipart/form-data" action="{{ route('surat-kuasa.pembayaran-store') }}" method="post">
                                    @csrf
                                    <input type="hidden" name="id" id="id" value="{{ Crypt::encrypt($suratKuasa->id) }}" readonly>
                                    <input type="hidden" name="jenis_pembayaran" id="jenis_pembayaran" readonly>
                                    <div class="form-group col-md-6 mx-auto">
                                        <label for="bukti">
                                            Unggah Bukti Pembayaran (Pdf /Image Max 2Mb) <span class="text-danger">*</span>
                                        </label>
                                        <input type="file" name="bukti_pembayaran" id="bukti_pembayaran" class="form-control" required>
                                        <div id="bukti_pembayaran_error" class="text-danger text-sm mt-1 text-start"></div>
                                        <button type="submit" id="submit-button" class="btn btn-primary btn-sm mt-2">
                                            Unggah Sekarang
                                        </button>
                                    </div>
                                </form>
                            </div>
                            <div class="mt-3 d-flex flex-wrap flex-row align-items-center justify-content-between gap-2">
                                <button id="pay-reset" class="btn btn-soft-primary btn-sm order-1 order-md-2 d-grid d-md-inline w-100 w-md-auto" style="display: none;">
                                    Reset Metode Pembayaran
                                </button>
                                <a href="{{ route('surat-kuasa.detail', ['id' => Crypt::encrypt($suratKuasa->id)]) }}"
                                    class="btn btn-secondary btn-sm order-2 order-md-1 d-grid d-md-inline w-100 w-md-auto">
                                    Kembali Ke Detail
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div><!--end container-->

        @include('admin.layout.content-footer')
    </main>
    <!--End page-content" -->
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const payTransfer = document.getElementById('pay-transfer');
            const payQris = document.getElementById('pay-qris');
            const payReset = document.getElementById('pay-reset');
            const paymentDetails = document.getElementById('payment-details');
            const transferInstructions = document.getElementById('transfer-instructions');
            const qrisInstructions = document.getElementById('qris-instructions');
            const jenisPembayaranInput = document.getElementById('jenis_pembayaran');
            const paymentForm = document.getElementById('payment-form');
            const submitButton = document.getElementById('submit-button');
            const buktiPembayaranError = document.getElementById('bukti_pembayaran_error');
            const transferCol = document.getElementById('transfer-col');
            const qrisCol = document.getElementById('qris-col');

            function selectPaymentMethod(method) {
                jenisPembayaranInput.value = method;
                paymentDetails.style.display = 'block';
                payReset.style.display = 'block';

                if (method === 'Transfer Bank') {
                    if (qrisCol) qrisCol.style.display = 'none';
                    if (transferCol) transferCol.classList.replace('col-lg-6', 'col-lg-12');
                    transferInstructions.style.display = 'block';
                    qrisInstructions.style.display = 'none';
                } else if (method === 'QRIS') {
                    if (transferCol) transferCol.style.display = 'none';
                    if (qrisCol) qrisCol.classList.replace('col-lg-6', 'col-lg-12');
                    qrisInstructions.style.display = 'block';
                    transferInstructions.style.display = 'none';
                }
            }

            if (payTransfer) {
                payTransfer.addEventListener('click', () => selectPaymentMethod('Transfer Bank'));
            }

            if (payQris) {
                payQris.addEventListener('click', () => selectPaymentMethod('QRIS'));
            }

            payReset.addEventListener('click', function() {
                if (transferCol) {
                    transferCol.style.display = 'block';
                    transferCol.classList.replace('col-lg-12', 'col-lg-6');
                }
                if (qrisCol) {
                    qrisCol.style.display = 'block';
                    qrisCol.classList.replace('col-lg-12', 'col-lg-6');
                }
                paymentDetails.style.display = 'none';
                payReset.style.display = 'none';
                jenisPembayaranInput.value = '';
                paymentForm.reset();
                buktiPembayaranError.textContent = '';
            });

            paymentForm.addEventListener('submit', async function(e) {
                e.preventDefault();
                buktiPembayaranError.textContent = '';
                submitButton.disabled = true;
                submitButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Mengunggah...';

                const formData = new FormData(paymentForm);
                const action = paymentForm.getAttribute('action');

                try {
                    const response = await fetch(action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                            'Accept': 'application/json',
                        },
                    });

                    const result = await response.json();

                    if (!response.ok) {
                        if (response.status === 422 && result.errors) {
                            if (result.errors.bukti_pembayaran) {
                                buktiPembayaranError.textContent = result.errors.bukti_pembayaran[0];
                            }
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: result.message || 'Silakan periksa kembali input Anda.',
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Terjadi Kesalahan',
                                text: result.message || 'Terjadi kesalahan pada server.',
                            });
                        }
                        throw new Error('Server error');
                    }

                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: result.message,
                        timer: 2000,
                        showConfirmButton: false,
                        timerProgressBar: true
                    }).then(() => {
                        window.location.href = "{{ route('surat-kuasa.detail', ['id' => Crypt::encrypt($suratKuasa->id)]) }}";
                    });

                } catch (error) {
                    console.error('Submission error:', error);
                } finally {
                    submitButton.disabled = false;
                    submitButton.innerHTML = 'Unggah Sekarang';
                }
            });
        });
    </script>
@endpush
