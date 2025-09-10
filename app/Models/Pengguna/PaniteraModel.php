<?php

namespace App\Models\Pengguna;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PaniteraModel extends Model
{
    use HasFactory;
    protected $table = 'sk_panitera';
    protected $primaryKey = 'id';

    protected $fillable = [
        'nip',
        'nama',
        'jabatan',
        'status',
        'aktif',
        'created_by'
    ];

    public $timestamps = true;

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }
}
