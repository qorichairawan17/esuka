<?php

namespace App\Helpers;

use App\Models\User;
use App\Enum\RoleEnum;
use Illuminate\Support\Facades\Notification;
use App\Notifications\SuratKuasaStatusNotification;
use App\Models\Suratkuasa\PendaftaranSuratKuasaModel;

class NotificationHelper
{
    /**
     * Send a notification to all administrators and superadministrators.
     *
     * @param PendaftaranSuratKuasaModel $pendaftaran
     * @param string $title
     * @param string $message
     * @return void
     */
    public static function sendToAdmins(PendaftaranSuratKuasaModel $pendaftaran, string $title, string $message): void
    {
        $admins = User::whereIn('role', [RoleEnum::Administrator->value, RoleEnum::Superadmin->value])->get();
        if ($admins->isNotEmpty()) {
            Notification::send($admins, new SuratKuasaStatusNotification($pendaftaran, $title, $message));
        }
    }
}
