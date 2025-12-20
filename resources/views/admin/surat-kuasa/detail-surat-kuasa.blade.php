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
                            <h6 class="card-title mb-0 text-dark">Surat Kuasa Terdaftar ID : {{ $suratKuasa->id_daftar }}, Atas Nama {{ $suratKuasa->pemohon }}</h6>
                            <a class="d-none d-md-inline-block btn btn-sm btn-secondary" href="{{ route('surat-kuasa.index') }}">Kembali</a>
                        </div>
                        <div class="card-body">
                            <ul class="nav nav-pills mb-3 gap-3" id="pills-tab" role="tablist">
                                <li class="nav-item">
                                    <button class="nav-link {{ $suratKuasa->pembayaran ? 'active' : '' }} p-2" id="pills-pendaftaran-tab" data-bs-toggle="pill" data-bs-target="#pills-pendaftaran"
                                        type="button" role="tab" aria-controls="pills-pendaftaran" aria-selected="true">
                                        Informasi Pendaftaran
                                    </button>
                                </li>
                                <li class="nav-item">
                                    <button class="nav-link {{ $suratKuasa->pembayaran ? '' : 'active' }} p-2" id="pills-pembayaran-tab" data-bs-toggle="pill" data-bs-target="#pills-pembayaran"
                                        type="button" role="tab" aria-controls="pills-pembayaran" aria-selected="false">
                                        Informasi Pembayaran
                                    </button>
                                </li>
                            </ul>
                            <div class="tab-content" id="pills-tabContent">
                                <div class="tab-pane fade {{ $suratKuasa->pembayaran ? 'show active' : '' }}" id="pills-pendaftaran" role="tabpanel" aria-labelledby="pills-pendaftaran-tab" tabindex="0">
                                    @if ($suratKuasa->status != \App\Enum\StatusSuratKuasaEnum::Disetujui->value)
                                        @include('admin.surat-kuasa.component.alert-info-surat-kuasa')
                                    @endif
                                    <div class="row mt-3">
                                        <div class="col-lg-6 col-md-6 col-sm-12">
                                            <h6>ID Pendaftaran</h6>
                                            <p>{{ $suratKuasa->id_daftar }}</p>
                                            <h6>Tanggal Pendaftaran</h6>
                                            <p>{{ \Carbon\Carbon::parse($suratKuasa->tanggal_daftar)->format('d-m-Y') }}
                                                {{ $suratKuasa->migrated_from_id ? '' : \Carbon\Carbon::parse($suratKuasa->created_at)->diffForHumans() }}
                                            </p>
                                            @if ($suratKuasa->register)
                                                <h6>Nomor Surat Kuasa</h6>
                                                <p class="text-primary fw-bold">{{ $suratKuasa->register->nomor_surat_kuasa }}</p>
                                            @endif
                                            <h6>Nama</h6>
                                            <p>{{ $suratKuasa->pemohon }}</p>
                                            <h6>Email</h6>
                                            <p>{{ $suratKuasa->user->email }}</p>
                                            <h6>No. Whatsapp</h6>
                                            <p>{{ $suratKuasa->user->profile->kontak ?? '-' }}</p>
                                            <h6>Perihal Surat Kuasa</h6>
                                            <p>{{ $suratKuasa->perihal }}</p>
                                            <h6>Jenis Surat Kuasa</h6>
                                            <p>{{ $suratKuasa->klasifikasi . ' (' . $suratKuasa->jenis_surat . ')' }}</p>
                                        </div>
                                        <div class="col-lg-6 col-md-6 col-sm-12">
                                            <!-- Notif Informasi Verifikasi Surat Kuasa -->
                                            <div class="row border-bottom mb-3">
                                                <div class="col-md-6 col-sm-12">
                                                    <h6>Tahapan</h6>
                                                    <p>
                                                        @php
                                                            $badgeClass = '';
                                                            switch ($suratKuasa->tahapan) {
                                                                case \App\Enum\TahapanSuratKuasaEnum::Pendaftaran->value:
                                                                    $badgeClass = 'bg-soft-primary';
                                                                    break;
                                                                case \App\Enum\TahapanSuratKuasaEnum::Verifikasi->value:
                                                                    $badgeClass = 'bg-soft-success';
                                                                    break;
                                                                case \App\Enum\TahapanSuratKuasaEnum::PerbaikanData->value:
                                                                    $badgeClass = 'bg-soft-warning';
                                                                    break;
                                                                case \App\Enum\TahapanSuratKuasaEnum::PengajuanPerbaikanData->value:
                                                                    $badgeClass = 'bg-soft-info';
                                                                    break;
                                                                case \App\Enum\TahapanSuratKuasaEnum::PerbaikanPembayaran->value:
                                                                    $badgeClass = 'bg-soft-danger';
                                                                    break;
                                                                case \App\Enum\TahapanSuratKuasaEnum::PengajuanPerbaikanPembayaran->value:
                                                                    $badgeClass = 'bg-soft-info';
                                                                    break;
                                                                default:
                                                                    $badgeClass = 'bg-soft-secondary';
                                                            }
                                                        @endphp
                                                        <span class="badge {{ $badgeClass }} fs-6">{{ $suratKuasa->tahapan }}</span>
                                                    </p>
                                                    @if ($suratKuasa->status == \App\Enum\StatusSuratKuasaEnum::Ditolak->value)
                                                        <h6>Status</h6>
                                                        <p>
                                                            <span class="badge bg-danger">Ditolak</span> <br>
                                                        </p>
                                                    @elseif ($suratKuasa->status == \App\Enum\StatusSuratKuasaEnum::Disetujui->value)
                                                        <p>
                                                            <span class="badge bg-success">Disetujui</span> <br>
                                                        </p>
                                                    @endif

                                                </div>
                                                @if ($suratKuasa->register && $suratKuasa->register->approval)
                                                    <div class="col-md-6 col-sm-12">
                                                        <h6>Petugas Verifikasi</h6>
                                                        <p>
                                                            {{ $suratKuasa->register->approval->name }}
                                                        </p>
                                                    </div>
                                                @endif

                                                @if (
                                                    $suratKuasa->status == \App\Enum\StatusSuratKuasaEnum::Ditolak->value ||
                                                        $suratKuasa->tahapan == \App\Enum\TahapanSuratKuasaEnum::PerbaikanData->value ||
                                                        $suratKuasa->tahapan == \App\Enum\TahapanSuratKuasaEnum::PerbaikanPembayaran->value ||
                                                        $suratKuasa->tahapan == \App\Enum\TahapanSuratKuasaEnum::PengajuanPerbaikanData->value ||
                                                        $suratKuasa->tahapan == \App\Enum\TahapanSuratKuasaEnum::PengajuanPerbaikanPembayaran->value)
                                                    <div class="col-md-12 col-sm-12">
                                                        <p class="text-danger">
                                                            Alasan : <br>
                                                            {{ $suratKuasa->keterangan }}
                                                        </p>
                                                    </div>
                                                @elseif ($suratKuasa->tahapan == \App\Enum\TahapanSuratKuasaEnum::Pembayaran->value)
                                                    <div class="col-md-12 col-sm-12">
                                                        <p class="text-default">
                                                            Keterangan : <br>
                                                            Pendaftaran Surat Kuasa Kamu sedang di verifikasi petugas. Mohon untuk melakukan cek berkala pada aplikasi atau notifikasi pada email masuk.
                                                        </p>
                                                    </div>
                                                @endif
                                                @if ($suratKuasa->register && $suratKuasa->register->panitera)
                                                    <div class="col-md-12 col-sm-12">
                                                        <h6>Disetujui Oleh</h6>
                                                        <p>
                                                            <span class="fw-bold text-primary">
                                                                {{ $suratKuasa->register->panitera->nama }}
                                                            </span> <br>
                                                            Panitera {{ $infoApp->pengadilan_negeri }}
                                                        </p>
                                                    </div>
                                                @endif

                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 col-sm-12">
                                                    <h6>Dokumen KTP</h6>
                                                    <a target="_blank"
                                                        href="{{ route('surat-kuasa.preview-file', ['id' => Crypt::encrypt($suratKuasa->id), 'jenis_dokumen' => \App\Enum\JenisDokumenEnum::KTP->name]) }}"
                                                        class="btn btn-sm btn-soft-primary mb-3">
                                                        Klik Untuk Melihat
                                                    </a>
                                                </div>
                                                <div class="col-md-6 col-sm-12">
                                                    @php
                                                        $isAdvokat = $suratKuasa->klasifikasi == \App\Enum\SuratKuasaEnum::Advokat->value;
                                                        $docLabel = $isAdvokat ? 'KTA' : 'KTPP';
                                                        $docEnumName = $isAdvokat ? \App\Enum\JenisDokumenEnum::KTA->name : \App\Enum\JenisDokumenEnum::KTTP->name;
                                                    @endphp
                                                    <h6>Dokumen {{ $docLabel }}</h6>
                                                    <a target="_blank" href="{{ route('surat-kuasa.preview-file', ['id' => Crypt::encrypt($suratKuasa->id), 'jenis_dokumen' => $docEnumName]) }}"
                                                        class="btn btn-sm btn-soft-primary mb-3">
                                                        Klik Untuk Melihat
                                                    </a>
                                                </div>
                                            </div>
                                            <div class="row">
                                                @if ($isAdvokat)
                                                    <div class="col-md-6 col-sm-12">
                                                        <h6>Dokumen BAS</h6>
                                                        <a target="_blank"
                                                            href="{{ route('surat-kuasa.preview-file', ['id' => Crypt::encrypt($suratKuasa->id), 'jenis_dokumen' => \App\Enum\JenisDokumenEnum::BAS->name]) }}"
                                                            class="btn btn-sm btn-soft-primary mb-3">
                                                            Klik Untuk Melihat
                                                        </a>
                                                    </div>
                                                @else
                                                    <div class="col-md-6 col-sm-12">
                                                        <h6>Dokumen Surat Tugas</h6>
                                                        <a target="_blank"
                                                            href="{{ route('surat-kuasa.preview-file', ['id' => Crypt::encrypt($suratKuasa->id), 'jenis_dokumen' => \App\Enum\JenisDokumenEnum::ST->name]) }}"
                                                            class="btn btn-sm btn-soft-primary mb-3">
                                                            Klik Untuk Melihat
                                                        </a>
                                                    </div>
                                                @endif
                                                <div class="col-md-6 col-sm-12">
                                                    <h6>Dokumen Surat Kuasa</h6>
                                                    <a target="_blank"
                                                        href="{{ route('surat-kuasa.preview-file', ['id' => Crypt::encrypt($suratKuasa->id), 'jenis_dokumen' => \App\Enum\JenisDokumenEnum::SK->name]) }}"
                                                        class="btn btn-sm btn-soft-primary mb-3">
                                                        Klik Untuk Melihat
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Pihak -->
                                    <div class="mt-3">
                                        <h6>Pihak Surat Kuasa</h6>
                                        <div class="table-responsive">
                                            <table class="table">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Jenis</th>
                                                        <th>Nama</th>
                                                        <th>NIK</th>
                                                        <th>Pekerjaan</th>
                                                        <th>Alamat</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @if ($suratKuasa->pihak)
                                                        @php
                                                            $no = 1;
                                                        @endphp
                                                        @foreach ($suratKuasa->pihak as $pihak)
                                                            <tr>
                                                                <td>{{ $no }}</td>
                                                                <td class="fw-bold {{ $pihak->jenis == 'Pemberi' ? 'text-primary' : 'text-warning' }}">{{ $pihak->jenis }} Kuasa</td>
                                                                <td>{{ $pihak->nama }}</td>
                                                                <td>
                                                                    @if (Auth::user()->role == \App\Enum\RoleEnum::Administrator->value || Auth::user()->role == \App\Enum\RoleEnum::Superadmin->value)
                                                                        {{ is_numeric($pihak->nik) ? \App\Helpers\StringHelper::censorData($pihak->nik) : $pihak->nik }}
                                                                    @else
                                                                        {{ $pihak->nik }}
                                                                    @endif
                                                                </td>
                                                                <td>{{ $pihak->pekerjaan }}</td>
                                                                <td>
                                                                    @if (Auth::user()->role == \App\Enum\RoleEnum::Administrator->value || Auth::user()->role == \App\Enum\RoleEnum::Superadmin->value)
                                                                        {{ strlen($pihak->alamat) > 4 ? Str::limit(\App\Helpers\StringHelper::censorData($pihak->alamat), 20) : $pihak->alamat }}
                                                                    @else
                                                                        {{ Str::limit($pihak->alamat, 20) }}
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                            @php
                                                                $no++;
                                                            @endphp
                                                        @endforeach
                                                    @endif
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade {{ $suratKuasa->pembayaran ? '' : 'show active' }}" id="pills-pembayaran" role="tabpanel" aria-labelledby="pills-pembayaran-tab" tabindex="0">
                                    @if ($suratKuasa->pembayaran)
                                        @if ($suratKuasa->status != \App\Enum\StatusSuratKuasaEnum::Disetujui->value)
                                            @include('admin.surat-kuasa.component.alert-info-pembayaran')
                                        @endif

                                        <div class="row mt-3">
                                            <div class="col-lg-6 col-md-6 col-sm-12">
                                                <h6>Jenis Pembayaran</h6>
                                                <p>{{ $suratKuasa->pembayaran->jenis_pembayaran }}</p>
                                                <h6>Tanggal Pembayaran</h6>
                                                <p>
                                                    {{ Carbon\Carbon::parse($suratKuasa->pembayaran->created_at)->isoFormat('dddd, D MMMM Y') }}
                                                </p>
                                            </div>
                                            <div class="col-lg-6 col-md-6 col-sm-12">
                                                <h6>Waktu Pembayaran</h6>
                                                <p>
                                                    {{ $suratKuasa->pembayaran->created_at }} - Diperbarui {{ $suratKuasa->pembayaran->updated_at }}
                                                </p>
                                                <h6>Bukti Pembayaran</h6>
                                                <a target="_blank" href="{{ route('surat-kuasa.pembayaran-preview', ['id' => Crypt::encrypt($suratKuasa->id)]) }}"
                                                    class="btn btn-sm  btn-soft-primary mb-3">
                                                    Klik Untuk Melihat
                                                </a>
                                            </div>
                                        </div>
                                    @else
                                        <div class="alert bg-soft-warning fw-medium" role="alert">
                                            <i class="uil uil-info-circle fs-5 align-middle me-1"></i>
                                            Pendaftaran Surat Kuasa kamu belum dibayar, silahkan bayar sekarang dengan mengklik
                                            tombol pembayaran dibawah ini !
                                        </div>
                                        <a href="{{ route('surat-kuasa.pembayaran', ['id' => Crypt::encrypt($suratKuasa->id)]) }}" class="btn  btn-sm btn-primary">
                                            Pembayaran <i class="uil uil-arrow-right"></i>
                                        </a>
                                    @endif
                                </div>
                            </div>

                            @if ($suratKuasa->status == \App\Enum\StatusSuratKuasaEnum::Disetujui->value && $suratKuasa->register->uuid != null)
                                <!-- Unduh Barcode -->
                                <div class="mt-3 bg-soft-success p-2 border-rounded">
                                    <h6 class="text-dark">Unduh Barcode Pendaftaran Surat Kuasa Elektronik </h6>
                                    <a href="{{ route('surat-kuasa.barcode', ['id' => Crypt::encrypt($suratKuasa->id)]) }}" title="Unduh Barcode Pendaftaran Surat Kuasa Elektronik"
                                        class="btn  btn-success btn-sm">
                                        Klik Untuk Mengunduh <i class="uil uil-file-download"></i>
                                    </a>
                                </div>
                            @endif
                            <div class="mt-3 border-top pt-3">
                                <div class="d-flex flex-wrap align-items-center justify-content-between">
                                    {{-- Tombol Aksi untuk User --}}
                                    @if (Auth::user()->role == \App\Enum\RoleEnum::User->value)
                                        <div class="d-flex flex-wrap align-items-center gap-2">
                                            @if ($suratKuasa->tahapan == \App\Enum\TahapanSuratKuasaEnum::PerbaikanData->value)
                                                <a href="{{ route('surat-kuasa.form', ['param' => 'edit', 'klasifikasi' => $suratKuasa->klasifikasi, 'id' => Crypt::encrypt($suratKuasa->id)]) }}"
                                                    class="btn btn-warning btn-sm">
                                                    Perbaiki Data Pendaftaran
                                                </a>
                                            @endif
                                            @if ($suratKuasa->tahapan == \App\Enum\TahapanSuratKuasaEnum::PerbaikanPembayaran->value)
                                                <a href="{{ route('surat-kuasa.pembayaran', ['id' => Crypt::encrypt($suratKuasa->id)]) }}" class="btn btn-warning btn-sm">
                                                    Perbaiki Pembayaran
                                                </a>
                                            @endif
                                        </div>
                                    @endif

                                    {{-- Tombol Aksi untuk Admin/Superadmin --}}
                                    @if (Auth::user()->role != \App\Enum\RoleEnum::User->value &&
                                            in_array($suratKuasa->tahapan, [
                                                \App\Enum\TahapanSuratKuasaEnum::Pembayaran->value,
                                                \App\Enum\TahapanSuratKuasaEnum::PerbaikanData->value,
                                                \App\Enum\TahapanSuratKuasaEnum::PengajuanPerbaikanData->value,
                                                \App\Enum\TahapanSuratKuasaEnum::PerbaikanPembayaran->value,
                                                \App\Enum\TahapanSuratKuasaEnum::PengajuanPerbaikanPembayaran->value,
                                            ]))
                                        <div class="d-flex flex-wrap align-items-center gap-2">
                                            <h6>Verifikasi Pendaftaran</h6>
                                            <button class="btn btn-success btn-sm mb-1 d-grid" data-bs-toggle="modal" data-bs-target="#setujui-surat-kuasa">Setujui</button>
                                            <button class="btn btn-danger btn-sm mb-1 d-grid" data-bs-toggle="modal" data-bs-target="#tolak-surat-kuasa">Tolak</button>
                                        </div>
                                    @endif
                                    <a href="{{ route('surat-kuasa.index') }}" class="btn btn-secondary btn-sm mb-1 d-grid">
                                        Kembali
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div><!--end container-->

        @if (Auth::user()->role != \App\Enum\RoleEnum::User->value &&
                in_array($suratKuasa->tahapan, [
                    \App\Enum\TahapanSuratKuasaEnum::Pembayaran->value,
                    \App\Enum\TahapanSuratKuasaEnum::PerbaikanData->value,
                    \App\Enum\TahapanSuratKuasaEnum::PengajuanPerbaikanData->value,
                    \App\Enum\TahapanSuratKuasaEnum::PerbaikanPembayaran->value,
                    \App\Enum\TahapanSuratKuasaEnum::PengajuanPerbaikanPembayaran->value,
                ]))

            <!-- Modal Setujui -->
            <div class="modal fade" id="setujui-surat-kuasa" tabindex="-1" aria-labelledby="setujui-surat-kuasa-title" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content rounded shadow border-0">
                        <form id="form-approve">
                            @csrf
                            <input type="hidden" name="id" value="{{ Crypt::encrypt($suratKuasa->id) }}">
                            <div class="modal-header border-bottom">
                                <h5 class="modal-title" id="setujui-surat-kuasa-title">Setujui Surat Kuasa ID : {{ $suratKuasa->id_daftar }}</h5>
                                <button type="button" class="btn btn-icon btn-close" data-bs-dismiss="modal" id="close-modal"><i class="uil uil-times fs-4 text-dark"></i></button>
                            </div>
                            <div class="modal-body">
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" role="switch" id="manualNomorSwitch">
                                    <label class="form-check-label" for="manualNomorSwitch">Gunakan Nomor Manual</label>
                                </div>
                                <div class="form-group mb-3">
                                    <label for="nomor_surat_kuasa">
                                        Nomor Surat Kuasa <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" id="nomor_surat_kuasa" name="nomor_surat_kuasa" value="{{ $nomorSuratKuasaBaru }}" readonly required>
                                    <div class="invalid-feedback" id="nomor_surat_kuasa-error"></div>
                                </div>
                                <div class="form-group">
                                    <label for="panitera_id">
                                        Pilih Panitera <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select" id="panitera_id" name="panitera_id" required>
                                        <option value="" selected disabled>--- Pilih Panitera ---</option>
                                        @foreach ($panitera as $row)
                                            <option value="{{ $row->id }}">{{ $row->nama }}</option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback" id="panitera_id-error"></div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" id="btn-approve" class="btn btn-success btn-sm">Setujui Pendaftaran</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Modal Tolak -->
            <div class="modal fade" id="tolak-surat-kuasa" tabindex="-1" aria-labelledby="tolak-surat-kuasa-title" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content rounded shadow border-0">
                        <form id="form-reject">
                            @csrf
                            <input type="hidden" name="id" value="{{ Crypt::encrypt($suratKuasa->id) }}">
                            <div class="modal-header border-bottom">
                                <h5 class="modal-title" id="tolak-surat-kuasa-title">Tolak Surat Kuasa ID : {{ $suratKuasa->id_daftar }}</h5>
                                <button type="button" class="btn btn-icon btn-close" data-bs-dismiss="modal" id="close-modal"><i class="uil uil-times fs-4 text-dark"></i></button>
                            </div>
                            <div class="modal-body">
                                <div class="form-group mb-3">
                                    <label for="tahapan">Tolak Tahapan <span class="text-danger">*</span></label>
                                    <select class="form-select" id="tahapan" name="tahapan" required>
                                        <option value="" selected disabled>--- Pilih Tahapan ---</option>
                                        <option value="{{ \App\Enum\TahapanSuratKuasaEnum::PerbaikanData->value }}">Perbaikan Data</option>
                                        <option value="{{ \App\Enum\TahapanSuratKuasaEnum::PerbaikanPembayaran->value }}">Perbaikan Pembayaran</option>
                                    </select>
                                    <div class="invalid-feedback" id="tahapan-error"></div>
                                </div>
                                <div class="form-group">
                                    <label for="keterangan">Alasan Ditolak <span class="text-danger">*</span></label>
                                    <textarea class="form-control" required id="keterangan" name="keterangan" placeholder="Isi alasan penolakan pendaftaran surat kuasa..."></textarea>
                                    <div class="invalid-feedback" id="keterangan-error"></div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" id="btn-reject" class="btn btn-danger btn-sm">Tolak Pendaftaran</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif

        @include('admin.layout.content-footer')
        <!-- End -->
    </main>
    <!--End page-content" -->
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // --- Approve Modal Logic ---
            const manualSwitch = document.getElementById('manualNomorSwitch');
            const nomorInput = document.getElementById('nomor_surat_kuasa');
            const formApprove = document.getElementById('form-approve');

            if (manualSwitch) {
                manualSwitch.addEventListener('change', function() {
                    if (this.checked) {
                        nomorInput.readOnly = false;
                    } else {
                        nomorInput.readOnly = true;
                        nomorInput.value = "{{ $nomorSuratKuasaBaru }}"; // Reset to auto value
                    }
                });
            }

            if (formApprove) {
                formApprove.addEventListener('submit', function(e) {
                    e.preventDefault();
                    handleFormSubmission(this, "{{ route('surat-kuasa.verifikasi.approve') }}", 'btn-approve');
                });
            }

            // --- Reject Modal Logic ---
            const formReject = document.getElementById('form-reject');
            if (formReject) {
                formReject.addEventListener('submit', function(e) {
                    e.preventDefault();
                    handleFormSubmission(this, "{{ route('surat-kuasa.verifikasi.reject') }}", 'btn-reject');
                });
            }

            // --- General Form Submission Handler ---
            async function handleFormSubmission(form, url, buttonId) {
                const button = document.getElementById(buttonId);
                const originalButtonHtml = button.innerHTML;
                button.disabled = true;
                button.innerHTML = `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Memproses...`;

                // Clear previous errors
                form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
                form.querySelectorAll('.invalid-feedback').forEach(el => el.textContent = '');

                const formData = new FormData(form);

                try {
                    const response = await fetch(url, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                        }
                    });

                    const result = await response.json();

                    if (!response.ok) {
                        if (response.status === 422) {
                            // Validation errors
                            Object.keys(result.errors).forEach(key => {
                                const errorEl = document.getElementById(`${key}-error`);
                                const inputEl = document.getElementById(key);
                                if (errorEl) errorEl.textContent = result.errors[key][0];
                                if (inputEl) inputEl.classList.add('is-invalid');
                            });
                            Swal.fire('Validasi Gagal', result.message, 'error');
                        } else {
                            // Other server errors (409, 500, etc)
                            Swal.fire('Terjadi Kesalahan', result.message, 'error');
                        }
                        throw new Error('Server error');
                    }

                    // Success
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: result.message,
                        timer: 2000,
                        showConfirmButton: false,
                        timerProgressBar: true,
                    }).then(() => {
                        window.location.reload();
                    });

                } catch (error) {
                    console.error('Form submission error:', error);
                } finally {
                    button.disabled = false;
                    button.innerHTML = originalButtonHtml;
                }
            }
        });
    </script>
@endpush
