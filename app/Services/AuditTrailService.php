<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\AuditTrail\AuditTrailModel;
use App\Models\User;

class AuditTrailService
{
    /**
     * Record an activity to the audit trail with detailed context.
     *
     * @param string $action The description of the action performed (e.g., 'telah memperbarui profil').
     * @param array $context Contains 'old' and 'new' associative arrays of the data that was changed.
     * @param User|null $user The user who performed the action. Defaults to the authenticated user.
     * @return AuditTrailModel|null
     */
    public static function record(string $action, array $context = [], ?User $user = null)
    {
        try {
            $currentUser = $user ?? Auth::user();

            if (!$currentUser) {
                Log::warning('AuditTrailService::record called without a user context.');
                return null;
            }

            $payload = "{$currentUser->name} {$action} pada " . now()->format('d F Y, h:i A') . ".";

            $oldValues = $context['old'] ?? [];
            $newValues = $context['new'] ?? [];

            if (!empty($oldValues) || !empty($newValues)) {
                $details = [];
                $allKeys = array_unique(array_merge(array_keys($oldValues), array_keys($newValues)));

                foreach ($allKeys as $key) {
                    $old = $oldValues[$key] ?? 'kosong';
                    $new = $newValues[$key] ?? 'kosong';
                    if ($old !== $new && !in_array($key, ['password', 'remember_token', 'updated_at'])) { // Ignore sensitive/irrelevant fields
                        $details[] = "mengubah '{$key}' dari '{$old}' menjadi '{$new}'";
                    }
                }
                if (!empty($details)) {
                    $payload .= " Detail perubahan: " . implode(', ', $details) . ".";
                }
            }

            return AuditTrailModel::create([
                'user_id' => $currentUser->id,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'method' => request()->method(),
                'url' => request()->url(),
                'payload' => $payload
            ]);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return null;
        }
    }
}
