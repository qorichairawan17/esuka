<?php

namespace App\Models\Suratkuasa;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PembayaranSuratKuasaModel extends Model
{
    use HasFactory;
    protected $table = 'sk_pembayaran_surat_kuasa';
    protected $primaryKey = 'id';

    protected $fillable = [
        'surat_kuasa_id',
        'tanggal_pembayaran',
        'jenis_pembayaran',
        'bukti_pembayaran',
        'user_payment_id',
    ];

    public $timestamps = true;

    public function userPayment(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_payment_id', 'id');
    }

    public function pendaftaran(): BelongsTo
    {
        return $this->belongsTo(PendaftaranSuratKuasaModel::class, 'surat_kuasa_id', 'id');
    }
}
