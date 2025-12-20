<div class="row">
    <div class="col-lg-6">
        <div class="form-group mb-3">
            <label for="ktp" class="form-label">
                KTP (Kartu Tanda Penduduk) <span class="text-danger">*</span>
            </label>
            <input type="file" class="form-control @error('ktp') is-invalid @enderror" id="ktp" name="ktp" {{ !$isEditMode ? 'required' : '' }}>
            @if ($isEditMode && $suratKuasa->edoc_kartu_tanda_penduduk)
                <div class="mt-2">
                    <a href="{{ route('surat-kuasa.download', ['path' => Crypt::encrypt($suratKuasa->edoc_kartu_tanda_penduduk)]) }}" target="_blank" class="btn btn-sm btn-soft-primary">
                        Lihat KTP Saat Ini
                    </a>
                    <small class="d-block text-muted mt-2">Kosongkan jika tidak ingin mengubah file.</small>
                </div>
            @endif
            <div class="invalid-feedback" id="ktp-error"></div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="form-group mb-3">
            <label for="ktpp" class="form-label">
                KTPP (Kartu Tanda Pengenal Pegawai) <span class="text-danger">*</span>
            </label>
            <input type="file" class="form-control @error('ktpp') is-invalid @enderror" id="ktpp" name="ktpp" {{ !$isEditMode ? 'required' : '' }}>
            @if ($isEditMode && $suratKuasa->edoc_kartu_tanda_pegawai)
                <div class="mt-2">
                    <a href="{{ route('surat-kuasa.download', ['path' => Crypt::encrypt($suratKuasa->edoc_kartu_tanda_pegawai)]) }}" target="_blank" class="btn btn-sm btn-soft-primary">
                        Lihat KTPP Saat Ini
                    </a>
                    <small class="d-block text-muted mt-2">Kosongkan jika tidak ingin mengubah file.</small>
                </div>
            @endif
            <div class="invalid-feedback" id="ktpp-error"></div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-6">
        <div class="form-group mb-3">
            <label for="suratTugas" class="form-label">
                ST (Surat Tugas) <span class="text-danger">*</span>
            </label>
            <input type="file" class="form-control @error('suratTugas') is-invalid @enderror" id="suratTugas" name="suratTugas" {{ !$isEditMode ? 'required' : '' }}>
            @if ($isEditMode && $suratKuasa->edoc_surat_tugas)
                <div class="mt-2">
                    <a href="{{ route('surat-kuasa.download', ['path' => Crypt::encrypt($suratKuasa->edoc_surat_tugas)]) }}" target="_blank" class="btn btn-sm btn-soft-primary">
                        Lihat Surat Tugas Saat Ini
                    </a>
                    <small class="d-block text-muted mt-2">Kosongkan jika tidak ingin mengubah file.</small>
                </div>
            @endif
            <div class="invalid-feedback" id="suratTugas-error"></div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="form-group mb-3">
            <label for="suratKuasa" class="form-label">
                Surat Kuasa <span class="text-danger">*</span>
            </label>
            <input type="file" class="form-control @error('suratKuasa') is-invalid @enderror" id="suratKuasa" name="suratKuasa" {{ !$isEditMode ? 'required' : '' }}>
            @if ($isEditMode && $suratKuasa->edoc_surat_kuasa)
                <div class="mt-2">
                    <a href="{{ route('surat-kuasa.download', ['path' => Crypt::encrypt($suratKuasa->edoc_surat_kuasa)]) }}" target="_blank" class="btn btn-sm btn-soft-primary">
                        Lihat Surat Kuasa Saat Ini
                    </a>
                    <small class="d-block text-muted mt-2">Kosongkan jika tidak ingin mengubah file.</small>
                </div>
            @endif
            <div class="invalid-feedback" id="suratKuasa-error"></div>
        </div>
    </div>
</div>
