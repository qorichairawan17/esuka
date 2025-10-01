<?php

namespace App\Models\Sync;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StagingSyncSuratKuasaModel extends Model
{
    use HasFactory;
    protected $table = 'staging_sync_surat_kuasa';

    protected $primaryKey = 'id';

    protected $fillable = [
        'source_id',
        'user_id',
        'tanggal_daftar',
        'email',
        'nama_lengkap',
        'perihal',
        'jenis_surat',
        'edoc_kartu_tanda_penduduk',
        'edoc_kartu_tanda_anggota',
        'edoc_kartu_tanda_pegawai',
        'edoc_berita_acara_sumpah',
        'edoc_surat_tugas',
        'edoc_surat_kuasa',
        'id_pemberi',
        'nik_pemberi',
        'nama_pemberi',
        'pekerjaan_pemberi',
        'alamat_pemberi',
        'id_penerima',
        'nik_penerima',
        'nama_penerima',
        'pekerjaan_penerima',
        'alamat_penerima',
        'bukti_pembayaran',
        'status',
        'tanggal_bayar',
        'keterangan',
        'panitera',
        'nomor_surat_kuasa',
        'tanggal_disetujui',
        'klasifikasi',
    ];

    public $timestamps = true;
}
