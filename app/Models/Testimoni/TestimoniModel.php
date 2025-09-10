<?php

namespace App\Models\Testimoni;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TestimoniModel extends Model
{
    use HasFactory;
    protected $table = 'sk_testimoni';
    protected $primaryKey = 'id';

    protected $fillable = [
        'user_id',
        'rating',
        'testimoni',
        'publish'
    ];

    public $timestamps = true;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
