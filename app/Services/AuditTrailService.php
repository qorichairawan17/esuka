<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\AuditTrail\AuditTrailModel;

class AuditTrailService
{

    public static function record($payload = null)
    {
        try {
            return AuditTrailModel::create([
                'user_id' => Auth::user()->id,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'method' => request()->method(),
                'url' => request()->url(),
                'payload' => Auth::user()->name . ' ' . $payload
            ]);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return null;
        }
    }
}
