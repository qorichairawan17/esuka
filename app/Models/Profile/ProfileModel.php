<?php

namespace App\Models\Profile;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProfileModel extends Model
{
    use HasFactory;
    protected $table = 'sk_user_profiles';
    protected $primaryKey = 'id';
    protected $fillable = [
        'nama_depan',
        'nama_belakang',
        'tanggal_lahir',
        'jenis_kelamin',
        'kontak',
        'alamat',
        'foto',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_lahir' => 'encrypted',
            'kontak' => 'encrypted',
            'alamat' => 'encrypted',
        ];
    }

    public $timestamps = true;

    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'profile_id');
    }
}
