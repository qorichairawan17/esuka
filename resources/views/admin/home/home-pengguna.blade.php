@extends('admin.layout.body')
@section('title', $title)
@section('content')
    <!-- Start Page Content -->
    <main class="page-content bg-light">

        @include('admin.component.top-header')

        <div class="container-fluid">
            <div class="layout-specing">

                @include('admin.component.breadcumb')

                <h5 class="text-primary mb-3">Hallo, {{ Auth::user()->name }}</h5>

                <div class="card shadow">
                    <div class="card-body">
                        <div class="alert alert-outline-primary alert-pills" role="alert">
                            <span class="badge rounded-pill bg-danger"> New </span>
                            <span class="alert-content"> Pintasan Scrolling</span>
                        </div>

                        <div class="scrolling-wrapper pb-2">
                            <a href="{{ route('surat-kuasa.index') }}" class="card card-shortcut shadow">
                                <div class="card-body">
                                    <i class="ti ti-file text-primary"></i>
                                    <p class="mb-0">Pendaftaran</p>
                                </div>
                            </a>
                            <a href="#" data-bs-toggle="modal" data-bs-target="#suratKuasaModel" class="card card-shortcut shadow">
                                <div class="card-body">
                                    <i class="ti ti-credit-card text-primary"></i>
                                    <p class="mb-0">Pembayaran</p>
                                </div>
                            </a>
                            <a href="#" data-bs-toggle="modal" data-bs-target="#testimoniModal" class="card card-shortcut shadow">
                                <div class="card-body">
                                    <i class="ti ti-heart text-warning"></i>
                                    <p class="mb-0">Testimoni</p>
                                </div>
                            </a>
                            <a href="#" class="card card-shortcut shadow">
                                <div class="card-body">
                                    <i class="ti ti-help text-primary"></i>
                                    <p class="mb-0">Panduan</p>
                                </div>
                            </a>
                            <a href="{{ route('profile.index') }}" class="card card-shortcut shadow">
                                <div class="card-body">
                                    <i class="ti ti-user text-primary"></i>
                                    <p class="mb-0">Profil Akun</p>
                                </div>
                            </a>
                            <a href="{{ route('audit-trail.index') }}" class="card card-shortcut shadow">
                                <div class="card-body">
                                    <i class="ti ti-database text-primary"></i>
                                    <p class="mb-0">Audit Trail</p>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-lg-8 col-sm-12">
                        <div class="card border-0 rounded shadow p-4 mb-3">
                            <h5 class="mb-0 mb-3">Grafik Pendaftaran Surat Kuasa</h5>
                            <div>
                                <canvas id="myChart"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-sm-12">
                        <div class="card shadow border-0 mb-3">
                            <div class="p-4 border-bottom">
                                <div class="d-flex justify-content-between">
                                    <h6 class="mb-0 fw-bold">Pendaftaran Terbaru</h6>

                                    <a href="#!" class="text-primary">
                                        Lihat Semua <i class="uil uil-arrow-right align-middle"></i>
                                    </a>
                                </div>
                            </div>

                            <div class="p-4 simplebar-scrollable-y" data-simplebar="init" style="height: 370px;">
                                <div class="simplebar-wrapper" style="margin: -24px;">
                                    <div class="simplebar-height-auto-observer-wrapper">
                                        <div class="simplebar-height-auto-observer"></div>
                                    </div>
                                    <div class="simplebar-mask">
                                        <div class="simplebar-offset" style="right: 0px; bottom: 0px;">
                                            <div class="simplebar-content-wrapper" tabindex="0" role="region" aria-label="scrollable content" style="height: 100%; overflow: hidden scroll;">
                                                <div class="simplebar-content" style="padding: 24px;">
                                                    <a href="javascript:void(0)" class="features feature-primary key-feature d-flex align-items-center justify-content-between mb-3">
                                                        <div class="d-flex align-items-center">
                                                            <div class="flex-1">
                                                                <h6 class="mb-0 text-dark">Nomor Pendaftaran : #1214</h6>
                                                                <small class="text-muted">12 Menit yang lalu</small>
                                                            </div>
                                                        </div>

                                                        <span class="badge bg-success">Disetujui</span>
                                                    </a>
                                                    <a href="javascript:void(0)" class="features feature-primary key-feature d-flex align-items-center justify-content-between mb-3">
                                                        <div class="d-flex align-items-center">
                                                            <div class="flex-1">
                                                                <h6 class="mb-0 text-dark">Nomor Pendaftaran : #1214</h6>
                                                                <small class="text-muted">12 Menit yang lalu</small>
                                                            </div>
                                                        </div>
                                                        <span class="badge bg-danger">Ditolak</span>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="simplebar-placeholder" style="width: 306px; height: 469px;"></div>
                                </div>
                                <div class="simplebar-track simplebar-horizontal" style="visibility: hidden;">
                                    <div class="simplebar-scrollbar" style="width: 0px; display: none; transform: translate3d(0px, 0px, 0px);"></div>
                                </div>
                                <div class="simplebar-track simplebar-vertical" style="visibility: visible;">
                                    <div class="simplebar-scrollbar" style="height: 284px; transform: translate3d(0px, 0px, 0px); display: block;">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div><!--end container-->
        @include('admin.layout.content-footer')
        <!-- End -->
    </main>
    <!-- Modal -->
    <div class="modal fade" id="testimoniModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded shadow border-0">
                <div class="modal-header border-bottom">
                    <h5 class="modal-title" id="testimoniModal">Kirim Testimoni Kamu</h5>
                    <button type="button" class="btn btn-icon btn-close" data-bs-dismiss="modal" id="close-modal">
                        <i class="uil uil-times fs-4 text-dark"></i></button>
                </div>

                <form id="formTestimoni" class="needs-validation" novalidate>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label d-block">Penilaian <span class="text-danger">*</span></label>
                            <fieldset class="rating" aria-label="Penilaian bintang">
                                <input type="radio" id="star5" name="rating" value="5" required>
                                <label for="star5" title="5 - Sangat Puas"></label>

                                <input type="radio" id="star4" name="rating" value="4">
                                <label for="star4" title="4 - Puas"></label>

                                <input type="radio" id="star3" name="rating" value="3">
                                <label for="star3" title="3 - Cukup"></label>

                                <input type="radio" id="star2" name="rating" value="2">
                                <label for="star2" title="2 - Tidak Puas"></label>

                                <input type="radio" id="star1" name="rating" value="1">
                                <label for="star1" title="1 - Sangat Tidak Puas"></label>
                            </fieldset>
                            <div class="form-text">
                                Nilai: <strong id="ratingValue">0</strong> / 5
                            </div>
                            <div class="invalid-feedback d-block" id="ratingInvalid" style="display:none;">Silakan pilih
                                jumlah bintang.</div>
                        </div>

                        <div class="mb-3">
                            <label for="pesan" class="form-label">Testimoni <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="pesan" name="pesan" rows="4" placeholder="Tulis pengalaman Kamu..." required></textarea>
                            <div class="invalid-feedback">Testimoni wajib diisi.</div>
                        </div>
                    </div>

                    <div class="modal-footer d-flex justify-content-center">
                        <button type="submit" class="btn btn-sm btn-primary w-50">Kirim</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="suratKuasaModel" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded shadow border-0">
                <div class="modal-header border-bottom">
                    <h5 class="modal-title" id="suratKuasaModel">Pilih Surat Kuasa</h5>
                    <button type="button" class="btn btn-icon btn-close" data-bs-dismiss="modal" id="close-modal">
                        <i class="uil uil-times fs-4 text-dark"></i></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="suratKuasa">
                            Surat Kuasa <span class="text-danger">*</span>
                        </label>
                        <select class="form-control" id="suratKuasa" name="suratKuasa" required>
                            <option value="">Pilih Surat Kuasa</option>
                            <option value="1">Surat Kuasa 1</option>
                            <option value="2">Surat Kuasa 2</option>
                            <option value="3">Surat Kuasa 3</option>
                            <option value="4">Surat Kuasa 4</option>
                        </select>
                    </div>

                    <div class="mt-3" id="info-surat-kuasa">
                        <h6>Informasi Surat Kuasa Kamu</h6>
                        <p>
                            Tanggal : {{ \Carbon\Carbon::now()->format('d-m-Y') }} <br>
                            Perihal : Lorem ipsum dolor sit amet
                        </p>
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-center">
                    <button type="submit" class="btn btn-sm btn-primary w-50">Lanjut Pembayaran</button>
                </div>
            </div>
        </div>
    </div>
    <!--End page-content" -->
    @push('scripts')
        <script src="{{ asset('admin/assets/plugins/chartjs/dist/chart.umd.js') }}"></script>
        <script>
            const ctx = document.getElementById('myChart');
            ctx.style.width = '100%';

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September',
                        'Oktober', 'November', 'Desember'
                    ],
                    datasets: [{
                        label: 'Surat Kuasa',
                        data: [12, 19, 3, 5, 2, 3],
                        borderWidth: 1
                    }]
                },
                options: {
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        </script>
        <script>
            // Update tampilan nilai rating di bawah bintang
            const ratingInputs = document.querySelectorAll('input[name="rating"]');
            const ratingValueEl = document.getElementById('ratingValue');
            const ratingInvalid = document.getElementById('ratingInvalid');

            ratingInputs.forEach(input => {
                input.addEventListener('change', () => {
                    ratingValueEl.textContent = input.value;
                    ratingInvalid.style.display = 'none';
                });
            });

            // Validasi form Bootstrap + submit demo
            const form = document.getElementById('formTestimoni');
            form.addEventListener('submit', function(e) {
                e.preventDefault();

                // Validasi built-in Bootstrap
                if (!form.checkValidity()) {
                    e.stopPropagation();
                }

                // Pastikan rating terpilih (karena input radio disembunyikan)
                const ratingChecked = document.querySelector('input[name="rating"]:checked');
                if (!ratingChecked) {
                    ratingInvalid.style.display = 'block';
                }

                form.classList.add('was-validated');

                if (form.checkValidity() && ratingChecked) {
                    // Ambil data
                    const data = {
                        nama: document.getElementById('nama').value.trim(),
                        rating: Number(ratingChecked.value),
                        pesan: document.getElementById('pesan').value.trim()
                    };

                    // DEMO: tampilkan hasil di halaman
                    const hasil = document.getElementById('hasil');
                    hasil.classList.remove('d-none');
                    hasil.querySelector('.alert').innerHTML =
                        `<strong>Terima kasih, ${data.nama}!</strong><br>` +
                        `Rating: ${'★'.repeat(data.rating)}${'☆'.repeat(5 - data.rating)} (${data.rating}/5)<br>` +
                        `Testimoni: ${data.pesan}`;

                    // Tutup modal & reset form
                    const modalEl = document.getElementById('testimoniModal');
                    const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
                    modal.hide();

                    form.reset();
                    form.classList.remove('was-validated');
                    ratingValueEl.textContent = '0';
                    ratingInvalid.style.display = 'none';
                }
            });
        </script>
    @endpush

@endsection
