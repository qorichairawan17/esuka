<div class="row">
    <div class="col-lg-6">
        <div class="form-group mb-3">
            <label for="ktp" class="form-label">
                KTP (Kartu Tanda Penduduk) <span class="text-danger">*</span>
            </label>
            <input type="file" class="form-control" id="ktp" name="ktp" {{ !$isEditMode ? 'required' : '' }}>
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
            <label for="kta" class="form-label">
                KTA (Kartu Tanda Anggota) <span class="text-danger">*</span>
            </label>
            <input type="file" class="form-control" id="kta" name="kta" {{ !$isEditMode ? 'required' : '' }}>
            @if ($isEditMode && $suratKuasa->edoc_kartu_tanda_anggota)
                <div class="mt-2">
                    <a href="{{ route('surat-kuasa.download', ['path' => Crypt::encrypt($suratKuasa->edoc_kartu_tanda_anggota)]) }}" target="_blank" class="btn btn-sm btn-soft-primary">
                        Lihat KTA Saat Ini
                    </a>
                    <small class="d-block text-muted mt-2">Kosongkan jika tidak ingin mengubah file.</small>
                </div>
            @endif
            <div class="invalid-feedback" id="kta-error"></div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-6">
        <div class="form-group mb-3">
            <label for="bas" class="form-label">
                BAS (Berita Acara Sumpah) <span class="text-danger">*</span>
            </label>
            <input type="file" class="form-control" id="bas" name="bas" {{ !$isEditMode ? 'required' : '' }}>
            @if ($isEditMode && $suratKuasa->edoc_berita_acara_sumpah)
                <div class="mt-2">
                    <a href="{{ route('surat-kuasa.download', ['path' => Crypt::encrypt($suratKuasa->edoc_berita_acara_sumpah)]) }}" target="_blank" class="btn btn-sm btn-soft-primary">
                        Lihat BAS Saat Ini
                    </a>
                    <small class="d-block text-muted mt-2">Kosongkan jika tidak ingin mengubah file.</small>
                </div>
            @endif
            <div class="invalid-feedback" id="bas-error"></div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="form-group mb-3">
            <label for="suratKuasa" class="form-label">
                Surat Kuasa <span class="text-danger">*</span>
            </label>
            <input type="file" class="form-control" id="suratKuasa" name="suratKuasa" {{ !$isEditMode ? 'required' : '' }}>
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
