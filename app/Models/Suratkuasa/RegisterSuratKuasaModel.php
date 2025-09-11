<?php

namespace App\Models\Suratkuasa;

use App\Models\Pengguna\PaniteraModel;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RegisterSuratKuasaModel extends Model
{
    use HasFactory;
    protected $table = 'sk_register_surat_kuasa';
    protected $primaryKey = 'id';
    protected $fillable = [
        'surat_kuasa_id',
        'uuid',
        'tanggal_register',
        'nomor_surat_kuasa',
        'approval_id',
        'panitera_id',
        'path_file'
    ];

    public $timestamps = true;

    public function pendaftaran(): BelongsTo
    {
        return $this->belongsTo(PendaftaranSuratKuasaModel::class, 'surat_kuasa_id', 'id');
    }

    public function approval(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approval_id', 'id');
    }

    public function panitera(): BelongsTo
    {
        return $this->belongsTo(PaniteraModel::class, 'panitera_id', 'id');
    }
}
