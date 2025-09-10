<?php

namespace App\Models\Pengaturan;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PejabatStrukturalModel extends Model
{
    use HasFactory;
    protected $table = 'sk_pejabat_struktural';
    protected $primaryKey = 'id';

    protected $fillable = [
        'ketua',
        'foto_ketua',
        'wakil_ketua',
        'foto_wakil_ketua',
        'panitera',
        'foto_panitera',
        'sekretaris',
        'foto_sekretaris'
    ];

    public $timestamps = true;
}
