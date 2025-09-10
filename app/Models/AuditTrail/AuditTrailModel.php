<?php

namespace App\Models\AuditTrail;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditTrailModel extends Model
{
    use HasFactory;
    protected $table = 'sk_audit_trail';
    protected $primaryKey = 'id';

    public $fillable = [
        'user_id',
        'ip_address',
        'user_agent',
        'method',
        'url',
        'payload',
    ];
    public $timestamps = true;

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
