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

    public $timestamps = true;

    /**
     * Get the user associated with the profile.
     * Based on the schema (users.profile_id), a Profile has one User.
     */
    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'profile_id');
    }
}
