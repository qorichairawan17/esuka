<?php

namespace App\Models\Pengaturan;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PembayaranPnbpModel extends Model
{
    use HasFactory;
    protected $table = 'sk_pembayaran_pnbp';
    protected $primaryKey = 'id';

    protected $fillable = [
        'nama_bank',
        'nomor_rekening',
        'logo_bank',
        'qris',
    ];

    public $timestamps = true;
}
