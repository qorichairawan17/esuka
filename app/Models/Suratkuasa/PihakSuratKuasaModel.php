<?php

namespace App\Models\Suratkuasa;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PihakSuratKuasaModel extends Model
{
    use HasFactory;
    protected $table = 'sk_pihak_surat_kuasa';
    protected $primaryKey = 'id';

    protected $fillable = [
        'surat_kuasa_id',
        'nik',
        'nama',
        'pekerjaan',
        'alamat',
        'jenis'
    ];

    public $timestamps = true;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'nik' => 'encrypted',
            'alamat' => 'encrypted',
        ];
    }
}
