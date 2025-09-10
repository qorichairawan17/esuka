<?php

namespace App\Models\Pengaturan;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AplikasiModel extends Model
{
    use HasFactory;
    protected $table = 'sk_aplikasi';
    protected $primaryKey = 'id';

    protected $fillable = [
        'pengadilan_tingi',
        'pengadilan_negeri',
        'kode_dipa',
        'Kode_surat_kuasa',
        'provinsi',
        'kabupaten',
        'kode_pos',
        'alamat',
        'website',
        'facebook',
        'instagram',
        'youtube',
        'kontak',
        'email',
        'logo',
        'maintance',
    ];

    public $timestamps = true;
}
