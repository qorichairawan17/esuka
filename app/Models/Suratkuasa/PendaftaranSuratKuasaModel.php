<?php

namespace App\Models\Suratkuasa;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PendaftaranSuratKuasaModel extends Model
{
    use HasFactory;
    protected $table = 'sk_pendaftaran_surat_kuasa';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id_daftar',
        'tanggal_daftar',
        'migrated_from_id',
        'perihal',
        'jenis_surat',
        'klasifikasi',
        'edoc_kartu_tanda_penduduk',
        'edoc_kartu_tanda_anggota',
        'edoc_kartu_tanda_pegawai',
        'edoc_berita_acara_sumpah',
        'edoc_surat_tugas',
        'edoc_surat_kuasa',
        'tahapan',
        'status',
        'keterangan',
        'user_id',
        'pemohon'
    ];

    public $timestamps = true;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function pihak(): HasMany
    {
        return $this->hasMany(PihakSuratKuasaModel::class, 'surat_kuasa_id', 'id');
    }

    public function register(): HasOne
    {
        return $this->hasOne(RegisterSuratKuasaModel::class, 'surat_kuasa_id', 'id');
    }

    public function pembayaran(): HasOne
    {
        return $this->hasOne(PembayaranSuratKuasaModel::class, 'surat_kuasa_id');
    }
}
